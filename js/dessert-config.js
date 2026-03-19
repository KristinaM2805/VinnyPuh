const dessertConfig = {
    'classic': {
        title: 'Классический торт',
        images: ['/image/-5.jpg', '/image/ -2.jpg', '/image/ -2.jpg'],
        parameters: [
            {
                type: 'dropdown',
                label: 'Вес',
                id: 'weight',
                options: ['2000 грамм', '3000 грамм', '4000 грамм', '5000 грамм', '6000 грамм'],
                default: '2000 грамм'
            },
            {
                type: 'dropdown',
                label: 'Вкус',
                id: 'taste',
                options: ['Манго-Маракуйя', 'Сникерс', 'Фисташка-Малина', 'Клубничный пломбир', 'Рафаэло', 'Красный бархат', 'Тирамису', 'Груша-Виннипуша', 'Дикая Смородина', 'Винни Пух', 'Шоко-Вишня', 'Молочный ломтик'],
                default: 'Манго-Маракуйя'
            },
            {
                type: 'dropdown',
                label: 'Свечи',
                id: 'candles',
                options: ['Без свечей', '1 свеча (в подарок)', '12 свечей (+10 byn)'],
                default: 'Без свечей'
            }
        ],
        price: '130 byn'
    },

    'bento': {
        title: 'Бенто торт',
        images: ['/image/bento1.jpg', '/image/bento2.jpg', '/image/bento3.jpg'],
        parameters: [
            {
                type: 'dropdown',
                label: 'Вес',
                id: 'weight',
                options: ['500 грамм', '700 грамм', '1000 грамм'],
                default: '500 грамм'
            },
            {
                type: 'dropdown',
                label: 'Вкус',
                id: 'taste',
                options: ['Винни Пух', 'Шоко Вишня', 'Сникерс', 'Клубничный пломбир'],
                default: 'Клубничный'
            },
            {
                type: 'dropdown',
                label: 'Свечи',
                id: 'candles',
                options: ['Без свечей', '1 свеча (в подарок)'],
                default: 'Без свечей'
            }
        ],
        price: '45 byn'
    },

    'cupcakes': {
        title: 'Капкейки',
        images: ['/image/cupcake1.jpg', '/image/cupcake2.jpg', '/image/cupcake3.jpg'],
        parameters: [
            {
                type: 'dropdown',
                label: 'Количество',
                id: 'quantity',
                options: ['6 штук', '12 штук', '18 штук', '24 штуки'],
                default: '6 штук'
            },
            {
                type: 'dropdown',
                label: 'Бисквид',
                id: 'taste',
                options: ['Ванильный', 'Шоколадный'],
                default: 'Ванильный'
            },
            {
                type: 'dropdown',
                label: 'Начинка',
                id: 'in_taste',
                options: ['шоколадная', 'ягодная', 'сливочно карамельная'],
                default: 'ягодная'
            },
            {
                type: 'dropdown',
                label: 'Шапочка',
                id: 'heads',
                options: ['крем-чиз', 'сметанный пломбир'],
                default: 'ягодная'
            }
        ],
        price: '35 byn'
    },

    'multitier': {
        title: 'Многоярусный торт',
        images: ['/image/tier1.jpg', '/image/tier2.jpg', '/image/tier3.jpg'],
        parameters: [
            {
                type: 'dropdown',
                label: 'Количество ярусов',
                id: 'tiers',
                options: ['2 яруса', '3 яруса', '4 яруса'],
                default: '2 яруса',
                linkedMultiSelect: 'tastes'
            },
            {
                type: 'dropdown',
                label: 'Общий вес',
                id: 'weight',
                options: ['3000 грамм', '5000 грамм', '7000 грамм', '10000 грамм'],
                default: '3000 грамм'
            },
            {
                type: 'multi-select',
                label: 'Вкусы ярусов',
                id: 'tastes',
                options: ['Манго-Маракуйя', 'Сникерс', 'Фисташка-Малина', 'Клубничный', 'Шоколадный', 'Ванильный'],
                default: ['Манго-Маракуйя', 'Сникерс'],
                maxSelections: 2,
                linkedDropdown: 'tiers' 
            }
        ],
        price: '250 byn'
    },

    'mousse': {
        title: 'Муссовый торт',
        images: ['/image/mousse1.jpg', '/image/mousse2.jpg', '/image/mousse3.jpg'],
        parameters: [
            {
                type: 'dropdown',
                label: 'Вес',
                id: 'weight',
                options: ['500 грамм', '1000 грамм', '1500 грамм'],
                default: '500 грамм'
            },
            {
                type: 'dropdown',
                label: 'Бисквит',
                id: 'taste',
                options: ['Ванильный', 'Шоколадный'],
                default: 'Ванильный'
            },
            {
                type: 'dropdown',
                label: 'Прослойка',
                id: 'in_taste',
                options: ['малина', 'манго', 'лимон'],
                default: 'малина'
            },
            {
                type: 'dropdown',
                label: 'Муссовая основа',
                id: 'mousse_base',
                options: ['ягодная', 'тропическая', 'шоколадная'],
                default: 'ягодная'
            }
        ],
        price: '120 byn'
    },

    'traifal': {
        title: 'Трайфл',
        images: ['/image/t1.jpg', '/image/t2.jpg', '/image/t3.jpg'],
        parameters: [
            {
                type: 'dropdown',
                label: 'Количество',
                id: 'quantity',
                options: ['6 штук', '12 штук', '18 штук', '24 штуки'],
                default: '6 штук'
            },
            {
                type: 'dropdown',
                label: 'Вкус',
                id: 'taste',
                options: ['Тирамиссу', 'Шоко Вишня', 'Сникерс', 'Красный бархат', 'Рафаэлло'],
                default: 'Клубничный'
            },
            {
                type: 'dropdown',
                label: 'Свечи',
                id: 'candles',
                options: ['Без свечей', '1 свеча (в подарок)'],
                default: 'Без свечей'
            }
        ],
        price: '120 byn'
    }
};