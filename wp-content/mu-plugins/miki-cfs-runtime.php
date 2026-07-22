<?php
/**
 * Plugin Name: Miki CFS Runtime
 * Description: Restores the site's ACF + CFS field stack and migrates SCF-backed CFS values without a database rollback.
 * Version: 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The original site used ACF for standalone fields and CFS for loop fields.
 * Secure Custom Fields replaced both during the upgrade, so the restored stack
 * must load ACF and CFS together while preventing SCF from loading.
 */
function miki_cfs_stack_files_ready() {
	return is_file( WP_PLUGIN_DIR . '/advanced-custom-fields/acf.php' )
		&& is_file( WP_PLUGIN_DIR . '/custom-field-suite/cfs.php' );
}

function miki_cfs_filter_active_plugins( $plugins ) {
	if ( ! miki_cfs_stack_files_ready() || ! is_array( $plugins ) ) {
		return $plugins;
	}

	$plugins = array_values(
		array_diff(
			$plugins,
			array(
				'secure-custom-fields/acf.php',
				'secure-custom-fields/secure-custom-fields.php',
			)
		)
	);

	foreach ( array( 'advanced-custom-fields/acf.php', 'custom-field-suite/cfs.php' ) as $plugin_file ) {
		if ( ! in_array( $plugin_file, $plugins, true ) ) {
			$plugins[] = $plugin_file;
		}
	}

	return $plugins;
}
add_filter( 'option_active_plugins', 'miki_cfs_filter_active_plugins', PHP_INT_MAX );

/**
 * Persist the same plugin state in the database after the MU-plugin has made
 * both restored plugin directories available.
 */
function miki_cfs_persist_active_plugins() {
	if ( ! miki_cfs_stack_files_ready() ) {
		return;
	}

	remove_filter( 'option_active_plugins', 'miki_cfs_filter_active_plugins', PHP_INT_MAX );
	$stored  = get_option( 'active_plugins', array() );
	$desired = miki_cfs_filter_active_plugins( $stored );
	if ( $desired !== $stored ) {
		update_option( 'active_plugins', $desired );
	}
	add_filter( 'option_active_plugins', 'miki_cfs_filter_active_plugins', PHP_INT_MAX );
}
add_action( 'muplugins_loaded', 'miki_cfs_persist_active_plugins', 1 );

/**
 * Define fields added by the rebuilt employee interview page. The legacy CFS
 * groups already contain header_title and the interview loop, so only the new
 * fields belong in this additional group.
 */
