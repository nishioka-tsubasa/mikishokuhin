<?php

/*-------------------------------------------*/
/*  カスタム投稿タイプ「イベント情報」を追加
/*-------------------------------------------*/
// add_action( 'init', 'add_post_type_event', 0 );
// function add_post_type_event() {
//     register_post_type( 'event', /* カスタム投稿タイプのスラッグ */
//         array(
//             'labels' => array(
//                 'name' => 'イベント情報',
//                 'singular_name' => 'イベント情報'
//             ),
//         'public' => true,
//         'menu_position' =>5,
//         'has_archive' => true,
//         'supports' => array('title','editor','excerpt','thumbnail','author')
//         )
//     );
// }

/*-------------------------------------------*/
/*  カスタム分類「イベント情報カテゴリー」を追加
/*-------------------------------------------*/
// add_action( 'init', 'add_custom_taxonomy_event', 0 );
// function add_custom_taxonomy_event() {
//     register_taxonomy(
//         'event-cat', /* カテゴリーの識別子 */
//         'event', /* 対象の投稿タイプ */
//         array(
//             'hierarchical' => true,
//             'update_count_callback' => '_update_post_term_count',
//             'label' => 'イベントカテゴリー',
//             'singular_label' => 'イベント情報カテゴリー',
//             'public' => true,
//             'show_ui' => true,
//         )
//     );
// }

/********* 備考1 **********
Lightningはカスタム投稿タイプを追加すると、
作成したカスタム投稿タイプのサイドバー用のウィジェットエリアが自動的に追加されます。
プラグイン VK All in One Expansion Unit のウィジェット機能が有効化してあると、
VK_カテゴリー/カスタム分類ウィジェット が使えるので、このウィジェットで、
今回作成した投稿タイプ用のカスタム分類を設定したり、
VK_アーカイブウィジェット で、今回作成したカスタム投稿タイプを指定する事もできます。

/********* 備考2 **********
カスタム投稿タイプのループ部分やサイドバーをカスタマイズしたい場合は、
下記の命名ルールでファイルを作成してアップしてください。
module_loop_★ポストタイプ名★.php
*/

/*-------------------------------------------*/
/*  フッターのウィジェットエリアの数を増やす
/*-------------------------------------------*/
// add_filter('lightning_footer_widget_area_count','lightning_footer_widget_area_count_custom');
// function lightning_footer_widget_area_count_custom($footer_widget_area_count){
//     $footer_widget_area_count = 4; // ← 1~4の半角数字で設定してください。
//     return $footer_widget_area_count;
// }

/*-------------------------------------------*/
/*  <head>タグ内に自分の追加したいタグを追加する
/*-------------------------------------------*/
function add_wp_head_custom(){ ?>
    <meta name="format-detection" content="telephone=no">
    <link href="https://fonts.googleapis.com/css?family=M+PLUS+Rounded+1c:400,700&display=swap&subset=japanese" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600&amp;display=swap" rel="stylesheet">
<?php }
add_action( 'wp_head', 'add_wp_head_custom',1);

/*-------------------------------------------*/
/*  <footer>タグ以下に自分の追加したいタグを追加する
/*-------------------------------------------*/
function add_wp_footer_custom(){ ?>
	<script type="text/javascript" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/common.js"></script>
	<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/slick.min.js"></script>
<?php }
add_action( 'wp_footer', 'add_wp_footer_custom', 1 );

function miki_version_child_stylesheet( $src, $handle ) {
	if ( 'lightning-theme-style' !== $handle ) {
		return $src;
	}

	$stylesheet_path = get_stylesheet_directory() . '/style.css';
	if ( ! is_file( $stylesheet_path ) ) {
		return $src;
	}

	return add_query_arg( 'ver', filemtime( $stylesheet_path ), remove_query_arg( 'ver', $src ) );
}
add_filter( 'style_loader_src', 'miki_version_child_stylesheet', PHP_INT_MAX, 2 );

