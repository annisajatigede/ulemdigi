const jwt = require('jsonwebtoken');
const prisma = require('../config/prisma');

async function authMiddleware(req, res, next) {
  try {
    let token = null;

    if (req.headers.authorization && req.headers.authorization.startsWith('Bearer ')) {
      token = req.headers.authorization.split(' ')[1];
    } else if (req.cookies && req.cookies.token) {
      token = req.cookies.token;
    }

    if (!token) {
      return res.status(401).json({ message: 'Unauthenticated.' });
    }

    const secret = process.env.JWT_SECRET || 'kawinan_super_secure_jwt_token_secret_key_2026';
    const decoded = jwt.verify(token, secret);

    const user = await prisma.users.findUnique({
      where: { id: BigInt(decoded.id) },
    });

    if (!user) {
      return res.status(401).json({ message: 'User not found.' });
    }

    req.user = user;
    next();
  } catch (error) {
    return res.status(401).json({ message: 'Invalid or expired token.' });
  }
}

// Optional auth: attaches req.user if token is present, but doesn't block if not
async function optionalAuthMiddleware(req, res, next) {
  try {
    let token = null;
    if (req.headers.authorization && req.headers.authorization.startsWith('Bearer ')) {
      token = req.headers.authorization.split(' ')[1];
    } else if (req.cookies && req.cookies.token) {
      token = req.cookies.token;
    }

    if (token) {
      const secret = process.env.JWT_SECRET || 'kawinan_super_secure_jwt_token_secret_key_2026';
      const decoded = jwt.verify(token, secret);
      const user = await prisma.users.findUnique({
        where: { id: BigInt(decoded.id) },
      });
      if (user) {
        req.user = user;
      }
    }
  } catch (e) {
    // Ignore invalid optional token
  }
  next();
}

module.exports = {
  authMiddleware,
  optionalAuthMiddleware,
};
