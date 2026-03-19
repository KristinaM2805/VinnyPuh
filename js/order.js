class OrderPage {
    constructor() {
        this.orders = [];
        this.init();
    }

    init() {
        console.log('OrderPage initialized');
        this.loadOrdersData();
        this.setupEventListeners();
        this.setupDeliveryToggle();
        this.updateOrderDisplay();
    }

    loadOrdersData() {
        try {
            const ordersData = JSON.parse(localStorage.getItem('currentOrders'));
            console.log('Loaded orders data from localStorage:', ordersData);
            
            if (ordersData && Array.isArray(ordersData) && ordersData.length > 0) {
                this.orders = ordersData;
                console.log(`Loaded ${this.orders.length} orders`);
            } else {
                this.orders = [];
                console.log('No orders found in localStorage');
            }
            this.updateOrderDisplay();
        } catch (error) {
            console.error('Error loading orders data:', error);
            this.orders = [];
            this.updateOrderDisplay();
        }
    }

    updateOrderDisplay() {
        console.log('Updating order display');
        this.createOrdersContainer();

        if (this.orders.length === 0) {
            console.log('No orders to display');
            this.showEmptyState();
            this.updateTotalPrice();
            return;
        }

        this.hideEmptyState();
        this.updateAllOrdersDisplay();
        this.updateTotalPrice();
        this.updateGridHeight();
    }

    createOrdersContainer() {
        const descrOrderGrid = document.querySelector('.descr_order_grid');
        if (!descrOrderGrid) {
            console.error('descr_order_grid element not found');
            return;
        }

        const oldContainer = document.getElementById('orders_container');
        if (oldContainer) {
            oldContainer.remove();
        }

        const ordersContainer = document.createElement('div');
        ordersContainer.id = 'orders_container';
        ordersContainer.style.cssText = `
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 20px;
        `;

        const addPositionBtn = document.getElementById('addPositionBtn');
        if (addPositionBtn) {
            descrOrderGrid.insertBefore(ordersContainer, addPositionBtn);
        } else {
            descrOrderGrid.appendChild(ordersContainer);
        }
        
        console.log('Orders container created');
    }

    updateGridHeight() {
        const descrOrderGrid = document.querySelector('.descr_order_grid');
        if (!descrOrderGrid) return;

        const additionalOrdersCount = Math.max(0, this.orders.length);
        const baseHeight = 600;
        const additionalHeight = additionalOrdersCount * 140;
        
        const totalHeight = baseHeight + additionalHeight;
        descrOrderGrid.style.height = `${totalHeight}px`;
        
        const formGrid = document.querySelector('.form_grid');
        if (formGrid) {
            formGrid.style.gridTemplateRows = `100px ${totalHeight}px`;
        }
    }

    updateAllOrdersDisplay() {
        const ordersContainer = document.getElementById('orders_container');
        if (!ordersContainer) {
            console.error('Orders container not found');
            return;
        }

        ordersContainer.innerHTML = '';

        this.orders.forEach((order, index) => {
            const orderElement = this.createOrderElement(order, index);
            ordersContainer.appendChild(orderElement);
        });
        
        console.log(`Displayed ${this.orders.length} orders`);
    }

    createOrderElement(order, index) {
        const orderDiv = document.createElement('div');
        orderDiv.className = 'additional-order-item';
        orderDiv.style.cssText = `
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 15px;
            align-items: center;
            padding: 15px;
        `;

        const imgContainer = document.createElement('div');
        imgContainer.className = 'img_container';
        
        const img = document.createElement('img');
        img.className = 'cake_com_img';
        img.src = order.image || '/image/default-cake.jpg';
        img.alt = order.title || 'Торт';
        img.style.cssText = `
            width: 100%;
            max-height: 150px;
            object-fit: cover;
            border-radius: 10px;
        `;
        imgContainer.appendChild(img);

        const descriptops = document.createElement('div');
        descriptops.className = 'descriptops';
        
        const paramsList = document.createElement('ul');
        paramsList.style.cssText = `
            list-style: none;
            padding: 0;
            margin: 0;
            font-size: 14px;
            font-family: "Monkey", sans-serif;
            color: #521D04;
        `;

        const typeItem = document.createElement('li');
        typeItem.textContent = `Тип: ${order.title || 'Не указано'}`;
        typeItem.style.marginBottom = '8px';
        paramsList.appendChild(typeItem);

        if (order.selectedParams) {
            Object.entries(order.selectedParams).forEach(([key, value]) => {
                const item = document.createElement('li');
                item.textContent = `${key}: ${value}`;
                item.style.marginBottom = '5px';
                paramsList.appendChild(item);
            });
        }

        descriptops.appendChild(paramsList);

        const quantityPanel = document.createElement('div');
        quantityPanel.className = 'quantity_panel';
        quantityPanel.style.cssText = `
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: center;
        `;

        const decreaseBtn = document.createElement('button');
        decreaseBtn.className = 'mini_btm';
        decreaseBtn.textContent = '-';
        decreaseBtn.style.cssText = `
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #F5E27A;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: all 0.3s ease;
        `;
        
        decreaseBtn.addEventListener('mouseenter', () => {
            decreaseBtn.style.backgroundColor = '#e6d46a';
            decreaseBtn.style.transform = 'translateY(-2px)';
        });
        
        decreaseBtn.addEventListener('mouseleave', () => {
            decreaseBtn.style.backgroundColor = '#F5E27A';
            decreaseBtn.style.transform = 'translateY(0)';
        });
        
        decreaseBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.changeQuantity(index, -1);
        });

        const quantityDisplay = document.createElement('div');
        quantityDisplay.className = 'circl';
        quantityDisplay.textContent = order.quantity || 1;
        quantityDisplay.style.cssText = `
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #F5E27A;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
        `;

        const increaseBtn = document.createElement('button');
        increaseBtn.className = 'mini_btm';
        increaseBtn.textContent = '+';
        increaseBtn.style.cssText = `
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #F5E27A;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: all 0.3s ease;
        `;
        
        increaseBtn.addEventListener('mouseenter', () => {
            increaseBtn.style.backgroundColor = '#e6d46a';
            increaseBtn.style.transform = 'translateY(-2px)';
        });
        
        increaseBtn.addEventListener('mouseleave', () => {
            increaseBtn.style.backgroundColor = '#F5E27A';
            increaseBtn.style.transform = 'translateY(0)';
        });
        
        increaseBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.changeQuantity(index, 1);
        });

        quantityPanel.appendChild(decreaseBtn);
        quantityPanel.appendChild(quantityDisplay);
        quantityPanel.appendChild(increaseBtn);

        const controlContainer = document.createElement('div');
        controlContainer.style.cssText = `
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        `;

        const priceElement = document.createElement('div');
        priceElement.className = 'price';
        priceElement.style.cssText = `
            font-size: 24px;
            font-weight: bold;
            color: #521D04;
        `;
        const positionPrice = (order.basePrice || 0) * (order.quantity || 1);
        priceElement.textContent = `${positionPrice} byn`;

        controlContainer.appendChild(priceElement);

        orderDiv.appendChild(imgContainer);
        orderDiv.appendChild(descriptops);
        orderDiv.appendChild(quantityPanel);
        orderDiv.appendChild(controlContainer);

        return orderDiv;
    }

    changeQuantity(index, change) {
        if (index < 0 || index >= this.orders.length) {
            console.error(`Invalid index: ${index}`);
            return;
        }

        const order = this.orders[index];
        const currentQuantity = order.quantity || 1;
        const newQuantity = currentQuantity + change;
        
        console.log(`Changing quantity for order ${index}: ${currentQuantity} + ${change} = ${newQuantity}`);
        
        if (newQuantity < 1) {
            console.log(`Removing order ${index} because quantity would be ${newQuantity}`);
            this.removeOrder(index);
        } else {
            order.quantity = newQuantity;
            console.log(`Updated order ${index} quantity to ${order.quantity}`);
            this.updateOrderDisplay();
            this.saveOrdersToStorage();
        }
    }

    removeOrder(index) {
        if (index < 0 || index >= this.orders.length) {
            console.error(`Invalid index for removal: ${index}`);
            return;
        }

        console.log(`Removing order at index ${index}`);
        this.orders.splice(index, 1);
        
        this.updateOrderDisplay();
        this.saveOrdersToStorage();
    }

    updateTotalPrice() {
        const total = this.orders.reduce((sum, order) => {
            return sum + ((order.basePrice || 0) * (order.quantity || 1));
        }, 0);

        console.log('Total price:', total);
     
        const totalPriceField = document.getElementById('total_price');
        if (totalPriceField) {
            totalPriceField.value = total;
        }
        
        return total;
    }

    showEmptyState() {
        const descrOrderGrid = document.querySelector('.descr_order_grid');
        if (!descrOrderGrid) {
            console.error('descr_order_grid not found for empty state');
            return;
        }

        const ordersContainer = document.getElementById('orders_container');
        if (ordersContainer) {
            ordersContainer.style.display = 'none';
        }

        let emptyState = document.getElementById('empty_state');
        if (!emptyState) {
            emptyState = document.createElement('div');
            emptyState.id = 'empty_state';
            emptyState.style.cssText = `
                grid-column: 1 / -1;
                grid-row: 1 / 3;
                text-align: center;
                padding: 40px;
                color: #666;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            `;
            emptyState.innerHTML = `
                <img src="/image/pack.png" alt="Корзина пуста" style="width: 100px; opacity: 0.5; margin-bottom: 20px;">
                <h3 style="margin: 0 0 10px 0; font-family: 'Monkey';">Корзина пуста</h3>
                <p style="margin: 0 0 20px 0;">Добавьте десерты, чтобы оформить заказ</p>
            `;
            descrOrderGrid.insertBefore(emptyState, descrOrderGrid.firstChild);
        }
        emptyState.style.display = 'flex';
        console.log('Empty state shown');
    }

    hideEmptyState() {
        const descrOrderGrid = document.querySelector('.descr_order_grid');
        if (!descrOrderGrid) return;

        const ordersContainer = document.getElementById('orders_container');
        if (ordersContainer) {
            ordersContainer.style.display = 'flex';
        }

        const emptyState = document.getElementById('empty_state');
        if (emptyState) {
            emptyState.style.display = 'none';
        }
        
        console.log('Empty state hidden');
    }

    saveOrdersToStorage() {
        localStorage.setItem('currentOrders', JSON.stringify(this.orders));
        console.log('Orders saved to localStorage:', this.orders);
    }

    setupEventListeners() {
        console.log('Setting up event listeners');

        const addPositionBtn = document.getElementById('addPositionBtn');
        if (addPositionBtn) {
            addPositionBtn.addEventListener('click', () => {
                console.log('Add position button clicked');
                this.addNewPosition();
            });
        } else {
            console.error('Add position button not found');
        }

        const form = document.getElementById('ordear_form');
        if (form) {
            form.addEventListener('submit', (e) => this.handleFormSubmit(e));
        } else {
            console.error('Order form not found');
        }

        if (form && !document.getElementById('order_items')) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.id = 'order_items';
            hiddenInput.name = 'order_items';
            form.appendChild(hiddenInput);
        }
        
        if (form && !document.getElementById('total_price')) {
            const totalInput = document.createElement('input');
            totalInput.type = 'hidden';
            totalInput.id = 'total_price';
            totalInput.name = 'total_price';
            form.appendChild(totalInput);
        }
    }

   addNewPosition() {
    console.log('Redirecting to dessert selection...');
    this.saveOrdersToStorage();

    window.location.href = 'dessertType.php';}

    setupDeliveryToggle() {
        const deliverySelect = document.getElementById('delivery_type');
        const addressGroup = document.getElementById('address_group');

        if (deliverySelect && addressGroup) {
            deliverySelect.addEventListener('change', (e) => {
                console.log('Delivery type changed:', e.target.value);
                if (e.target.value === 'delivery') {
                    addressGroup.style.display = 'flex';
                } else {
                    addressGroup.style.display = 'none';
                }
            });

            if (deliverySelect.value === 'pickup') {
                addressGroup.style.display = 'none';
            }
        } else {
            console.log('Delivery toggle elements not found');
        }
    }

    handleFormSubmit(e) {
    e.preventDefault();
    
    if (this.orders.length === 0) {
        alert('Пожалуйста, добавьте хотя бы один десерт в заказ');
        return false;
    }
    
    if (!this.validateForm()) { 
        return false;
    }
    
    const orderData = {
        full_name: document.getElementById('full_name').value.trim(),
        phone: document.getElementById('phone').value.trim(),
        email: document.getElementById('email').value.trim(),
        delivery_type: document.getElementById('delivery_type').value,
        address: document.getElementById('address')?.value.trim() || '',
        wishes: document.getElementById('wishes')?.value.trim() || '',
        order_items: JSON.stringify(this.orders),
        total_price: this.updateTotalPrice()
    };
    
    console.log('Submitting order data:', orderData);
    
    const submitBtn = document.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    const originalHTML = submitBtn.innerHTML;
    submitBtn.textContent = 'Отправка...';
    submitBtn.disabled = true;
    submitBtn.style.opacity = '0.7';
    
    const formData = new FormData();
    for (const key in orderData) {
        formData.append(key, orderData[key]);
    }
    
    fetch('/php/process_order.php', {
        method: 'POST',
        body: formData,
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Response status:', response.status, response.statusText);
        
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        
        return response.text().then(text => {
            console.log('Raw response:', text);
            
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('JSON parse error:', e);
                console.error('Response text that failed to parse:', text);
                throw new Error('Сервер вернул некорректные данные. Пожалуйста, попробуйте еще раз.');
            }
        });
    })
    .then(data => {
        console.log('Parsed response:', data);
        
        if (data.success) {
            alert(data.message);

            localStorage.removeItem('currentOrders');
            this.orders = [];
       
            setTimeout(() => {
                window.location.href = data.redirect || 'thanks.php';
            }, 1500);
            
        } else {
            alert('Ошибка: ' + data.message);
            console.error('Server error:', data);
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        alert('Произошла ошибка при отправке заказа: ' + error.message + 
              '\nПожалуйста, свяжитесь с нами по телефону.');
    })
    .finally(() => {
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
        submitBtn.style.opacity = '1';
    });
    
    return false;}

    validateForm() {
    const fullName = document.getElementById('full_name')?.value.trim();
    const email = document.getElementById('email')?.value.trim();
    const phone = document.getElementById('phone')?.value.trim();
    const deliveryType = document.getElementById('delivery_type')?.value;
    const address = document.getElementById('address');

    if (!fullName) {
        alert('Пожалуйста, введите ваше имя');
        return false;
    }
    if (fullName.length < 2) {
        alert('Имя должно содержать минимум 2 символа');
        return false;
    }
    if (fullName.length > 100) {
        alert('Имя слишком длинное (максимум 100 символов)');
        return false;
    }
    const nameRegex = /^[а-яА-ЯёЁa-zA-Z\s\-]+$/u;
    if (!nameRegex.test(fullName)) {
        alert('Имя может содержать только буквы, пробелы и дефисы');
        return false;
    }
    if (!email) {
        alert('Пожалуйста, введите ваш email');
        return false;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert('Пожалуйста, введите корректный email адрес');
        return false;
    }
    
    if (email.length > 100) {
        alert('Email слишком длинный (максимум 100 символов)');
        return false;
    }
    if (!phone) {
        alert('Пожалуйста, введите ваш номер телефона');
        return false;
    }
    
    const cleanPhone = phone.replace(/[^\d+]/g, '');
    
    if (cleanPhone === '0' || cleanPhone === '+0') {
        alert('Пожалуйста, введите корректный номер телефона');
        return false;
    }
    
    if (cleanPhone.length < 7) {
        alert('Номер телефона слишком короткий (минимум 7 цифр)');
        return false;
    }
    
    if (cleanPhone.length > 20) {
        alert('Номер телефона слишком длинный');
        return false;
    }
    
    const phoneRegex = /^[\+]?[0-9\s\-\(\)]{7,20}$/;
    if (!phoneRegex.test(phone)) {
        alert('Неверный формат телефона. Пример: +375291234567 или 80291234567');
        return false;
    }
    if (deliveryType === 'delivery' && address && !address.value.trim()) {
        alert('Пожалуйста, введите адрес доставки');
        return false;
    }
    
    if (address && address.value.trim().length > 500) {
        alert('Адрес слишком длинный (максимум 500 символов)');
        return false;
    }

    if (this.orders.length === 0) {
        alert('Пожалуйста, добавьте хотя бы один десерт в заказ');
        return false;
    }

    return true;
}
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DOM полностью загружен ===');
    console.log('Current URL:', window.location.href);
    console.log('Pathname:', window.location.pathname);
   ё
    const isOrderPage = document.querySelector('.descr_order_grid') !== null ||
                        document.getElementById('ordear_form') !== null ||
                        window.location.pathname.includes('orderPage3') ||
                        window.location.pathname.includes('orderPage3.php');
    
    console.log('Is this order page?', isOrderPage);
    
    if (isOrderPage) {
        console.log('Initializing OrderPage...');
        if (window.orderPageInstance) {
            console.log('Clearing previous instance');
            delete window.orderPageInstance;
        }
        
        window.orderPageInstance = new OrderPage();
        
        const storedOrders = localStorage.getItem('currentOrders');
        console.log('localStorage has orders:', storedOrders !== null);
        console.log('Orders data:', storedOrders);
        
        setTimeout(() => {
            if (window.orderPageInstance) {
                window.orderPageInstance.updateOrderDisplay();
            }
        }, 100);
    }
});

window.addEventListener('load', function() {
    console.log('=== Window fully loaded ===');
    
    const isOrderPage = document.querySelector('.descr_order_grid') !== null;
    if (isOrderPage && !window.orderPageInstance) {
        console.log('Late initialization for dynamically loaded page');
        window.orderPageInstance = new OrderPage();
    }
});


window.refreshOrders = function() {
    if (window.orderPageInstance) {
        window.orderPageInstance.loadOrdersData();
        window.orderPageInstance.updateOrderDisplay();
    }
};