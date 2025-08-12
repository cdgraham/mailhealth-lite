=== MailHealth Lite – SMTP, DMARC, SPF Email Deliverability Checker ===
Contributors: yourname
Tags: smtp, email, deliverability, dmarc, spf, wordpress email not sending, wp_mail, gmail smtp, office 365 smtp
Requires at least: 6.3
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 0.9.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Fix **WordPress not sending emails** fast. Configure SMTP, send a test email, and run **DMARC/SPF** checks for better deliverability. Upgrade to Pro for **scheduled canary sends**, **alerts**, and **blacklist monitoring**.

== Description ==
**MailHealth Lite** is a lightweight SMTP setup and email deliverability checker for WordPress. If `wp_mail()` fails or your contact forms land in spam, use MailHealth to test sending, verify SMTP credentials, and validate DNS records (**SPF** and **DMARC**).

**Use cases**
- WordPress **email not sending** (contact forms, order emails, password reset).
- Migrate hosts and need to confirm SMTP/DNS is still correct.
- Validate DMARC/SPF TXT records to improve inbox placement.

**Features (Lite)**
- SMTP wizard: host, port, TLS/SSL, auth, From address.
- **Send Test** button with round‑trip time.
- DMARC / SPF TXT lookup (REST) with clean display.
- Minimal local log for troubleshooting.

**Pro adds**
- **Scheduled canary test‑sends** (every 5–60 minutes).
- **Slack/Webhook alerts** on failures.
- **Blacklist checks** for sending IPs.
- Extended logs & DNS change alerts.

== Frequently Asked Questions ==
= Does Lite run background checks? =
No. Lite is on-demand only. Pro adds scheduled canary checks and alerts.

= Will this conflict with other SMTP plugins? =
If another plugin configures PHPMailer, disable it to avoid conflicts. MailHealth Lite can replace most simple SMTP setups.

= Does this send through Gmail or Microsoft 365? =
Yes—enter the SMTP settings provided by your provider (Gmail, Google Workspace, Microsoft 365, your transactional email service).

== Screenshots ==
1. SMTP Settings & Send Test
2. DNS Check (SPF/DMARC)

== Installation ==
1. Upload the ZIP via **Plugins → Add New → Upload Plugin**.
2. Activate and go to **MailHealth Lite → SMTP Settings**.
3. Enter SMTP details and send a test email.
4. Use **DNS Check** to verify SPF/DMARC.

== Changelog ==
= 0.9.0 =
* Initial WordPress.org Lite release.
