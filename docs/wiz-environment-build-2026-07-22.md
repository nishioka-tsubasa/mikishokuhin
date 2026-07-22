# WIZ Environment Build Record

Date: 2026-07-22 JST

Target: `https://mikishokuhin.wiz-services.com/`

## Result

- WIZの公開ディレクトリへ、検証済み移行環境のWordPress、データベース、テーマ、プラグイン、SCF移行結果、MW WP Form修正を構築した。
- Home URLは`https://mikishokuhin.wiz-services.com/`、WordPress URLは`https://mikishokuhin.wiz-services.com/cms`。
- PHP 8.4.23、WordPress 7.0.2、MySQL 32テーブル、有効プラグイン11本、子テーマ`lightning_for_miki`で動作している。
- WIZ環境は検索エンジンのインデックスを無効にし、`X-Robots-Tag: noindex, nofollow, noarchive`を返す。
- 現本番`https://mikishokuhin.co.jp/`とAlpha移行環境は、WIZ構築作業では変更していない。
- WIZの公開可否は、指定予定の新しい問い合わせ送信先への実メール2通の到着確認後に判断する。

## Versions

| Component | WIZ result |
| --- | ---: |
| PHP | 8.4.23 |
| WordPress | 7.0.2 |
| Lightning parent theme | 15.37.1 |
| Lightning for Miki child theme | 1.0.0 + compatibility fixes |

### Active Plugins

| Plugin | Version |
| --- | ---: |
| All-in-One WP Migration and Backup | 7.107 |
| Classic Editor | 1.7.0 |
| Yoast Duplicate Post | 4.7 |
| MonsterInsights | 11.1.0 |
| MW WP Form | 5.1.4 + PHP 8.4 compatibility patch |
| OptinMonster | 2.16.24 |
| Secure Custom Fields | 6.9.2 |
| Advanced Editor Tools | 5.9.2 |
| UserFeedback Lite | 1.11.3 + PHP 8.4 compatibility patch |
| WPForms Lite | 2.0.0.2 |
| WPFront Scroll Top | 3.0.1 |

WordPress本体、プラグイン、テーマ、翻訳の更新候補は最終確認ですべて0件。

## Source And Database Correction

配置元ファイルはAlpha移行環境の最終QA済みスナップショット。ローカル保護コピーは次のディレクトリに保存した。

`work/private-backups/20260722/wiz-deployment-source/`

| Artifact | Bytes | SHA-256 | Usage |
| --- | ---: | --- | --- |
| `wiz-deploy-source-20260722-b0b290fb.zip` | 328,532,408 | `510569e56ad934d2ca0db8652b95aa1f5dc5fc44d2f89983baa67174bd676d65` | Source files |
| `wiz-deploy-source-20260722-b0b290fb.sql.gz` | 411,396 | `695347ade8113156c9def325e5e3c6c204d39b3c3a161bcbdf31f1f9644bc807` | Evidence only; do not import directly |
| `wiz-deploy-source-20260722-percent-restored.sql.gz` | 402,214 | `68a11e99bddd8f8372c82089b373e73a516da6f21496e96dc2b5ac97e105ac0d` | Corrected reusable database source |

元SQLにはWordPressの`$wpdb->_real_escape()`が生成した`%`用プレースホルダーが3,719件残っていた。直接インポートすると日本語スラッグやパーマリンクを破損するため、完全一致するプレースホルダーを`%`へ復元し、gzip検証後にデータベース全体を再インポートした。

修正版SQLの結果:

- 119 SQL statements、32 tables。
- WIZ URLへのserialized-data-safe置換195件。
- 完全プレースホルダー、断片プレースホルダーともに残存0件。
- パーマリンク構造は`/%category%/%postname%/`。
- 日本語ニューススラッグと詳細ページを再巡回し、HTTP 200を確認した。

元SQLは証跡として保存するが、以後の再構築には必ず修正版SQLを使用する。

## WIZ-Specific Configuration

