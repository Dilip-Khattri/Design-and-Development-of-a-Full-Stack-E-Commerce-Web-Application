/**
 * Cart JavaScript
 * Handles cart functionality with AJAX
 */

// Add to cart
function addToCart(productId, quantity = 1) {
    fetch('cart-handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add&product_id=${productId}&quantity=${quantity}&csrf_token=${getCsrfToken()}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Product added to cart', 'success');
            updateCartCount(data.cart_count);
        } else {
            showNotification(data.message || 'Failed to add to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

// Update cart quantity
function updateCartQuantity(cartId, quantity) {
    if (quantity < 1) {
        if (!confirm('Remove this item from cart?')) {
            return;
        }
    }
    
    fetch('cart-handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=update&cart_id=${cartId}&quantity=${quantity}&csrf_token=${getCsrfToken()}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showNotification(data.message || 'Failed to update cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

// Remove from cart
function removeFromCart(cartId) {
    if (!confirm('Remove this item from cart?')) {
        return;
    }
    
    fetch('cart-handler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=remove&cart_id=${cartId}&csrf_token=${getCsrfToken()}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showNotification(data.message || 'Failed to remove item', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

// Update cart count in header
function updateCartCount(count) {
    const cartCountElement = document.querySelector('.cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = count;
        if (count > 0) {
            cartCountElement.style.display = 'inline-block';
        } else {
            cartCountElement.style.display = 'none';
        }
    }
}

// Get CSRF token
function getCsrfToken() {
    const tokenInput = document.querySelector('input[name="csrf_token"]');
    if (tokenInput) {
        return tokenInput.value;
    }
    // Try to get from meta tag
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    if (tokenMeta) {
        return tokenMeta.getAttribute('content');
    }
    return '';
}

// Show notification
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '250px';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.transition = 'opacity 0.5s';
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 500);
    }, 3000);
}

// Quantity controls on cart page
document.addEventListener('DOMContentLoaded', function() {
    const quantityInputs = document.querySelectorAll('.quantity-input');
    
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const cartId = this.dataset.cartId;
            const quantity = parseInt(this.value);
            
            if (quantity > 0) {
                updateCartQuantity(cartId, quantity);
            }
        });
    });
    
    // Quantity increment/decrement buttons
    const incrementButtons = document.querySelectorAll('.qty-increment');
    const decrementButtons = document.querySelectorAll('.qty-decrement');
    
    incrementButtons.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.quantity-input');
            input.value = parseInt(input.value) + 1;
            const cartId = input.dataset.cartId;
            updateCartQuantity(cartId, parseInt(input.value));
        });
    });
    
    decrementButtons.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.quantity-input');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
                const cartId = input.dataset.cartId;
                updateCartQuantity(cartId, parseInt(input.value));
            }
        });
    });
});
