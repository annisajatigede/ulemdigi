const prisma = require('../../config/prisma');

class AdminTransactionController {
  async index(req, res) {
    try {
      const { status, start_date, end_date, q } = req.query;

      const where = {};
      if (status && ['paid', 'pending', 'failed', 'expired'].includes(status)) {
        where.status = status;
      }
      if (start_date) {
        where.created_at = { ...(where.created_at || {}), gte: new Date(start_date) };
      }
      if (end_date) {
        const end = new Date(end_date);
        end.setHours(23, 59, 59, 999);
        where.created_at = { ...(where.created_at || {}), lte: end };
      }
      if (q) {
        where.OR = [
          { order_id: { contains: q } },
          { users: { name: { contains: q } } },
          { users: { email: { contains: q } } },
        ];
      }

      const transactions = await prisma.transactions.findMany({
        where,
        include: {
          users: true,
          plans: true,
          invitations: true,
        },
        orderBy: { id: 'desc' },
      });

      const all = await prisma.transactions.findMany({ select: { status: true, amount: true } });
      const paidTrans = all.filter((t) => t.status === 'paid');

      const stats = {
        total_amount: paidTrans.reduce((acc, curr) => acc + Number(curr.amount), 0),
        total_count: all.length,
        paid_count: paidTrans.length,
        pending_count: all.filter((t) => t.status === 'pending').length,
      };

      return res.json({ transactions, stats, status, start_date, end_date, q });
    } catch (error) {
      console.error('adminTransactions error:', error);
      return res.status(500).json({ message: 'Gagal memuat transaksi.' });
    }
  }

  async exportCsv(req, res) {
    try {
      const { status, start_date, end_date } = req.query;

      const where = {};
      if (status) where.status = status;
      if (start_date) where.created_at = { ...(where.created_at || {}), gte: new Date(start_date) };
      if (end_date) {
        const end = new Date(end_date);
        end.setHours(23, 59, 59, 999);
        where.created_at = { ...(where.created_at || {}), lte: end };
      }

      const transactions = await prisma.transactions.findMany({
        where,
        include: { users: true, plans: true, invitations: true },
        orderBy: { id: 'desc' },
      });

      let csv = 'Order ID,Nama Pengguna,Email,Paket,Jumlah (IDR),Status,Gateway,Waktu Transaksi,Waktu Bayar,Slug Undangan\n';

      transactions.forEach((t) => {
        csv += `"${t.order_id}","${t.users?.name || ''}","${t.users?.email || ''}","${t.plans?.name || ''}","${t.amount}","${t.status}","${t.gateway}","${t.created_at ? new Date(t.created_at).toISOString() : ''}","${t.paid_at ? new Date(t.paid_at).toISOString() : ''}","${t.invitations?.slug || ''}"\n`;
      });

      res.header('Content-Type', 'text/csv');
      res.attachment(`transaksi_${Date.now()}.csv`);
      return res.send(csv);
    } catch (error) {
      console.error('adminTransaction exportCsv error:', error);
      return res.status(500).json({ message: 'Gagal mengekspor CSV transaksi.' });
    }
  }
}

module.exports = new AdminTransactionController();
