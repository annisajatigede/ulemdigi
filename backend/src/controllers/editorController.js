const prisma = require('../config/prisma');
const { getSafeData } = require('../services/invitationHelper');

class EditorController {
  async edit(req, res) {
    try {
      const id = BigInt(req.params.id);

      const invitation = await prisma.invitations.findUnique({
        where: { id },
        include: {
          templates: true,
          plans: true,
          invitation_events: { orderBy: { start_at: 'asc' } },
        },
      });

      if (!invitation) {
        return res.status(404).json({ message: 'Undangan tidak ditemukan.' });
      }

      if (invitation.user_id !== req.user.id && req.user.role !== 'admin') {
        return res.status(403).json({ message: 'Akses ditolak.' });
      }

      const templates = await prisma.templates.findMany({ where: { is_active: true } });
      const presetMusics = await prisma.musics.findMany({
        where: { is_active: true },
        orderBy: [{ sort_order: 'asc' }, { id: 'desc' }],
      });

      const safeData = getSafeData(invitation.data);

      const eventsData = invitation.invitation_events.map((e) => ({
        title: e.title,
        start_at: e.start_at ? new Date(e.start_at).toISOString().slice(0, 16) : '',
        end_at: e.end_at ? new Date(e.end_at).toISOString().slice(0, 16) : '',
        timezone: e.timezone || 'WIB',
        venue_name: e.venue_name || '',
        address: e.address || '',
        maps_url: e.maps_url || '',
      }));

      return res.json({
        invitation,
        templates,
        presetMusics,
        safeData,
        eventsData,
      });
    } catch (error) {
      console.error('editor edit error:', error);
      return res.status(500).json({ message: 'Gagal memuat editor undangan.' });
    }
  }

