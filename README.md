# Shopwave

Shopwave is a modern, full-featured e-commerce application built with **Laravel 12**, **Inertia.js 2**, and **Vue 3**. It provides a seamless shopping experience for customers and a powerful administration console for store managers.

## 🚀 Tech Stack

- **Backend:** Laravel 12.x
- **Frontend:** Vue 3.x, Inertia.js 2.x
- **Styling:** Tailwind CSS 3.x
- **Database:** MySQL / SQLite
- **State Management:** Pinia (implied via Vue ecosystem) / Inertia Shared Props
- **Asset Bundling:** Vite
- **Key Libraries:**
  - `cloudinary-laravel`: Image management
  - `tiptap`: Rich text editing
  - `vuedraggable`: Drag-and-drop interfaces
  - `lucide-vue-next` & `@heroicons/vue`: Iconography

## ✨ Features

### 🛍️ Customer Storefront
- **Modern UI/UX:** Responsive design with a clean, user-friendly interface.
- **Home Page:** Dynamic hero slider, trending products, and category highlights.
- **Product Catalog:** Browse products by category, view details, and filter options.
- **Shopping Cart:** Real-time cart management.
- **Checkout:** Streamlined checkout process with shipping address management.
- **User Account:** Order history, profile management, and saved addresses.
- **Reviews:** Product review and rating system.

### 🛠️ Admin Console
- **Dashboard:** At-a-glance statistics and charts.
- **Product Management:** Create, edit, and organize products with variants.
- **Category Management:** Hierarchical category tree with drag-and-drop sorting.
- **Order Management:** View and process customer orders.
- **Marketing:**
  - **Banner Management:** Manage homepage carousels and promotional banners.
  - **Coupons:** Create and manage discount codes with usage limits.
- **CMS:**
  - **Pages:** Manage "About Us", "Contact Us" and other static content.
  - **Navigation:** Drag-and-drop menu builder for header and footer.
- **System Settings:** Configure site name, currency, timezone, and contact info.

## 🛠️ Installation

Follow these steps to set up the project locally:

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/Dat0801/shopwave.git
    cd shopwave
    ```

2.  **Install Backend Dependencies:**
    ```bash
    composer install
    ```

3.  **Install Frontend Dependencies:**
    ```bash
    npm install
    ```

4.  **Environment Setup:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Configure your database settings in the `.env` file.*

5.  **Database Migration & Seeding:**
    ```bash
    php artisan migrate --seed
    ```
    *This will set up the database structure and populate it with sample data (products, categories, users).*

6.  **Build Assets:**
    ```bash
    npm run build
    ```

## 🏃‍♂️ Usage

Start the development server:

```bash
npm run dev
```
(Or if using the Laravel wrapper script: `composer run dev`)

Visit `http://localhost:8000` (or the URL provided by your local server) in your browser.

## 🔐 Default Credentials

The seeder creates the following default users:

| Role | Email | Password |
|------|-------|----------|
| **Admin** | `admin@shopwave.test` | `password` |
| **Customer** | `user@shopwave.test` | `password` |

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
