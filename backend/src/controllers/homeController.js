const prisma = require('../config/prisma');

class HomeController {
  async getLandingData(req, res) {
    try {
      const selectedCategory = req.query.category || 'all';

      const where = { is_active: true };
      if (selectedCategory !== 'all' && ['wedding', 'khitan', 'general'].includes(selectedCategory)) {
        where.category = selectedCategory;
      }

      const templates = await prisma.templates.findMany({
        where,
        orderBy: { id: 'asc' },
      });

      const plans = await prisma.plans.findMany({
        orderBy: { price: 'asc' },
      });

      const settingsList = await prisma.settings.findMany();
      const settings = {
        site_name: 'Kawinan.id',
        site_tagline: 'Platform Undangan Digital Berbayar Modern & Elegan',
        site_description: 'Solusi undangan online terbaik untuk pernikahan, khitanan, dan acara spesial.',
        contact_whatsapp: '6281234567890',
      };

      settingsList.forEach((s) => {
        if (s.value) settings[s.key] = s.value;
      });

      return res.json({
        templates,
        plans,
        settings,
        selectedCategory,
      });
    } catch (error) {
      console.error('getLandingData error:', error);
      return res.status(500).json({ message: 'Gagal memuat data landing page.' });
    }
  }
}

module.exports = new HomeController();
