# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

MailHealth Lite is a WordPress plugin that helps fix email delivery issues by providing SMTP configuration, email testing, and DNS validation for SPF/DMARC records. This is the lite version with manual testing capabilities - the Pro version adds scheduled monitoring and alerts.

## Architecture

The plugin follows WordPress coding standards with PSR-4 autoloading and a modular namespace structure:

- **Main Entry**: `mailhealth-lite.php` - Plugin header and custom autoloader that supports both PSR-4 (`Admin/Menu.php`) and WordPress conventions (`Admin/class-menu.php`)
- **Core Plugin**: `src/class-plugin.php` - Initializes all components
- **Admin Interface**: `src/Admin/class-menu.php` - WordPress admin pages and AJAX handlers
- **REST API**: `src/Rest/class-routes.php` - DNS checking endpoint (`/wp-json/mailhealth-lite/v1/dns-check`)
- **SMTP Handler**: `src/Core/class-smtpconfigurator.php` - PHPMailer configuration
- **Logging**: `src/Core/class-logger.php` - Local troubleshooting logs

Frontend uses vanilla JavaScript (`assets/js/admin.js`, `assets/js/dns.js`) with WordPress REST API calls.

## Development Commands

### Code Quality
```bash
# Run PHP CodeSniffer with WordPress standards
composer lint
# OR
phpcs --standard=phpcs.xml.dist
```

### Version Management
```bash
# Bump version across plugin files
php tools/bump-version.php 0.9.1 mailhealth-lite
```

### Build & Distribution
```bash
# Build WordPress.org distribution ZIP
SLUG=mailhealth-lite VERSION=0.9.0 tools/build-zip.sh
# Creates ../mailhealth-lite-0.9.0.zip

# Prepare SVN structure for WordPress.org submission
SLUG=mailhealth-lite VERSION=0.9.0 tools/make-svn-export.sh
# Creates svn-mailhealth-lite-0.9.0/ directory
```

## Code Standards

- Follows WordPress-Extra and WordPress-Docs coding standards
- PHP 7.4+ compatibility required
- WordPress 6.3+ required
- All code must pass `composer lint` before commits
- Uses WordPress hooks, settings API, and REST API patterns
- Security: Uses WordPress nonces, capability checks (`manage_options`), and sanitization

## Key Plugin Structure

- **Settings**: Stored in `mailhealth_lite_settings` option (SMTP config)
- **Permissions**: All admin functionality requires `manage_options` capability
- **AJAX Endpoints**: `mailhealth_lite_send_test`, `mailhealth_lite_dismiss_upgrade`
- **REST Endpoint**: `GET /wp-json/mailhealth-lite/v1/dns-check?domain=example.com`
- **Admin Pages**: Main settings, DNS check, and logs under "MailHealth Lite" menu

## Testing

The plugin provides manual testing through the admin interface. Always test SMTP configuration changes with the "Send Test" feature before deployment.