# CMsys

CMsys is a lightweight, fast, and modular Content Management System built on top of the **ICTM Framework 4.5** (4.5.1). Designed with an MVC architecture, it offers a scalable structure that supports custom modules, robust multi-variant theming, environmental configuration (`.env`), dual database engine support (MySQL and SQLite), built-in CSRF protection, and a straightforward web-based installation process.

## Key Features

- **MVC Architecture:** Powered by the lightweight, high-performance ICTM Framework 4.5.
- **Modern PHP Ready (8.2+ / 8.3+):** Utilizes modern PHP features, strict typing (`declare(strict_types=1);`), and PSR-12 compliance.
- **Dual Database Support:** Native support for both **MySQL** and **SQLite** engines via parameterized query builders.
- **Environment Configuration:** Secure secret and environment variable management using `APP/.env` and the `env()` helper.
- **CSRF Protection Suite:** Built-in token generation, verification, and hidden form input/meta helpers.
- **Modular Architecture:** Extend functionality cleanly using self-contained add-on modules (`APP/Modules/`).
- **Adaptive Theming Engine:** Full control over website appearance with custom themes (`APP/Views/Themes/`).
- **Web-Based Installer:** Quick initial setup wizard with auto-table generation.
- **Admin Dashboard:** Comprehensive admin suite for posts, pages, custom post types, menus, media, widgets, and settings.
- **Built-in Security:** Automated XSS sanitization (`validate_data()`), SQL injection prevention (via prepared statements), CSRF tokens, and reverse-proxy IP detection.

## System Requirements

- PHP >= 8.2 (PHP 8.3+ recommended) with `PDO`, `pdo_mysql`, and `pdo_sqlite` extensions.
- Composer
- Web Server (Apache/Nginx) with URL Rewriting enabled.
- MySQL / MariaDB or SQLite.

## How to Install

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/ictmatrics/CMsys.git
   cd CMsys
   ```

2. **Configure Environment Variables:**
   - Copy `APP/env.example` to `APP/.env`:
     ```bash
     cp APP/env.example APP/.env
     ```
   - Update your database credentials (`DB_CONNECTION=mysql` or `DB_CONNECTION=sqlite`), site title, and mailer settings in `APP/.env`.

3. **Install Dependencies:**
   Run Composer to install or validate dependencies:
   ```bash
   composer install
   ```

4. **Configure the Web Server:**
   - Point your web server's document root to the `public_html` directory.
   - For Apache, an `.htaccess` file is provided in `public_html/`. Make sure `mod_rewrite` is enabled.

5. **Run the Web Installer or Access System:**
   - Open your browser and navigate to `http://yourdomain.com/install`.
   - The installer sets up the admin credentials and default site options.
   - Once completed, log in to the admin panel at `/login` or `/admin`!

## Directory Structure

```text
├── APP/
│   ├── Config/          # Routing, autoloading, constants, and database configuration
│   ├── Controllers/     # Request controllers (App\Controllers)
│   ├── Filters/         # Middleware & authentication guards
│   ├── Helpers/         # Utility functions (env, csrf, url, format, flash, db, hook)
│   ├── Libraries/       # Core libraries (Env, Csrf, HookManager)
│   ├── Models/          # Database models (App\Models)
│   ├── Modules/         # Add-on modular extensions
│   ├── System/          # ICTM Framework 4.5 core engine
│   ├── Views/           # Views, Admin panel, and Themes
│   ├── .env             # Active environment configuration
│   └── env.example      # Environment configuration template
├── public_html/         # Web root directory (index.php, CSS, JS, Images, Themes)
│   ├── css/             # Core stylesheets
│   ├── js/              # Core JavaScript assets
│   ├── Themes/          # Public assets for installed themes
│   └── index.php        # Front controller
├── composer.json        # Dependencies & PSR-4 mapping
└── README.md            # Project documentation
```

## Contributing

Contributions are welcome! Please follow PSR-12 coding standards and ensure you use the framework's native helpers and syntax rules before submitting a pull request.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
