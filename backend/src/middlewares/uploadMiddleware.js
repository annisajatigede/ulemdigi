const multer = require('multer');
const path = require('path');
const fs = require('fs');
const prisma = require('../config/prisma');

// Use memoryStorage so uploads never crash on read-only serverless filesystems (Vercel)
const storage = multer.memoryStorage();

const upload = multer({
  storage: storage,
  limits: {
    fileSize: 20 * 1024 * 1024, // 20 MB max
  },
});

// Helper function to persist an in-memory file to DB cache and disk (if writable)
async function processAndSaveFile(file, subfolder = 'uploads') {
  if (!file || !file.buffer) return file;

  const ext = path.extname(file.originalname);
  const safeName = path.basename(file.originalname, ext).replace(/[^a-zA-Z0-9_-]/g, '_');
  const filename = `${Date.now()}_${safeName}${ext}`;
  file.filename = filename;

  // 1. Try writing to local disk (if filesystem is writable, e.g. localhost)
  try {
    const destDir = path.join(__dirname, '../../public', subfolder);
    if (!fs.existsSync(destDir)) {
      fs.mkdirSync(destDir, { recursive: true });
    }
    fs.writeFileSync(path.join(destDir, filename), file.buffer);
  } catch (fsErr) {
    // Read-only filesystem on Vercel/serverless: safe to ignore because we persist in DB
  }

  // 2. Persist in MySQL `cache` table so it persists permanently on Vercel
  try {
    const payload = JSON.stringify({
      mimetype: file.mimetype || 'application/octet-stream',
      data: file.buffer.toString('base64'),
    });
    await prisma.cache.upsert({
      where: { key: `file:${filename}` },
      update: {
        value: payload,
        expiration: 2147483647,
      },
      create: {
        key: `file:${filename}`,
        value: payload,
        expiration: 2147483647,
      },
    });
  } catch (dbErr) {
    console.error('Failed to persist uploaded file to DB cache:', dbErr);
  }

  return file;
}

// Wrapper middleware to process files after multer parses multipart/form-data
function autoSaveUploads(req, res, next) {
  const promises = [];

  if (req.file) {
    let subfolder = 'uploads';
    if (req.file.fieldname === 'music_file' || req.file.fieldname === 'custom_music') {
      subfolder = 'audio';
    } else if (req.file.fieldname === 'thumbnail') {
      subfolder = 'templates/thumbnails';
    }
    promises.push(processAndSaveFile(req.file, subfolder));
  }

  if (req.files) {
    if (Array.isArray(req.files)) {
      for (const f of req.files) {
        promises.push(processAndSaveFile(f, 'uploads'));
      }
    } else {
      for (const field of Object.keys(req.files)) {
        let subfolder = 'uploads';
        if (field === 'music_file' || field === 'custom_music') {
          subfolder = 'audio';
        } else if (field === 'thumbnail') {
          subfolder = 'templates/thumbnails';
        }
        for (const f of req.files[field]) {
          promises.push(processAndSaveFile(f, subfolder));
        }
      }
    }
  }

  Promise.all(promises)
    .then(() => next())
    .catch((err) => next(err));
}

module.exports = {
  upload,
  autoSaveUploads,
  processAndSaveFile,
};
