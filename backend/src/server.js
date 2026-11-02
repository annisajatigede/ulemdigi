require('dotenv').config();
const express = require('express');
const cors = require('cors');
const cookieParser = require('cookie-parser');
const path = require('path');

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
const fs = require('fs');
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
