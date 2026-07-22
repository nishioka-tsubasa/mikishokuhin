<?php
/**
 * Plugin Name: Miki Site Fields
 * Description: Defines the site-specific fields migrated from Custom Field Suite.
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register site fields after Secure Custom Fields has initialized.
 */
function miki_register_site_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'                   => 'group_miki_home_settings',
			'title'                 => 'トップページ - お知らせ設定',
			'fields'                => array(
				array(
					'key'   => 'field_miki_notice_new_time',
					'label' => 'NEWをつける日数',
					'name'  => 'notice_new_time',
					'type'  => 'number',
					'min'   => 0,
					'step'  => 1,
				),
				array(
					'key'   => 'field_miki_notice_display_count',
					'label' => '一覧に表示する数',
					'name'  => 'notice_display_count',
					'type'  => 'number',
					'min'   => 1,
					'step'  => 1,
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'front-page.php',
					),
				),
			),
			'menu_order'            => 100,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => array( 'the_content' ),
			'active'                => true,
			'show_in_rest'          => 0,
		)
	);

	$product_fields = array(
		array(
			'key'          => 'field_miki_nishinomiya_loop',
			'label'        => '西宮工場 取扱商品',
			'name'         => 'nishinomiya_loop',
			'type'         => 'repeater',
			'layout'       => 'block',
			'button_label' => '取扱商品を追加',
			'sub_fields'   => array(
				miki_site_field_text( 'nishinomiya_name', '商品名' ),
				miki_site_field_image( 'nishinomiya_img', '商品画像', '画像サイズ（横x縦）：700px x 700px' ),
				miki_site_field_textarea( 'nishinomiya_about', '商品説明文' ),
				miki_site_field_url( 'nishinomiya_url', '商品へのリンク' ),
			),
		),
		array(
			'key'          => 'field_miki_cosme_html',
			'label'        => '化粧品やヘアケア商品の説明',
			'name'         => 'cosme_html',
			'type'         => 'wysiwyg',
			'tabs'         => 'all',
			'toolbar'      => 'full',
			'media_upload' => 1,
		),
		array(
			'key'          => 'field_miki_sannan_loop',
			'label'        => '山南工場 取扱商品',
			'name'         => 'sannan_loop',
			'type'         => 'repeater',
			'layout'       => 'block',
			'button_label' => '取扱商品を追加',
			'sub_fields'   => array(
				miki_site_field_text( 'sannan_name', '商品名' ),
				miki_site_field_image( 'sannan_img', '商品画像', '画像サイズ（横x縦）：700px x 700px' ),
				miki_site_field_textarea( 'sannan_about', '商品説明文' ),
				miki_site_field_url( 'sannan_url', '商品へのリンク' ),
			),
		),
	);
	miki_add_template_field_group( 'group_miki_product', '商品一覧', $product_fields, 'page-product.php', true );

	$interview_fields = array(
		array(
			'key'          => 'field_miki_header_title',
			'label'        => '旧ヘッダータイトル部分（既存データ互換）',
			'name'         => 'header_title',
			'type'         => 'wysiwyg',
			'tabs'         => 'all',
			'toolbar'      => 'full',
			'media_upload' => 1,
		),
		array(
			'key'           => 'field_miki_interview_hero_eyebrow',
			'label'         => 'ヒーロー：英字見出し',
			'name'          => 'interview_hero_eyebrow',
			'type'          => 'text',
			'default_value' => "EMPLOYEES' VOICES | 社員の声",
		),
		miki_site_field_textarea( 'interview_hero_title', 'ヒーロー：見出し（緑）' ),
		miki_site_field_textarea( 'interview_hero_accent', 'ヒーロー：見出し（えんじ）' ),
		miki_site_field_textarea( 'interview_hero_lead', 'ヒーロー：紹介文' ),
		miki_site_field_image( 'interview_profile_image', 'プロフィール写真', '未設定の場合はインタビュー1件目の写真を表示します。' ),
		array(
			'key'           => 'field_miki_interview_profile_label',
			'label'         => 'プロフィール：ラベル',
			'name'          => 'interview_profile_label',
			'type'          => 'text',
			'default_value' => 'PROFILE',
		),
		miki_site_field_text( 'interview_profile_name', 'プロフィール：氏名' ),
		miki_site_field_text( 'interview_profile_name_en', 'プロフィール：英字表記・職種' ),
		array(
			'key'          => 'field_miki_interview_profile_meta',
			'label'        => 'プロフィール：補足情報',
			'name'         => 'interview_profile_meta',
			'type'         => 'repeater',
			'layout'       => 'table',
			'button_label' => 'プロフィール項目を追加',
			'sub_fields'   => array(
				miki_site_field_text( 'interview_profile_meta_label', 'ラベル' ),
				miki_site_field_textarea( 'interview_profile_meta_value', '内容' ),
			),
		),
		array(
			'key'           => 'field_miki_interview_heading_en',
			'label'         => 'インタビュー：英字見出し',
			'name'          => 'interview_heading_en',
			'type'          => 'text',
			'default_value' => 'Interview',
		),
		array(
			'key'           => 'field_miki_interview_heading_ja',
			'label'         => 'インタビュー：日本語見出し',
			'name'          => 'interview_heading_ja',
			'type'          => 'text',
			'default_value' => '社員インタビュー',
		),
		array(
			'key'          => 'field_miki_interview',
			'label'        => 'インタビュー',
			'name'         => 'interview',
			'type'         => 'repeater',
			'layout'       => 'block',
			'button_label' => 'ブロック追加',
			'sub_fields'   => array(
				miki_site_field_textarea( 'interrupt', '割り込み' ),
				miki_site_field_textarea( 'question', '質問文' ),
				array(
					'key'          => 'field_miki_answer',
					'label'        => '回答',
					'name'         => 'answer',
					'type'         => 'wysiwyg',
					'tabs'         => 'all',
					'toolbar'      => 'full',
					'media_upload' => 1,
				),
				miki_site_field_image( 'img', '画像', '横長：456px x 307px、縦長：350px x 470px' ),
				array(
					'key'          => 'field_miki_img_detail',
					'label'        => '画像説明',
					'name'         => 'img-detail',
					'type'         => 'wysiwyg',
					'tabs'         => 'all',
					'toolbar'      => 'full',
					'media_upload' => 1,
				),
				array(
					'key'           => 'field_miki_bg',
					'label'         => '背景',
					'name'          => 'bg',
					'type'          => 'select',
					'choices'       => array(
						'standard'       => '標準',
						'white'          => '白',
						'standard-image' => '標準+柄',
						'image'          => '白+柄',
					),
					'return_format' => 'value',
				),
				array(
					'key'           => 'field_miki_position',
					'label'         => '配置',
					'name'          => 'position',
					'type'          => 'select',
					'choices'       => array(
						'text_l' => 'テキスト左、画像右',
						'text_r' => 'テキスト右、画像左',
					),
					'return_format' => 'value',
				),
			),
		),
		array(
			'key'           => 'field_miki_interview_schedule_heading_en',
			'label'         => '1日の流れ：英字見出し',
			'name'          => 'interview_schedule_heading_en',
			'type'          => 'text',
			'default_value' => 'Daily Schedule',
		),
		array(
			'key'           => 'field_miki_interview_schedule_heading_ja',
			'label'         => '1日の流れ：日本語見出し',
			'name'          => 'interview_schedule_heading_ja',
			'type'          => 'text',
			'default_value' => 'ある日のスケジュール',
		),
		array(
			'key'           => 'field_miki_interview_schedule_hide',
			'label'         => '1日の流れを非表示にする',
			'name'          => 'interview_schedule_hide',
			'type'          => 'true_false',
			'ui'            => 1,
			'default_value' => 0,
		),
		array(
			'key'          => 'field_miki_interview_schedule',
			'label'        => '1日の流れ',
			'name'         => 'interview_schedule',
			'type'         => 'repeater',
			'layout'       => 'block',
			'button_label' => '時刻を追加',
			'sub_fields'   => array(
				miki_site_field_text( 'interview_schedule_time', '時刻' ),
				miki_site_field_text( 'interview_schedule_label', '英字ラベル' ),
				miki_site_field_text( 'interview_schedule_title', '項目名' ),
				miki_site_field_textarea( 'interview_schedule_description', '説明' ),
				miki_site_field_textarea( 'interview_schedule_note', '本人コメント（任意）' ),
				array(
					'key'           => 'field_miki_interview_schedule_accent',
					'label'         => '時刻の丸印を強調する',
					'name'          => 'interview_schedule_accent',
					'type'          => 'true_false',
					'ui'            => 1,
					'default_value' => 0,
				),
			),
		),
		array(
			'key'           => 'field_miki_interview_message_label',
			'label'         => 'メッセージ：ラベル',
			'name'          => 'interview_message_label',
			'type'          => 'text',
			'default_value' => '— Message —',
		),
		miki_site_field_textarea( 'interview_message_title', 'メッセージ：見出し' ),
		miki_site_field_textarea( 'interview_message_body', 'メッセージ：本文' ),
		miki_site_field_text( 'interview_message_signature', 'メッセージ：署名' ),
		array(
			'key'           => 'field_miki_interview_cta_heading',
			'label'         => '採用導線：見出し',
			'name'          => 'interview_cta_heading',
			'type'          => 'text',
			'default_value' => 'もっと、三基食品で働く人を知る。',
		),
		miki_site_field_textarea( 'interview_cta_body', '採用導線：本文' ),
		array(
			'key'          => 'field_miki_interview_cta_links',
			'label'        => '採用導線：ボタン',
			'name'         => 'interview_cta_links',
			'type'         => 'repeater',
			'layout'       => 'table',
			'button_label' => 'ボタンを追加',
			'sub_fields'   => array(
				miki_site_field_text( 'interview_cta_link_label', 'ボタン文言' ),
				miki_site_field_url( 'interview_cta_link_url', 'リンク先' ),
				array(
					'key'           => 'field_miki_interview_cta_link_style',
					'label'         => '表示スタイル',
					'name'          => 'interview_cta_link_style',
					'type'          => 'select',
					'choices'       => array(
						'primary' => 'ゴールド',
						'ghost'   => '白枠',
					),
					'default_value' => 'primary',
					'return_format' => 'value',
				),
			),
		),
	);
	miki_add_template_field_group( 'group_miki_interview', '採用情報 - 社員インタビュー', $interview_fields, 'page-recruit-detail.php', true );

	$employee_fields = array(
		array(
			'key'          => 'field_miki_employee_list',
			'label'        => '社員一覧',
			'name'         => 'employee_list',
			'type'         => 'repeater',
			'layout'       => 'block',
			'button_label' => '社員を追加する',
			'sub_fields'   => array(
				miki_site_field_image( 'employee_img', '社員写真' ),
				miki_site_field_image( 'employee_belongs', '所属画像' ),
				miki_site_field_textarea( 'employee_name', '社員名' ),
				miki_site_field_textarea( 'employee_catch', '社員キャッチコピー' ),
				miki_site_field_text( 'employee_link', '詳細ページリンク' ),
			),
		),
	);
	miki_add_template_field_group( 'group_miki_employee_list', '採用情報 - 社員の声', $employee_fields, 'page-recruit.php', false );
}
add_action( 'acf/init', 'miki_register_site_fields' );

