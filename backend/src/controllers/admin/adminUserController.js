const bcrypt = require('bcryptjs');
const prisma = require('../../config/prisma');

class AdminUserController {
  async index(req, res) {
    try {
      const search = req.query.q || '';
      const role = req.query.role || '';

      const where = {};
      if (role && ['user', 'admin'].includes(role)) {
        where.role = role;
      }
      if (search) {
        where.OR = [
          { name: { contains: search } },
          { email: { contains: search } },
        ];
      }

      const users = await prisma.users.findMany({
        where,
        include: {
          _count: { select: { invitations: true } },
        },
        orderBy: { id: 'desc' },
      });

      const formatted = users.map((u) => ({
        id: u.id,
        name: u.name,
        email: u.email,
        role: u.role,
        invitations_count: u._count.invitations,
        created_at: u.created_at,
      }));

      return res.json({ users: formatted, search, role });
    } catch (error) {
      console.error('adminUsers error:', error);
      return res.status(500).json({ message: 'Gagal memuat pengguna.' });
    }
  }

  async store(req, res) {
    try {
      const { name, email, password, role } = req.body;

      if (!name || !email || !password || !role) {
        return res.status(422).json({ message: 'Seluruh field wajib diisi.' });
      }

      const existing = await prisma.users.findUnique({ where: { email } });
      if (existing) {
        return res.status(422).json({ message: 'Email sudah terdaftar.' });
      }

      const hashedPassword = await bcrypt.hash(password, 12);

      const user = await prisma.users.create({
        data: {
          name,
          email,
          password: hashedPassword,
          role: role === 'admin' ? 'admin' : 'user',
          created_at: new Date(),
          updated_at: new Date(),
        },
      });

      return res.status(201).json({ message: 'Pengguna baru berhasil ditambahkan!', user });
    } catch (error) {
      console.error('adminUser store error:', error);
      return res.status(500).json({ message: 'Gagal menambahkan pengguna.' });
    }
  }

  async update(req, res) {
    try {
      const id = BigInt(req.params.id);
      const { name, email, password, role } = req.body;

      const user = await prisma.users.findUnique({ where: { id } });
      if (!user) return res.status(404).json({ message: 'Pengguna tidak ditemukan.' });

      const dataToUpdate = { updated_at: new Date() };
      if (name) dataToUpdate.name = name;
      if (role && ['user', 'admin'].includes(role)) dataToUpdate.role = role;

      if (email && email !== user.email) {
        const check = await prisma.users.findUnique({ where: { email } });
        if (check && check.id !== id) {
          return res.status(422).json({ message: 'Email sudah dipakai pengguna lain.' });
        }
        dataToUpdate.email = email;
      }

      if (password) {
        dataToUpdate.password = await bcrypt.hash(password, 12);
      }

      const updated = await prisma.users.update({
        where: { id },
        data: dataToUpdate,
      });

      return res.json({ message: 'Data pengguna berhasil diperbarui!', user: updated });
    } catch (error) {
      console.error('adminUser update error:', error);
      return res.status(500).json({ message: 'Gagal memperbarui pengguna.' });
    }
  }

  async destroy(req, res) {
    try {
      const id = BigInt(req.params.id);

      if (req.user.id === id) {
        return res.status(422).json({ message: 'Anda tidak dapat menghapus akun Anda sendiri.' });
      }

      await prisma.users.delete({ where: { id } });
      return res.json({ message: 'Pengguna berhasil dihapus.' });
    } catch (error) {
      console.error('adminUser destroy error:', error);
      return res.status(500).json({ message: 'Gagal menghapus pengguna.' });
    }
  }
}

module.exports = new AdminUserController();
