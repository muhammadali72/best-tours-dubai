# Setup — Best Tours Dubai

Place `public/` contents in the server document root (`public_html`). Place `private/` contents in a sibling `btd-private/` directory, outside that root. Preserve hidden `.htaccess` files. For another layout, set `PRIVATE_DIR` in the front controller.

Update `SITE_ORIGIN`, `ADMIN_EMAIL`, `ADMIN_EMAILS` and `BOOKING_EMAIL` in `private/core.php` for your deployment. `admin@example.com` is a placeholder. HTTPS is required for authentication cookies.

Create a private `initial-admin-accounts.json` in `btd-private/` mapping each permitted administrator email to a freshly generated `password_hash(..., PASSWORD_BCRYPT)` hash. Use your own password and keep this file outside version control. Administrator hashes from production are excluded. Initial accounts require a password change on first login. Without this file the public website can initialize, but no administrator is created.

The first request initializes SQLite in `btd-private/storage/`. Public seed pages/settings are included; live content edits, booking records and runtime uploads are not. Configure your own SMTP in `btd-private/storage/mail-config.json` following `private/mail-smtp.php` before enabling email delivery.

The deployed dashboard assets are included in `public/dashboard-assets/`; the editable React source is in `dashboard.tsx` and `dashboard-entry.tsx`. Public styles and scripts are included directly. Never expose the private directory through the web server.
