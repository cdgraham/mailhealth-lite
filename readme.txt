=== MailHealth Lite – SMTP & Deliverability Monitor ===
Contributors: ChilliChalli
Tags: smtp, email, deliverability, dmarc, spf
Requires at least: 6.3
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 0.9.0
License: GPLv2 or later
License URI: [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)

Fix WordPress email fast. Set up SMTP, send a test, and check SPF/DMARC to improve deliverability. Pro adds scheduling, alerts, blacklists.

== Description ==
**MailHealth Lite** helps you fix “WordPress not sending email” problems quickly. If `wp_mail()` fails or contact forms land in spam, MailHealth lets you test sending, verify SMTP credentials, and validate DNS records (SPF and DMARC).

**Use cases**

* WordPress email not sending (contact forms, orders, password resets)
* After a host/domain/DNS change to confirm SMTP/DNS is still correct
* Validate DMARC/SPF TXT records to improve inbox placement

**Features (Lite)**

* SMTP wizard: host, port, TLS/SSL, auth, From address
* **Send Test** button with round-trip time
* DMARC / SPF TXT lookup (REST) with clean display
* Minimal local log for troubleshooting

**Pro adds**

* Scheduled canary test-sends (every 5–60 minutes)
* Slack/Webhook alerts on failures
* Blacklist checks for sending IPs
* Extended logs & DNS change alerts

== Frequently Asked Questions ==
= Does Lite run background checks? =
No. Lite is on-demand only. Pro adds scheduled canary checks and alerts.

= Will this conflict with other SMTP plugins? =
If another plugin configures PHPMailer, disable it to avoid conflicts. MailHealth Lite can replace most simple SMTP setups.

= Does this send through Gmail or Microsoft 365? =
Yes. Enter the SMTP settings from your provider (Gmail/Google Workspace, Microsoft 365, or your transactional email service).

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
