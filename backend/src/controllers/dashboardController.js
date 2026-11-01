const prisma = require('../config/prisma');
const { daysRemaining, isExpired, getSafeData } = require('../services/invitationHelper');

class DashboardController {
  async index(req, res) {
    try {
      const userId = req.user.id;

      const rawInvitations = await prisma.invitations.findMany({
        where: { user_id: userId },
        include: {
          templates: true,
          plans: true,
          invitation_events: { orderBy: { start_at: 'asc' } },
          _count: {
            select: { guests: true, wishes: true },
          },
        },
        orderBy: { id: 'desc' },
      });

      const invitations = rawInvitations.map((inv) => {
        const safeData = getSafeData(inv.data);
        const expired = isExpired(inv.status, inv.expires_at);
        const remainingDays = daysRemaining(inv.expires_at);

        return {
          ...inv,
          safeData,
          is_expired: expired,
          days_remaining: remainingDays,
          guests_count: inv._count?.guests || 0,
          wishes_count: inv._count?.wishes || 0,
        };
      });

      const transactions = await prisma.transactions.findMany({
        where: { user_id: userId },
        include: {
          plans: true,
          invitations: true,
        },
        orderBy: { id: 'desc' },
        take: 5,
      });

      const stats = {
        total: invitations.length,
        published: invitations.filter((i) => i.status === 'published' && !i.is_expired).length,
        draft: invitations.filter((i) => i.status === 'draft').length,
        expired: invitations.filter((i) => i.is_expired).length,
        total_guests: invitations.reduce((acc, curr) => acc + curr.guests_count, 0),
        total_wishes: invitations.reduce((acc, curr) => acc + curr.wishes_count, 0),
      };

      return res.json({
        invitations,
        transactions,
        stats,
      });
    } catch (error) {
      console.error('dashboard index error:', error);
      return res.status(500).json({ message: 'Gagal memuat dashboard pengguna.' });
    }
  }
}

module.exports = new DashboardController();
