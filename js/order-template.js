class DessertPage {
    constructor() {
        this.config = null;
        this.userUploadedImage = null; 
        this.init();
    }

    getDessertType() {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get('type') || 'classic';
    }

    loadConfig() {
        const dessertType = this.getDessertType();
        this.config = dessertConfig[dessertType];
        if (!this.config) {
            console.error('Конфигурация для десерта не найдена:', dessertType);
            this.config = dessertConfig['classic']; 
        }
    }

    setPageTitle() {
        document.title = `Винни Пух: ${this.config.title}`;
        document.getElementById('dessertTitle').textContent = this.config.title;
    }

    loadImages() {
        const mainImage = document.getElementById('mainImage');
        const otherImagesContainer = document.getElementById('otherImages');

        if (this.config.images && this.config.images.length > 0) {
            mainImage.src = this.config.images[0];
        }

        if (this.config.images && this.config.images.length > 1) {
            otherImagesContainer.innerHTML = '';
            this.config.images.forEach((src, index) => {
                const img = document.createElement('img');
                img.className = 'other_img';
                img.src = src;
                img.alt = `Вариант ${index + 1}`;
                img.addEventListener('click', () => this.changeMainImage(src, img));
                otherImagesContainer.appendChild(img);
            });
        }
    }
createFileUpload() {
        const group = document.createElement('div');
        group.className = 'parametr_group';

        const label = document.createElement('p');
        label.className = 'parametr_label';
        label.textContent = 'Ваш дизайн';
        group.appendChild(label);

        const previewContainer = document.createElement('div');
        previewContainer.className = 'user-design-preview';
        previewContainer.style.display = 'none';
        previewContainer.style.margin = '10px 0';
        previewContainer.style.textAlign = 'center';

        const previewImage = document.createElement('img');
        previewImage.className = 'user-design-image';
        previewImage.style.maxWidth = '200px';
        previewImage.style.maxHeight = '200px';
        previewImage.style.border = '2px dashed #521D04';
        previewImage.style.borderRadius = '10px';
        previewContainer.appendChild(previewImage);

        const removeButton = document.createElement('button');
        removeButton.type = 'button';
        removeButton.textContent = '× Удалить';
        removeButton.className = 'remove-design-btn';
        removeButton.style.marginTop = '5px';
        removeButton.style.padding = '5px 10px';
        removeButton.style.background = '#ff4444';
        removeButton.style.color = 'white';
        removeButton.style.border = 'none';
        removeButton.style.borderRadius = '5px';
        removeButton.style.cursor = 'pointer';
        removeButton.addEventListener('click', () => this.removeUserDesign(previewContainer, fileInput));
        previewContainer.appendChild(removeButton);

        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.id = 'userDesignInput';
        fileInput.className = 'file-input';
        fileInput.accept = 'image/*,.pdf,.psd,.ai';
        fileInput.style.display = 'none';

        const fileLabel = document.createElement('label');
        fileLabel.htmlFor = 'userDesignInput';
        fileLabel.className = 'file-label';
        fileLabel.textContent = 'Загрузить ваш дизайн';
        fileLabel.style.display = 'block';
        fileLabel.style.padding = '15px';
        fileLabel.style.border = '2px dashed #521D04';
        fileLabel.style.borderRadius = '10px';
        fileLabel.style.textAlign = 'center';
        fileLabel.style.cursor = 'pointer';
        fileLabel.style.background = '#F8EB9A';
        fileLabel.style.transition = 'all 0.3s ease';

        fileLabel.addEventListener('mouseenter', () => {
            fileLabel.style.background = '#F5E27A';
        });
        fileLabel.addEventListener('mouseleave', () => {
            fileLabel.style.background = '#F8EB9A';
        });


        fileInput.addEventListener('change', (e) => this.handleUserDesignUpload(e, previewContainer, previewImage, fileLabel));

        const helpText = document.createElement('div');
        helpText.className = 'file-help-text';
        helpText.innerHTML = '<p style="font-size: 0.9rem; color: #666; margin-top: 5px;">Можно загрузить: JPG, PNG, PDF, PSD, AI</p>';

        group.appendChild(previewContainer);
        group.appendChild(fileInput);
        group.appendChild(fileLabel);
        group.appendChild(helpText);

        return group;
    }
    handleUserDesignUpload(event, previewContainer, previewImage, fileLabel) {
        const file = event.target.files[0];
        if (file) {
            if (file.size > 10 * 1024 * 1024) {
                alert('Файл слишком большой. Максимальный размер: 10MB');
                event.target.value = '';
                return;
            }

            const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            if (!allowedTypes.includes(file.type)) {
                alert('Неподдерживаемый формат файла. Разрешены: JPG, PNG, GIF, PDF');
                event.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
               
                this.userUploadedImage = {
                    file: file,
                    dataUrl: e.target.result
                };

                if (file.type.startsWith('image/')) {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block';
                } else {
                   
                    previewImage.src = '/images/file-icon.png'; 
                    previewImage.alt = file.name;
                    previewContainer.style.display = 'block';
                }

               
                fileLabel.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    }


    removeUserDesign(previewContainer, fileInput) {
        this.userUploadedImage = null;
        fileInput.value = '';
        previewContainer.style.display = 'none';
        
        const fileLabel = document.querySelector('label[for="userDesignInput"]');
        if (fileLabel) {
            fileLabel.style.display = 'block';
        }
    }
    createDropdownParam(param) {
        const group = document.createElement('div');
        group.className = 'parametr_group';

        const label = document.createElement('p');
        label.className = 'parametr_label';
        label.textContent = param.label;
        group.appendChild(label);

        const dropdownContainer = document.createElement('div');
        dropdownContainer.className = 'drop_btm_cake';
        dropdownContainer.id = `${param.id}Dropdown`;

        const dropBtn = document.createElement('button');
        dropBtn.className = 'drop_btn';
        dropBtn.textContent = param.default;
        dropdownContainer.appendChild(dropBtn);

        const dropdownContent = document.createElement('div');
        dropdownContent.className = 'dropdown-content';

        param.options.forEach(option => {
            const optionBtn = document.createElement('button');
            optionBtn.className = 'dropdown-option';
            optionBtn.textContent = option;
            optionBtn.addEventListener('click', () => this.selectParam(param.id, option, dropBtn));
            dropdownContent.appendChild(optionBtn);
        });

        dropdownContainer.appendChild(dropdownContent);
        group.appendChild(dropdownContainer);

        return group;
    }
    createMultiSelectParam(param) {
        const group = document.createElement('div');
        group.className = 'parametr_group';

        const label = document.createElement('p');
        label.className = 'parametr_label';
        label.textContent = param.label;
        group.appendChild(label);

        const multiSelectContainer = document.createElement('div');
        multiSelectContainer.className = 'multi-select';
        multiSelectContainer.id = `${param.id}MultiSelect`;

        const validationMessage = document.createElement('div');
        validationMessage.className = 'validation-message';
        validationMessage.style.display = 'none';
        validationMessage.style.color = '#521D04';
        validationMessage.style.fontSize = '0.9rem';
        validationMessage.style.marginTop = '5px';

        param.options.forEach(option => {
            const labelEl = document.createElement('label');
            labelEl.className = 'multi-select-option';

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.value = option;
            checkbox.checked = param.default.includes(option);
           
            checkbox.addEventListener('change', () => this.validateMultiSelect(param, multiSelectContainer, validationMessage));

            labelEl.appendChild(checkbox);
            labelEl.appendChild(document.createTextNode(option));
            multiSelectContainer.appendChild(labelEl);
        });

        group.appendChild(multiSelectContainer);
        group.appendChild(validationMessage);

        return group;
    }


    validateMultiSelect(param, container, validationMessage) {
        const checkboxes = container.querySelectorAll('input[type="checkbox"]:checked');
        const selectedCount = checkboxes.length;
   
        let maxSelections = param.maxSelections;
        
        if (param.linkedDropdown) {
            const linkedDropdown = document.querySelector(`#${param.linkedDropdown}Dropdown .drop_btn`);
            if (linkedDropdown) {
                const tierText = linkedDropdown.textContent;
                const tierCount = parseInt(tierText);
                maxSelections = tierCount;
            }
        }
        if (selectedCount > maxSelections) {
            checkboxes[checkboxes.length - 1].checked = false;
            this.showValidationMessage(validationMessage, `Можно выбрать только ${maxSelections} вкуса для ${maxSelections} ярусов`, true);
        } else {
            this.showValidationMessage(validationMessage, `Выбрано ${selectedCount} из ${maxSelections} вкусов`, false);
        }
    }

    showValidationMessage(element, message, isError) {
        element.textContent = message;
        element.style.color = isError ? '#ff4444' : '#521D04';
        element.style.display = 'block';
        
        if (!isError) {
            setTimeout(() => {
                element.style.display = 'none';
            }, 3000);
        }
    }

    createDropdownParam(param) {
        const group = document.createElement('div');
        group.className = 'parametr_group';

        const label = document.createElement('p');
        label.className = 'parametr_label';
        label.textContent = param.label;
        group.appendChild(label);

        const dropdownContainer = document.createElement('div');
        dropdownContainer.className = 'drop_btm_cake';
        dropdownContainer.id = `${param.id}Dropdown`;

        const dropBtn = document.createElement('button');
        dropBtn.className = 'drop_btn';
        dropBtn.textContent = param.default;
        dropdownContainer.appendChild(dropBtn);

        const dropdownContent = document.createElement('div');
        dropdownContent.className = 'dropdown-content';

        param.options.forEach(option => {
            const optionBtn = document.createElement('button');
            optionBtn.className = 'dropdown-option';
            optionBtn.textContent = option;
            optionBtn.addEventListener('click', () => {
                this.selectParam(param.id, option, dropBtn);
              
                if (param.linkedMultiSelect) {
                    this.updateLinkedMultiSelect(param.linkedMultiSelect, option);
                }
            });
            dropdownContent.appendChild(optionBtn);
        });

        dropdownContainer.appendChild(dropdownContent);
        group.appendChild(dropdownContainer);

        return group;
    }

    updateLinkedMultiSelect(multiSelectId, dropdownValue) {
        const tierCount = parseInt(dropdownValue); 
        const multiSelectContainer = document.getElementById(`${multiSelectId}MultiSelect`);
        const validationMessage = multiSelectContainer?.nextElementSibling;
        
        if (multiSelectContainer && validationMessage) {
            const checkboxes = multiSelectContainer.querySelectorAll('input[type="checkbox"]:checked');
            if (checkboxes.length > tierCount) {
                for (let i = tierCount; i < checkboxes.length; i++) {
                    checkboxes[i].checked = false;
                }
            }
            

            const currentSelected = multiSelectContainer.querySelectorAll('input[type="checkbox"]:checked').length;
            this.showValidationMessage(validationMessage, `Выбрано ${currentSelected} из ${tierCount} вкусов`, false);
        }
    }

    createFileUpload() {
        const group = document.createElement('div');
        group.className = 'parametr_group';

        const label = document.createElement('p');
        label.className = 'parametr_label';
        label.textContent = 'Дизайн';
        group.appendChild(label);

        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.id = 'fileInput';
        fileInput.className = 'file-input';
        fileInput.accept = 'image/*';
        fileInput.addEventListener('change', (e) => this.handleImageUpload(e));

        const fileLabel = document.createElement('label');
        fileLabel.htmlFor = 'fileInput';
        fileLabel.className = 'file-label';
        fileLabel.textContent = 'Загрузить изображение';

        group.appendChild(fileInput);
        group.appendChild(fileLabel);

        return group;
    }

    createPriceBlock() {
        const priceDiv = document.createElement('div');
        priceDiv.className = 'total_cost';
        priceDiv.textContent = this.config.price;

        const textBox = document.createElement('div');
        textBox.className = 'order_text_boxs';
        textBox.innerHTML = '<p>*это примерная цена, конечную мы сообщим после того, как оценим сложность предложенного вами дизайна. Мы не берем доплату за дизайн состоящего только из крема (как на картинках)</p>';

        const container = document.createElement('div');
        container.appendChild(priceDiv);
        container.appendChild(textBox);

        return container;
    }

    loadParameters() {
        const container = document.getElementById('parametersContainer');
        container.innerHTML = '';

        this.config.parameters.forEach(param => {
            let paramElement;
            if (param.type === 'dropdown') {
                paramElement = this.createDropdownParam(param);
            } else if (param.type === 'multi-select') {
                paramElement = this.createMultiSelectParam(param);
            }
            
            if (paramElement) {
                container.appendChild(paramElement);
            }
        });

        container.appendChild(this.createFileUpload());
        container.appendChild(this.createPriceBlock());
    }

    setupAddToCartButton() {
        const button = document.getElementById('addToCartBtn');
        button.addEventListener('click', () => this.addToCart());
    }

    changeMainImage(newSrc, clickedImage) {
        const mainImage = document.getElementById('mainImage');
        const tempSrc = mainImage.src;
        mainImage.src = newSrc;
        clickedImage.src = tempSrc;
    }

    selectParam(paramId, value, buttonElement) {
        buttonElement.textContent = value;
    }

    handleImageUpload(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('mainImage').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }


addToCart() {
    if (this.getDessertType() === 'multitier') {
        const tiersParam = this.config.parameters.find(p => p.id === 'tiers');
        const tastesParam = this.config.parameters.find(p => p.id === 'tastes');
        
        if (tiersParam && tastesParam) {
            const tierButton = document.querySelector(`#${tiersParam.id}Dropdown .drop_btn`);
            const selectedCheckboxes = document.querySelectorAll(`#${tastesParam.id}MultiSelect input[type="checkbox"]:checked`);
            
            const tierCount = parseInt(tierButton.textContent);
            const tasteCount = selectedCheckboxes.length;
            
            if (tasteCount !== tierCount) {
                alert(`Пожалуйста, выберите ${tierCount} вкуса для ${tierCount} ярусов`);
                return;
            }
        }
    }

    const selectedParams = {};
    
    this.config.parameters.forEach(param => {
        if (param.type === 'dropdown') {
            const button = document.querySelector(`#${param.id}Dropdown .drop_btn`);
            if (button) {
                selectedParams[param.label] = button.textContent;
            }
        } else if (param.type === 'multi-select') {
            const checkboxes = document.querySelectorAll(`#${param.id}MultiSelect input[type="checkbox"]:checked`);
            if (checkboxes.length > 0) {
                const values = Array.from(checkboxes).map(cb => cb.value);
                selectedParams[param.label] = values.join(', ');
            }
        }
    });

    if (this.userUploadedImage) {
        selectedParams['Ваш дизайн'] = 'Загружен пользователем';
    }

    const orderData = {
        type: this.getDessertType(),
        title: this.config.title,
        image: document.getElementById('mainImage').src,
        basePrice: this.extractPrice(this.config.price),
        selectedParams: selectedParams,
        quantity: 1,
        configReference: {
            dessertType: this.getDessertType(),
            parameters: this.config.parameters.map(p => ({
                id: p.id,
                label: p.label,
                type: p.type
            }))
        }
    };

    this.saveOrderToStorage(orderData);
    
    alert('Торт добавлен в корзину!');
    
    setTimeout(() => {
        window.location.href = 'orderPage3.php';
    }, 500);
}

extractPrice(priceString) {
    const match = priceString.match(/(\d+(\.\d+)?)/);
    return match ? parseFloat(match[1]) : 0;
}

saveOrderToStorage(orderData) {
    try {
        let currentOrders = JSON.parse(localStorage.getItem('currentOrders')) || [];
        orderData.id = Date.now();
        orderData.addedAt = new Date().toISOString();
        
        orderData.itemDetails = {
            type: orderData.type,
            title: orderData.title,
            basePrice: orderData.basePrice,
            parameters: orderData.selectedParams,
            image: orderData.image
        };
        
        currentOrders.push(orderData);
        
        localStorage.setItem('currentOrders', JSON.stringify(currentOrders));
        
        console.log('Order saved with details:', orderData);
        
    } catch (error) {
        console.error('Error saving order to localStorage:', error);
    }
}

  
    init() {
        this.loadConfig();
        this.setPageTitle();
        this.loadImages();
        this.loadParameters();
        this.setupAddToCartButton();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new DessertPage();
});