function miki_cfs_extended_interview_fields() {
	$fields  = array();
	$next_id = 1;
	$add     = function ( $name, $label, $type = 'text', $parent_id = 0, $options = array(), $notes = '' ) use ( &$fields, &$next_id ) {
		$id       = $next_id++;
		$fields[] = array(
			'id'        => $id,
			'name'      => $name,
			'label'     => $label,
			'type'      => $type,
			'notes'     => $notes,
			'parent_id' => $parent_id,
			'weight'    => count( $fields ),
			'options'   => $options,
		);
		return $id;
	};

	$text     = array( 'default_value' => '', 'required' => '0' );
	$textarea = array( 'default_value' => '', 'formatting' => 'auto_br', 'required' => '0' );
	$image    = array( 'file_type' => 'image', 'return_value' => 'url', 'required' => '0' );
	$loop     = function ( $row_label, $button_label ) {
		return array(
			'row_display' => '0',
			'row_label'   => $row_label,
			'button_label'=> $button_label,
		);
	};

	$add( 'interview_hero_eyebrow', 'ヒーロー：英字見出し', 'text', 0, array_merge( $text, array( 'default_value' => "EMPLOYEES' VOICES | 社員の声" ) ) );
	$add( 'interview_hero_title', 'ヒーロー：見出し（緑）', 'textarea', 0, $textarea );
	$add( 'interview_hero_accent', 'ヒーロー：見出し（えんじ）', 'textarea', 0, $textarea );
	$add( 'interview_hero_lead', 'ヒーロー：紹介文', 'textarea', 0, $textarea );
	$add( 'interview_profile_image', 'プロフィール写真', 'file', 0, $image, '未設定の場合はインタビュー1件目の写真を表示します。' );
	$add( 'interview_profile_label', 'プロフィール：ラベル', 'text', 0, array_merge( $text, array( 'default_value' => 'PROFILE' ) ) );
	$add( 'interview_profile_name', 'プロフィール：氏名', 'text', 0, $text );
	$add( 'interview_profile_name_en', 'プロフィール：英字表記・職種', 'text', 0, $text );

	$profile_loop = $add( 'interview_profile_meta', 'プロフィール：補足情報', 'loop', 0, $loop( 'プロフィール項目', 'プロフィール項目を追加' ) );
	$add( 'interview_profile_meta_label', 'ラベル', 'text', $profile_loop, $text );
	$add( 'interview_profile_meta_value', '内容', 'textarea', $profile_loop, $textarea );

	$add( 'interview_heading_en', 'インタビュー：英字見出し', 'text', 0, array_merge( $text, array( 'default_value' => 'Interview' ) ) );
	$add( 'interview_heading_ja', 'インタビュー：日本語見出し', 'text', 0, array_merge( $text, array( 'default_value' => '社員インタビュー' ) ) );
	$add( 'interview_schedule_heading_en', '1日の流れ：英字見出し', 'text', 0, array_merge( $text, array( 'default_value' => 'Daily Schedule' ) ) );
	$add( 'interview_schedule_heading_ja', '1日の流れ：日本語見出し', 'text', 0, array_merge( $text, array( 'default_value' => 'ある日のスケジュール' ) ) );
	$add( 'interview_schedule_hide', '1日の流れを非表示にする', 'true_false', 0, array( 'message' => '1日の流れを非表示にする', 'required' => '0' ) );

	$schedule_loop = $add( 'interview_schedule', '1日の流れ', 'loop', 0, $loop( '時刻', '時刻を追加' ) );
	$add( 'interview_schedule_time', '時刻', 'text', $schedule_loop, $text );
	$add( 'interview_schedule_label', '英字ラベル', 'text', $schedule_loop, $text );
	$add( 'interview_schedule_title', '項目名', 'text', $schedule_loop, $text );
	$add( 'interview_schedule_description', '説明', 'textarea', $schedule_loop, $textarea );
	$add( 'interview_schedule_note', '本人コメント（任意）', 'textarea', $schedule_loop, $textarea );
	$add( 'interview_schedule_accent', '時刻の丸印を強調する', 'true_false', $schedule_loop, array( 'message' => '強調する', 'required' => '0' ) );

	$add( 'interview_message_label', 'メッセージ：ラベル', 'text', 0, array_merge( $text, array( 'default_value' => '— Message —' ) ) );
	$add( 'interview_message_title', 'メッセージ：見出し', 'textarea', 0, $textarea );
	$add( 'interview_message_body', 'メッセージ：本文', 'textarea', 0, $textarea );
	$add( 'interview_message_signature', 'メッセージ：署名', 'text', 0, $text );
	$add( 'interview_cta_heading', '採用導線：見出し', 'text', 0, array_merge( $text, array( 'default_value' => 'もっと、三基食品で働く人を知る。' ) ) );
	$add( 'interview_cta_body', '採用導線：本文', 'textarea', 0, $textarea );

	$cta_loop = $add( 'interview_cta_links', '採用導線：ボタン', 'loop', 0, $loop( 'ボタン', 'ボタンを追加' ) );
	$add( 'interview_cta_link_label', 'ボタン文言', 'text', $cta_loop, $text );
	$add( 'interview_cta_link_url', 'リンク先', 'text', $cta_loop, $text );
	$add(
		'interview_cta_link_style',
		'表示スタイル',
		'select',
		$cta_loop,
		array(
			'choices'     => array( 'primary' => 'ゴールド', 'ghost' => '白枠' ),
			'multiple'    => '0',
			'select2'     => '0',
			'force_single'=> '1',
			'required'    => '0',
		)
	);

	return $fields;
}

