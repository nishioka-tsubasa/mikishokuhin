# WordPress / PHP / Plugin Upgrade Completion Report

Date: 2026-07-22 JST

Target: `/logs/_migration/cms`

Production deployment: **Not performed**

## Result

- PHP 8.4.2でWordPress 7.0.2を動作確認した。
- WordPress本体・有効プラグイン・テーマの更新候補は0件になった。
- 管理画面、Site Health、更新、プラグイン、MW WP Form編集画面のGateway Timeoutを解消した。
- REST API、WordPress.org通信、ループバック、WP-Cronを実通信で確認した。
- Custom Field Suiteの値をSecure Custom Fieldsへ移行し、CFS本体をWebルート外へ退避した。
- MW WP Formは公式5.1.4のままとし、旧プラグイン直接改修を子テーマのフックへ移した。今後も公式更新を適用できる。
- 最終QAで検出した会社情報ページのPHP Warningを修正し、再巡回後のFatal / Warning / Deprecated / Noticeは0件だった。
- 一時診断PHP、診断MUプラグイン、公開診断JSONはすべて削除した。

## Versions

| Component | Before | After |
| --- | ---: | ---: |
| PHP | 8.4.2（作業開始時に更新済み） | 8.4.2 |
| WordPress | 6.9.4 | 7.0.2 |
| Lightning parent theme | 15.37.1 | 15.37.1 |
| Lightning for Miki child theme | 1.0.0 | 1.0.0 + compatibility fixes |

### Active Plugins

| Plugin | Before this final stage | Final |
| --- | ---: | ---: |
| All-in-One WP Migration and Backup | 7.106 | 7.107 |
| Classic Editor | 1.7.0 | 1.7.0 |
| Yoast Duplicate Post | 4.7 | 4.7 |
| MonsterInsights | 10.2.2 | 11.1.0 |
| MW WP Form | 5.1.4 | 5.1.4 |
| OptinMonster | 2.16.24 | 2.16.24 |
| Secure Custom Fields | 6.9.1 | 6.9.2 |
| Advanced Editor Tools | 5.9.2 | 5.9.2 |
| UserFeedback Lite | 1.11.2 | 1.11.3 |
| WPForms Lite | 1.10.2.1 | 2.0.0.2 |
| WPFront Scroll Top | 3.0.1 | 3.0.1 |

WordPressの更新APIによる最終確認では、WordPress本体、プラグイン、テーマの更新残件はいずれも0件。

## Compatibility And Security Work

### Admin And Network

- `miki-admin-network-compat.php`を2.3.0へ更新した。
- 外部HTTPを移行環境のHTTPプロキシへ送り、サイト自身へのREST / loopback / WP-Cronは127.0.0.1へ固定した。
- WordPress 7でも管理画面を約281秒停止させていたcommand palette生成を管理画面のみ無効化した。
- 最終レンダー時間はDashboard 0.0783秒、Plugins 0.0633秒、Site Health 0.1108秒、Updates 1.9932秒だった。

### Custom Field Suite Replacement

- CFSの4フィールドグループ、9投稿分の値をSCF互換メタへ移行した。
- `miki-site-fields.php`でサイト固有フィールドをコード登録した。
- 子テーマの取得ヘルパーをSCF優先、CFSフォールバックに変更した。
- CFS 2.5.16は無効化し、Webルート外へ退避した。CFSのDBテーブルと投稿はロールバック用に保持したが、実行コードは公開領域に存在しない。
- 競合を避けるため、非アクティブだったAdvanced Custom Fields 6.8.5もWebルート外へ退避した。既存ACFフィールドグループはSCFが継続して扱う。

### Inactive Plugin Hardening

- 非アクティブだった7プラグインディレクトリと`hello.php`をWebルート外へ退避した。
- 公開プラグイン領域は有効11本と`index.php`だけになった。
- 退避後のプラグイン画面で「すべて11 / 使用中11」を確認した。
- 退避物は削除しておらず、必要なプラグインだけ個別に復元できる。

### MW WP Form

