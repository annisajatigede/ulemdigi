const prisma = require('../../config/prisma');

class AdminSettingController {
  async index(req, res) {
    try {
      const settingsList = await prisma.settings.findMany();
      const settings = {
        site_name: 'Forkoway.com',
        site_tagline: 'Platform Undangan Digital Berbayar Modern & Elegan',
        site_description: 'Solusi undangan online terbaik untuk momen spesial Anda.',
        contact_whatsapp: '6281234567890',
        contact_email: 'support@forkoway.com',
        logo_url: null,
        favicon_url: null,
        midtrans_server_key: process.env.MIDTRANS_SERVER_KEY || '',
        midtrans_client_key: process.env.MIDTRANS_CLIENT_KEY || '',
        midtrans_is_production: '0',
      };

      settingsList.forEach((s) => {
        if (s.value !== null) settings[s.key] = s.value;
      });

      return res.json({ settings });
    } catch (error) {
      console.error('adminSettings error:', error);
      return res.status(500).json({ message: 'Gagal memuat pengaturan.' });
    }
  }

  async update(req, res) {
    try {
      const keys = [
        'site_name',
        'site_tagline',
        'site_description',
        'contact_whatsapp',
        'contact_email',
        'midtrans_server_key',
        'midtrans_client_key',
        'midtrans_is_production',
      ];

      for (const k of keys) {
        if (req.body[k] !== undefined) {
          await prisma.settings.upsert({
            where: { key: k },
            update: { value: req.body[k], updated_at: new Date() },
            create: { key: k, value: req.body[k], created_at: new Date(), updated_at: new Date() },
          });
        }
      }

      if (req.files && req.files.logo && req.files.logo[0]) {
        const logoUrl = `/uploads/${req.files.logo[0].filename}`;
        await prisma.settings.upsert({
          where: { key: 'logo_url' },
          update: { value: logoUrl, updated_at: new Date() },
          create: { key: 'logo_url', value: logoUrl, created_at: new Date(), updated_at: new Date() },
        });
      }

      return res.json({ message: 'Pengaturan website berhasil disimpan!' });
    } catch (error) {
      console.error('adminSetting update error:', error);
      return res.status(500).json({ message: 'Gagal memperbarui pengaturan.' });
    }
  }
}

module.exports = new AdminSettingController();