function miki_cfs_install_extended_interview_group() {
	if ( ! function_exists( 'CFS' ) || ! is_object( CFS()->field_group ) ) {
		return;
	}

	$group = get_page_by_path( 'miki-recruit-interview-extended', OBJECT, 'cfs' );
	if ( $group instanceof WP_Post ) {
		return;
	}

	CFS()->field_group->import(
		array(
			'import_code' => array(
				array(
					'post_title' => '採用情報 - 社員インタビュー（追加項目）',
					'post_name'  => 'miki-recruit-interview-extended',
					'cfs_fields' => miki_cfs_extended_interview_fields(),
					'cfs_rules'  => array(
						'page_templates' => array(
							'operator' => '==',
							'values'   => array( 'page-recruit-detail.php' ),
						),
					),
					'cfs_extras' => array(
						'order'       => 110,
						'context'     => 'normal',
						'hide_editor' => '1',
					),
				),
			),
		)
	);
	CFS()->field_group->cache = array();
}
add_action( 'cfs_init', 'miki_cfs_install_extended_interview_group', 20 );

/**
 * Add an editable text department field to the existing employee-list loop.
 */
function miki_cfs_install_employee_department_field() {
	if ( ! function_exists( 'CFS' ) || 1 <= (int) get_option( 'miki_cfs_employee_list_department_field_version', 0 ) ) {
		return;
	}

	$group_ids = get_posts(
		array(
			'post_type'      => 'cfs',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);
	$installed = false;

	foreach ( $group_ids as $group_id ) {
		$fields = get_post_meta( $group_id, 'cfs_fields', true );
		if ( ! is_array( $fields ) ) {
			continue;
		}

		$loop_id   = 0;
		$max_id    = 0;
		$has_field = false;
		foreach ( $fields as $field ) {
			$field_id = (int) miki_array_value( $field, 'id' );
			$max_id   = max( $max_id, $field_id );
			if ( 'employee_list' === (string) miki_array_value( $field, 'name' ) && 'loop' === (string) miki_array_value( $field, 'type' ) ) {
				$loop_id = $field_id;
			}
		}
		if ( ! $loop_id ) {
			continue;
		}

		foreach ( $fields as $field ) {
			if ( 'employee_department' === (string) miki_array_value( $field, 'name' ) && $loop_id === (int) miki_array_value( $field, 'parent_id' ) ) {
				$has_field = true;
				break;
			}
		}

		if ( ! $has_field ) {
			$fields[] = array(
				'id'        => $max_id + 1,
				'name'      => 'employee_department',
				'label'     => '所属（一覧表示・文字）',
				'type'      => 'textarea',
				'notes'     => '社員の声一覧に表示します。',
				'parent_id' => $loop_id,
				'weight'    => count( $fields ),
				'options'   => array( 'default_value' => '', 'formatting' => 'auto_br', 'required' => '0' ),
			);
			update_post_meta( $group_id, 'cfs_fields', $fields );
		}
		$installed = true;
	}

	if ( $installed ) {
		if ( is_object( CFS()->field_group ) ) {
			CFS()->field_group->cache = array();
		}
		update_option( 'miki_cfs_employee_list_department_field_version', 1 );
	}
}
add_action( 'cfs_init', 'miki_cfs_install_employee_department_field', 25 );

/**
 * Keep saved loop data untouched while making every CFS row collapsed on load.
 */
function miki_cfs_collapse_loop_rows_by_default() {
	if ( ! function_exists( 'CFS' ) || 1 <= (int) get_option( 'miki_cfs_collapsed_loop_version', 0 ) ) {
		return;
	}

	$group_ids = get_posts(
		array(
			'post_type'      => 'cfs',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);

	foreach ( $group_ids as $group_id ) {
		$fields  = get_post_meta( $group_id, 'cfs_fields', true );
		$changed = false;
		if ( ! is_array( $fields ) ) {
			continue;
		}

		foreach ( $fields as &$field ) {
			if ( ! is_array( $field ) || 'loop' !== miki_array_value( $field, 'type' ) ) {
				continue;
			}
			if ( ! isset( $field['options'] ) || ! is_array( $field['options'] ) ) {
				$field['options'] = array();
			}
			if ( '0' !== (string) miki_array_value( $field['options'], 'row_display' ) ) {
				$field['options']['row_display'] = '0';
				$changed                         = true;
			}
		}
		unset( $field );

		if ( $changed ) {
			update_post_meta( $group_id, 'cfs_fields', $fields );
		}
	}

	if ( is_object( CFS()->field_group ) ) {
		CFS()->field_group->cache = array();
	}
	update_option( 'miki_cfs_collapsed_loop_version', 1 );
}
add_action( 'cfs_init', 'miki_cfs_collapse_loop_rows_by_default', 30 );

function miki_cfs_add_raw_scalars( $post_id, $names, &$data ) {
	foreach ( $names as $name ) {
		if ( metadata_exists( 'post', $post_id, $name ) ) {
			$data[ $name ] = get_post_meta( $post_id, $name, true );
		}
	}
}

function miki_cfs_read_raw_repeater( $post_id, $name, $sub_fields ) {
	if ( ! metadata_exists( 'post', $post_id, $name ) ) {
		return null;
	}

	$count = max( 0, (int) get_post_meta( $post_id, $name, true ) );
	$rows  = array();
	for ( $row_index = 0; $row_index < $count; $row_index++ ) {
		$row = array();
		foreach ( $sub_fields as $sub_field ) {
			$meta_key          = $name . '_' . $row_index . '_' . $sub_field;
			$row[ $sub_field ] = get_post_meta( $post_id, $meta_key, true );
		}
		$rows[] = $row;
	}

	return $rows;
}

function miki_cfs_add_raw_repeater( $post_id, $name, $sub_fields, &$data ) {
	$rows = miki_cfs_read_raw_repeater( $post_id, $name, $sub_fields );
	if ( null !== $rows ) {
		$data[ $name ] = $rows;
	}
}

function miki_cfs_collect_scf_data( $post_id, $template ) {
	$data = array();

	if ( 'front-page.php' === $template ) {
		miki_cfs_add_raw_scalars( $post_id, array( 'notice_new_time', 'notice_display_count' ), $data );
	}

	if ( 'page-product.php' === $template ) {
		miki_cfs_add_raw_repeater( $post_id, 'nishinomiya_loop', array( 'nishinomiya_name', 'nishinomiya_img', 'nishinomiya_about', 'nishinomiya_url' ), $data );
		miki_cfs_add_raw_scalars( $post_id, array( 'cosme_html' ), $data );
		miki_cfs_add_raw_repeater( $post_id, 'sannan_loop', array( 'sannan_name', 'sannan_img', 'sannan_about', 'sannan_url' ), $data );
	}

	if ( 'page-recruit.php' === $template ) {
		miki_cfs_add_raw_repeater( $post_id, 'employee_list', array( 'employee_img', 'employee_belongs', 'employee_department', 'employee_name', 'employee_catch', 'employee_link' ), $data );
	}

	if ( 'page-recruit-detail.php' === $template ) {
		miki_cfs_add_raw_scalars( $post_id, array( 'header_title' ), $data );
		miki_cfs_add_raw_repeater( $post_id, 'interview', array( 'interrupt', 'question', 'answer', 'img', 'img-detail', 'bg', 'position' ), $data );
		miki_cfs_add_raw_scalars(
			$post_id,
			array(
				'interview_hero_eyebrow', 'interview_hero_title', 'interview_hero_accent', 'interview_hero_lead',
				'interview_profile_image', 'interview_profile_label', 'interview_profile_name', 'interview_profile_name_en',
				'interview_heading_en', 'interview_heading_ja', 'interview_schedule_heading_en', 'interview_schedule_heading_ja',
				'interview_schedule_hide', 'interview_message_label', 'interview_message_title', 'interview_message_body',
				'interview_message_signature', 'interview_cta_heading', 'interview_cta_body',
			),
			$data
		);
		miki_cfs_add_raw_repeater( $post_id, 'interview_profile_meta', array( 'interview_profile_meta_label', 'interview_profile_meta_value' ), $data );
		miki_cfs_add_raw_repeater( $post_id, 'interview_schedule', array( 'interview_schedule_time', 'interview_schedule_label', 'interview_schedule_title', 'interview_schedule_description', 'interview_schedule_note', 'interview_schedule_accent' ), $data );
		miki_cfs_add_raw_repeater( $post_id, 'interview_cta_links', array( 'interview_cta_link_label', 'interview_cta_link_url', 'interview_cta_link_style' ), $data );
	}

	return $data;
}

/**
 * Persist the Kitamura page's rendered fallback copy into CFS so every visible
 * heading, profile item, schedule item, message, and CTA can be edited there.
 */
function miki_cfs_seed_kitamura_defaults() {
	$page = get_page_by_path( 'recruit/kitamura' );
	if ( ! $page instanceof WP_Post ) {
		return array( 'kitamura:not-found' );
	}

	$post_id = $page->ID;
	CFS()->api->cache[ $post_id ] = null;
	$current = CFS()->get( false, $post_id, array( 'format' => 'raw' ) );
	$current = is_array( $current ) ? $current : array();

	$defaults = array(
		'interview_hero_eyebrow'             => "EMPLOYEES' VOICES | 社員の声",
		'interview_hero_title'               => "大手企業の安定より\n中小企業での",
		'interview_hero_accent'              => 'やりがいを求めて。',
		'interview_hero_lead'                => "大手企業から三基食品へ転職し、製造現場の第一線で活躍する喜多村さん。\n自分の意見を活かせる環境、仲間と連携して製品をつくる面白さ、\nそして人を育てる責任について話を聞きました。",
		'interview_profile_label'            => 'PROFILE',
		'interview_profile_name'             => '喜多村さん',
		'interview_profile_name_en'          => 'KITAMURA / Production',
		'interview_heading_en'               => 'Interview',
		'interview_heading_ja'               => '社員インタビュー',
		'interview_schedule_heading_en'      => 'Daily Schedule',
		'interview_schedule_heading_ja'      => 'ある日のスケジュール',
		'interview_schedule_hide'            => '0',
		'interview_message_label'            => '— Message —',
		'interview_message_title'            => "「健康・安全・自然」を、\n毎日の手のひらから。",
		'interview_message_body'             => "特別な一日ではなく、ふつうの一日。\nその積み重ねが、三基食品の商品をつくっています。\nあなたの「ふつうの一日」も、ここで始まるかもしれません。",
		'interview_message_signature'        => '— Kitamura',
		'interview_cta_heading'              => 'もっと、三基食品で働く人を知る。',
		'interview_cta_body'                 => '他の社員のインタビューや、新卒・中途採用の最新情報をご覧いただけます。あなたの“一日”を、私たちと一緒に。',
		'interview_profile_meta'             => array(
			array( 'interview_profile_meta_label' => 'Department', 'interview_profile_meta_value' => '山南工場 製造課' ),
			array( 'interview_profile_meta_label' => 'Joined', 'interview_profile_meta_value' => '2019年入社' ),
			array( 'interview_profile_meta_label' => 'Hobby', 'interview_profile_meta_value' => '休日は自家製パンづくり、たまに山歩き' ),
			array( 'interview_profile_meta_label' => 'Motto', 'interview_profile_meta_value' => '「ていねいに、たのしく」' ),
		),
		'interview_schedule'                 => array(
			array(
				'interview_schedule_time' => '8:30', 'interview_schedule_label' => 'BRIEFING', 'interview_schedule_title' => '朝礼・チームミーティング',
				'interview_schedule_description' => '製造部のメンバーで、その日の生産計画と注意点を共有。新人さんへのフォローやライン替えのタイミングも、このときに細かくすり合わせます。',
				'interview_schedule_note' => '声をかけ合うことが品質につながる。チームの空気を作る、大事な10分です。', 'interview_schedule_accent' => '1',
			),
			array(
				'interview_schedule_time' => '9:00', 'interview_schedule_label' => 'PRODUCTION', 'interview_schedule_title' => '製造ラインへ',
				'interview_schedule_description' => '原料投入から包装まで、ラインの流れを見守りながら品質をチェック。機械の音やコンベアのリズムに耳を澄まして、わずかな違和感も逃さないように。',
				'interview_schedule_note' => '', 'interview_schedule_accent' => '0',
			),
			array(
				'interview_schedule_time' => '11:00', 'interview_schedule_label' => 'CHECK', 'interview_schedule_title' => '品質確認・記録',
				'interview_schedule_description' => 'サンプルを取って、品質管理チームと一緒に確認。日々のデータが、安心しておいしく食べてもらえる商品づくりの土台になります。',
				'interview_schedule_note' => '数字の小さな変化が、お客様の食卓につながっている。記録は未来への手紙のようなものです。', 'interview_schedule_accent' => '0',
			),
			array(
				'interview_schedule_time' => '12:00', 'interview_schedule_label' => 'LUNCH', 'interview_schedule_title' => 'お昼休み',
				'interview_schedule_description' => '食堂で同期や先輩とごはん。仕事の話半分、休日の話半分。趣味のパンを差し入れすると、思いのほか喜んでもらえてうれしい瞬間です。',
				'interview_schedule_note' => '', 'interview_schedule_accent' => '0',
			),
			array(
				'interview_schedule_time' => '13:00', 'interview_schedule_label' => 'PRODUCTION', 'interview_schedule_title' => '午後の製造・新人フォロー',
				'interview_schedule_description' => '午後は別のラインに入りつつ、後輩のサポート。先輩から教わってきたことを、自分の言葉で伝えるよう心がけています。',
				'interview_schedule_note' => '「教える」ことは、自分の仕事を見直す時間でもあります。', 'interview_schedule_accent' => '1',
			),
			array(
				'interview_schedule_time' => '17:00', 'interview_schedule_label' => 'EVENING', 'interview_schedule_title' => '退社・帰り道',
				'interview_schedule_description' => '明日の段取りを軽くメモして、退社。鳴尾浜の夕焼けを見ながら帰る道は、一日のごほうびのような時間です。',
				'interview_schedule_note' => '今日もいいものが作れた。そう思えた日は、足取りが少し軽くなります。', 'interview_schedule_accent' => '0',
			),
		),
		'interview_cta_links'                => array(
			array( 'interview_cta_link_label' => '他の社員紹介を見る', 'interview_cta_link_url' => home_url( '/recruit/voices/' ), 'interview_cta_link_style' => 'primary' ),
			array( 'interview_cta_link_label' => '採用エントリー', 'interview_cta_link_url' => home_url( '/contact/#recruit' ), 'interview_cta_link_style' => 'ghost' ),
		),
	);

	if ( empty( $current['interview_profile_image'] ) && ! empty( $current['interview'] ) ) {
		foreach ( (array) $current['interview'] as $interview_row ) {
			if ( ! empty( $interview_row['img'] ) ) {
				$defaults['interview_profile_image'] = $interview_row['img'];
				break;
			}
		}
	}

	$seed = array();
	foreach ( $defaults as $field_name => $default_value ) {
		if ( ! array_key_exists( $field_name, $current ) || '' === $current[ $field_name ] || array() === $current[ $field_name ] ) {
			$seed[ $field_name ] = $default_value;
		}
	}

	if ( empty( $seed ) ) {
		return array();
	}

	CFS()->save( $seed, array( 'ID' => $post_id ), array( 'format' => 'api' ) );
	CFS()->api->cache[ $post_id ] = null;
	$stored = CFS()->get( false, $post_id, array( 'format' => 'raw' ) );
	$errors = array();
	foreach ( $seed as $field_name => $expected ) {
		if ( ! array_key_exists( $field_name, $stored ) ) {
			$errors[] = $post_id . ':' . $field_name;
		} elseif ( is_array( $expected ) && count( (array) $stored[ $field_name ] ) !== count( $expected ) ) {
			$errors[] = $post_id . ':' . $field_name;
		}
	}

	return $errors;
}

/**
 * Copy the most recent SCF/ACF-compatible postmeta into CFS storage. This keeps
 * edits made after the 2026-07-22 migration without restoring an older DB dump.
 */
function miki_cfs_migrate_scf_values() {
	if ( ! function_exists( 'CFS' ) || 2 <= (int) get_option( 'miki_cfs_cutover_version', 0 ) ) {
		return;
	}

	$templates = array( 'front-page.php', 'page-product.php', 'page-recruit.php', 'page-recruit-detail.php' );
	$failures  = array();
	$migrated  = 0;

	foreach ( $templates as $template ) {
		$post_ids = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => array( 'publish', 'draft', 'pending', 'private' ),
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_key'       => '_wp_page_template',
				'meta_value'     => $template,
			)
		);

		foreach ( $post_ids as $post_id ) {
			$data = miki_cfs_collect_scf_data( $post_id, $template );
			if ( empty( $data ) ) {
				continue;
			}

			CFS()->save( $data, array( 'ID' => $post_id ), array( 'format' => 'api' ) );
			CFS()->api->cache[ $post_id ] = null;
			$raw = CFS()->get( false, $post_id, array( 'format' => 'raw' ) );

			foreach ( $data as $field_name => $expected ) {
				if ( is_array( $expected ) && ! empty( $expected ) ) {
					if ( ! isset( $raw[ $field_name ] ) || count( (array) $raw[ $field_name ] ) !== count( $expected ) ) {
						$failures[] = $post_id . ':' . $field_name;
					}
				} elseif ( ! is_array( $expected ) && '' !== (string) $expected ) {
					if ( ! array_key_exists( $field_name, $raw ) || (string) $raw[ $field_name ] !== (string) $expected ) {
						$failures[] = $post_id . ':' . $field_name;
					}
				}
			}

			$migrated++;
		}
	}

	$failures = array_merge( $failures, miki_cfs_seed_kitamura_defaults() );

	if ( empty( $failures ) ) {
		update_option( 'miki_cfs_cutover_version', 2 );
		update_option(
			'miki_cfs_cutover_result',
			array(
				'migrated_posts' => $migrated,
				'completed_at'   => current_time( 'mysql' ),
			)
		);
	} else {
		error_log( 'Miki CFS cutover validation failed: ' . implode( ', ', array_unique( $failures ) ) );
	}
}
add_action( 'init', 'miki_cfs_migrate_scf_values', 30 );

