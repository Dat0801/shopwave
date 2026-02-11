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
- **Home & Shop:** Landing page, product listing, and product detail pages.
- **Cart & Coupons:** Add/update/remove items and apply coupon codes.
- **Checkout & Payments:** Stripe payment intent flow and webhook handling.
- **Orders:** Order history, order detail, and cancel requests.
- **Account:** Profile management, saved addresses, and payment methods.
- **Wishlist:** Save products and move all wishlist items to cart.
- **Reviews:** Authenticated product reviews.
- **Blog:** Blog listing and detail pages.
- **Comments:** Comment on supported content types.
- **Contact:** Contact form submission.
- **Notifications:** Inbox with unread filter, mark read, and delete.
- **Follow:** Follow and unfollow users.

### 🔐 Authentication
- **Email/Password:** Registration, login, logout, and email verification.
- **Password Reset:** Forgot-password and reset-password flows.
- **Google OAuth:** Sign in with Google.

### 🛠️ Admin Console
- **Dashboard:** Admin overview and notifications.
- **Products:** Create, update, and delete products.
- **Categories:** Manage categories and bulk status updates.
- **Banners:** Manage homepage banners and reorder.
- **Orders:** View and update order statuses.
- **Customers:** Customer listing.
- **Coupons:** Create and manage discount codes.
- **Reviews:** Moderation actions.
- **Contacts:** View, update status, and delete contact messages.
- **Settings:** Site settings management.
- **Blog & Categories:** Manage blog posts and blog categories.
- **Pages:** Manage static pages.
- **Navigation:** Build and reorder navigation menus.

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
