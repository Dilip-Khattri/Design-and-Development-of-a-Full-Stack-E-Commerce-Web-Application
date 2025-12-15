const express = require('express');
const router = express.Router();
const {
  getAllProducts,
  getProduct,
  createProduct,
  updateProduct,
  deleteProduct,
  uploadMiddleware
} = require('../controllers/productController');
const { protect, authorize } = require('../middlewares/auth');

router.route('/')
  .get(getAllProducts)
  .post(protect, authorize('admin'), uploadMiddleware, createProduct);

router.route('/:id')
  .get(getProduct)
  .put(protect, authorize('admin'), uploadMiddleware, updateProduct)
  .delete(protect, authorize('admin'), deleteProduct);

module.exports = router;