/**
 * Add editable department text to every employee detail page. The original
 * employee loop stored this copy inside a speech-bubble image, so the list
 * page could not present it cleanly as accessible text.
 */
function miki_cfs_seed_employee_departments() {
	if ( ! function_exists( 'CFS' ) || 1 <= (int) get_option( 'miki_cfs_employee_department_version', 0 ) ) {
		return;
	}

	$departments = array(
		'kitamura'     => '山南工場 製造課',
		'adachi'       => "西宮工場 製造課\n山南工場 製造課",
		'matsushita_m' => "品質管理グループ\n食品課",
		'matsushita_k' => '西宮工場 包装１課',
		'maeda_a'      => '総務部人事課',
		'ofiji'        => "品質管理グループ\n食品課",
	);
	$failures    = array();

	foreach ( $departments as $slug => $department ) {
		$page = get_page_by_path( 'recruit/' . $slug );
		if ( ! $page instanceof WP_Post ) {
			$failures[] = $slug . ':not-found';
			continue;
		}

		$post_id = $page->ID;
		CFS()->api->cache[ $post_id ] = null;
		$current = CFS()->get( false, $post_id, array( 'format' => 'raw' ) );
		$rows    = isset( $current['interview_profile_meta'] ) && is_array( $current['interview_profile_meta'] )
			? $current['interview_profile_meta']
			: array();
		$found   = false;

		foreach ( $rows as $row ) {
			$label = (string) miki_array_value( $row, 'interview_profile_meta_label' );
			if ( preg_match( '/department|所属|部署/iu', $label ) ) {
				$found = true;
				break;
			}
		}

		if ( ! $found ) {
			array_unshift(
				$rows,
				array(
					'interview_profile_meta_label' => 'Department',
					'interview_profile_meta_value' => $department,
				)
			);
			CFS()->save( array( 'interview_profile_meta' => $rows ), array( 'ID' => $post_id ), array( 'format' => 'api' ) );
			CFS()->api->cache[ $post_id ] = null;
		}

		$stored     = CFS()->get( false, $post_id, array( 'format' => 'raw' ) );
		$stored_rows = isset( $stored['interview_profile_meta'] ) && is_array( $stored['interview_profile_meta'] )
			? $stored['interview_profile_meta']
			: array();
		$verified    = false;
		foreach ( $stored_rows as $stored_row ) {
			$label = (string) miki_array_value( $stored_row, 'interview_profile_meta_label' );
			if ( preg_match( '/department|所属|部署/iu', $label ) && '' !== trim( (string) miki_array_value( $stored_row, 'interview_profile_meta_value' ) ) ) {
				$verified = true;
				break;
			}
		}
		if ( ! $verified ) {
			$failures[] = $slug . ':department';
		}
	}

	if ( empty( $failures ) ) {
		update_option( 'miki_cfs_employee_department_version', 1 );
	} else {
		error_log( 'Miki employee department seed failed: ' . implode( ', ', array_unique( $failures ) ) );
	}
}
add_action( 'init', 'miki_cfs_seed_employee_departments', 35 );

