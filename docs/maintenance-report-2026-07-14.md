# WordPress/PHP/Plugin Upgrade Investigation

Date: 2026-07-14 JST
Site: https://mikishokuhin.co.jp/

## Scope

Production reflection has not been performed. This record covers read-only investigation, temporary diagnostic script execution and removal, current-state backup/snapshot collection, public-page QA, and upgrade planning.

## Temporary Scripts

- `/cms/php-probe-2c5f0a776d069df1b65a52cf.php`: uploaded for PHP runtime read-only check, then deleted.
- `/cms/probe-2c5f0a776d069df1b65a52cf.php`: uploaded for WordPress/plugin/theme read-only check, then deleted.
- FTP delete verification returned `450 ... no such file or directory`, confirming removal.

## Current Environment

- PHP: 7.2.24, `apache2handler`
- PHP ini: `/etc/opt/rh/rh-php72/php.ini`
- WordPress: 6.9.4
- Home URL: `https://mikishokuhin.co.jp/`
- Site URL: `https://mikishokuhin.co.jp/cms/`
- WP_DEBUG: false
- PHP display_errors: 0
- PHP log_errors: 1
- `wp-content/debug.log`: not present
- Current theme: `lightning_for_miki` 1.0.0
- Parent theme: Lightning 7.0.7

Evidence:

- `docs/evidence/server-probe-before.json`
- `docs/evidence/wp-core-version-check-2026-07-14.json`
- `docs/evidence/wporg-plugin-info-2026-07-14.json`
- `docs/evidence/public-qa-before.json`
- `docs/evidence/browser-qa-before.json`

## Active Plugins

| Plugin | Current | Latest checked | PHP requirement for latest | Status |
| --- | ---: | ---: | ---: | --- |
| Advanced Custom Fields | 5.8.7 | 6.8.5 | 7.4 | Update blocked by PHP 7.2 |
| All-in-One WP Migration | 7.6 | 7.106 | 5.3 | Update possible on PHP 7.2, but test first |
| Classic Editor | 1.5 | 1.7.0 | 5.2.4 | Update possible |
| Custom Field Suite | 2.5.16 | Not available from WP.org API | n/a | High risk: closed/discontinued and known unfixed vulnerabilities |
| Duplicate Post | 3.2.4 | 4.7 | 7.4 | Update blocked by PHP 7.2 |
| MonsterInsights | 10.2.2 | 10.2.2 | 7.2 | Current |
| MW WP Form | 4.2.0 | 5.1.4 | 8.0 | Critical/high risk; update blocked by PHP 7.2 |
| OptinMonster | 2.16.5 | 2.16.24 | 7.2 | Update possible |
| TinyMCE Advanced / Advanced Editor Tools | 5.3.0 | 5.9.2 | 5.6 | Update possible |
| UserFeedback Lite | 1.1.1 | 1.11.2 | 7.4 | Update blocked by PHP 7.2 |
| WPForms Lite | 1.9.0.4 | 1.10.2.1 | 7.2 | Update possible |
| WPFront Scroll Top | 2.0.2 | 3.0.1 | 7.2 | Update possible |

## Inactive Plugins Present

Inactive plugin directories/files are still present under `/cms/wp-content/plugins`:

- Akismet Anti-Spam 4.1.3
- All In One SEO Pack 3.3.4
- All-in-One WP Migration File Extension 1.5
- Hello Dolly 1.7.2
- Limit Login Attempts 1.7.1
- SiteGuard WP Plugin 1.5.0
- WP Multibyte Patch 2.8.3
- zipaddr-jp 1.23

Recommendation: remove unused inactive plugins after backup and confirmation, especially old security/login/migration plugins, because dormant files can still increase attack surface.

## Security Findings

- PHP 7.2.24 is end-of-life and blocks several required plugin/core updates. This is the first blocker.
- WordPress 7.0.1 is available and requires PHP 7.4 according to the WordPress core version check API.
- MW WP Form 4.2.0 is materially vulnerable. WPScan lists multiple fixed issues through 5.1.4, including unauthenticated file move, stored XSS, and information disclosure vulnerabilities.
- Custom Field Suite is closed in WPScan and has known vulnerabilities with no known fix through 2.6.7, including contributor-level SQL injection and PHP code injection. Current site uses 2.5.16 and the child theme calls `$cfs` directly, so replacement requires careful template/data compatibility work.
- Advanced Custom Fields 5.8.7 is far behind and below several fixed vulnerability versions.
- All-in-One WP Migration 7.6 is far behind; WPScan lists many fixed vulnerabilities up to 7.106.
- WPForms Lite 1.9.0.4 is below 1.9.9.2, which fixed an unauthenticated sensitive information exposure issue.

