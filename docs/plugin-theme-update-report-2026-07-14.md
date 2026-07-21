# Plugin and Theme Update Report - 2026-07-14

## Scope

- Target: `/logs/_migration/cms/wp-content/plugins` and `/logs/_migration/cms/wp-content/themes`
- PHP: already upgraded to 8.4 per request
- Production/public `/cms` was not edited
- Test environment files outside `/logs/_migration/cms/wp-content` were not edited

## Backup and Evidence

- Full pre-update backup: `work/migration-wpcontent-backup-20260714-185536`
- Backup manifest: `work/migration-wpcontent-backup-20260714-185536-manifest.json`
- Before inventory: `work/migration-wpcontent-version-inventory.json`
- Staged update metadata: `work/wporg-updates-stage-20260714-185536.json`
- Upload record: `work/wporg-updates-upload-20260714-185536.json`
- After inventory: `work/migration-wpcontent-version-after.json`
- Patched file hash verification: `work/remote-patched-file-verify-20260714-185536.json`

## Updates Applied

| Plugin | Before | After | Notes |
|---|---:|---:|---|
| `advanced-custom-fields` | 5.8.7 | 6.8.5 | updated |
| `akismet` | 4.1.3 | 5.7 | updated |
| `all-in-one-seo-pack` | 3.3.4 | 4.9.10 | updated |
| `all-in-one-wp-migration` | 7.6 | 7.106 | updated |
| `all-in-one-wp-migration-file-extension` | 1.5 | 1.5 | skipped / unchanged |
| `classic-editor` | 1.5 | 1.7.0 | updated |
| `custom-field-suite` | 2.5.16 | 2.5.16 | skipped / unchanged |
| `duplicate-post` | 3.2.4 | 4.7 | updated |
| `google-analytics-for-wordpress` | 10.2.0 | 10.2.2 | updated |
| `hello` | 1.7.2 | 1.7.2 | skipped / unchanged |
| `limit-login-attempts` | 1.7.1 | 1.7.2 | updated |
| `mw-wp-form` | 4.2.0 | 5.1.4 | updated |
| `optinmonster` | 2.16.5 | 2.16.24 | updated |
| `siteguard` | 1.5.0 | 1.8.7 | updated |
| `tinymce-advanced` | 5.3.0 | 5.9.2 | updated |
| `userfeedback-lite` | 1.1.1 | 1.11.2 | updated |
| `wp-multibyte-patch` | 2.8.3 | 2.9.3 | updated |
| `wpforms-lite` | 1.9.0.4 | 1.10.2.1 | updated |
| `wpfront-scroll-top` | 2.0.2 | 3.0.1 | updated |
| `zipaddr-jp` | 1.23 | 1.43 | updated |

| Theme | Before | After | Notes |
|---|---:|---:|---|
| `lightning` | 7.0.7 | 15.37.1 | updated |
| `lightning_for_miki` | 1.0.0 | 1.0.0 | child theme PHP compatibility fixes retained |
| `twentyfifteen` | 3.8 | 4.2 | updated |
| `twentynineteen` | 2.9 | 3.3 | updated |
| `twentyseventeen` | 3.7 | 4.1 | updated |
| `twentysixteen` | 3.3 | 3.8 | updated |
| `twentytwenty` | 2.7 | 3.1 | updated |
| `twentytwentyfive` | 1.0 | 1.5 | updated |
| `twentytwentyfour` | 1.2 | 1.5 | updated |
| `twentytwentythree` | 1.5 | 1.6 | updated |
| `twentytwentytwo` | 1.8 | 2.1 | updated |

## Compatibility Fixes

- Reapplied the existing MW WP Form customization in `classes/services/class.mail-parser.php` so inquiry posts keep using submitted `name` as `post_title`.
- Added PHP nullable type fixes to suppress PHP 8.4/8.5 deprecation warnings in:
  - `userfeedback-lite/includes/lib/class-mobile-detect.php`
  - `wpforms-lite/vendor_prefixed/symfony/css-selector/...`
  - `wpforms-lite/vendor_prefixed/tijsverkoyen/css-to-inline-styles/...`
  - `mw-wp-form/classes/abstract/class.validation-rule.php`
- Existing child theme PHP compatibility fixes from the prior pass remain in place.

## 2026-07-21 Admin Timeout Follow-up

- User-side proxy/private-window testing showed `POST /cms/codex-post-test.php` returned `POST OK`; POST routing itself was not the timeout cause.
- Temporary trace logs on the migration copy showed `GET /cms/wp-admin/` delaying around 112 seconds between `init` and completion of `admin_init`, then eventually shutting down much later. The strongest current suspect is admin-side update checks or other external-call work triggered during `admin_init`.
- Added temporary MU plugin `/logs/_migration/cms/wp-content/mu-plugins/codex-admin-timeout-mitigation.php` to remove WordPress core/plugin/theme update checks from `admin_init` while diagnosing the migration admin timeout. This is a migration-admin mitigation and should be removed or replaced with a permanent server/network fix before final production operation.
- Removed temporary POST test endpoint `/logs/_migration/cms/codex-post-test.php` after user confirmed `POST OK`.
- Patched PHP 8.4 dynamic-property deprecation in `/logs/_migration/cms/wp-content/plugins/custom-field-suite/includes/fields/loop.php` by declaring `public $values = array();` on `class cfs_loop`.
- Backup before the CFS patch: `/logs/_migration/cms/wp-content/plugins/custom-field-suite/includes/fields/loop.php.bak-20260721-173927`.

## Verification

- Staged plugin/theme PHP lint completed with local PHP 8.5.1.
- No remaining `Warning`, `Fatal error`, `Deprecated`, `Notice`, `Parse error`, or `Errors parsing` lines were found in `work/wporg-updates-stage-lint-after-patch.txt`.
- Remote version headers were re-downloaded after upload and saved to `work/migration-wpcontent-version-after.json`.
- Patched remote files were compared against staged files by SHA-256; all checked files matched.
- Temporary FTP swap directories `.__old_20260714-185536` / `.__new_20260714-185536` were cleaned up; remaining count: 0.

## Not Fully Remediated

- `custom-field-suite` remains at 2.5.16 because it is no longer available from the WordPress.org update API. This remains the largest residual risk and should be replaced or removed after confirming field usage.
- `all-in-one-wp-migration-file-extension` remains at 1.5 because it is a non-WordPress.org extension and no official update package was available in this environment.
- `hello.php` remains unchanged because it is inactive/demo-style code and not part of the functional site stack.
- Runtime browser QA for `/logs/_migration/cms/` could not be completed from here because the migration URL returns HTTP 403. Public `/cms` was intentionally not edited.

## Rollback

1. Restore the required plugin or theme directory from `work/migration-wpcontent-backup-20260714-185536`.
2. Upload it back to the matching path under `/logs/_migration/cms/wp-content/plugins` or `/logs/_migration/cms/wp-content/themes`.
3. Re-run PHP lint locally on the restored directory if further edits are made.
4. Re-check remote headers and clear any temporary swap directories.
