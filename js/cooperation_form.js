document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('cooprt_form');
    
    if (!form) return; 

    function showError(input, message) {
        clearError(input);
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.style.cssText = `
            color: #ff4444;
            font-size: 14px;
            margin-top: 5px;
        `;
        errorDiv.textContent = message;
        
        input.parentNode.appendChild(errorDiv);
        input.style.borderColor = '#ff4444';
    }
    
    function clearError(input) {
        const errorDiv = input.parentNode.querySelector('.error-message');
        if (errorDiv) {
            errorDiv.remove();
        }
        input.style.borderColor = '';
    }
    
    function validateForm() {
        let isValid = true;
        const nameInput = document.getElementById('full_name_c');
        const phoneInput = document.getElementById('phone_c');
        const messageInput = document.getElementById('message') || document.querySelector('textarea[name="message"]');
        
    
        if (nameInput && nameInput.value.length < 2) {
            showError(nameInput, 'Имя должно содержать минимум 2 символа');
            isValid = false;
        } else if (nameInput) {
            clearError(nameInput);
        }
        
        if (phoneInput) {
            const cleanPhone = phoneInput.value.replace(/[^\d+]/g, '');
            if (cleanPhone.length < 7) {
                showError(phoneInput, 'Телефон должен содержать минимум 7 цифр');
                isValid = false;
            } else {
                clearError(phoneInput);
            }
        }
        
        if (messageInput && messageInput.value.length < 10) {
            showError(messageInput, 'Сообщение должно содержать минимум 10 символов');
            isValid = false;
        } else if (messageInput) {
            clearError(messageInput);
        }
        
        return isValid;
    }
  
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!validateForm()) {
            return;
        }
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        const originalHTML = submitBtn.innerHTML;
        
        submitBtn.innerHTML = '<span class="spinner"></span> Отправка...';
        submitBtn.disabled = true;
        
        const formData = new FormData(form);
        
        const actionUrl = form.getAttribute('action') || '/php/process_cooperation.php';
        
        fetch(actionUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Ответ сервера:', data);
            
            if (data.success) {

                showSuccessMessage(data.message || 'Заявка отправлена успешно!');
            
                form.reset();
                
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 2000);
                }
            } else {
                showErrorMessage(data.message || 'Произошла ошибка при отправке');
                if (data.errors) {
                    highlightErrors(data.errors);
                }
            }
        })
        .catch(error => {
            console.error('Ошибка сети:', error);
            showErrorMessage('Ошибка сети. Пожалуйста, попробуйте еще раз.');
        })
        .finally(() => {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    });
    

    function showSuccessMessage(message) {

        const notification = document.createElement('div');
        notification.className = 'success-notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #4CAF50;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            animation: slideIn 0.3s ease;
            max-width: 400px;
        `;
        
        notification.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 20px;">✅</span>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
 
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
   
        if (!document.querySelector('#notification-styles')) {
            const style = document.createElement('style');
            style.id = 'notification-styles';
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOut {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(100%); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }
    }

    function showErrorMessage(message) {
        const notification = document.createElement('div');
        notification.className = 'error-notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #f44336;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            animation: slideIn 0.3s ease;
            max-width: 400px;
        `;
        
        notification.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 20px;">❌</span>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }

    function highlightErrors(errors) {
        if (typeof errors === 'object') {
            Object.keys(errors).forEach(fieldName => {
                const input = form.querySelector(`[name="${fieldName}"]`);
                if (input) {
                    showError(input, errors[fieldName]);
                }
            });
        }
    }

    const nameInput = document.getElementById('full_name_c');
    const phoneInput = document.getElementById('phone_c');
    const messageInput = document.getElementById('message') || document.querySelector('textarea[name="message"]');
    
    if (nameInput) {
        nameInput.addEventListener('blur', function() {
            if (this.value.length < 2) {
                showError(this, 'Имя должно содержать минимум 2 символа');
            } else {
                clearError(this);
            }
        });
    }
    
    if (phoneInput) {
        phoneInput.addEventListener('blur', function() {
            const cleanPhone = this.value.replace(/[^\d+]/g, '');
            if (cleanPhone.length < 7) {
                showError(this, 'Телефон должен содержать минимум 7 цифр');
            } else {
                clearError(this);
            }
        });
    }
    
    if (messageInput) {
        messageInput.addEventListener('blur', function() {
            if (this.value.length < 10) {
                showError(this, 'Сообщение должно содержать минимум 10 символов');
            } else {
                clearError(this);
            }
        });
    }

    if (!document.querySelector('#spinner-styles')) {
        const spinnerStyle = document.createElement('style');
        spinnerStyle.id = 'spinner-styles';
        spinnerStyle.textContent = `
            .spinner {
                display: inline-block;
                width: 16px;
                height: 16px;
                border: 2px solid rgba(255,255,255,.3);
                border-radius: 50%;
                border-top-color: #fff;
                animation: spin 1s ease-in-out infinite;
                margin-right: 8px;
                vertical-align: middle;
            }
            
            @keyframes spin {
                to { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(spinnerStyle);
    }
});