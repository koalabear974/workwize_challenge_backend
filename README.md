# Workwize Challenge [Backend]

Here is the backend solution to the tech assessment from Workwize.

## Requirements

Here are the main requirements for this project:
- Models and Migrations:
    - User (role: supplier or user)
    - Product (name, description, image, stock, price, supplier_id)
    - Order (user_id, status)
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
Tech stack: Php, Laravel, Breeze

### Research and setup 

1. Research & trials of boilerplates & Starter kits
2. Once a good candidate has been chosen and tested in local I went through online setup.
3. First I created a online database for ease of use and future online setup
4. For the backend I first tried hosting on Vercel but had some issues ended up hosting on DigitalOcean as PHP instances generaly require a whole LAMP stack.
5. For the frontend Vercel offer a pretty easy and free hosting for react so I went for that.
6. Once everything was working while being hosted I went back to local for development

### Structure and Planning

1. First I laid down which requirements are needed for backend and frontend by doing that I had a clear view on what models, routes, controller and pages I would need
2. Created all migrations needed to create and modify tables

### Development

1. [BE] Added all models, related factories and seeders
2. [BE] Added Product controller, added tests for it and added an AuthServiceProvider to ensure products are modified by the roles
2. [BE] Added Order Controller and added tests for it 
3. [BE] Updated all the related routes
4. [FE] Updated all auth pages to fit requirements 
5. [FE] Added Product page for user & supplier
6. [FE] Added CRUD pages for supplier 
7. [FE] Added cart system for users and order creation
8. [BE] Added method to fetch and update order
9. [FE] Added user and supplier view for their orders 
10. [FE] Added all admin routes and update all views to fit

### Things to improve
- Styling (Loading states, branding etc.)
- Stock management
- Find a solution for multiple supplier in one order 
