const prisma = require('../config/prisma');
const crypto = require('crypto');

class GuestController {
  async index(req, res) {
    try {
      const invitationId = BigInt(req.params.id);

      const invitation = await prisma.invitations.findUnique({
        where: { id: invitationId },
        include: {
          plans: true,
          wishes: { orderBy: { id: 'desc' } },
        },
      });

      if (!invitation) {
        return res.status(404).json({ message: 'Undangan tidak ditemukan.' });
      }

      if (invitation.user_id !== req.user.id && req.user.role !== 'admin') {
        return res.status(403).json({ message: 'Akses ditolak.' });
      }

      const search = req.query.q || '';
      const statusFilter = req.query.status || '';

      const where = { invitation_id: invitationId };
      if (statusFilter && ['attended', 'not_attended', 'maybe', 'pending'].includes(statusFilter)) {
        where.rsvp_status = statusFilter;
      }
      if (search) {
        where.OR = [
          { name: { contains: search } },
          { phone: { contains: search } },
        ];
      }

      const guests = await prisma.guests.findMany({
        where,
        orderBy: { id: 'desc' },
      });

      const allGuests = await prisma.guests.findMany({
        where: { invitation_id: invitationId },
      });

      const analytics = {
        total_invitations: allGuests.length,
        total_guests_estimated: allGuests.reduce((acc, g) => acc + (g.guest_count || 1), 0),
        attended: allGuests.filter((g) => g.rsvp_status === 'attended').length,
        not_attended: allGuests.filter((g) => g.rsvp_status === 'not_attended').length,
        maybe: allGuests.filter((g) => g.rsvp_status === 'maybe').length,
        pending: allGuests.filter((g) => g.rsvp_status === 'pending').length,
        checked_in: allGuests.filter((g) => g.checked_in_at !== null).length,
        total_wishes: invitation.wishes.length,
      };

      return res.json({
        invitation,
        guests,
        analytics,
        search,
        statusFilter,
      });
    } catch (error) {
      console.error('guest index error:', error);
      return res.status(500).json({ message: 'Gagal memuat daftar tamu.' });
    }
  }

  async store(req, res) {
    try {
      const invitationId = BigInt(req.params.id);
      const { name, phone, guest_count, rsvp_status } = req.body;

      if (!name) {
        return res.status(422).json({ message: 'Nama tamu wajib diisi.' });
      }

      const invitation = await prisma.invitations.findUnique({ where: { id: invitationId } });
      if (!invitation) {
        return res.status(404).json({ message: 'Undangan tidak ditemukan.' });
      }

      if (invitation.user_id !== req.user.id && req.user.role !== 'admin') {
        return res.status(403).json({ message: 'Akses ditolak.' });
      }

      let personalToken = crypto.randomBytes(4).toString('hex');
      while (await prisma.guests.findUnique({ where: { personal_token: personalToken } })) {
        personalToken = crypto.randomBytes(4).toString('hex');
      }

      const guest = await prisma.guests.create({
        data: {
          invitation_id: invitationId,
          name,
          phone: phone || null,
          personal_token: personalToken,
          guest_count: Number(guest_count) || 1,
          rsvp_status: rsvp_status || 'pending',
          created_at: new Date(),
          updated_at: new Date(),
        },
      });

      return res.status(201).json({
        message: 'Tamu berhasil ditambahkan.',
        guest,
      });
    } catch (error) {
      console.error('guest store error:', error);
      return res.status(500).json({ message: 'Gagal menambahkan tamu.' });
    }
  }