- WIZ DB情報と新しいsaltで`cms/wp-config.php`を新規作成し、権限を`0400`に設定した。
- CloudFrontからオリジンへHTTP転送されてもWordPressがHTTPS URLを生成するよう、`mikishokuhin.wiz-services.com`に限定したHTTPS判定を追加した。
- Alpha専用`miki-admin-network-compat.php`は配置していない。Alphaのプロキシ、127.0.0.1固定、通信停止時フォールバックはWIZでは不要であり、持ち込むとREST、loopback、WP-Cronを破損する可能性がある。
- `miki-site-fields.php`は配置し、Custom Field Suiteから移行したサイト固有フィールドをSecure Custom Fieldsで継続利用している。
- WordPress 7の管理画面を停止させるコマンドパレット生成だけを`miki-admin-performance.php`へ分離した。WIZに不要な通信設定は含まない。
- 検証終了時に`WP_DEBUG`と`WP_DEBUG_LOG`を無効化した。

## PHP 8.4 Compatibility

### MW WP Form 5.1.4

公式5.1.4の`classes/abstract/class.validation-rule.php`に、暗黙nullable引数を明示nullableへ変更する1行だけを適用した。

- Patched SHA-256: `4ba50ddae4a34c9884e04015a7bc2f62c6296a778bf2760200923fc4abcecec5`
- 公式ZIPとの差分はこの1行だけであることを確認した。
- 公式更新で上書きされるため、上流で修正されるまでは更新後に再確認する。

### UserFeedback Lite 1.11.3

`includes/lib/class-mobile-detect.php`の暗黙nullable引数を明示nullableへ変更した。

- Patched SHA-256: `1a6dcef71faaa6017902d9bd9995615179ec1276f947675492824439edb460d5`
- WIZサーバーのPHP 8.4でlint成功。

### Admin Performance MU Plugin

- File: `wp-content/mu-plugins/miki-admin-performance.php`
- SHA-256: `b6702a9f075a325ec51bf8b8b86c2f5ff4e3fb9de17e67f074a3cfaa843b1e54`
- ローカルとWIZ配置後のハッシュ一致を確認した。

## Backups

WIZの配置前状態は公開ファイルとDBテーブルがない状態だった。配置途中のDBは次の保護バックアップを作成した。

| Backup | SHA-256 | Purpose |
| --- | --- | --- |
| `before-percent-marker-repair.sql.gz` | `56cc6329e6fc21b2b8869fde37775caa15a868a2ac0367c794ca4d9af5794d30` | Initial import before placeholder repair |
| `before-corrected-reimport.sql.gz` | `37b5ecb5637c9ef53c6c309ec518e5b933e2b327b9c0a74b4b832aa8c0540882` | State immediately before corrected full reimport |

これらはローカル保護領域へ回収済み。Web公開領域からは削除した。

## QA

### Public Pages

- トップ、主要固定ページ、事業・製品・製造・品質・採用、社員6ページ、問い合わせ、プライバシー、ニュース一覧、カテゴリ、ニュース詳細3件の22/22ページがHTTP 200。
- 全22ページで`</html>`まで出力され、公開HTMLのFatal / Warning / Deprecated / Noticeは0件。
- 旧本番URL、DBプレースホルダー、生の問い合わせJavaScript / CSSの露出は0件。
- FirefoxプライベートウィンドウでPC通常、スクロール固定ヘッダー、スマホ通常、ハンバーガー開閉、問い合わせフォームを確認した。
- PCヘッダーの右端配置、アイコンと文字位置、スマホメニュー各行の高さ、お問い合わせ帯直下の余白なしを確認した。

### Authenticated Administration

Firefoxプライベートウィンドウと認証済みHTTPセッションの両方で確認した。検証専用管理者は完了後に削除した。

| Screen | HTTP | Authenticated render |
| --- | ---: | ---: |
| Dashboard | 200 | 1.763 sec including login and redirect |
| Plugins | 200 | 0.914 sec |
| Site Health | 200 | 0.349 sec |
| Updates | 200 | 1.417 sec |
| MW WP Form 157 editor | 200 | 0.479 sec |

