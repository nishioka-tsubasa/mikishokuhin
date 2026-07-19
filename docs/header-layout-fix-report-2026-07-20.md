# Header layout fix report - 2026-07-20

## Scope

- Target: `/logs/_migration/cms/wp-content/themes/lightning_for_miki`
- Environment note: PHP is already 8.4.
- Production `/cms` files were not changed.

## Issue

After the Lightning parent theme update, the desktop header menu text was shifted upward. When the page was scrolled, Lightning's `body.header_scrolled` behavior moved the global menu away from the logo row, causing a large vertical misalignment.

## Cause

The updated Lightning parent theme applies desktop header and scrolled-header rules to `.siteHeadContainer`, `.navbar-header`, and `.gMenu_outer`. The child theme's previous layout override did not fully neutralize the newer scrolled-header positioning, so the logo and global menu were no longer held in the same 56px header row.

## Changes

- `wp-content/themes/lightning_for_miki/assets/css/common.scss`
- `wp-content/themes/lightning_for_miki/assets/css/common.css`

Desktop-only CSS was added for `min-width: 992px` to:

- keep `header.siteHeader`, `.siteHeadContainer`, `.navbar-header`, `.gMenu_outer`, and `.gMenu` at 56px height;
- align the logo and menu items vertically with flex layout;
- keep menu icons and text centered together;
- disable the Lightning scrolled-header repositioning that caused the menu row to drop.

Mobile header CSS was not changed.

## Backup

Remote files were downloaded before upload:

- `/Users/n.tsubasa/Documents/Codex/2026-07-14/wordpress-wordpress-php-github-php-warning/work/header-layout-backup-20260720-0115/common.css`
- `/Users/n.tsubasa/Documents/Codex/2026-07-14/wordpress-wordpress-php-github-php-warning/work/header-layout-backup-20260720-0115/common.scss`
- `/Users/n.tsubasa/Documents/Codex/2026-07-14/wordpress-wordpress-php-github-php-warning/work/header-layout-backup-20260720-0115-manifest.json`

## Upload Verification

Uploaded files matched local hashes:

- `common.css`: `2118332ce5070f3087ee70c6575ceca4ed56c216d4b9116174b98c1a04369b25`
- `common.scss`: `adfd6ef3e1b24bc2ba88dc2b7063ad9875fe8d9141b8e5a464926141e3ab5d05`

Evidence:

- `/Users/n.tsubasa/Documents/Codex/2026-07-14/wordpress-wordpress-php-github-php-warning/work/header-layout-upload-verify-20260720-0115.json`

## QA Status

- Static CSS brace validation passed for both files.
- `php -l` passed for `header.php`; no PHP file was modified in this fix.
- Browser visual QA still needs confirmation in the user's Firefox private window after a hard reload, because the private proxy window cannot be reliably controlled from Codex without macOS Accessibility permission.

## Rollback

Restore the two backed-up files to:

- `/logs/_migration/cms/wp-content/themes/lightning_for_miki/assets/css/common.css`
- `/logs/_migration/cms/wp-content/themes/lightning_for_miki/assets/css/common.scss`
