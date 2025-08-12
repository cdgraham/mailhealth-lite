# Repo Setup Checklist (Per Repo)

## Describe the repo
- **Description**: Clear one-liner (e.g., "SMTP & deliverability checker for WordPress (Lite)")
- **Website**: https://chillichalli.com
- **Topics**: wordpress, plugin, smtp, woocommerce, fees, discounts, dmarc, spf, freemius

## Default branch
- Use **main**.

## Branch protection (Settings → Branches → main → Protect)
- Require pull request reviews: **on** (1 approval)
- Require status checks to pass: **on**
  - Add: `Run PHPCS`
- Require linear history: **on**
- Require conversation resolution: **on**
- Lock branch: leave **off** (optional)
- Include administrators: **on** (recommended)

## Secrets (Lite repos only)
- `WPORG_USERNAME`: your WordPress.org username
- `WPORG_PASSWORD`: your WordPress.org password

## CI expectations
- Tag like `v0.9.0` (Lite) or `v0.2.2` (Pro)
- PHPCS runs. If errors, fix or downgrade to warnings in `phpcs.xml.dist`.
- ZIP is attached to GitHub Release.
- For Lite: SVN auto-deploy runs on the same tag.

## Release hygiene
- Update changelog in `readme.txt` (Lite) or `CHANGELOG.md` (Pro)
- Bump version via `php tools/bump-version.php <version> <slug>`
- Create tag and push.