/**
 * Register a field group assigned to a page template.
 */
function miki_add_template_field_group( $key, $title, $fields, $template, $hide_editor ) {
	acf_add_local_field_group(
		array(
			'key'                   => $key,
			'title'                 => $title,
			'fields'                => $fields,
			'location'              => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => $template,
					),
				),
			),
			'menu_order'            => 100,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => $hide_editor ? array( 'the_content' ) : array(),
			'active'                => true,
			'show_in_rest'          => 0,
		)
	);
}

/**
 * Build a text field with a stable key.
 */
function miki_site_field_text( $name, $label ) {
	return array(
		'key'   => 'field_miki_' . str_replace( '-', '_', $name ),
		'label' => $label,
		'name'  => $name,
		'type'  => 'text',
	);
}

/**
 * Build a URL field with a stable key.
 */
function miki_site_field_url( $name, $label ) {
	$field         = miki_site_field_text( $name, $label );
	$field['type'] = 'url';
	return $field;
}

/**
 * Build a textarea field matching CFS automatic line-break formatting.
 */
function miki_site_field_textarea( $name, $label ) {
	return array(
		'key'       => 'field_miki_' . str_replace( '-', '_', $name ),
		'label'     => $label,
		'name'      => $name,
		'type'      => 'textarea',
		'new_lines' => 'br',
	);
}

/**
 * Build an image field that keeps the templates' URL return contract.
 */
function miki_site_field_image( $name, $label, $instructions = '' ) {
	return array(
		'key'           => 'field_miki_' . str_replace( '-', '_', $name ),
		'label'         => $label,
		'name'          => $name,
		'type'          => 'image',
		'instructions'  => $instructions,
		'return_format' => 'url',
		'preview_size'  => 'medium',
		'library'       => 'all',
	);
}
