# EMAB Electrical

Website for **EMAB Electrical**, an electrical services company based in **Bungoma town** and serving **Mumias, Kakamega, Busia** and surrounding areas.

> Delivering results, Exceeding Expectations

The site is services-only. There is no shop and no online payment. The one thing visitors do is **request a quote**, and the team follows up by phone, WhatsApp or email.

---

## Features

**Public site**
- Home page with a hero carousel, quick quote form, services, areas served, how-it-works steps, projects and testimonials
- Services page (one block per category) and a detail page for each service
- Projects page with category filter and search (shown once there are enough projects) and a gallery page for each project
- About and Contact pages
- Request a Quote page, also available from the home page and from each service page
- Floating WhatsApp button and a scroll-to-top button

**Quote requests**
- Saved to the database with a status, starting at `new`
- Honeypot field to block bots
- Optional photo or plan upload, stored on the **private disk** (never public)
- Email notifications to the team

**Admin**
- Login with roles: super admin and admin
- Manage services, categories, projects, testimonials, quote requests and site settings

**Content stays editable**
- Phone, WhatsApp, company name and other details come from the **settings** table
- Service areas come from `config/emab.php`, so locations are changed in one place

---

## Tech stack

- Laravel (PHP)
- Blade templates
- Tailwind CSS with custom `brand-*` colours taken from the logo
- Vite
- Bootstrap Icons (`bi bi-*`)
- Database configured in `.env`
- Hosting target: shared cPanel hosting

---

## Getting started (local)

Requirements: PHP, Composer, Node.js and npm, and a database.

```bash
git clone https://github.com/VivianNova/emab.git
cd emab

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Open `.env` and set the database, mail and app URL values. Then:

```bash
php artisan migrate
php artisan storage:link
```

Load the starter descriptions (optional, run only what you need):

```bash
php artisan db:seed --class=ServiceCategoryDescriptionSeeder
php artisan db:seed --class=ServiceDescriptionSeeder
php artisan db:seed --class=ProjectDescriptionSeeder
```

Start the site:

```bash
npm run dev
php artisan serve
```

Then open `http://127.0.0.1:8000`.

---

## Images are not in the repository

Images are kept out of git (see `.gitignore`). After cloning, add them yourself:

- `public/images/logo.png` is the logo, used in the header, footer and quotation PDF
- `public/images/` holds the hero and section photos referenced in `resources/views/home.blade.php`
- Photos uploaded through the admin are stored in `storage/app/public`. Run `php artisan storage:link` so they are served

If a design image file is missing, the home page shows a plain navy block instead of a broken image.

---

## Configuration you will edit most

| What | Where |
|---|---|
| Base town, service areas, area text | `config/emab.php` (run `php artisan config:clear` after editing) |
| Company name, phone, WhatsApp, email | Settings in the admin (`company_name`, `phone`, `whatsapp`, `email`) |
| Home page numbers strip | Settings: `stat_projects`, `stat_years`, `stat_clients`, `stat_areas` (any empty value is hidden) |
| Hero slides and their photos | The `$slides` array at the top of `resources/views/home.blade.php` |
| Brand colours | The `brand-*` tokens in the Tailwind config |

WhatsApp numbers must be stored with the country code, no plus sign and no leading zero (for example `2547XXXXXXXX`).

---

## Project layout (key files)

```
app/Http/Controllers/
    HomeController.php        Home page data
    ServiceController.php     Services page and service detail
    ProjectController.php     Projects page and project detail
    QuoteController.php       Quote form and saving requests
app/Models/                   Service, ServiceCategory, Project, QuoteRequest, Testimonial, ...
config/emab.php               Base town and service areas
database/seeders/             Description seeders
resources/views/
    home.blade.php, services.blade.php, service.blade.php,
    projects.blade.php, project.blade.php, quote.blade.php
    partials/                 cta, whatsapp, project-card, quote-success, ...
```

---

## Deploying to cPanel

1. Build the assets (`npm run build`) and upload the project, with `public/` as the web root (or its contents in `public_html` with `index.php` paths adjusted).
2. Upload `public/images` and any files from `storage/app/public` separately, since git does not carry them.
3. Create `.env` on the server with production values (`APP_ENV=production`, `APP_DEBUG=false`, database, mail).
4. Run, from the project folder:

```bash
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

5. If queued jobs or scheduled tasks are used, add a cron job that runs every minute:

```
* * * * * cd /home/USER/path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

After a deploy that changes code, run `php artisan queue:restart` if queues are in use.

Never commit `.env`. It holds the application key, database password and mail credentials.

---

## Roadmap

**Done**
- Public pages, quote requests and admin
- Email notifications for quote requests
- Brand styling, locations (Bungoma, Mumias, Kakamega, Busia) and description seeders

**In progress / next**
- **Quotations:** build a quotation in the admin or upload the CEO's own PDF, send it to the client with a secure expiring link, and track Accepted, Declined and Expired. Database tables, models and the branded PDF template are written. The admin form, send and status steps remain.
- **Background tasks and caching:** reminders and follow-ups, cached home page data, deploy script
- **AI chatbot:** 24/7 answers about services and areas, with lead capture into quote requests and handover to a person for hazards

---

## Security notes

- `.env`, uploaded quote files and quotation documents must never be public.
- Quote form uploads are validated and stored on the private disk, and downloaded only through logged-in admin routes.
- Privacy consent is collected on the quote form. Keep the Privacy Policy page up to date with anything new that stores personal data.

---

## License

Proprietary. All rights reserved by EMAB Electrical. This code is not open source.
