# Site for cake shop

An online cake shop and ordering system for a pastry shop "Sweet Cakes". This project was developed as part of a university software testing course. It allows customers to customize cakes (classic, bento, cupcakes, multi-tiered, mousse), add them to a cart, and place an order.

The Russian version is available here.

## Description

Many pastry shops lack a convenient online ordering system with customization options, leading to phone tag and order errors. This website solves the problem by providing a clear catalog, flexible cake customization (weight, flavor, candles, multi-tier options), a persistent shopping cart, and an order management panel for administrators. Managers receive structured order data, reducing errors and speeding up fulfillment.

## Technologies Used

- **Frontend:** HTML5, CSS3, JavaScript (vanilla)
- **Backend:** PHP (native)
- **Databases:** MySQL (orders, cooperation requests)
- **Storage:** `localStorage` for cart persistence, session-based authentication
- **Infrastructure:** Local server (MAMP/XAMPP) for development

## Features

- **Catalog & Customization:** Five cake categories with dynamic parameter selection (weight, flavor, candles).
- **Multi-tier Cake Logic:** Dynamic form validation for selecting a unique flavor for each tier.
- **Shopping Cart:** Add multiple items, change quantity, remove items, persistent storage (`localStorage`).
- **Order Form:** Customer info, delivery/pickup choice, address field toggle, basic validation (to be improved).
- **Admin Panel:**
    - View all orders with customer and delivery details.
    - Change order status (new, confirmed, in progress, completed, cancelled).
    - Search orders by ID/name/phone/email yet).
    - View order details as JSON.
    - Export cooperation requests to CSV (planned).
- **Design Reference Upload:** Customers can upload a design image.
- **Responsive Design:** Partially implemented (mobile view has known issues).

## Screenshots

### Main catalog page
![Main catalog page](images/main_page.png)

### Order customization page
![Order customization page](images/choose_dessert_page.png)

### Admin panel – orders list
![Admin panel](images/admin_panel.png)

### Requirements

- Windows / macOS / Linux with a local server environment (MAMP, XAMPP, or Docker).
- PHP 7.4+ and MySQL 5.7+.
- Git.
