# Workwize Challenge [Backend]

Here is the backend solution to the tech assessment from Workwize.

## Requirements

Here are the main requirements for this project:
- Models and Migrations:
    - User (role: supplier or user)
    - Product (name, description, image, stock, price, supplier_id)
    - Order (user_id, total_price, status)
    - OrderItem (order_id, product_id, quantity)
- Authentication:
    - Role-based: Supplier, User
- Routes
    - Supplier: Register/Login, Admin panel with: CRUD Products, View Orders
    - User: Register/Login, View Products, Cart Management, Simple Checkout
- Policies
    - Role-based access control

## Setup & Instalation

Backend is based on Laravel with Breeze for a quick Auth setup. https://github.com/Nilanth/laravel-breeze-react?tab=readme-ov-file
Here is how to install:
- Clone the repository
- Run `composer install`
- Copy .env.example to .env and setup required configuration (notably FRONTEND_URL)
- For ease of use you can connect to an already set up online database:
  - Add credentials to .env for the online database (will be provided by mail)
- For local database:
  - Setup and connect your database in the .env (mysql or postgre)
  - Run `php artisan migrate`
  - Run `php artisan db:seed` to generate basic products and user if needed
- Run `php artisan serve` to run the server

### Deploy

Currently this repo is hosted on DigitalOcean and has an automatic CI whenever pushed to the repo.

## Walkthrough to development

Here you will see what steps I went through to develop this project.

### Research and setup 

1. Research & trials of boilerplates & Starter kits
2. Once a good candidate has been chosen and tested in local I went through online setup.
3. First I created a online database for ease of use and future online setup
4. For the backend I first try hosting on Vercel but had some issues ended up hosting on DigitalOcean as PHP instances generaly require a whole LAMP stack.
5. For the frontend Vercel offer a pretty easy and free hosting for react so I went for that.
6. Once everything was working while being hosted I went back to local for development

### Structure and Planning

1. First I laid down which requirements are needed for backend and frontend by doin that I had a clear view on what models, routes, controller and pages I would need
2. Created all migrations needed to create and modify tables
3. Created related seeders

### Development











TODO: 

# Backend Todo List (Laravel)

1. **Models and Migrations**
    - Create models for `User`, `Supplier`, `Product`, `Order`, and `OrderItem`.
    - Create migrations for the above models with necessary fields and relationships.

2. **Authentication**
    - Ensure existing auth setup distinguishes between suppliers and normal users.
    - Update `User` model to include a `role` field (supplier or user).

3. **Routes and Controllers**
    - Supplier Routes
        - CRUD operations for products.
        - View orders for their products.
    - User Routes
        - View all products.
        - Add products to cart.
        - Checkout and create orders.
        - View order history.

4. **Controllers**
    - `SupplierController` for managing products.
    - `UserController` for managing user-specific actions (view products, add to cart, etc.).
    - `OrderController` for handling checkout and order history.

5. **Policies and Middleware**
    - Implement policies to ensure only suppliers can manage products and only users can make purchases.
    - Middleware for route protection based on roles.

6. **Services**
    - Payment processing service for handling checkouts (stub/mock if short on time).

7. **Notifications**
    - Notify suppliers when a user buys their product.

# Frontend Todo List (React + Tailwind)

1. **Project Setup**
    - Ensure Tailwind is integrated with React.

2. **Routing**
    - Setup routing with React Router for different user flows (supplier and normal user).

3. **Authentication**
    - Login and registration forms for suppliers and users.
    - Maintain session state (using context or a state management library like Redux).

4. **Supplier Interface**
    - Dashboard for suppliers to manage products.
    - Forms for creating and updating products.
    - View for listing orders received.

5. **User Interface**
    - Product listing page.
    - Product detail page.
    - Shopping cart page.
    - Checkout page.
    - Order history page.

6. **Components**
    - Reusable components such as `ProductCard`, `OrderList`, `CartItem`, etc.
    - Navigation component that adjusts based on user role.

7. **State Management**
    - Manage global state for cart, user session, etc.

8. **API Integration**
    - Services for API calls to backend