- Site Healthの総合表示は「良好」。
- Pluginsは「すべて11 / 使用中11」。
- UpdatesはWordPress 7.0.2を表示し、更新残件0。
- 全画面でGateway Timeout、Fatal / Warning / Deprecated / Noticeは0件。
- コマンドパレット資産は管理画面HTMLに出力されていない。

### Network And Cron

- Site loopback: HTTP 200。
- REST API: HTTP 200。
- WordPress.org API: HTTP 200。
- WP-Cron HTTP spawn: HTTP 200。
- `wp cron test`: success。
- Alpha用プロキシMUプラグインなしで上記を確認した。

### Contact Form

- 入力、確認、戻る、値保持、radio/同意状態、条件表示を確認した。
- 管理者通知と自動返信の2通が`wp_mail`まで到達し、宛先・本文・ヘッダーを配送前に捕捉した。
- QA用投稿、設定変更、実メール送信は残していない。
- WIZでは`mail()`とsendmail設定が存在する。
- 実メール配送は未実施。新しい管理者送信先が未指定のため、現在の送信先を変更していない。

### Logs And Cleanup

- 全QA後のWordPressデバッグログは0 bytes。SHA-256は空ファイルの`e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855`。
- ソースZIP内に残っていたテーマ3件、MW WP Form 1件の`.bak-*`をWIZ公開ツリーから削除した。
- 配置用PHP、認証QA用PHP、ZIP、SQL、WP-CLI、state、debug log、保護作業ディレクトリを公開領域から削除した。
- FTPルートの最終残存は`.htaccess`、`index.php`、`cms`だけ。
- 配置用PHP、認証QA用PHP、保護state URLはいずれもHTTP 404。
- 認証QA用管理者は削除済みで、旧QA cookieはログイン画面へ戻ることを確認した。

Protected evidence:

| Evidence | SHA-256 |
| --- | --- |
| `wiz-deployment-status-before-cleanup.json` | `866bf443a03a9e1f374c384b31a4f23dd50341caf3a93a2c1e3179d7b02d63b5` |
| `wiz-deployment-qa-after-finalize.json` | `a13d7503694f848e3f95ed0ac10b83b09511f2fc420448ff24a9692de6ff60de` |
| `wiz-deployment-finalize.json` | `a9cad1599ae96f035c17c1cf9add0b7d448c3819d3da113a2e91a157787b5280` |

## Remaining Release Items

1. 新しいMW WP Form管理者送信先を確定し、フォームID 157へ設定する。
2. 管理者通知と問い合わせ者自動返信を実送信し、双方の受信、From、Reply-To、迷惑メール判定を確認する。
3. 利用者がWIZの表示と管理画面を受け入れ確認する。
4. 公開先として切り替える場合はnoindexと`X-Robots-Tag`を解除し、CloudFrontキャッシュを更新する。

実配送と送信先変更が終わるまでは、WIZを最終公開可能とは判定しない。

## Rollback

### WIZを配置前状態へ戻す場合

1. WIZへのアクセスを停止し、現在のDBと`/mikishokuhin`を新規バックアップする。
2. WIZ DBのWordPress 32テーブルを削除し、配置前の空DB状態へ戻す。
3. `/mikishokuhin`から`.htaccess`、`index.php`、`cms`を削除し、配置前の空公開ディレクトリへ戻す。
4. CloudFront/LiteSpeedキャッシュを消去し、配置前と同じ404を確認する。

### WIZを再構築する場合

1. ファイルZIP`wiz-deploy-source-20260722-b0b290fb.zip`を展開する。
2. Alpha専用`miki-admin-network-compat.php`を配置しない。
3. WIZ用`wp-config.php`を作成し、CloudFront HTTPS判定、権限`0400`、デバッグ非表示を設定する。
4. 元SQLではなく`wiz-deploy-source-20260722-percent-restored.sql.gz`をインポートする。
5. serialized-data-safe URL置換、Home/Site URL設定、rewrite flush、キャッシュ削除を行う。
6. `miki-admin-performance.php`とPHP 8.4互換パッチを反映し、本書の全QAを再実行する。

現本番とAlpha移行環境はWIZから独立しており、WIZロールバックの対象にしない。