if ( ! function_exists( 'miki_get_acf_image_url' ) ) {
	function miki_get_acf_image_url( $field_name, $post_id = false ) {
		$image = get_field( $field_name, $post_id );
		if ( is_array( $image ) ) {
			return isset( $image['url'] ) ? $image['url'] : '';
		}
		if ( is_numeric( $image ) ) {
			$url = wp_get_attachment_image_url( $image, 'full' );
			return $url ? $url : '';
		}
		return is_string( $image ) ? $image : '';
	}
}

if ( ! function_exists( 'miki_get_acf_image_alt' ) ) {
	function miki_get_acf_image_alt( $field_name, $post_id = false ) {
		$image = get_field( $field_name, $post_id );
		if ( is_array( $image ) ) {
			return isset( $image['alt'] ) ? $image['alt'] : '';
		}
		if ( is_numeric( $image ) ) {
			return (string) get_post_meta( $image, '_wp_attachment_image_alt', true );
		}
		return '';
	}
}

if ( ! function_exists( 'miki_get_cfs_loop' ) ) {
	function miki_get_cfs_loop( $field_name ) {
		if ( function_exists( 'get_field' ) ) {
			$value = get_field( $field_name );
			if ( is_array( $value ) ) {
				return $value;
			}
		}

		global $cfs;
		if ( ! is_object( $cfs ) || ! method_exists( $cfs, 'get' ) ) {
			return array();
		}
		$value = $cfs->get( $field_name );
		return is_array( $value ) ? $value : array();
	}
}

if ( ! function_exists( 'miki_get_cfs_value' ) ) {
	function miki_get_cfs_value( $field_name, $default = '' ) {
		if ( function_exists( 'get_field' ) ) {
			$value = get_field( $field_name );
			if ( null !== $value && false !== $value ) {
				return $value;
			}
		}

		global $cfs;
		if ( ! is_object( $cfs ) || ! method_exists( $cfs, 'get' ) ) {
			return $default;
		}
		$value = $cfs->get( $field_name );
		return null === $value || false === $value ? $default : $value;
	}
}

if ( ! function_exists( 'miki_choice_value' ) ) {
	function miki_choice_value( $value ) {
		if ( is_array( $value ) ) {
			if ( isset( $value['value'] ) ) {
				return (string) $value['value'];
			}
			return (string) miki_first_array_key( $value );
		}
		return is_scalar( $value ) ? (string) $value : '';
	}
}

if ( ! function_exists( 'miki_array_value' ) ) {
	function miki_array_value( $array, $key, $default = '' ) {
		return is_array( $array ) && array_key_exists( $key, $array ) ? $array[ $key ] : $default;
	}
}

if ( ! function_exists( 'miki_first_array_key' ) ) {
	function miki_first_array_key( $array ) {
		if ( ! is_array( $array ) ) {
			return '';
		}
		foreach ( $array as $key => $value ) {
			return $key;
		}
		return '';
	}
}

if ( ! function_exists( 'miki_get_page_id_by_path' ) ) {
	function miki_get_page_id_by_path( $path ) {
		$page = get_page_by_path( $path );
		return $page instanceof WP_Post ? $page->ID : 0;
	}
}

if ( ! function_exists( 'miki_get_bootstrap_type' ) ) {
	function miki_get_bootstrap_type() {
		return isset( $GLOBALS['bootstrap'] ) ? (string) $GLOBALS['bootstrap'] : '4';
	}
}

