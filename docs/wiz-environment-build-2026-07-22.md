# WIZ environment build record

## Status

- Target: `https://mikishokuhin.wiz-services.com/`
- State at 2026-07-22 17:55 JST: target returns HTTP 404 through CloudFront/LiteSpeed.
- Source snapshot: complete and checksum verified.
- Target deployment: waiting for WIZ hosting and database access details.
- Current production `https://mikishokuhin.co.jp/` must not be modified as part of this build.

## Source snapshot

The snapshot was created from the validated Alpha migration tree at `/logs/_migration` after all WordPress, plugin, theme, PHP compatibility, admin timeout, CFS replacement, and MW WP Form work was completed.

| Item | Value |
| --- | --- |
| WordPress | 7.0.2 |
| PHP | 8.4.2 |
| Active plugins | 11 |
| Active theme | `lightning_for_miki` 1.0.0, parent `lightning` |
| File count | 16,875 |
| Uncompressed bytes | 496,880,757 |
| File archive | `wiz-deploy-source-20260722-b0b290fb.zip` |
| File archive bytes | 328,532,408 |
| File archive SHA-256 | `510569e56ad934d2ca0db8652b95aa1f5dc5fc44d2f89983baa67174bd676d65` |
| Database archive | `wiz-deploy-source-20260722-b0b290fb.sql.gz` |
| Database archive bytes | 411,396 |
| Database archive SHA-256 | `695347ade8113156c9def325e5e3c6c204d39b3c3a161bcbdf31f1f9644bc807` |

Local protected copy:

`work/private-backups/20260722/wiz-deployment-source/`

Server-side protected copy:

`/logs/_migration/logs/_migration/`

The one-time packaging script was removed from the public theme directory after collection and its absence was verified over FTPS.

The deployment ZIP intentionally excludes:

- `cms/wp-config.php`
- `.maintenance` and `wp-content/debug.log`
- All-in-One WP Migration backup files
- cache and WordPress upgrade working directories
- Codex one-time scripts and public reports

The source `wp-config.php` is retained separately in the local protected copy for configuration comparison only. It must not be uploaded to WIZ because it contains Alpha database credentials and salts.

## WIZ-specific differences

The expected URL layout is:

- Home URL: `https://mikishokuhin.wiz-services.com/`
- WordPress URL: `https://mikishokuhin.wiz-services.com/cms/`

Create a new target `wp-config.php` using the WIZ database credentials and fresh WordPress salts. Keep `WP_DEBUG_DISPLAY` disabled. Enable a private debug log only during deployment QA and remove or disable it after the log is clean.

Do not deploy `cms/wp-content/mu-plugins/miki-admin-network-compat.php`. It is specific to the Alpha migration network and defines the Alpha outbound proxy plus local cURL resolution. Carrying it to WIZ could break WordPress.org, REST, loopback, and WP-Cron requests. Keep `cms/wp-content/mu-plugins/miki-site-fields.php`, which contains the portable replacement for Custom Field Suite usage.

Only reintroduce the command-palette performance workaround from the migration MU plugin if WIZ admin timing measurements prove it is still needed. It must then be separated from all Alpha proxy behavior.

## Deployment sequence

1. Record the WIZ document root, PHP version, database state, disk quota, and current files.
2. Back up the WIZ document root and database before changing anything, even if the site currently appears empty.
3. Configure the target for PHP 8.4 and confirm required extensions before importing WordPress.
4. Extract the verified file archive into the WIZ document root.
5. Remove the Alpha-only network MU plugin before the first WordPress request.
6. Create the WIZ-specific `cms/wp-config.php` with least-privilege database credentials and fresh salts.
7. Import the gzip SQL backup into the target database.
8. Perform a serialized-data-safe URL replacement from `https://mikishokuhin.co.jp` to `https://mikishokuhin.wiz-services.com` across all WordPress-prefixed tables. Do not use raw SQL replacement on serialized values.
9. Explicitly set `home` and `siteurl`, flush rewrite rules, clear WordPress/LiteSpeed caches, and invalidate the CloudFront distribution.
10. Confirm the MW WP Form administrator recipient and approved test recipient before sending a test submission.
11. Run the full QA list below and inspect PHP, WordPress, web server, and mail logs.
12. Delete all import helpers and archives from any web-accessible directory.

Preferred URL replacement when WP-CLI is available:

```bash
wp search-replace 'https://mikishokuhin.co.jp' 'https://mikishokuhin.wiz-services.com' --all-tables-with-prefix --precise --recurse-objects --skip-columns=guid
wp option update home 'https://mikishokuhin.wiz-services.com'
wp option update siteurl 'https://mikishokuhin.wiz-services.com/cms'
wp rewrite flush --hard
```

## Required QA

- Home page, principal fixed pages, news list, and news detail return HTTP 200.
- Desktop and mobile header/menu layouts match the approved migration result, including fixed header and hamburger menu states.
- Contact input, confirmation, back, validation, completion, and conditional referral fields work without raw JavaScript or CSS appearing on the page.
- Approved test messages reach both administrator and submitter, with correct From, Reply-To, and recipient values.
- `/cms/wp-admin/`, Plugins, Updates, and Site Health complete without Gateway Timeout.
- REST API, WordPress.org connectivity, loopback requests, and WP-Cron pass on WIZ without the Alpha compatibility MU plugin.
- Custom fields supplied by Secure Custom Fields and `miki-site-fields.php` render correctly; Custom Field Suite remains absent.
- No public `Fatal error`, `Warning`, `Deprecated`, or `Notice` output is present.
- PHP, WordPress, LiteSpeed, and mail logs contain no unresolved fatal, warning, deprecated, loopback, cron, or delivery failures.
- No one-time scripts, SQL files, ZIP files, debug logs, or backup files remain web-accessible.

## Rollback

Restore the WIZ file and database backups taken immediately before deployment, purge LiteSpeed/CloudFront caches, and verify the pre-deployment HTTP response. The Alpha migration snapshot and current production remain independent and must not be used as destructive rollback targets.
