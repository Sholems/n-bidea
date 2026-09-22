# NB-CCI Business Enumeration & Verification Portal

A comprehensive web application for business enumeration, verification, and compliance management built with Laravel 13.

## Features

- **Four User Roles**: Super Admin, Admin, Business Owner, Government Official
- **Business Registration**: Full CRUD with document upload and verification workflow
- **Verification Pipeline**: Submit → Review → Approve/Reject/Correction → Verify
- **Annual Renewal**: Business owners request renewals, admins approve/reject
- **Fee Tracking**: Track fees per business with payment status
- **Government Search**: Officials search and verify businesses in-person
- **Audit Logging**: Full audit trail of all critical actions
- **Role-Based Access Control**: Middleware-enforced role permissions

## Tech Stack

- Laravel 13.10.1 / PHP 8.3
- Bootstrap 5 (CDN)
- MySQL / SQLite
- Blade Templates

## Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Super Admin | admin@nb-cci.gov.ng | password |
| Admin | reviewer@nb-cci.gov.ng | password |
| Business Owner | business@example.com | password |
| Government Official | official@nb-cci.gov.ng | password |

## Local Development (Laragon)

1. Clone into `C:\laragon\www\n-bidea`
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure database
4. Run `php artisan key:generate`
5. Run `php artisan migrate --seed`
6. Run `php artisan serve`
7. Visit `http://127.0.0.1:8080`

## cPanel Deployment

1. **Upload** all project files via File Manager or SSH
2. **Composer**: Run `composer install --optimize-autoloader --no-dev` in terminal
3. **Environment**: Copy `.env.example` → `.env`, set:
   ```
   APP_URL=https://yourdomain.com
   DB_HOST=localhost
   DB_DATABASE=your_cpanel_db
   DB_USERNAME=your_db_user
   DB_PASSWORD=your_db_password
   ```
4. **Generate Key**: `php artisan key:generate`
5. **Database**: Create MySQL database in cPanel, then run:
   ```
   php artisan migrate --seed
   ```
6. **Storage Link**: `php artisan storage:link`
7. **Point Document Root** to the `public/` directory
8. **.htaccess** in `public/` handles Laravel routing automatically

### cPanel .htaccess (if needed in project root)

```apache
RewriteEngine On
RewriteRule ^(.*)$ public/$1 [L]
```

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/           # Login, Register, Password Reset
│   │   ├── Dashboard/      # Role-specific dashboards
│   │   ├── Business/       # Owner business management
│   │   ├── Admin/          # Admin review & management
│   │   ├── SuperAdmin/     # System-wide admin
│   │   ├── Government/     # Official search & verify
│   │   └── Document/       # Document upload/download
│   ├── Middleware/          # RoleMiddleware, AuditMiddleware
│   └── Requests/           # Form Request validation
├── Models/                 # Eloquent models
├── Policies/               # Authorization policies
├── Services/               # AuditService, RegistryNumberService
└── Providers/              # AppServiceProvider with gates
database/
├── migrations/             # 13 migration files
└── seeders/                # 6 seeder files
resources/views/
├── layouts/                # app, dashboard, public
├── auth/                   # Login, register, password views
├── business/               # Owner CRUD views
├── admin/                  # Admin review views
├── super-admin/            # System management views
├── government/             # Official search views
└── documents/              # Document management views
```

## License

Proprietary - NB-CCI
