# Laravel Church App

A Laravel-based web application for church community management, featuring FAQs, programs, news, favorite Bible verses, friend requests, and contact messaging functionality.

## Project Overview

This application provides a comprehensive platform for church community members to:

-   Browse and manage FAQs and their categories
-   View church programs and events
-   Access church news updates
-   Save and manage favorite Bible verses
-   Send and receive friend requests
-   Submit contact messages to the church

## Setup Instructions

Follow these steps to get the project up and running:

### Prerequisites

-   PHP 8.2 or higher
-   Composer
-   Node.js and npm
-   A database system (SQLite, MySQL, PostgreSQL, etc.)

### Installation Steps

1. **Clone the repository**

    ```bash
    git clone https://github.com/yourusername/laravel-church-app.git
    cd laravel-church-app
    ```

2. **Install PHP dependencies**

    ```bash
    composer install
    ```

3. **Install JavaScript dependencies**

    ```bash
    npm install
    ```

4. **Create environment configuration**

    ```bash
    cp .env.example .env
    ```

5. **Generate application key**

    ```bash
    php artisan key:generate
    ```

6. **Configure database**

    - The project is pre-configured to use SQLite (as used during development)
    - For SQLite: Ensure `database/database.sqlite` exists or create it with `touch database/database.sqlite`
    - For other databases: Update the `DB_CONNECTION` and related settings in your `.env` file
    - Configure your mail settings for the contact form functionality

7. **Run database migrations**

    ```bash
    php artisan migrate
    ```

8. **Seed the database (optional)**

    ```bash
    php artisan db:seed
    ```

9. **Start the development server**

-   Using Herd (recommended on Windows):

    ```bash
    herd start
    ```

    If `herd` is not available on your `PATH`, you can run Artisan with Herd's bundled PHP as a fallback:

    ```bash
    "C:\\Users\\YOUR_USER\\AppData\\Local\\Programs\\Herd\\php\\php.exe" artisan serve
    ```

    The application will be available at `http://localhost:8000` (or the address reported by Herd).

10. **Build front-end assets**

    ```bash
    npm run dev
    ```

    For production:

    ```bash
    npm run build
    ```

## Technology Stack

-   **Backend**: Laravel 12
-   **Frontend**: Laravel Blade views with Vite
-   **Database**: SQLite (development), configurable for production
-   **Package Manager**: Composer (PHP), npm (JavaScript)

## Key Features

-   **User Management**: Authentication and authorization
-   **FAQ System**: Organized FAQs with categories
-   **Church Programs**: Schedule and manage church events
-   **News Management**: Post and display church news
-   **Resource Management**: Manage church resources and materials
-   **Bible Integration**: Save favorite Bible verses with integration
-   **Social Features**: Friend requests and connection management
-   **Contact System**: Form-based contact messaging

## Project Structure

-   `app/` - Application code (Models, Controllers, Services)
-   `config/` - Configuration files
-   `database/` - Migrations, factories, and seeders
-   `resources/` - Views and front-end assets
-   `routes/` - Application routes
-   `tests/` - Test files
-   `storage/` - File storage and logs
-   `public/` - Publicly accessible assets

## Sources

-   **Canvas Slides**: Course materials and theory from Canvas
-   **Laravel Documentation**: [laravel.com/docs](https://laravel.com/docs)

## AI Usage

This project was developed with assistance from AI tools as follows:

-   **GitHub Copilot**: Used for development acceleration after initial code structure was established. Copilot was employed throughout the project to speed up development workflow. All generated code has been carefully reviewed, analyzed, verified for correctness, and understood before acceptance. Usage was distributed across the entire project as needed for various components and features.

-   **Antigravity (Gemini 3 Pro)**: Used at the end of the project for styling and CSS to ensure a polished user interface.
