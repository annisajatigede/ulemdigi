require('dotenv').config();
const express = require('express');
const cors = require('cors');
const cookieParser = require('cookie-parser');
const path = require('path');
const fs = require('fs');
const prisma = require('./config/prisma');

const apiRoutes = require('./routes/api');

const app = express();
const PORT = process.env.PORT || 3000;

// CORS configuration
const allowedOrigins = [
  'http://localhost:5173',
  'http://127.0.0.1:5173',
  process.env.FRONTEND_URL,
].filter(Boolean);

app.use(
  cors({
    origin: function (origin, callback) {
      if (!origin || allowedOrigins.includes(origin)) {
        callback(null, true);
      } else {
        callback(null, true); // Allow during local development
      }
    },
    credentials: true,
  })
);

app.use(cookieParser());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Serve static assets
const publicPath = path.join(__dirname, '../public');
app.use('/uploads', express.static(path.join(publicPath, 'uploads')));
app.use('/audio', express.static(path.join(publicPath, 'audio')));
app.use('/templates', express.static(path.join(publicPath, 'templates')));
app.use('/storage', express.static(path.join(publicPath, 'storage')));
app.use('/images', express.static(path.join(publicPath, 'images')));

// Dynamic WhatsApp / Open Graph Preview for Public Invitations
app.get(['/u/:slug', '/u/:slug/:token'], async (req, res, next) => {
  try {
    const { slug, token } = req.params;
    const invitation = await prisma.invitations.findUnique({
      where: { slug },
      include: {
        templates: true,
        invitation_events: { orderBy: { start_at: 'asc' } },
      },
    });

    if (!invitation) {
      return next();
    }

    const data =
      typeof invitation.data === 'string'
        ? JSON.parse(invitation.data)
        : invitation.data || {};

    const party1 = data.party1?.name || data.party1?.full_name || 'Mempelai';
    const party2 = data.party2?.name || data.party2?.full_name || 'Mempelai';

    let guestName = null;
    if (token) {
      const guest = await prisma.guests.findFirst({
        where: { invitation_id: invitation.id, personal_token: token },
      });
      if (guest) {
        guestName = guest.name;
      }
    }

    // Determine Wedding Photo for WhatsApp og:image
    let photoPath =
      data.party1?.photo_path ||
      data.party2?.photo_path ||
      data.cover_image;

    if (!photoPath && Array.isArray(data.gallery) && data.gallery.length > 0) {
      photoPath =
        typeof data.gallery[0] === 'string'
          ? data.gallery[0]
          : data.gallery[0]?.url;
    }

    if (!photoPath && invitation.templates?.thumbnail_path) {
      photoPath = invitation.templates.thumbnail_path;
    }

    const host = req.get('host') || 'www.forkoway.com';
    const protocol =
      req.headers['x-forwarded-proto'] ||
      (req.protocol === 'http' && !host.includes('localhost') ? 'https' : req.protocol);
    const baseUrl = `${protocol}://${host}`;

    let ogImage = `${baseUrl}/images/forkoway-og.jpg`;
    if (photoPath) {
      if (photoPath.startsWith('http://') || photoPath.startsWith('https://')) {
        ogImage = photoPath;
      } else {
        ogImage = `${baseUrl}${photoPath.startsWith('/') ? '' : '/'}${photoPath}`;
      }
    }

    const ogTitle = guestName
      ? `Kepada Yth. ${guestName} - Undangan Pernikahan ${party1} & ${party2}`
      : `The Wedding of ${party1} & ${party2} - Undangan Pernikahan`;

    let eventDateStr = '';
    if (invitation.invitation_events && invitation.invitation_events.length > 0) {
      const ev = invitation.invitation_events[0];
      eventDateStr = new Date(ev.start_at).toLocaleDateString('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
      });
    }

    const ogDesc = eventDateStr
      ? `Tanpa mengurangi rasa hormat, kami mengundang Anda untuk hadir pada hari bahagia kami (${eventDateStr}). Klik untuk membuka undangan.`
      : `Tanpa mengurangi rasa hormat, kami mengundang Anda untuk hadir di hari bahagia pernikahan kami. Klik untuk membuka undangan.`;

    const ogUrl = `${baseUrl}/u/${slug}${token ? '/' + token : ''}`;

    // Read index.html from dist
    const candidates = [
      path.join(__dirname, '../../dist/index.html'),
      path.join(__dirname, '../../frontend/dist/index.html'),
      path.join(__dirname, '../../frontend/index.html'),
    ];
    let html = '';
    for (const c of candidates) {
      if (fs.existsSync(c)) {
        html = fs.readFileSync(c, 'utf8');
        break;
      }
    }

    if (!html) {
      return next();
    }

    const metaTags = `
    <!-- Dynamic WhatsApp Open Graph Metadata for Invitation -->
    <title>${ogTitle}</title>
    <meta name="title" content="${ogTitle}" />
    <meta name="description" content="${ogDesc}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="${ogUrl}" />
    <meta property="og:title" content="${ogTitle}" />
    <meta property="og:description" content="${ogDesc}" />
    <meta property="og:image" content="${ogImage}" />
    <meta property="og:image:secure_url" content="${ogImage}" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <meta property="og:site_name" content="Forkoway.com" />
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="${ogTitle}" />
    <meta name="twitter:description" content="${ogDesc}" />
    <meta name="twitter:image" content="${ogImage}" />
    `;

    // Strip default title and existing og tags
    html = html.replace(/<title>.*?<\/title>/i, '');
    html = html.replace(/<meta\s+property=["']og:[^"']*["'][^>]*>/gi, '');
    html = html.replace(/<meta\s+name=["'](twitter|title|description):[^"']*["'][^>]*>/gi, '');
    html = html.replace('</head>', `${metaTags}\n</head>`);

    res.setHeader('Content-Type', 'text/html; charset=utf-8');
    return res.send(html);
  } catch (err) {
    console.error('Error serving OG invitation page:', err);
    return next();
  }
});

// Mount API routes
app.use('/api', apiRoutes);
app.use('/', apiRoutes);

// Root health check / info (only for /api-status)
app.get('/api-status', (req, res) => {
  res.json({
    status: 'online',
    app: 'Forkoway.com API Server (Node.js + Express + Prisma)',
    version: '2.0.0',
    time: new Date().toISOString(),
  });
});

// Serve frontend dist if available (SPA Fallback)
const distPath = path.join(__dirname, '../../frontend/dist');
if (fs.existsSync(distPath)) {
  app.use(express.static(distPath));
  app.get('*', (req, res, next) => {
    if (
      req.path.startsWith('/api') ||
      req.path.startsWith('/uploads') ||
      req.path.startsWith('/audio') ||
      req.path.startsWith('/templates') ||
      req.path.startsWith('/storage')
    ) {
      return next();
    }
    res.sendFile(path.join(distPath, 'index.html'));
  });
} else {
  app.get('/', (req, res) => {
    res.json({
      status: 'online',
      app: 'Forkoway.com API Server (Node.js + Express + Prisma)',
      version: '2.0.0',
      time: new Date().toISOString(),
    });
  });
}

// Global error handler
app.use((err, req, res, next) => {
  console.error('Unhandled server error:', err);
  res.status(err.status || 500).json({
    message: err.message || 'Terjadi kesalahan internal pada server.',
  });
});

if (require.main === module) {
  app.listen(PORT, () => {
    console.log(`🚀 Forkoway.com Backend Server is running on http://localhost:${PORT}`);
  });
}

module.exports = app;
