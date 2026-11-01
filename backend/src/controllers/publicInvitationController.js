const prisma = require('../config/prisma');
const { getSafeData, isExpired } = require('../services/invitationHelper');
const crypto = require('crypto');

class PublicInvitationController {
  async show(req, res) {
    try {
      const slug = req.params.slug;

      const invitation = await prisma.invitations.findUnique({
        where: { slug },
        include: {
          templates: true,
          plans: true,
          invitation_events: { orderBy: { start_at: 'asc' } },
          wishes: { orderBy: { id: 'desc' }, take: 30 },
        },
      });

      if (!invitation) {
        return res.status(404).json({ message: 'Undangan tidak ditemukan.' });
      }

      const isOwnerOrAdmin = req.user && (req.user.id === invitation.user_id || req.user.role === 'admin');

      if (!isOwnerOrAdmin) {
        if (invitation.status === 'draft') {
          return res.status(200).json({
            not_published: true,
            invitation: {
              slug: invitation.slug,
              category: invitation.category,
            },
          });
        }

        if (isExpired(invitation.status, invitation.expires_at)) {
          return res.status(200).json({
            expired: true,
            invitation: {
              slug: invitation.slug,
              category: invitation.category,
            },
          });
        }
      }

      const recipientName = req.query.to || 'Tamu Undangan';
      const safeData = getSafeData(invitation.data);

      return res.json({
        invitation,
        template: invitation.templates,
        data: safeData,
        events: invitation.invitation_events,
        wishes: invitation.wishes,
        recipientName,
        isPersonal: false,
        guest: null,
      });
    } catch (error) {
      console.error('publicInvitation show error:', error);
      return res.status(500).json({ message: 'Gagal memuat undangan publik.' });
    }
  }

  async showGuest(req, res) {
    try {
      const { slug, token } = req.params;

      const invitation = await prisma.invitations.findUnique({
        where: { slug },
        include: {
          templates: true,
          plans: true,
          invitation_events: { orderBy: { start_at: 'asc' } },
          wishes: { orderBy: { id: 'desc' }, take: 30 },
        },
      });

      if (!invitation) {
        return res.status(404).json({ message: 'Undangan tidak ditemukan.' });
      }

      const isOwnerOrAdmin = req.user && (req.user.id === invitation.user_id || req.user.role === 'admin');

      if (!isOwnerOrAdmin) {
        if (invitation.status === 'draft') {
          return res.status(200).json({
            not_published: true,
            invitation: { slug: invitation.slug, category: invitation.category },
          });
        }

        if (isExpired(invitation.status, invitation.expires_at)) {
          return res.status(200).json({
            expired: true,
            invitation: { slug: invitation.slug, category: invitation.category },
          });
        }
      }

      const guest = await prisma.guests.findFirst({
        where: {
          invitation_id: invitation.id,
          personal_token: token,
        },
      });

      const recipientName = guest ? guest.name : (req.query.to || 'Tamu Undangan');
      const safeData = getSafeData(invitation.data);

      return res.json({
        invitation,
        template: invitation.templates,
        data: safeData,
        events: invitation.invitation_events,
        wishes: invitation.wishes,
        recipientName,
        isPersonal: !!guest,
        guest,
      });
    } catch (error) {
      console.error('publicInvitation showGuest error:', error);
      return res.status(500).json({ message: 'Gagal memuat undangan personal.' });
    }
  }

  async submitRsvp(req, res) {
    try {
      const slug = req.params.slug;

      const invitation = await prisma.invitations.findUnique({ where: { slug } });
      if (!invitation) {
        return res.status(404).json({ message: 'Undangan tidak ditemukan.' });
      }

      const { name, phone, rsvp_status, guest_count, message, personal_token } = req.body;

      if (!name) {
        return res.status(422).json({ message: 'Nama tamu wajib diisi.' });
      }

      let guest = null;
      if (personal_token) {
        guest = await prisma.guests.findFirst({
          where: { invitation_id: invitation.id, personal_token },
        });
      }

      if (!guest && phone) {
        guest = await prisma.guests.findFirst({
          where: { invitation_id: invitation.id, phone },
        });
      }

      const validStatus = ['attended', 'not_attended', 'maybe', 'pending'].includes(rsvp_status)
        ? rsvp_status
        : 'attended';

      if (guest) {
        guest = await prisma.guests.update({
          where: { id: guest.id },
          data: {
            name,
            phone: phone || guest.phone,
            rsvp_status: validStatus,
            guest_count: Number(guest_count) || 1,
            updated_at: new Date(),
          },
        });
      } else {
        const randToken = crypto.randomBytes(4).toString('hex');
        guest = await prisma.guests.create({
          data: {
            invitation_id: invitation.id,
            name,
            phone: phone || null,
            personal_token: randToken,
            rsvp_status: validStatus,
            guest_count: Number(guest_count) || 1,
            created_at: new Date(),
            updated_at: new Date(),
          },
        });
      }

      let newWish = null;
      if (message && message.trim().length > 0) {
        newWish = await prisma.wishes.create({
          data: {
            invitation_id: invitation.id,
            guest_id: guest.id,
            name,
            message: message.trim(),
            created_at: new Date(),
            updated_at: new Date(),
          },
        });
      }

      return res.json({
        success: true,
        message: 'Terima kasih atas konfirmasi kehadiran dan doa restu Anda!',
        guest,
        wish: newWish
          ? {
              id: newWish.id,
              name: newWish.name,
              message: newWish.message,
              created_at: newWish.created_at,
            }
          : null,
      });
    } catch (error) {
      console.error('submitRsvp error:', error);
      return res.status(500).json({ message: 'Gagal mengirimkan RSVP.' });
    }
  }
}

module.exports = new PublicInvitationController();
