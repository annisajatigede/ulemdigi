const multer = require('multer');
const path = require('path');
const fs = require('fs');

const storage = multer.diskStorage({
  destination: function (req, file, cb) {
    let dest = path.join(__dirname, '../../public/uploads');

    if (file.fieldname === 'music_file' || file.fieldname === 'custom_music') {
      dest = path.join(__dirname, '../../public/audio');
    } else if (file.fieldname === 'thumbnail') {
      dest = path.join(__dirname, '../../public/templates/thumbnails');
    }

    if (!fs.existsSync(dest)) {
      fs.mkdirSync(dest, { recursive: true });
    }
    cb(null, dest);
  },
  filename: function (req, file, cb) {
    const ext = path.extname(file.originalname);
    const safeName = path.basename(file.originalname, ext).replace(/[^a-zA-Z0-9_-]/g, '_');
    const unique = `${Date.now()}_${safeName}${ext}`;
    cb(null, unique);
  },
});

const upload = multer({
  storage: storage,
  limits: {
    fileSize: 20 * 1024 * 1024, // 20 MB max
  },
});

module.exports = upload;
