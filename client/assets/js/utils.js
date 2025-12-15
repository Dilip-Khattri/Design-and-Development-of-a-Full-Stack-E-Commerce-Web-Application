// API Base URL
const API_URL = window.location.origin + '/api';

// Get token from localStorage
const getToken = () => {
  return localStorage.getItem('token');
};

// Get user from localStorage
const getUser = () => {
  const user = localStorage.getItem('user');
  return user ? JSON.parse(user) : null;
};

// Set auth data
const setAuthData = (token, user) => {
  localStorage.setItem('token', token);
  localStorage.setItem('user', JSON.stringify(user));
};

// Clear auth data
const clearAuthData = () => {
  localStorage.removeItem('token');
  localStorage.removeItem('user');
};

// Check if user is logged in
const isLoggedIn = () => {
  return !!getToken();
};

// Check if user is admin
const isAdmin = () => {
  const user = getUser();
  return user && user.role === 'admin';
};

// API request helper
const apiRequest = async (endpoint, options = {}) => {
  const token = getToken();
  
  const defaultOptions = {
    headers: {
      'Content-Type': 'application/json'
    }
  };

  if (token) {
    defaultOptions.headers['Authorization'] = `Bearer ${token}`;
  }

  // Merge options
  const config = {
    ...defaultOptions,
    ...options,
    headers: {
      ...defaultOptions.headers,
      ...options.headers
    }
  };

  try {
    const response = await fetch(`${API_URL}${endpoint}`, config);
    const data = await response.json();

    if (!response.ok) {
      throw new Error(data.message || 'Request failed');
    }

    return data;
  } catch (error) {
    console.error('API Error:', error);
    throw error;
  }
};

// Show alert message
const showAlert = (message, type = 'success') => {
  const alertDiv = document.createElement('div');
  alertDiv.className = `alert alert-${type}`;
  alertDiv.textContent = message;
  
  const container = document.querySelector('.container') || document.body;
  container.insertBefore(alertDiv, container.firstChild);
  
  setTimeout(() => {
    alertDiv.remove();
  }, 3000);
};

// Show loading spinner
const showLoading = (element) => {
  element.innerHTML = '<div class="spinner"></div>';
};

// Format currency
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD'
  }).format(amount);
};

// Format date
const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
};

// Update navbar with user info
const updateNavbar = () => {
  const user = getUser();
  const navbar = document.querySelector('.navbar-menu');
  
  if (!navbar) return;

  if (isLoggedIn()) {
    const isUserAdmin = isAdmin();
    
    navbar.innerHTML = `
      <li><a href="/pages/products.html">Products</a></li>
      <li><a href="/pages/cart.html">Cart <span class="cart-badge" id="cart-count">0</span></a></li>
      <li><a href="/pages/orders.html">My Orders</a></li>
      ${isUserAdmin ? '<li><a href="/pages/admin.html">Admin Panel</a></li>' : ''}
      <li><a href="#" id="logout-btn">Logout</a></li>
    `;

    // Add logout event listener
    document.getElementById('logout-btn').addEventListener('click', (e) => {
      e.preventDefault();
      clearAuthData();
      showAlert('Logged out successfully');
      setTimeout(() => {
        window.location.href = '/pages/login.html';
      }, 1000);
    });

    // Update cart count
    updateCartCount();
  } else {
    navbar.innerHTML = `
      <li><a href="/pages/products.html">Products</a></li>
      <li><a href="/pages/login.html">Login</a></li>
      <li><a href="/pages/signup.html">Sign Up</a></li>
    `;
  }
};

// Update cart count in navbar
const updateCartCount = async () => {
  if (!isLoggedIn()) return;

  try {
    const response = await apiRequest('/cart');
    const cartBadge = document.getElementById('cart-count');
    
    if (cartBadge && response.data) {
      const itemCount = response.data.items.reduce((total, item) => total + item.quantity, 0);
      cartBadge.textContent = itemCount;
    }
  } catch (error) {
    console.error('Error updating cart count:', error);
  }
};

// Protect route - redirect if not logged in
const protectRoute = () => {
  if (!isLoggedIn()) {
    showAlert('Please login to access this page', 'danger');
    setTimeout(() => {
      window.location.href = '/pages/login.html';
    }, 1000);
    return false;
  }
  return true;
};

// Protect admin route
const protectAdminRoute = () => {
  if (!isLoggedIn() || !isAdmin()) {
    showAlert('Admin access required', 'danger');
    setTimeout(() => {
      window.location.href = '/pages/login.html';
    }, 1000);
    return false;
  }
  return true;
};

// Initialize navbar on page load
document.addEventListener('DOMContentLoaded', () => {
  updateNavbar();
});
