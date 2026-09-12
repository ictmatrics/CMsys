# CMsys

CMsys is a lightweight, fast, and modular Content Management System built on top of the **ICTM Framework 4.5** (4.5.1 ). Designed with a decoupled MVC architecture, CMsys delivers high performance, robust multi-variant theming with strict directory isolation, custom modular extensions, environmental configuration (`.env`), dual database engine support (MySQL and SQLite), built-in CSRF protection, and a secure post-deployment setup isolation process.

---

## Key Features

- **MVC Architecture:** Powered by the lightweight, high-performance ICTM Framework.
- **Modern PHP Ready (8.2+ / 8.3+):** Strict typing (`declare(strict_types=1);`), typed properties, match expressions, and PSR-12 standard compliance.
- **Dual Database Engine Support:** Native support for both **MySQL** and **SQLite** engines using secure, parameterized query builders.
- **Decoupled Extension Management:**
  - **Custom Vendor Themes:** Standardized path under `/app/vendors/themes/{theme_name}` (`APP/Vendors/themes/`).
  - **Custom Vendor Modules:** Standardized path under `/app/vendors/modules/{module_name}` (`APP/Vendors/modules/`).
  - **Core System Templates:** Strictly restricted to `/app/views/themes` (`APP/Views/Themes/`) to safeguard system integrity against overwrite or deletion.
- **Secure Web Installer & Deployment Isolation:**
  - Setup assets relocated to `/app/views/install` (`install.php` and clean DDL `schema.sql`).
  - Strict system lockdown: Frontend and Admin views remain locked until setup resources are purged.
  - Interactive post-deployment action control (`/install/purge`) permanently deletes setup assets and unlocks the application.
- **Adaptive Theming Engine:** Hierarchical template resolution prioritizing vendor themes with seamless core system fallback.
- **Environment Configuration:** Secret and environment variable management using `APP/.env` and the `env()` helper.
- **CSRF Protection Suite:** Built-in token generation, verification, and hidden form input/meta helpers.
- **Admin Dashboard:** Comprehensive administration suite for posts, pages, custom post types, menus, media, widgets, themes, modules, and system settings.
- **Defense-in-Depth Security:** Automated XSS sanitization (`validate_data()`), SQL injection prevention, ZIP slip / path traversal defenses, reverse-proxy IP detection, and role-based authentication (`ICTM_Auth`).

---

## System Requirements

- **PHP:** >= 8.2 (PHP 8.3+ recommended) with `PDO`, `pdo_mysql`, and `pdo_sqlite` extensions.
- **Composer** (optional for third-party libraries).
- **Web Server:** Apache or Nginx with URL Rewriting enabled.
- **Database:** MySQL 5.7+ / MariaDB 10.3+ or SQLite 3.

---

## How to Install & Deploy

1. **Clone or Extract the Repository:**
   ```bash
   git clone https://github.com/ictmatrics/CMsys.git
   cd CMsys
   ```

2. **Configure Environment Variables:**
   - Copy `APP/env.example` to `APP/.env`:
     ```bash
     cp APP/env.example APP/.env
     ```
   - Update your database credentials (`DB_CONNECTION=mysql` or `DB_CONNECTION=sqlite`), site title, and mail settings in `APP/.env`.

3. **Configure the Web Server:**
   - Point your web server's document root to the `public_html` directory.
   - For Apache, an `.htaccess` file is provided in `public_html/`. Ensure `mod_rewrite` is enabled.

4. **Execute Setup & Deploy:**
   - Navigate to `http://yourdomain.com/install` in your browser.
   - Input the initial administrator credentials. The installer runs the clean DDL schema from `APP/Views/install/schema.sql` and provisions core settings.
   - **Purge Setup Resources:** Upon successful deployment, click **Purge Setup Resources & Unlock System** (`/install/purge`). This permanently purges setup scripts from disk and unlocks frontend and admin access.

---

## Architectural Directory Structure

```text
├── APP/
│   ├── Config/          # Routing (Route.php), autoloading, constants, database config
│   ├── Controllers/     # MVC Request controllers (App\Controllers)
│   ├── Filters/         # Middleware & authentication guards
│   ├── Helpers/         # Procedural helpers (env, csrf, url, format, flash, db, hook)
│   ├── Libraries/       # Core application libraries & managers
│   ├── Models/          # Database models (App\Models)
│   ├── System/          # ICTM Framework core engine (Read-only)
│   ├── Vendors/         # Extension management root
│   │   ├── themes/      # Custom vendor themes (/app/vendors/themes/{theme_name})
│   │   └── modules/     # Custom vendor modules (/app/vendors/modules/{module_name})
│   ├── Views/           # Application views & core templates
│   │   ├── Themes/      # Immutable core system templates (classic, admin)
│   │   ├── admin/       # Administration panel layouts & views
│   │   └── install/     # Setup assets & schema (purged post-deployment)
│   ├── .env             # Active environment configuration
│   └── env.example      # Environment configuration template
├── cmsys120/            # Setup assets archive (for repository updates & fresh setups)
├── public_html/         # Public web root
│   ├── css/             # Stylesheets & Bootstrap framework
│   ├── js/              # Core JavaScript & jQuery libraries
│   ├── index.php        # Front controller & request entry point
│   └── .htaccess        # Rewrite rules & security headers
├── composer.json        # Dependencies & PSR-4 namespace mapping
├── README.md            # Markdown documentation
└── README.html          # HTML documentation rendering
```

---

## Extension Standards & Guidelines

### Themes (`/app/vendors/themes/{theme_name}`)
- Installed custom themes reside in `APP/Vendors/themes/{theme_name}/`.
- Must contain a valid `manifest.json` specifying `name`, `version`, `author`, and `scope` (`frontend` or `backend`).
- Theme templates are automatically resolved by controllers via `$this->view('Themes/{theme_name}/{view}')`.
- Core system templates under `APP/Views/Themes/` remain immutable and cannot be overwritten or deleted.

### Modules (`/app/vendors/modules/{module_name}`)
- Custom add-on modules reside in `APP/Vendors/modules/{module_name}/`.
- Must contain a `manifest.json` describing the module.
- Bootstrapped via `init.php` during the application lifecycle via `ExtensionModel::bootActiveModules()`.

---

## Contributing

Contributions are welcome! Please follow PSR-12 coding standards, enforce strict typing (`declare(strict_types=1);`), and adhere to the framework's native helpers and syntax rules.

---

## License

This project is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