External references checked:

- WordPress core update API: `https://api.wordpress.org/core/version-check/1.7/`
- WordPress plugin API: `https://api.wordpress.org/plugins/info/1.2/`
- WPScan MW WP Form: `https://wpscan.com/plugin/mw-wp-form/`
- WPScan Custom Field Suite: `https://wpscan.com/plugin/custom-field-suite/`
- WPScan Advanced Custom Fields: `https://wpscan.com/plugin/advanced-custom-fields/`
- WPScan All-in-One WP Migration: `https://wpscan.com/plugin/all-in-one-wp-migration/`
- WPScan WPForms Lite: `https://wpscan.com/plugin/wpforms-lite/`
- WPScan UserFeedback Lite: `https://wpscan.com/plugin/userfeedback-lite/`
- Wordfence MW WP Form advisory: `https://www.wordfence.com/blog/2026/04/200000-wordpress-sites-affected-by-arbitrary-file-move-vulnerability-in-mw-wp-form-wordpress-plugin/`

## Public QA Before Update

HTTP crawl:

- 17 fixed pages, latest 3 notices, and notice archive checked.
- All returned HTTP 200.
- No `PHP Warning`, `Fatal error`, `Deprecated`, `Notice`, or `Parse error` markers found in HTML.

Browser QA:

- Viewports: PC 1280x900, mobile 390x844.
- Pages: top, contact, notice archive.
- No visible PHP error markers.
- No broken images detected by browser DOM.
- No site-origin console errors/warnings recorded.

Screenshots are stored outside the Git repo at:

- `work/browser-screenshots-before/pc-home.png`
- `work/browser-screenshots-before/pc-contact.png`
- `work/browser-screenshots-before/pc-notice.png`
- `work/browser-screenshots-before/sp-home.png`
- `work/browser-screenshots-before/sp-contact.png`
- `work/browser-screenshots-before/sp-notice.png`

## Backup / Snapshot

- Server inventory files: `work/server-inventory/`
- Focused code backup: `work/focused-backup/`
- Partial broader code snapshot before interruption: `work/server-snapshot/`
- Git snapshot in this repository: child theme `wp-content/themes/lightning_for_miki`

Note: no database dump was taken because only FTP access was available. A full rollback-safe update requires a DB backup from hosting control panel, phpMyAdmin, WP-CLI, or an authenticated admin migration/export path.

## Recommended Test Update Order

1. Take full file and database backup.
2. Confirm a restore path using the backup.
3. Upgrade test PHP to 8.0 first if MW WP Form must remain, preferably 8.1 or 8.2 if all plugins pass.
4. Re-run public QA and admin login smoke test.
5. Update MW WP Form to 5.1.4 first because current version carries the highest public-facing risk.
6. Update All-in-One WP Migration to 7.106.
7. Update WPForms Lite to 1.10.2.1.
8. Update Advanced Custom Fields to 6.8.5 after PHP is at least 7.4.
9. Replace or remove Custom Field Suite; do not treat updating it as sufficient because it is closed and has no known fix for later issues.
10. Update Duplicate Post, UserFeedback, OptinMonster, Classic Editor, TinyMCE Advanced, WPFront Scroll Top.
11. Update WordPress core to 7.0.1 after PHP is at least 7.4 and plugin compatibility has passed.
12. Remove inactive unused plugins.
13. Final crawl/browser QA and log review.

## Custom Field Suite Replacement Notes

The child theme uses `$cfs->get()` in:

- `page-about.php`
- `page-product.php`
- `page-recruit.php`
- `page-recruit-detail.php`

Recommended replacement path:

- Inventory CFS field groups and stored meta in the test DB.
- Recreate field groups in ACF or native meta.
- Add a thin compatibility helper in the child theme if needed to preserve template output.
- QA pages that depend on loop fields before removing CFS.

## Rollback Outline

1. Put the site in maintenance mode.
2. Restore database backup from the pre-update dump.
3. Restore `/cms` file backup or at minimum `wp-content/plugins`, `wp-content/themes`, and changed core files.
4. Clear caches if present.
5. Verify top, contact, notice list/detail, recruit pages, and admin login.
6. Remove maintenance mode.

## Production Reflection Status

Not performed. Awaiting explicit approval after test-environment updates and QA.
