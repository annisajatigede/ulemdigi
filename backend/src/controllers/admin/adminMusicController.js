const prisma = require('../../config/prisma');

class AdminMusicController {
  async index(req, res) {
    try {
      const category = req.query.category || 'all';

      const where = {};
      if (category !== 'all' && ['wedding', 'khitan', 'general'].includes(category)) {
        where.category = category;
      }

      const musics = await prisma.musics.findMany({
        where,
        orderBy: [{ sort_order: 'asc' }, { id: 'desc' }],
      });

      const all = await prisma.musics.findMany();
      const stats = {
        total: all.length,
        active: all.filter((m) => m.is_active).length,
        wedding: all.filter((m) => m.category === 'wedding').length,
        khitan: all.filter((m) => m.category === 'khitan').length,
        general: all.filter((m) => m.category === 'general').length,
      };

      return res.json({ musics, stats, category });
    } catch (error) {
      console.error('adminMusics error:', error);
      return res.status(500).json({ message: 'Gagal memuat pustaka musik.' });
    }
  }

  async store(req, res) {
    try {
      const { title, artist, category, sort_order, file_url } = req.body;

      if (!title || !category) {
        return res.status(422).json({ message: 'Judul dan kategori wajib diisi.' });
      }

      let filePath = null;
      if (req.file) {
        filePath = `/audio/${req.file.filename}`;
      } else if (file_url) {
        filePath = file_url;
      }

      if (!filePath) {
        return res.status(422).json({ message: 'Harap unggah berkas audio atau masukkan tautan URL.' });
      }

      const music = await prisma.musics.create({
        data: {
          title,
          artist: artist || 'Kawinan Studio',
          category,
          file_path: filePath,
          is_active: true,
          sort_order: Number(sort_order) || 0,
          created_at: new Date(),
          updated_at: new Date(),
        },
      });

      return res.status(201).json({ message: 'Musik latar baru berhasil ditambahkan!', music });
    } catch (error) {
      console.error('adminMusic store error:', error);
      return res.status(500).json({ message: 'Gagal menambahkan musik.' });
    }
  }

  async toggle(req, res) {
    try {
      const id = BigInt(req.params.id);

      const music = await prisma.musics.findUnique({ where: { id } });
      if (!music) return res.status(404).json({ message: 'Musik tidak ditemukan.' });

      const updated = await prisma.musics.update({
        where: { id },
        data: {
          is_active: !music.is_active,
          updated_at: new Date(),
        },
      });

      return res.json({
        message: `Status aktif musik "${updated.title}" berhasil diubah.`,
        music: updated,
      });
    } catch (error) {
      console.error('adminMusic toggle error:', error);
      return res.status(500).json({ message: 'Gagal mengubah status musik.' });
    }
  }

  async destroy(req, res) {
    try {
      const id = BigInt(req.params.id);
      await prisma.musics.delete({ where: { id } });
      return res.json({ message: 'Musik latar berhasil dihapus dari pustaka.' });
    } catch (error) {
      console.error('adminMusic destroy error:', error);
      return res.status(500).json({ message: 'Gagal menghapus musik.' });
    }
  }
}

module.exports = new AdminMusicController();