/**
 * Seed the new employee-list department field from the legacy bubble copy.
 */
function miki_cfs_seed_employee_list_departments() {
	if ( ! function_exists( 'CFS' ) || 1 <= (int) get_option( 'miki_cfs_employee_list_department_version', 0 ) ) {
		return;
	}

	$page = get_page_by_path( 'recruit' );
	if ( ! $page instanceof WP_Post ) {
		return;
	}

	$departments = array(
		'kitamura'     => '山南工場 製造課',
		'adachi'       => "西宮工場 製造課\n山南工場 製造課",
		'matsushita_m' => "品質管理グループ\n食品課",
		'matsushita_k' => '西宮工場 包装１課',
		'maeda_a'      => '総務部人事課',
		'ofiji'        => "品質管理グループ\n食品課",
	);
	$post_id     = $page->ID;
	CFS()->api->cache[ $post_id ] = null;
	$current = CFS()->get( false, $post_id, array( 'format' => 'raw' ) );
	$rows    = isset( $current['employee_list'] ) && is_array( $current['employee_list'] )
		? $current['employee_list']
		: array();

	if ( empty( $rows ) ) {
		return;
	}

	$changed = false;
	foreach ( $rows as &$row ) {
		$link = trim( (string) miki_array_value( $row, 'employee_link' ) );
		$path = wp_parse_url( $link, PHP_URL_PATH );
		if ( is_string( $path ) && '' !== $path ) {
			$link = $path;
		}
		$slug = sanitize_title( basename( untrailingslashit( $link ) ) );
		if ( '' === trim( (string) miki_array_value( $row, 'employee_department' ) ) && isset( $departments[ $slug ] ) ) {
			$row['employee_department'] = $departments[ $slug ];
			$changed                    = true;
		}
	}
	unset( $row );

	if ( $changed ) {
		CFS()->save( array( 'employee_list' => $rows ), array( 'ID' => $post_id ), array( 'format' => 'api' ) );
		CFS()->api->cache[ $post_id ] = null;
	}

	$stored      = CFS()->get( false, $post_id, array( 'format' => 'raw' ) );
	$stored_rows = isset( $stored['employee_list'] ) && is_array( $stored['employee_list'] )
		? $stored['employee_list']
		: array();
	$verified    = ! empty( $stored_rows );
	foreach ( $stored_rows as $stored_row ) {
		$link = trim( (string) miki_array_value( $stored_row, 'employee_link' ) );
		$path = wp_parse_url( $link, PHP_URL_PATH );
		if ( is_string( $path ) && '' !== $path ) {
			$link = $path;
		}
		$slug = sanitize_title( basename( untrailingslashit( $link ) ) );
		if ( isset( $departments[ $slug ] ) && '' === trim( (string) miki_array_value( $stored_row, 'employee_department' ) ) ) {
			$verified = false;
			break;
		}
	}

	if ( $verified ) {
		update_option( 'miki_cfs_employee_list_department_version', 1 );
	}
}
add_action( 'init', 'miki_cfs_seed_employee_list_departments', 36 );
