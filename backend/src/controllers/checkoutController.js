const prisma = require('../config/prisma');
const paymentService = require('../services/paymentService');

class CheckoutController {
  async getCheckoutOptions(req, res) {
    try {
      const templateId = req.query.template_id ? BigInt(req.query.template_id) : null;
      const planCode = req.query.plan_code;
      const invitationId = req.query.invitation_id ? BigInt(req.query.invitation_id) : null;

      let template = templateId ? await prisma.templates.findUnique({ where: { id: templateId } }) : null;
      if (!template) {
        template = await prisma.templates.findFirst({ where: { is_active: true } });
      }

      let plan = planCode ? await prisma.plans.findUnique({ where: { code: planCode } }) : null;
      if (!plan) {
        plan = await prisma.plans.findUnique({ where: { code: 'standard' } }) || await prisma.plans.findFirst();
      }

      let existingInvitation = null;
      if (invitationId && req.user) {
        existingInvitation = await prisma.invitations.findFirst({
          where: { id: invitationId, user_id: req.user.id },
        });
      }

      const allPlans = await prisma.plans.findMany({ orderBy: { price: 'asc' } });
      const allTemplates = await prisma.templates.findMany({ where: { is_active: true } });

      return res.json({
        template,
        plan,
        allPlans,
        allTemplates,
        existingInvitation,
      });
    } catch (error) {
      console.error('getCheckoutOptions error:', error);
      return res.status(500).json({ message: 'Gagal memuat opsi checkout.' });
    }
  }

  async createTransaction(req, res) {
    try {
      const { template_id, plan_id, invitation_id } = req.body;

      if (!template_id || !plan_id) {
        return res.status(422).json({ message: 'Template dan Paket wajib dipilih.' });
      }

      const template = await prisma.templates.findUnique({ where: { id: BigInt(template_id) } });
      const plan = await prisma.plans.findUnique({ where: { id: BigInt(plan_id) } });

      if (!template || !plan) {
        return res.status(404).json({ message: 'Template atau Paket tidak ditemukan.' });
      }

      let existingInvitation = null;
      if (invitation_id) {
        existingInvitation = await prisma.invitations.findFirst({
          where: { id: BigInt(invitation_id), user_id: req.user.id },
        });
      }

      const result = await paymentService.initiateOrder(req.user, template, plan, existingInvitation);

      return res.json({
        message: 'Transaksi berhasil dibuat.',
        transaction: result.transaction,
        snap_token: result.snap_token,
        redirect_url: result.redirect_url,
      });
    } catch (error) {
      console.error('createTransaction error:', error);
      return res.status(500).json({ message: 'Gagal memulai transaksi pembayaran.' });
    }
  }

  async getTransaction(req, res) {
    try {
      const id = BigInt(req.params.id);
      const transaction = await prisma.transactions.findUnique({
        where: { id },
        include: {
          plans: true,
          invitations: true,
        },
      });

      if (!transaction) {
        return res.status(404).json({ message: 'Transaksi tidak ditemukan.' });
      }

      if (transaction.user_id !== req.user.id && req.user.role !== 'admin') {
        return res.status(403).json({ message: 'Akses ditolak.' });
      }

      const settingKey = await prisma.settings.findUnique({ where: { key: 'midtrans_client_key' } });
      const settingProd = await prisma.settings.findUnique({ where: { key: 'midtrans_is_production' } });

      const clientKey = settingKey?.value || process.env.MIDTRANS_CLIENT_KEY || '';
      const isProduction = settingProd?.value === '1';

      return res.json({
        transaction,
        snap_token: transaction.raw_payload?.snap_token || null,
        client_key: clientKey,
        is_production: isProduction,
      });
    } catch (error) {
      console.error('getTransaction error:', error);
      return res.status(500).json({ message: 'Gagal mengambil detail transaksi.' });
    }
  }

  async midtransWebhook(req, res) {
    try {
      const payload = req.body;
      const signatureHeader = req.headers['x-signature'] || '';
      const result = await paymentService.processWebhook(payload, signatureHeader);

      return res.json({
        status: 'success',
        message: 'Webhook processed successfully.',
        data: result,
      });
    } catch (error) {
      console.error('Midtrans Webhook Error:', error);
      return res.status(400).json({
        status: 'error',
        message: error.message,
      });
    }
  }

  async simulatorCallback(req, res) {
    try {
      const { order_id, status } = req.body;

      if (!order_id) {
        return res.status(422).json({ message: 'order_id wajib disertakan.' });
      }

      const transaction = await prisma.transactions.findUnique({
        where: { order_id },
      });

      if (!transaction) {
        return res.status(404).json({ message: 'Transaksi tidak ditemukan.' });
      }

      const targetStatus = status || 'paid';
      const midtransStatus = targetStatus === 'paid' ? 'settlement' : (targetStatus === 'expired' ? 'expire' : 'deny');

      const dummyPayload = {
        order_id,
        transaction_status: midtransStatus,
        status_code: '200',
        gross_amount: Math.round(Number(transaction.amount)).toString(),
        fraud_status: 'accept',
        signature_key: 'mock-signature-for-simulator',
      };

      const result = await paymentService.processWebhook(dummyPayload, '', true);

      return res.json({
        status: 'success',
        message: 'Simulasi pembayaran berhasil diproses.',
        result,
      });
    } catch (error) {
      console.error('Simulator Callback Error:', error);
      return res.status(400).json({
        status: 'error',
        message: error.message,
      });
    }
  }

  async getSuccessData(req, res) {
    try {
      const id = BigInt(req.params.id);
      const transaction = await prisma.transactions.findUnique({
        where: { id },
        include: {
          invitations: true,
          plans: true,
        },
      });

      if (!transaction) {
        return res.status(404).json({ message: 'Transaksi tidak ditemukan.' });
      }

      if (transaction.user_id !== req.user.id && req.user.role !== 'admin') {
        return res.status(403).json({ message: 'Akses ditolak.' });
      }

      return res.json({
        transaction,
        invitation: transaction.invitations,
      });
    } catch (error) {
      console.error('getSuccessData error:', error);
      return res.status(500).json({ message: 'Gagal mengambil data sukses transaksi.' });
    }
  }
}

module.exports = new CheckoutController();
