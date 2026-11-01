const express = require('express');
const router = express.Router();

const { authMiddleware, optionalAuthMiddleware } = require('../middlewares/authMiddleware');
const adminMiddleware = require('../middlewares/adminMiddleware');
const upload = require('../middlewares/uploadMiddleware');

// Controllers
const authController = require('../controllers/authController');
const homeController = require('../controllers/homeController');
const checkoutController = require('../controllers/checkoutController');
const dashboardController = require('../controllers/dashboardController');
const editorController = require('../controllers/editorController');
const publicInvitationController = require('../controllers/publicInvitationController');
const guestController = require('../controllers/guestController');

// Admin Controllers
const adminDashboardController = require('../controllers/admin/adminDashboardController');
const adminUserController = require('../controllers/admin/adminUserController');
const adminTemplateController = require('../controllers/admin/adminTemplateController');
const adminMusicController = require('../controllers/admin/adminMusicController');
const adminPlanController = require('../controllers/admin/adminPlanController');
const adminTransactionController = require('../controllers/admin/adminTransactionController');
const adminSettingController = require('../controllers/admin/adminSettingController');

// 1. Auth & Profile
router.post('/auth/login', (req, res) => authController.login(req, res));
router.post('/auth/register', (req, res) => authController.register(req, res));
router.post('/auth/logout', (req, res) => authController.logout(req, res));
router.get('/auth/me', authMiddleware, (req, res) => authController.me(req, res));
router.put('/profile', authMiddleware, (req, res) => authController.updateProfile(req, res));

// 2. Landing Page
router.get('/home', (req, res) => homeController.getLandingData(req, res));

// 3. Public Digital Invitations
router.get('/invitations/public/:slug', optionalAuthMiddleware, (req, res) => publicInvitationController.show(req, res));
router.get('/invitations/public/:slug/:token', optionalAuthMiddleware, (req, res) => publicInvitationController.showGuest(req, res));
router.post('/invitations/public/:slug/rsvp', (req, res) => publicInvitationController.submitRsvp(req, res));

// 4. Checkout & Payments
router.get('/checkout/select', optionalAuthMiddleware, (req, res) => checkoutController.getCheckoutOptions(req, res));
router.post('/checkout/pay', authMiddleware, (req, res) => checkoutController.createTransaction(req, res));
router.get('/checkout/transactions/:id', authMiddleware, (req, res) => checkoutController.getTransaction(req, res));
router.get('/checkout/success/:id', authMiddleware, (req, res) => checkoutController.getSuccessData(req, res));
router.post('/payment/midtrans/webhook', (req, res) => checkoutController.midtransWebhook(req, res));
router.post('/payment/simulator/callback', (req, res) => checkoutController.simulatorCallback(req, res));

// 5. User Dashboard
router.get('/dashboard', authMiddleware, (req, res) => dashboardController.index(req, res));

// 6. Section-based Invitation Editor
const editorUploadFields = upload.fields([
  { name: 'party1_photo', maxCount: 1 },
  { name: 'party2_photo', maxCount: 1 },
  { name: 'custom_music', maxCount: 1 },
  { name: 'gallery_photos', maxCount: 20 },
]);

router.get('/editor/:id', authMiddleware, (req, res) => editorController.edit(req, res));
router.put('/editor/:id', authMiddleware, editorUploadFields, (req, res) => editorController.update(req, res));
router.post('/editor/:id/toggle-publish', authMiddleware, (req, res) => editorController.togglePublish(req, res));

// 7. Guests Management & RSVP
router.get('/invitations/:id/guests', authMiddleware, (req, res) => guestController.index(req, res));
router.post('/invitations/:id/guests', authMiddleware, (req, res) => guestController.store(req, res));
router.put('/invitations/:id/guests/:guestId', authMiddleware, (req, res) => guestController.update(req, res));
router.delete('/invitations/:id/guests/:guestId', authMiddleware, (req, res) => guestController.destroy(req, res));
router.post('/invitations/:id/guests/:guestId/checkin', authMiddleware, (req, res) => guestController.checkIn(req, res));
router.get('/invitations/:id/guests/export', authMiddleware, (req, res) => guestController.exportCsv(req, res));

// 8. Admin Panel
router.use('/admin', authMiddleware, adminMiddleware);

router.get('/admin/dashboard', (req, res) => adminDashboardController.index(req, res));

// Admin Users
router.get('/admin/users', (req, res) => adminUserController.index(req, res));
router.post('/admin/users', (req, res) => adminUserController.store(req, res));
router.put('/admin/users/:id', (req, res) => adminUserController.update(req, res));
router.delete('/admin/users/:id', (req, res) => adminUserController.destroy(req, res));

// Admin Templates
router.get('/admin/templates', (req, res) => adminTemplateController.index(req, res));
router.post('/admin/templates', upload.single('thumbnail'), (req, res) => adminTemplateController.store(req, res));
router.put('/admin/templates/:id', upload.single('thumbnail'), (req, res) => adminTemplateController.update(req, res));
router.delete('/admin/templates/:id', (req, res) => adminTemplateController.destroy(req, res));

// Admin Musics Library
router.get('/admin/musics', (req, res) => adminMusicController.index(req, res));
router.post('/admin/musics', upload.single('music_file'), (req, res) => adminMusicController.store(req, res));
router.post('/admin/musics/:id/toggle', (req, res) => adminMusicController.toggle(req, res));
router.delete('/admin/musics/:id', (req, res) => adminMusicController.destroy(req, res));

// Admin Plans
router.get('/admin/plans', (req, res) => adminPlanController.index(req, res));
router.put('/admin/plans/:id', (req, res) => adminPlanController.update(req, res));

// Admin Transactions
router.get('/admin/transactions', (req, res) => adminTransactionController.index(req, res));
router.get('/admin/transactions/export', (req, res) => adminTransactionController.exportCsv(req, res));

// Admin Settings
router.get('/admin/settings', (req, res) => adminSettingController.index(req, res));
router.post('/admin/settings', upload.fields([{ name: 'logo', maxCount: 1 }]), (req, res) =>
  adminSettingController.update(req, res)
);

module.exports = router;
