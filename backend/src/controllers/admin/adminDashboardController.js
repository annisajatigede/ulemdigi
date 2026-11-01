const prisma = require('../../config/prisma');

class AdminDashboardController {
  async index(req, res) {
    try {
      const paidTransactions = await prisma.transactions.findMany({
        where: { status: 'paid' },
        select: { amount: true },
      });

      const totalRevenue = paidTransactions.reduce((acc, curr) => acc + Number(curr.amount), 0);
      const totalTransactions = await prisma.transactions.count();
      const paidTransactionsCount = paidTransactions.length;
      const totalUsers = await prisma.users.count();
      const totalInvitations = await prisma.invitations.count();
      const activeTemplates = await prisma.templates.count({ where: { is_active: true } });

      const recentTransactions = await prisma.transactions.findMany({
        include: {
          users: true,
          plans: true,
          invitations: true,
        },
        orderBy: { id: 'desc' },
        take: 8,
      });

      const recentUsers = await prisma.users.findMany({
        orderBy: { id: 'desc' },
        take: 5,
      });

      return res.json({
        totalRevenue,
        totalTransactions,
        paidTransactions: paidTransactionsCount,
        totalUsers,
        totalInvitations,
        activeTemplates,
        recentTransactions,
        recentUsers,
      });
    } catch (error) {
      console.error('adminDashboard error:', error);
      return res.status(500).json({ message: 'Gagal memuat dashboard admin.' });
    }
  }
}

module.exports = new AdminDashboardController();