- MW WP Form本体は公式5.1.4へ戻し、プラグインファイルの直接改修を廃止した。
- 問い合わせ保存時に担当者名を投稿タイトルへ反映する処理を、子テーマの`mwform_contact_data_save-mwf_157`フックへ移した。
- フォームID 157の編集画面は0.4077秒で完全レンダーした。
- 管理者メールと自動返信メールの2通が`wp_mail`まで到達することを確認した。QAでは`pre_wp_mail`で配送を止め、実メールは送信していない。
- 送信先設定、フォーム設定、投稿データはQA前後で不変。新しい送信先が未指定のため現行アドレスを維持している。

### Theme Fixes

- `page-company.php`の未定義`$post_id`を`get_queried_object_id()`へ置換した。
- `page-recruit-detail.php`でSCF select値を正しくCSS classへ変換した。
- モバイルメニューは親テーマのクリック処理が動かなかった場合だけ作動するフォールバックを子テーマ`common.js`へ追加した。
- 既存のPCヘッダー配置、スクロール時配置、モバイルメニュー間隔を維持した。

## Backup Paths

### Database And Core

- `/logs/_migration/logs/_migration/mikishokuhin-migration-db-before-cfs-20260722-024329.sql.gz`
  - SHA-256: `be3330eecd7ab62129860f88fc45a8b7b57085612ddf22c35af605bbe2f48c8c`
- `/logs/_migration/logs/_migration/mikishokuhin-migration-db-before-updates-20260722-033805.sql.gz`
  - SHA-256: `c36fcf6f31c2d02fac7ae674e0e7fb65dadfeff4ffcfb1ed45f56470af74bfcf`
- `/logs/_migration/logs/_migration/core-backup-wordpress-6.9.4-20260722-040921.zip`
  - SHA-256: `a4d0bdeb5cab6f8bba933845e0b2990dd4703e2ab3f1b75cc61c9c9431ec4979`
- WordPress 7.0.2 official package
  - SHA-256: `a616580ed2152ae71d81439884b4bcda329c5322f9bd2092ac7a3a68dbcea7a7`

### Plugins

- `plugin-backup-secure-custom-fields-6.9.1-20260722-034655.zip`
  - SHA-256: `17a222db9797d34b9ee5825dc7edd8d7f2f5e6fba9d404bf49aebc3387966eb2`
- `plugin-backup-wpforms-lite-1.10.2.1-20260722-035353.zip`
  - SHA-256: `5c35e91aee49460ed259ba940115dbe14c469d5f9a0a280e448cd2226dcb03f8`
- `plugin-backup-google-analytics-for-wordpress-10.2.2-20260722-035640.zip`
  - SHA-256: `4f6327e9fedfee344aaa6a95b338c4ce9046922403fd9895704c5896515b83a1`
- `plugin-backup-userfeedback-lite-1.11.2-20260722-035921.zip`
  - SHA-256: `7cdf8240c88eacdfd951366ead44c0da945e41192b211234fce4ef6120e9213e`
- `plugin-backup-all-in-one-wp-migration-7.106-20260722-040154.zip`
  - SHA-256: `0f8b91a5a3982f6bc66f7cd9d00724f8daa0dca80ed846b1c58effc31677f2dc`
- CFS archive: `/logs/_migration/logs/_migration/plugins/custom-field-suite-2.5.16-20260722-1235`
- Inactive ACF archive: `/logs/_migration/logs/_migration/plugins/advanced-custom-fields-6.8.5-inactive-20260722-1305`
- Other inactive plugins: `/logs/_migration/logs/_migration/plugins/inactive-unused-20260722-1430`
  - `akismet`
  - `all-in-one-seo-pack`
  - `all-in-one-wp-migration-file-extension`
  - `hello.php`
  - `limit-login-attempts`
  - `siteguard`
  - `wp-multibyte-patch`
  - `zipaddr-jp`

### Final Theme Fixes

- `work/private-backups/20260722/theme-before-final-warning-fix/page-company.php`
  - SHA-256: `a2288e48e59da3277f1bf94c1422a533529cbe357081d183970c070960565f55`
- `work/private-backups/20260722/theme-before-mobile-menu-fallback/common.js`
  - SHA-256: `6620b62c5d56f7e7f41c266831d9466af8c8a5f7de34dfacbea0a0d92f11dd60`

## Final QA

