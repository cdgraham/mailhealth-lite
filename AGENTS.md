# AGENTS.md

Guidance for coding agents, derived only from this repository's own files.

## What the project is

MailHealth Lite is a WordPress plugin (GPLv2 or later, requires PHP 7.4 and WordPress 6.3; see `README.md`, `readme.txt`, and the plugin header in `mailhealth-lite.php`) that fixes "WordPress not sending email" problems by:

- configuring SMTP (host, port, TLS/SSL, auth, From address) through the WordPress settings API,
- sending a manual test email,
- looking up SPF/DMARC TXT records over a REST endpoint,
- capturing failed sends (`wp_mail_failed`) in a local table for troubleshooting.

This is the Lite version: on-demand/manual only, no scheduled or background checks (`src/class-plugin.php`, `README.md`).

## Layout

- `mailhealth-lite.php` — plugin header plus an autoloader that tries PSR-4 first, then WordPress-style `class-*.php` names.
- `src/class-plugin.php` — initializes the Admin, Core, and Rest components.
- `src/Admin/class-menu.php` — admin pages (SMTP settings, DNS check, logs) and AJAX handlers.
- `src/Rest/class-routes.php` — REST route `GET mailhealth-lite/v1/dns-check` (SPF/DMARC TXT lookup).
- `src/Core/class-smtpconfigurator.php` — configures PHPMailer from the saved settings.
- `src/Core/class-logger.php` — stores `wp_mail_failed` entries in the `{$wpdb->prefix}mailhealth_lite_log` table.
- `assets/js/admin.js`, `assets/js/dns.js`, `assets/css/admin.css` — admin frontend (jQuery AJAX and REST calls).
- `assets-wporg/` — banners and icons for WordPress.org.
- `tools/` — maintenance scripts.
- `readme.txt`, `README.md`, `CHECKLIST.md` — documentation.

Facts shown by the code: settings are stored in the `mailhealth_lite_settings` option (`src/Admin/class-menu.php`); admin functionality is gated on the `manage_options` capability; AJAX actions use nonces (`src/Admin/class-menu.php`, `assets/js/admin.js`).

## Commands

Every command below appears in a tracked file; sources are named.

- **Lint** — `composer lint` (the `lint` script in `composer.json`, which runs `phpcs --standard=phpcs.xml.dist`). CI runs the ruleset directly: `vendor/bin/phpcs -q -n` after `composer install` (`.forgejo/workflows/ci.yml`).
- **Test** — none found. The repository has no automated test suite; "Send Test" is a runtime admin action (`src/Admin/class-menu.php`, `assets/js/admin.js`), not a test command.
- **Build** — `tools/build-zip.sh` builds the distribution ZIP (default `../mailhealth-lite-0.9.0.zip`; override with `SLUG=`/`VERSION=` per the script header). `tools/make-svn-export.sh` prepares the WordPress.org SVN structure (default `svn-mailhealth-lite-0.9.0/`; same `SLUG=`/`VERSION=` overrides per the script header).
- **Version bump** — `php tools/bump-version.php <version> <slug>` (usage line in `tools/bump-version.php`).
- **CI** — the steps in `.forgejo/workflows/ci.yml`: `php -l` on every tracked `.php` file, `node --check` on every `.js`, `python3 -m json.tool` on every `.json`, `bash -n` on every `.sh`, then `composer install --no-interaction --no-progress` and `vendor/bin/phpcs -q -n`.

## Areas

- `core` — `mailhealth-lite.php`, `src/`
- `assets` — `assets/`, `assets-wporg/`
- `tools` — `tools/`
- `docs` — `README.md`, `readme.txt`, `CHECKLIST.md`
- `ci` — `.forgejo/`, `.github/`
- `dev-config` — `composer.json`, `composer.lock`, `phpcs.xml.dist`
