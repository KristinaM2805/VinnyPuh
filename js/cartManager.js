class CartManager {
    static updateCartCounter() {
        try {
            const orders = JSON.parse(localStorage.getItem('currentOrders')) || [];
            const totalItems = orders.reduce((total, order) => total + (order.quantity || 1), 0);
            const cartCounter = document.getElementById('cartCounter');
            if (cartCounter) {
                cartCounter.textContent = totalItems;
            }
        } catch (error) {
            console.error('Error updating cart counter:', error);
        }
    }

    static getCartItemsCount() {
        try {
            const orders = JSON.parse(localStorage.getItem('currentOrders')) || [];
            return orders.reduce((total, order) => total + (order.quantity || 1), 0);
        } catch (error) {
            console.error('Error getting cart items count:', error);
            return 0;
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    CartManager.updateCartCounter();
});

window.addEventListener('storage', function(e) {
    if (e.key === 'currentOrders') {
        CartManager.updateCartCounter();
    }
});