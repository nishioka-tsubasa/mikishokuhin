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

## Follow-up Adjustment

After user review, the menu labels still appeared slightly higher than the menu icons. A second desktop-only CSS adjustment was applied:

- restored the final `display:flex` / `align-items:center` override after the later `.device-pc .gMenu > li > a` rule;
- moved the menu label group down by `2px`;
- moved the pseudo-element menu icons up by `2px` so the icon/text pair visually centers together.

Upload verification:

- `/Users/n.tsubasa/Documents/Codex/2026-07-14/wordpress-wordpress-php-github-php-warning/work/header-menu-text-upload-verify-20260720-0150b.json`

Second backup:

- `/Users/n.tsubasa/Documents/Codex/2026-07-14/wordpress-wordpress-php-github-php-warning/work/header-menu-text-backup-20260720-0150b/`

## Final Adjustment

The user confirmed the visible issue remained in both normal and scrolled states. A third adjustment was applied:

- added a high-specificity desktop-only override at the end of `common.css` and `common.scss` so it is evaluated after the existing child theme header rules;
- forced the desktop header, logo row, and global menu to remain in the same 56px flex row in both normal and `body.header_scrolled` states;
- increased the label-only visual offset to `6px` while counter-offsetting the pseudo-element icons so the text aligns lower without pushing the icons down;
- added `filemtime()` cache busting to the `common.css` link in `header.php`, because the existing hardcoded CSS URL had no version parameter and could be held by Firefox or the proxy cache.

Validation:

- `php -l wp-content/themes/lightning_for_miki/header.php` passed.
- Static CSS brace validation passed for `common.css` and `common.scss`.

Upload verification:

- `/Users/n.tsubasa/Documents/Codex/2026-07-14/wordpress-wordpress-php-github-php-warning/work/header-final-fix-upload-verify-20260720-0205.json`

Final backup:

- `/Users/n.tsubasa/Documents/Codex/2026-07-14/wordpress-wordpress-php-github-php-warning/work/header-final-fix-backup-20260720-0205/`

## Position Rebalance

The final override was too broad: it lowered the label text too far and removed the original right-aligned menu positioning. A fourth adjustment was applied:

- restored the global menu's original `display:block`, `float:right`, and `right:0` positioning so the menu stays aligned with the current production layout;
- kept `position:static` only for the scrolled header menu wrapper to prevent the Lightning scrolled-header drop;
- reduced the menu label offset from `6px` to `2px`, with a matching icon counter-offset;
- left the `common.css` `filemtime()` cache busting in `header.php` unchanged.

Validation:

- `php -l wp-content/themes/lightning_for_miki/header.php` passed.
- Static CSS brace validation passed for `common.css` and `common.scss`.

Upload verification:

- `/Users/n.tsubasa/Documents/Codex/2026-07-14/wordpress-wordpress-php-github-php-warning/work/header-position-rebalance-upload-verify-20260720-0209.json`

Rebalance backup:

- `/Users/n.tsubasa/Documents/Codex/2026-07-14/wordpress-wordpress-php-github-php-warning/work/header-position-rebalance-backup-20260720-0209/`

## Right Alignment Adjustment

The menu labels and icons were visually improved, but the menu still stopped too far left. A fifth adjustment was applied:

- changed the final desktop header override from flex-based placement back to the current site's float-based header layout;
- restored `.navbar-header` / `.siteHeader_logo` as left-floating elements;
- restored `.gMenu` as `display:block`, `float:right`, and `right:0`;
- kept the `2px` label/icon vertical offset and scrolled-header `position:static` protection.

Validation:

- Static CSS brace validation passed for `common.css` and `common.scss`.

Upload verification:

- `/Users/n.tsubasa/Documents/Codex/2026-07-14/wordpress-wordpress-php-github-php-warning/work/header-right-align-upload-verify-20260720-0216.json`

Right alignment backup:

- `/Users/n.tsubasa/Documents/Codex/2026-07-14/wordpress-wordpress-php-github-php-warning/work/header-right-align-backup-20260720-0216/`

## Normal Row Adjustment

The right alignment was improved, but the pre-scroll menu flowed onto the next row over the hero image. A sixth adjustment was applied:

- made `.siteHeadContainer` the positioning context with `position:relative`;
- positioned `#gMenu_outer` absolutely at `top:0; right:0` inside the header container;
- kept the existing right alignment and label/icon vertical offsets unchanged.

Validation:

- Static CSS brace validation passed for `common.css` and `common.scss`.

Upload verification:

- `/Users/n.tsubasa/Documents/Codex/2026-07-14/wordpress-wordpress-php-github-php-warning/work/header-normal-row-upload-verify-20260720-0319.json`

Normal row backup:

- `/Users/n.tsubasa/Documents/Codex/2026-07-14/wordpress-wordpress-php-github-php-warning/work/header-normal-row-backup-20260720-0319/`

## Mobile Navigation Hook Adjustment

The hamburger menu did not open after the Lightning update. The updated Lightning mobile navigation can output its button/menu HTML on the `lightning_site_footer_after` hook, while the child theme footer only exposed the older `lightning_footer_after` hook. A compatibility hook was added:

- kept the existing `do_action( 'lightning_footer_after' )`;
- added `do_action( 'lightning_site_footer_after' )` before `wp_footer()`.

Validation:

- `php -l wp-content/themes/lightning_for_miki/footer.php` passed.

Upload verification:

- `/Users/n.tsubasa/Documents/Codex/2026-07-14/wordpress-wordpress-php-github-php-warning/work/mobile-nav-footer-hook-clean-upload-verify-20260720-0330.json`

Footer hook backup:

- `/Users/n.tsubasa/Documents/Codex/2026-07-14/wordpress-wordpress-php-github-php-warning/work/mobile-nav-footer-hook-clean-backup-20260720-0330/`

## Mobile Menu Spacing Adjustment

The hamburger menu opened, but the open-state menu content was shifted too high: the area above "会社情報" was cramped and a white gap appeared below "お問い合わせ". A mobile-only spacing adjustment was applied:

- changed `body div.vk-mobile-nav.vk-mobile-nav-open` from `padding: 45px 0 0` to `padding: 85px 0 0`;
- kept the desktop `min-width: 992px` header overrides untouched.

Validation:

- Static CSS brace validation passed for `common.css` and `common.scss`.

Upload verification:

- `/Users/n.tsubasa/Documents/Codex/2026-07-14/wordpress-wordpress-php-github-php-warning/work/mobile-menu-spacing-upload-verify-20260720-0344.json`

Mobile menu spacing backup:

- `/Users/n.tsubasa/Documents/Codex/2026-07-14/wordpress-wordpress-php-github-php-warning/work/mobile-menu-spacing-backup-20260720-0344/`

## Rollback

Restore the backed-up files to:

- `/logs/_migration/cms/wp-content/themes/lightning_for_miki/header.php`
- `/logs/_migration/cms/wp-content/themes/lightning_for_miki/footer.php`
- `/logs/_migration/cms/wp-content/themes/lightning_for_miki/assets/css/common.css`
- `/logs/_migration/cms/wp-content/themes/lightning_for_miki/assets/css/common.scss`
