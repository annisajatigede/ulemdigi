const prisma = require('../../config/prisma');

class AdminTemplateController {
  async index(req, res) {
    try {
      const templates = await prisma.templates.findMany({
        include: {
          _count: { select: { invitations: true } },
        },
        orderBy: { id: 'asc' },
      });

      const formatted = templates.map((t) => ({
        ...t,
        invitations_count: t._count.invitations,
      }));

      return res.json({ templates: formatted });
    } catch (error) {
      console.error('adminTemplates error:', error);
      return res.status(500).json({ message: 'Gagal memuat template.' });
    }
  }

  async store(req, res) {
    try {
      const { name, category, style, blade_view } = req.body;

      if (!name || !category || !style) {
        return res.status(422).json({ message: 'Nama, kategori, dan gaya wajib diisi.' });
      }

      let thumbnailPath = null;
      if (req.file) {
        thumbnailPath = `/templates/thumbnails/${req.file.filename}`;
      }

      const template = await prisma.templates.create({
        data: {
          name,
          category,
          style,
          blade_view: blade_view || `templates.${name.toLowerCase().replace(/[^a-z0-9]/g, '_')}`,
          thumbnail_path: thumbnailPath,
          is_active: true,
          created_at: new Date(),
          updated_at: new Date(),
        },
      });

      return res.status(201).json({ message: 'Template baru berhasil ditambahkan!', template });
    } catch (error) {
      console.error('adminTemplate store error:', error);
      return res.status(500).json({ message: 'Gagal menambahkan template.' });
    }
  }

  async update(req, res) {
    try {
      const id = BigInt(req.params.id);
      const { name, category, style, is_active } = req.body;

      const template = await prisma.templates.findUnique({ where: { id } });
      if (!template) return res.status(404).json({ message: 'Template tidak ditemukan.' });

      const dataToUpdate = { updated_at: new Date() };
      if (name) dataToUpdate.name = name;
      if (category) dataToUpdate.category = category;
      if (style) dataToUpdate.style = style;
      if (is_active !== undefined) dataToUpdate.is_active = is_active === 'true' || is_active === true;

      if (req.file) {
        dataToUpdate.thumbnail_path = `/templates/thumbnails/${req.file.filename}`;
      }

      const updated = await prisma.templates.update({
        where: { id },
        data: dataToUpdate,
      });

      return res.json({ message: 'Template berhasil diperbarui!', template: updated });
    } catch (error) {
      console.error('adminTemplate update error:', error);
      return res.status(500).json({ message: 'Gagal memperbarui template.' });
    }
  }

  async destroy(req, res) {
    try {
      const id = BigInt(req.params.id);

      const inUse = await prisma.invitations.findFirst({ where: { template_id: id } });
      if (inUse) {
        return res.status(422).json({ message: 'Template ini sedang digunakan dan tidak dapat dihapus.' });
      }

      await prisma.templates.delete({ where: { id } });
      return res.json({ message: 'Template berhasil dihapus.' });
    } catch (error) {
      console.error('adminTemplate destroy error:', error);
      return res.status(500).json({ message: 'Gagal menghapus template.' });
    }
  }
}

module.exports = new AdminTemplateController();
