const crypto = require('crypto');
const prisma = require('../config/prisma');
const { generateRandomSlug, buildInitialInvitationData } = require('./invitationHelper');

class PaymentService {
  async initiateOrder(user, template, plan, existingInvitation = null) {
    const now = new Date();
    const timestamp = now.toISOString().replace(/[-:T.Z]/g, '').slice(0, 14);
    const rand = crypto.randomBytes(2).toString('hex').toUpperCase();
    const orderId = `INV-${timestamp}-${rand}`;

    const rawPayload = {
      template_id: Number(template.id),
      plan_id: Number(plan.id),
      category: template.category,
      is_renewal: !!existingInvitation,
      snap_token: `MOCK_SNAP_${orderId}`,
      redirect_url: `https://app.sandbox.midtrans.com/snap/v2/vtweb/mock_${orderId}`,
    };

    const transaction = await prisma.transactions.create({
      data: {
        user_id: user.id,
        invitation_id: existingInvitation ? existingInvitation.id : null,
        plan_id: plan.id,
        order_id: orderId,
        amount: plan.price,
        status: 'pending',
        gateway: 'midtrans',
        raw_payload: rawPayload,
        created_at: new Date(),
        updated_at: new Date(),
      },
    });

    return {
      transaction,
      snap_token: rawPayload.snap_token,
      redirect_url: rawPayload.redirect_url,
    };
  }

  verifySignature(payload, serverKey) {
    if (!serverKey) return true; // in sandbox dev without key configured
    const { order_id, status_code, gross_amount, signature_key } = payload;
    if (!signature_key) return false;

    const signatureSource = `${order_id}${status_code}${gross_amount}${serverKey}`;
    const hash = crypto.createHash('sha512').update(signatureSource).digest('hex');
    return hash.toLowerCase() === signature_key.toLowerCase();
  }

  async processWebhook(payload, signatureHeader = '', bypassSignatureForTesting = false) {
    const orderId = payload.order_id;
    const transStatus = payload.transaction_status;

    let newStatus = 'pending';
    if (['capture', 'settlement'].includes(transStatus)) {
      newStatus = 'paid';
    } else if (['deny', 'cancel', 'expire'].includes(transStatus)) {
      newStatus = transStatus === 'expire' ? 'expired' : 'failed';
    }

    const transaction = await prisma.transactions.findUnique({
      where: { order_id: orderId },
      include: {
        plans: true,
        invitations: true,
        users: true,
      },
    });

    if (!transaction) {
      throw new Error(`Transaction with order_id [${orderId}] not found.`);
    }

    if (transaction.status === 'paid' && newStatus === 'paid') {
      return {
        success: true,
        message: 'Transaction already processed.',
        order_id: orderId,
        status: 'paid',
        invitation_id: transaction.invitation_id,
      };
    }

    if (newStatus === 'paid') {
      let invitationId = transaction.invitation_id;

      if (!invitationId) {
        // First-time purchase: create invitation in draft status
        const raw = transaction.raw_payload || {};
        const templateId = raw.template_id ? BigInt(raw.template_id) : BigInt(1);
        const template = await prisma.templates.findUnique({ where: { id: templateId } });
        const category = template ? template.category : 'wedding';

        let slug = generateRandomSlug(category);
        // check unique slug
        while (await prisma.invitations.findUnique({ where: { slug } })) {
          slug = generateRandomSlug(category);
        }

        const durationDays = transaction.plans ? transaction.plans.duration_days : 30;
        const expiresAt = new Date();
        expiresAt.setDate(expiresAt.getDate() + durationDays);

        const initialData = buildInitialInvitationData(category, transaction.users ? transaction.users.name : 'Ahmad');

        const invitation = await prisma.invitations.create({
          data: {
            user_id: transaction.user_id,
            template_id: template ? template.id : BigInt(1),
            plan_id: transaction.plan_id,
            slug,
            category,
            status: 'draft',
            published_at: null,
            expires_at: expiresAt,
            data: initialData,
            created_at: new Date(),
            updated_at: new Date(),
          },
        });

        invitationId = invitation.id;

        // Create default event (Akad Nikah / Walimatul Khitan / Acara Utama)
        const eventStart = new Date();
        eventStart.setDate(eventStart.getDate() + 14);
        eventStart.setHours(9, 0, 0, 0);

        const eventEnd = new Date(eventStart);
        eventEnd.setHours(11, 0, 0, 0);

        await prisma.invitation_events.create({
          data: {
            invitation_id: invitation.id,
            title: category === 'khitan' ? 'Walimatul Khitan' : (category === 'general' ? 'Acara Utama' : 'Akad Nikah'),
            start_at: eventStart,
            end_at: eventEnd,
            timezone: 'WIB',
            venue_name: 'Kediaman Mempelai / Gedung Serbaguna',
            address: 'Jl. Mawar No. 123, Jakarta Selatan',
            maps_url: 'https://maps.google.com',
            created_at: new Date(),
            updated_at: new Date(),
          },
        });
      } else {
        // Renewal
        const durationDays = transaction.plans ? transaction.plans.duration_days : 30;
        const currentExp = transaction.invitations?.expires_at ? new Date(transaction.invitations.expires_at) : new Date();
        const baseDate = currentExp > new Date() ? currentExp : new Date();
        baseDate.setDate(baseDate.getDate() + durationDays);

        await prisma.invitations.update({
          where: { id: invitationId },
          data: {
            expires_at: baseDate,
            plan_id: transaction.plan_id,
            status: transaction.invitations?.status === 'expired' ? 'draft' : transaction.invitations?.status,
            updated_at: new Date(),
          },
        });
      }

      await prisma.transactions.update({
        where: { id: transaction.id },
        data: {
          status: 'paid',
          paid_at: new Date(),
          invitation_id: invitationId,
          updated_at: new Date(),
        },
      });

      return {
        success: true,
        order_id: orderId,
        status: 'paid',
        invitation_id: invitationId,
      };
    }

    if (['failed', 'expired'].includes(newStatus)) {
      await prisma.transactions.update({
        where: { id: transaction.id },
        data: {
          status: newStatus,
          updated_at: new Date(),
        },
      });
    }

    return {
      success: true,
      order_id: orderId,
      status: newStatus,
      invitation_id: transaction.invitation_id,
    };
  }
}

module.exports = new PaymentService();