  async update(req, res) {
    try {
      const guestId = BigInt(req.params.guestId);
      const { name, phone, guest_count, rsvp_status } = req.body;

      const guest = await prisma.guests.findUnique({
        where: { id: guestId },
        include: { invitations: true },
      });

      if (!guest) {
        return res.status(404).json({ message: 'Tamu tidak ditemukan.' });
      }

      if (guest.invitations.user_id !== req.user.id && req.user.role !== 'admin') {
        return res.status(403).json({ message: 'Akses ditolak.' });
      }

      const updated = await prisma.guests.update({
        where: { id: guestId },
        data: {
          name: name || guest.name,
          phone: phone !== undefined ? phone : guest.phone,
          guest_count: guest_count ? Number(guest_count) : guest.guest_count,
          rsvp_status: rsvp_status || guest.rsvp_status,
          updated_at: new Date(),
        },
      });

      return res.json({
        message: 'Data tamu berhasil diperbarui.',
        guest: updated,
      });
    } catch (error) {
      console.error('guest update error:', error);
      return res.status(500).json({ message: 'Gagal memperbarui tamu.' });
    }
  }

  async destroy(req, res) {
    try {
      const guestId = BigInt(req.params.guestId);

      const guest = await prisma.guests.findUnique({
        where: { id: guestId },
        include: { invitations: true },
      });

      if (!guest) {
        return res.status(404).json({ message: 'Tamu tidak ditemukan.' });
      }

      if (guest.invitations.user_id !== req.user.id && req.user.role !== 'admin') {
        return res.status(403).json({ message: 'Akses ditolak.' });
      }

      await prisma.guests.delete({ where: { id: guestId } });

      return res.json({ message: 'Tamu berhasil dihapus.' });
    } catch (error) {
      console.error('guest destroy error:', error);
      return res.status(500).json({ message: 'Gagal menghapus tamu.' });
    }
  }

  async checkIn(req, res) {
    try {
      const guestId = BigInt(req.params.guestId);

      const guest = await prisma.guests.findUnique({
        where: { id: guestId },
        include: { invitations: true },
      });

      if (!guest) {
        return res.status(404).json({ message: 'Tamu tidak ditemukan.' });
      }

      if (guest.invitations.user_id !== req.user.id && req.user.role !== 'admin') {
        return res.status(403).json({ message: 'Akses ditolak.' });
      }

      const updated = await prisma.guests.update({
        where: { id: guestId },
        data: {
          checked_in_at: guest.checked_in_at ? null : new Date(),
          updated_at: new Date(),
        },
      });

      return res.json({
        message: updated.checked_in_at ? 'Tamu berhasil check-in.' : 'Check-in dibatalkan.',
        guest: updated,
      });
    } catch (error) {
      console.error('guest checkIn error:', error);
      return res.status(500).json({ message: 'Gagal mengubah status check-in.' });
    }
  }

  async exportCsv(req, res) {
    try {
      const invitationId = BigInt(req.params.id);

      const invitation = await prisma.invitations.findUnique({ where: { id: invitationId } });
      if (!invitation) return res.status(404).json({ message: 'Undangan tidak ditemukan.' });

      if (invitation.user_id !== req.user.id && req.user.role !== 'admin') {
        return res.status(403).json({ message: 'Akses ditolak.' });
      }

      const guests = await prisma.guests.findMany({
        where: { invitation_id: invitationId },
        orderBy: { id: 'asc' },
      });

      let csv = 'Nama Tamu,Nomor Telepon,Status RSVP,Jumlah Hadir,Waktu Check-in,Token Personal,Tautan Undangan\n';

      guests.forEach((g) => {
        const link = `http://localhost:5173/u/${invitation.slug}/${g.personal_token}`;
        csv += `"${g.name}","${g.phone || ''}","${g.rsvp_status}","${g.guest_count}","${g.checked_in_at ? new Date(g.checked_in_at).toISOString() : ''}","${g.personal_token}","${link}"\n`;
      });

      res.header('Content-Type', 'text/csv');
      res.attachment(`tamu_${invitation.slug}_${Date.now()}.csv`);
      return res.send(csv);
    } catch (error) {
      console.error('guest exportCsv error:', error);
      return res.status(500).json({ message: 'Gagal mengekspor CSV.' });
    }
  }
}

module.exports = new GuestController();