  async update(req, res) {
    try {
      const id = BigInt(req.params.id);

      const invitation = await prisma.invitations.findUnique({
        where: { id },
        include: { plans: true },
      });

      if (!invitation) {
        return res.status(404).json({ message: 'Undangan tidak ditemukan.' });
      }

      if (invitation.user_id !== req.user.id && req.user.role !== 'admin') {
        return res.status(403).json({ message: 'Akses ditolak.' });
      }

      const {
        slug,
        template_id,
        theme_color,
        opening,
        party1,
        party2,
        story,
        gifts,
        closing,
        rsvp_enabled,
        wishes_enabled,
        music_source,
        preset_music_id,
        preset_music,
        events,
      } = req.body;

      if (!slug) {
        return res.status(422).json({ message: 'Slug undangan wajib diisi.' });
      }

      // Check unique slug
      const checkSlug = await prisma.invitations.findFirst({
        where: {
          slug,
          NOT: { id },
        },
      });

      if (checkSlug) {
        return res.status(422).json({
          message: 'Slug sudah digunakan oleh undangan lain.',
          errors: { slug: ['Slug tautan ini sudah dipakai. Silakan gunakan nama unik lain.'] },
        });
      }

      const currentData = getSafeData(invitation.data);

      // Handle photos from multer
      const files = req.files || {};

      if (files.party1_photo && files.party1_photo[0]) {
        currentData.party1.photo_path = `/uploads/${files.party1_photo[0].filename}`;
      }

      if (files.party2_photo && files.party2_photo[0]) {
        currentData.party2.photo_path = `/uploads/${files.party2_photo[0].filename}`;
      }

      // Handle background music
      const selectedSource = music_source || 'preset';

      if (selectedSource === 'none') {
        currentData.music_path = null;
        currentData.music_title = null;
        currentData.music_source = 'none';
        currentData.music_url = null;
      } else if (selectedSource === 'custom' || (files.custom_music && files.custom_music[0])) {
        if (files.custom_music && files.custom_music[0]) {
          currentData.music_path = `/audio/${files.custom_music[0].filename}`;
          currentData.music_title = files.custom_music[0].originalname;
          currentData.music_url = currentData.music_path;
        }
        currentData.music_source = 'custom';
      } else {
        // Preset
        if (preset_music_id) {
          const preset = await prisma.musics.findUnique({ where: { id: BigInt(preset_music_id) } });
          if (preset) {
            currentData.music_path = preset.file_path;
            currentData.music_title = `${preset.title}${preset.artist ? ' - ' + preset.artist : ''}`;
            currentData.music_source = 'preset';
            currentData.music_url = preset.file_path;
          }
        } else if (preset_music) {
          currentData.music_path = preset_music;
          currentData.music_source = 'preset';
          currentData.music_url = preset_music;
        }
      }

      // Handle gallery photos upload
      let gallery = currentData.gallery || [];
      if (files.gallery_photos && files.gallery_photos.length > 0) {
        const maxPhotos = invitation.plans?.max_photos || 15;
        for (const file of files.gallery_photos) {
          if (gallery.length >= maxPhotos) break;
          gallery.push({
            url: `/uploads/${file.filename}`,
            caption: '',
          });
        }
      }

      // Remove gallery index if specified
      if (req.body.remove_gallery_index !== undefined) {
        const removeIndexes = Array.isArray(req.body.remove_gallery_index)
          ? req.body.remove_gallery_index.map(Number)
          : [Number(req.body.remove_gallery_index)];
        gallery = gallery.filter((_, idx) => !removeIndexes.includes(idx));
      }
      currentData.gallery = gallery;

      // Merge text fields
      if (theme_color) currentData.theme_color = theme_color;

      const parsedOpening = typeof opening === 'string' ? JSON.parse(opening) : (opening || {});
      const parsedParty1 = typeof party1 === 'string' ? JSON.parse(party1) : (party1 || {});
      const parsedParty2 = typeof party2 === 'string' ? JSON.parse(party2) : (party2 || {});
      const parsedClosing = typeof closing === 'string' ? JSON.parse(closing) : (closing || {});
      const parsedStory = typeof story === 'string' ? JSON.parse(story) : (story || []);
      const parsedGifts = typeof gifts === 'string' ? JSON.parse(gifts) : (gifts || []);

      currentData.opening = { ...currentData.opening, ...parsedOpening };
      currentData.party1 = { ...currentData.party1, ...parsedParty1 };
      currentData.party2 = { ...currentData.party2, ...parsedParty2 };
      currentData.closing = { ...currentData.closing, ...parsedClosing };

      currentData.story = (Array.isArray(parsedStory) ? parsedStory : []).filter(
        (item) => item && (item.title || item.description)
      );

      currentData.gifts = (Array.isArray(parsedGifts) ? parsedGifts : []).filter(
        (item) => item && (item.account_number || item.bank_name)
      );

      currentData.rsvp_enabled = rsvp_enabled === 'true' || rsvp_enabled === true || rsvp_enabled === '1' || rsvp_enabled === 1;
      currentData.wishes_enabled = wishes_enabled === 'true' || wishes_enabled === true || wishes_enabled === '1' || wishes_enabled === 1;

      // Update invitation record
      const updated = await prisma.invitations.update({
        where: { id },
        data: {
          slug,
          template_id: template_id ? BigInt(template_id) : invitation.template_id,
          data: currentData,
          updated_at: new Date(),
        },
      });

      // Sync events
      let parsedEvents = [];
      if (events) {
        parsedEvents = typeof events === 'string' ? JSON.parse(events) : events;
      }

      if (Array.isArray(parsedEvents) && parsedEvents.length > 0) {
        const maxEvents = invitation.plans?.max_events || 3;
        const validEvents = parsedEvents.slice(0, maxEvents);

        await prisma.invitation_events.deleteMany({ where: { invitation_id: id } });

        for (const ev of validEvents) {
          if (ev.title && ev.start_at) {
            await prisma.invitation_events.create({
              data: {
                invitation_id: id,
                title: ev.title,
                start_at: new Date(ev.start_at),
                end_at: ev.end_at ? new Date(ev.end_at) : null,
                timezone: ev.timezone || 'WIB',
                venue_name: ev.venue_name || '',
                address: ev.address || '',
                maps_url: ev.maps_url || '',
                created_at: new Date(),
                updated_at: new Date(),
              },
            });
          }
        }
      }

      return res.json({
        message: 'Perubahan undangan berhasil disimpan!',
        invitation: updated,
        safeData: currentData,
      });
    } catch (error) {
      console.error('editor update error:', error);
      return res.status(500).json({ message: 'Gagal menyimpan perubahan undangan.' });
    }
  }

  async togglePublish(req, res) {
    try {
      const id = BigInt(req.params.id);

      const invitation = await prisma.invitations.findUnique({ where: { id } });
      if (!invitation) {
        return res.status(404).json({ message: 'Undangan tidak ditemukan.' });
      }

      if (invitation.user_id !== req.user.id && req.user.role !== 'admin') {
        return res.status(403).json({ message: 'Akses ditolak.' });
      }

      const newStatus = invitation.status === 'published' ? 'draft' : 'published';
      const publishedAt = newStatus === 'published' ? new Date() : invitation.published_at;

      const updated = await prisma.invitations.update({
        where: { id },
        data: {
          status: newStatus,
          published_at: publishedAt,
          updated_at: new Date(),
        },
      });

      return res.json({
        message: newStatus === 'published' ? 'Undangan berhasil dipublikasikan!' : 'Status undangan diubah menjadi Draft.',
        status: newStatus,
        invitation: updated,
      });
    } catch (error) {
      console.error('togglePublish error:', error);
      return res.status(500).json({ message: 'Gagal mengubah status publikasi undangan.' });
    }
  }
}

module.exports = new EditorController();
