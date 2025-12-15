const express = require('express');
const router = express.Router();
const { getAllUsers, getDashboardStats } = require('../controllers/adminController');
const { protect, authorize } = require('../middlewares/auth');

router.use(protect);
router.use(authorize('admin'));

router.get('/users', getAllUsers);
router.get('/stats', getDashboardStats);

module.exports = router;
