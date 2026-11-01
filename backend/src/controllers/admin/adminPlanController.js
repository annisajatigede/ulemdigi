const prisma = require('../../config/prisma');

class AdminPlanController {
  async index(req, res) {
    try {
      const plans = await prisma.plans.findMany({
        include: {
          _count: { select: { invitations: true, transactions: true } },
        },
        orderBy: { price: 'asc' },
      });

      const formatted = plans.map((p) => ({
        ...p,
        invitations_count: p._count.invitations,
        transactions_count: p._count.transactions,
      }));

      return res.json({ plans: formatted });
    } catch (error) {
      console.error('adminPlans error:', error);
      return res.status(500).json({ message: 'Gagal memuat paket.' });
    }
  }

  async update(req, res) {
    try {
      const id = BigInt(req.params.id);
      const { name, price, duration_days, max_photos, max_events, features_text, features } = req.body;

      let featureList = [];
      if (Array.isArray(features)) {
        featureList = features;
      } else if (features_text) {
        featureList = features_text.split('\n').map((s) => s.trim()).filter(Boolean);
      }

      const updated = await prisma.plans.update({
        where: { id },
        data: {
          name,
          price: price !== undefined ? Number(price) : undefined,
          duration_days: duration_days !== undefined ? Number(duration_days) : undefined,
          max_photos: max_photos !== undefined ? Number(max_photos) : undefined,
          max_events: max_events !== undefined ? Number(max_events) : undefined,
          features: featureList,
          updated_at: new Date(),
        },
      });

      return res.json({ message: `Paket ${updated.name} berhasil diperbarui!`, plan: updated });
    } catch (error) {
      console.error('adminPlan update error:', error);
      return res.status(500).json({ message: 'Gagal memperbarui paket.' });
    }
  }
}

module.exports = new AdminPlanController();