/*-------------------------------------------*/
/*  MW WP FORM チェックボックス チェック数バリデート
/*-------------------------------------------*/
if ( class_exists( 'MW_WP_Form_Abstract_Validation_Rule' ) ) {
	class MW_WP_Form_Validation_Rule_MaxCheckBox extends MW_WP_Form_Abstract_Validation_Rule {
		protected $name = 'maxcheckbox';

		public function rule( $key, array $options = array() ) {
			$value = $this->Data->get( $key );
			if ( ! MWF_Functions::is_empty( $value ) ) {
				$max = isset( $options['max'] ) ? $options['max'] : 0;
				$separator = $this->Data->get_separator_value( $key );
				$values = explode( $separator, $value );
				if ( MWF_Functions::is_numeric( $max ) && count( $values ) > $max ) {
					$defaults = array(
						'max' => 0,
						'message' => sprintf( '最大 %d つまで選択可能です', $max )
					);
					$options = array_merge( $defaults, $options );
					return $options['message'];
				}
			}
		}

		public function admin( $key, $value ) {
			$max = '';
			$rule_value = is_array( $value ) && isset( $value[ $this->getName() ] ) ? $value[ $this->getName() ] : array();
			if ( is_array( $rule_value ) && isset( $rule_value['max'] ) ) {
				$max = $rule_value['max'];
			}
			?>
			<table>
				<tr>
					<td>最大チェック数</td>
					<td><input type="text" value="<?php echo esc_attr( $max ); ?>" size="3" name="<?php echo MWF_Config::NAME; ?>[validation][<?php echo $key; ?>][<?php echo esc_attr( $this->getName() ); ?>][max]" /></td>
				</tr>
			</table>
			<?php
		}
	}

	function mwform_validation_rule_maxcheckbox( $validation_rules ) {
		$instance = new MW_WP_Form_Validation_Rule_MaxCheckBox();
		$validation_rules[$instance->getName()] = $instance;
		return $validation_rules;
	}

	add_filter( 'mwform_validation_rules', 'mwform_validation_rule_maxcheckbox' );
}

/*-------------------------------------------*/
/*  MW WP FORM 日本語化
/*-------------------------------------------*/
function my_mwform_inquiry_data_columns_157( $columns ) {
	$columns = array(
        'inquiry_type' => 'お問い合わせ種別',
        'company_name' => '法人名',
		'facility_name' => '施設名',
        'zip_code' => '郵便番号',
        'address_p' => '都道府県',
        'address_l' => '市町村',
		'address_n' => '番地・建物名',
		'tel' => '電話番号',
        'fax' => 'FAX番号',
        'mailaddress' => 'メールアドレス',
        'name' => 'ご担当者名',
		'kana' => 'フリガナ',
        'request_check' => 'ご要望内容',
        'sample_check' => 'サンプルの種類',
        'detail' => 'お問い合わせ内容',
	);
	return $columns;
}
add_filter( 'mwform_inquiry_data_columns-mwf_157', 'my_mwform_inquiry_data_columns_157' );

/*-------------------------------------------*/
/*  MW WP FORM 状態に[対応中]を追加
/*-------------------------------------------*/
function my_mwform_response_statuses( $response_statuses ) {
	$response_statuses = array(
		'not-supported' => esc_html__( 'Not supported', 'mw-wp-form' ),
		'r_s_supported' => esc_html__( 'Supported', 'mw-wp-form' ),
		'r_s_reservation' => esc_html__( 'Reservation', 'mw-wp-form' ),
		'r_s_now_supporting' => '対応中'
	);
	return $response_statuses;
}
add_filter( 'mwform_response_statuses_mwf_200', 'my_mwform_response_statuses' );

/*-------------------------------------------*/
/*  MW WP FORM 問い合わせ一覧の件名を担当者名にする
/*-------------------------------------------*/
function miki_set_mwform_inquiry_title_157( $post_id ) {
	if ( 'mwf_157' !== get_post_type( $post_id ) ) {
		return;
	}

	$name = get_post_meta( $post_id, 'name', true );
	if ( is_array( $name ) ) {
		$name = implode( ' ', array_map( 'strval', $name ) );
	}
	$name = sanitize_text_field( (string) $name );
	if ( '' === $name ) {
		return;
	}

	wp_update_post(
		array(
			'ID'         => $post_id,
			'post_title' => $name,
		)
	);
}
add_action( 'mwform_contact_data_save-mwf_157', 'miki_set_mwform_inquiry_title_157' );

remove_filter('the_content', 'wpautop'); // 記事の自動整形を無効にする