- Public crawl: top, company, business, product, production, quality, recruit, six interview pages, contact, notice list/detail, privacy; 17/17 HTTP 200, maximum 0.1063 seconds.
- HTML: all 17 pages completed through `</html>`; no visible Fatal / Warning / Deprecated / Notice.
- Content comparison: original 10 pagesはscript/style差分を除く本文DOMが更新前と一致し、`<br>`数も一致した。
- Admin browser: Dashboard, Site Health, Updates, Plugins, MW WP Form edit pageをFirefox private windowで正常表示した。
- Plugin inventory after hardening: すべて11 / 使用中11。非アクティブコードの残存0件。
- Site Health: REST API `good`、WordPress.org通信`good`、loopback requests `good`。
- Network: home loopback 200、REST 200、WP-Cron HTTP 200、WordPress.org 200。
- WP-Cron: 実イベント登録、HTTP起動、callback発火、nonce一致、イベント消去を確認した。
- Contact: フォームHTML、確認ボタン、フォームID、返信メール項目を確認し、管理者メールと自動返信メールの生成に成功した。
- Visual: PC通常・スクロール時ヘッダー、モバイル通常、ハンバーガー開閉、スライダーをFirefox private windowで確認した。
- PHP runtime capture: 最終一式QA後、Fatal / Warning / Deprecated / Noticeは0件。捕捉MUプラグインは削除済み。
- Temporary files: WordPress直下の診断PHP、診断MUプラグイン、Web公開診断JSON、Webルート内MUバックアップを削除し、FTP一覧で残存0件を確認した。

## Changed Repository Files

- `wp-content/mu-plugins/miki-admin-network-compat.php`
- `wp-content/mu-plugins/miki-site-fields.php`
- `wp-content/themes/lightning_for_miki/functions.php`
- `wp-content/themes/lightning_for_miki/page-recruit-detail.php`
- `wp-content/themes/lightning_for_miki/page-company.php`
- `wp-content/themes/lightning_for_miki/assets/js/common.js`

Core and plugin package updates are present in the migration server but are not vendored into this repository.

## Remaining Risks And Required Input

- MW WP Form 5.1.4 is the current official package and passed PHP 8.4 / WordPress 7 QA, but its published compatibility declaration is older than WordPress 7. Continue form regression testing after future core updates.
- The new MW WP Form destination address has not been provided. Form ID 157 is editable and ready for the change; current destination remains unchanged.
- The outbound proxy is environment-specific. At production cutover, re-test direct outbound connectivity. If direct communication works, move/remove the proxy constants while retaining the loopback behavior as required.
- CFS DB data is retained only for rollback. After an agreed retention period and a fresh backup, it can be removed separately.
- No production deployment has been performed.

## Production Deployment Procedure

1. Freeze content changes and take a fresh production database and `/cms` file backup.
2. Record current PHP, WordPress, plugin and theme versions.
3. Set PHP to 8.4 and verify the CLI/FPM/Apache runtime actually serving WordPress.
4. Deploy the tested WordPress 7.0.2 core and plugin versions one stage at a time.
5. Deploy the repository changes, including both MU plugins and the child-theme files.
6. Migrate CFS values to SCF, verify the nine affected posts, then deactivate and move CFS outside the Web root.
7. Configure the confirmed MW WP Form destination in form ID 157 and run a controlled real delivery test.
8. Re-run Dashboard, Site Health, Updates, Plugins, REST, loopback, WP-Cron, 17-page crawl, contact form and PC/mobile browser QA.
9. Check hosting PHP logs and WordPress logs for Fatal / Warning / Deprecated / Notice before ending maintenance.

## Rollback Procedure

1. Put the affected environment into maintenance mode and stop content/form writes.
2. Restore the pre-update database dump matching the rollback point.
3. Restore WordPress 6.9.4 from the core backup and restore affected plugin ZIPs, or restore the full pre-update `/cms` backup.
4. Restore CFS from its protected archive and the pre-CFS database only if rolling back the SCF migration; do not run CFS and SCF field editing concurrently.
5. Restore the child-theme/MU files from the Git commit before this work or the focused local backups.
6. Restore an archived inactive plugin from `inactive-unused-20260722-1430` only if its former functionality is explicitly required.
7. Remove `.maintenance`, clear caches, and verify login, top, company, product, recruit, notice and contact pages.
8. Confirm form delivery and logs before reopening traffic.
