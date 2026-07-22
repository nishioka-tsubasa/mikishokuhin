<?php
/*
 * @copyright     Copyright 2019, Wiz Co., Ltd. (https://www.wiznet.co.jp)
 * @author Kazuyoshi Watanabe
 * Template Name: 採用情報（子ページ）
 */

get_header();

$post_id       = get_queried_object_id();
$page_slug     = (string) get_post_field( 'post_name', $post_id );
$is_kitamura   = 'kitamura' === $page_slug;
$legacy_header = (string) miki_get_cfs_value( 'header_title', '' );

$field_value = static function ( $field_name, $fallback = '' ) {
	$value = miki_get_cfs_value( $field_name, '' );
	if ( is_string( $value ) && '' === trim( $value ) ) {
		return $fallback;
	}
	if ( null === $value || false === $value ) {
		return $fallback;
	}
	return $value;
};

$legacy_value = static function ( $tag ) use ( $legacy_header ) {
	if ( '' === $legacy_header ) {
		return '';
	}
	$pattern = '#<' . preg_quote( $tag, '#' ) . '\\b[^>]*>(.*?)</' . preg_quote( $tag, '#' ) . '>#is';
	if ( ! preg_match( $pattern, $legacy_header, $matches ) ) {
		return '';
	}
	$value = preg_replace( '#<br\\s*/?>#i', "\n", $matches[1] );
	return trim( wp_strip_all_tags( html_entity_decode( $value, ENT_QUOTES, 'UTF-8' ) ) );
};

$image_url = static function ( $image ) {
	if ( is_array( $image ) ) {
		return isset( $image['url'] ) ? (string) $image['url'] : '';
	}
	if ( is_numeric( $image ) ) {
		$url = wp_get_attachment_image_url( $image, 'full' );
		return $url ? $url : '';
	}
	return is_string( $image ) ? $image : '';
};

$plain_text = static function ( $value ) {
	$value = preg_replace( '#<br\s*/?>#i', "\n", (string) $value );
	return trim( wp_strip_all_tags( html_entity_decode( $value, ENT_QUOTES, 'UTF-8' ) ) );
};

$interviews = miki_get_cfs_loop( 'interview' );
$first_photo = '';
foreach ( $interviews as $interview_row ) {
	$first_photo = $image_url( miki_array_value( $interview_row, 'img' ) );
	if ( '' !== $first_photo ) {
		break;
	}
}

$kitamura_defaults = array(
	'hero_title'     => "大手企業の安定より\n中小企業での",
	'hero_accent'    => 'やりがいを求めて。',
	'hero_lead'      => "大手企業から三基食品へ転職し、製造現場の第一線で活躍する喜多村さん。\n自分の意見を活かせる環境、仲間と連携して製品をつくる面白さ、\nそして人を育てる責任について話を聞きました。",
	'profile_name'   => '喜多村さん',
	'profile_name_en'=> 'KITAMURA / Production',
	'message_title'  => "「健康・安全・自然」を、\n毎日の手のひらから。",
	'message_body'   => "特別な一日ではなく、ふつうの一日。\nその積み重ねが、三基食品の商品をつくっています。\nあなたの「ふつうの一日」も、ここで始まるかもしれません。",
	'message_sign'   => '— Kitamura',
);

$hero_title_default   = $is_kitamura ? $kitamura_defaults['hero_title'] : $legacy_value( 'h1' );
$hero_accent_default  = $is_kitamura ? $kitamura_defaults['hero_accent'] : '';
$hero_lead_default    = $is_kitamura ? $kitamura_defaults['hero_lead'] : $legacy_value( 'h2' );
$profile_name_default = $is_kitamura ? $kitamura_defaults['profile_name'] : $legacy_value( 'h3' );
$profile_en_default   = $is_kitamura ? $kitamura_defaults['profile_name_en'] : '';

$hero_eyebrow   = $plain_text( $field_value( 'interview_hero_eyebrow', "EMPLOYEES' VOICES | 社員の声" ) );
$hero_title     = $plain_text( $field_value( 'interview_hero_title', $hero_title_default ) );
$hero_accent    = $plain_text( $field_value( 'interview_hero_accent', $hero_accent_default ) );
$hero_lead      = $plain_text( $field_value( 'interview_hero_lead', $hero_lead_default ) );
$profile_label  = (string) $field_value( 'interview_profile_label', 'PROFILE' );
$profile_name   = (string) $field_value( 'interview_profile_name', $profile_name_default );
$profile_name_en= (string) $field_value( 'interview_profile_name_en', $profile_en_default );
$profile_photo  = $image_url( $field_value( 'interview_profile_image', $first_photo ) );

$profile_meta = miki_get_cfs_loop( 'interview_profile_meta' );
if ( empty( $profile_meta ) && $is_kitamura ) {
	$profile_meta = array(
		array( 'interview_profile_meta_label' => 'Department', 'interview_profile_meta_value' => '山南工場 製造課' ),
		array( 'interview_profile_meta_label' => 'Joined', 'interview_profile_meta_value' => '2019年入社' ),
		array( 'interview_profile_meta_label' => 'Hobby', 'interview_profile_meta_value' => '休日は自家製パンづくり、たまに山歩き' ),
		array( 'interview_profile_meta_label' => 'Motto', 'interview_profile_meta_value' => '「ていねいに、たのしく」' ),
	);
} elseif ( empty( $profile_meta ) && '' !== $legacy_value( 'p' ) ) {
	$profile_meta = array(
		array( 'interview_profile_meta_label' => 'Department', 'interview_profile_meta_value' => preg_replace( '/^所属[：:]\\s*/u', '', $legacy_value( 'p' ) ) ),
	);
}

$schedule = miki_get_cfs_loop( 'interview_schedule' );
if ( empty( $schedule ) && $is_kitamura ) {
	$schedule = array(
		array(
			'interview_schedule_time'        => '8:30',
			'interview_schedule_label'       => 'BRIEFING',
			'interview_schedule_title'       => '朝礼・チームミーティング',
			'interview_schedule_description' => '製造部のメンバーで、その日の生産計画と注意点を共有。新人さんへのフォローやライン替えのタイミングも、このときに細かくすり合わせます。',
			'interview_schedule_note'        => '声をかけ合うことが品質につながる。チームの空気を作る、大事な10分です。',
			'interview_schedule_accent'      => true,
		),
		array(
			'interview_schedule_time'        => '9:00',
			'interview_schedule_label'       => 'PRODUCTION',
			'interview_schedule_title'       => '製造ラインへ',
			'interview_schedule_description' => '原料投入から包装まで、ラインの流れを見守りながら品質をチェック。機械の音やコンベアのリズムに耳を澄まして、わずかな違和感も逃さないように。',
			'interview_schedule_note'        => '',
			'interview_schedule_accent'      => false,
		),
		array(
			'interview_schedule_time'        => '11:00',
			'interview_schedule_label'       => 'CHECK',
			'interview_schedule_title'       => '品質確認・記録',
			'interview_schedule_description' => 'サンプルを取って、品質管理チームと一緒に確認。日々のデータが、安心しておいしく食べてもらえる商品づくりの土台になります。',
			'interview_schedule_note'        => '数字の小さな変化が、お客様の食卓につながっている。記録は未来への手紙のようなものです。',
			'interview_schedule_accent'      => false,
		),
		array(
			'interview_schedule_time'        => '12:00',
			'interview_schedule_label'       => 'LUNCH',
			'interview_schedule_title'       => 'お昼休み',
			'interview_schedule_description' => '食堂で同期や先輩とごはん。仕事の話半分、休日の話半分。趣味のパンを差し入れすると、思いのほか喜んでもらえてうれしい瞬間です。',
			'interview_schedule_note'        => '',
			'interview_schedule_accent'      => false,
		),
		array(
			'interview_schedule_time'        => '13:00',
			'interview_schedule_label'       => 'PRODUCTION',
			'interview_schedule_title'       => '午後の製造・新人フォロー',
			'interview_schedule_description' => '午後は別のラインに入りつつ、後輩のサポート。先輩から教わってきたことを、自分の言葉で伝えるよう心がけています。',
			'interview_schedule_note'        => '「教える」ことは、自分の仕事を見直す時間でもあります。',
			'interview_schedule_accent'      => true,
		),
		array(
			'interview_schedule_time'        => '17:00',
			'interview_schedule_label'       => 'EVENING',
			'interview_schedule_title'       => '退社・帰り道',
			'interview_schedule_description' => '明日の段取りを軽くメモして、退社。鳴尾浜の夕焼けを見ながら帰る道は、一日のごほうびのような時間です。',
			'interview_schedule_note'        => '今日もいいものが作れた。そう思えた日は、足取りが少し軽くなります。',
			'interview_schedule_accent'      => false,
		),
	);
}

$message_title = $plain_text( $field_value( 'interview_message_title', $is_kitamura ? $kitamura_defaults['message_title'] : '' ) );
$message_body  = $plain_text( $field_value( 'interview_message_body', $is_kitamura ? $kitamura_defaults['message_body'] : '' ) );
$message_sign  = (string) $field_value( 'interview_message_signature', $is_kitamura ? $kitamura_defaults['message_sign'] : '' );

$cta_links = miki_get_cfs_loop( 'interview_cta_links' );
if ( empty( $cta_links ) ) {
	$cta_links = array(
		array(
			'interview_cta_link_label' => '他の社員紹介を見る',
			'interview_cta_link_url'   => home_url( '/recruit/#slick' ),
			'interview_cta_link_style' => 'primary',
		),
		array(
			'interview_cta_link_label' => '採用エントリー',
			'interview_cta_link_url'   => home_url( '/recruit/' ),
			'interview_cta_link_style' => 'ghost',
		),
	);
}
?>

<main class="miki-interview">
	<section class="miki-interview-hero">
		<div class="miki-interview-hero__inner">
			<div class="miki-interview-hero__copy">
				<p class="miki-interview-eyebrow"><?php echo esc_html( $hero_eyebrow ); ?></p>
				<h1 class="miki-interview-hero__title">
					<?php echo nl2br( esc_html( $hero_title ) ); ?>
					<?php if ( '' !== $hero_accent ) : ?>
						<span><?php echo nl2br( esc_html( $hero_accent ) ); ?></span>
					<?php endif; ?>
				</h1>
				<?php if ( '' !== $hero_lead ) : ?>
					<p class="miki-interview-hero__lead"><?php echo nl2br( esc_html( $hero_lead ) ); ?></p>
				<?php endif; ?>
				<p class="miki-interview-scroll" aria-hidden="true">SCROLL</p>
			</div>

			<aside class="miki-interview-profile" aria-label="<?php echo esc_attr( $profile_label ); ?>">
				<p class="miki-interview-profile__label"><?php echo esc_html( $profile_label ); ?></p>
				<?php if ( '' !== $profile_photo ) : ?>
					<div class="miki-interview-profile__photo">
						<img src="<?php echo esc_url( $profile_photo ); ?>" alt="<?php echo esc_attr( $profile_name ); ?>">
					</div>
				<?php endif; ?>
				<?php if ( '' !== $profile_name ) : ?>
					<p class="miki-interview-profile__name"><?php echo esc_html( $profile_name ); ?></p>
				<?php endif; ?>
				<?php if ( '' !== $profile_name_en ) : ?>
					<p class="miki-interview-profile__name-en"><?php echo esc_html( $profile_name_en ); ?></p>
				<?php endif; ?>
				<?php if ( ! empty( $profile_meta ) ) : ?>
					<dl class="miki-interview-profile__meta">
						<?php foreach ( $profile_meta as $profile_row ) : ?>
							<?php
							$meta_label = (string) miki_array_value( $profile_row, 'interview_profile_meta_label' );
							$meta_value = (string) miki_array_value( $profile_row, 'interview_profile_meta_value' );
							if ( '' === trim( $meta_label . $meta_value ) ) {
								continue;
							}
							?>
							<dt><?php echo esc_html( $meta_label ); ?></dt>
							<dd><?php echo nl2br( esc_html( $plain_text( $meta_value ) ) ); ?></dd>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
			</aside>
		</div>
	</section>

	<section class="miki-interview-questions" aria-labelledby="miki-interview-heading">
		<header class="miki-interview-section-heading">
			<h2 id="miki-interview-heading"><?php echo esc_html( (string) $field_value( 'interview_heading_en', 'Interview' ) ); ?></h2>
			<p><?php echo esc_html( (string) $field_value( 'interview_heading_ja', '社員インタビュー' ) ); ?></p>
		</header>

		<?php foreach ( $interviews as $index => $interview ) : ?>
			<?php
			$question  = (string) miki_array_value( $interview, 'question' );
			$answer    = (string) miki_array_value( $interview, 'answer' );
			$interrupt = (string) miki_array_value( $interview, 'interrupt' );
			$photo     = $image_url( miki_array_value( $interview, 'img' ) );
			$caption   = (string) miki_array_value( $interview, 'img-detail' );
			$position  = miki_choice_value( miki_array_value( $interview, 'position', 'text_l' ) );
			$bg        = miki_choice_value( miki_array_value( $interview, 'bg', 'white' ) );
			$position  = in_array( $position, array( 'text_l', 'text_r' ), true ) ? $position : 'text_l';
			$bg        = in_array( $bg, array( 'standard', 'white', 'standard-image', 'image' ), true ) ? $bg : 'white';
			$row_class = array( 'miki-interview-question', 'is-' . $position, 'is-' . $bg );
			if ( '' === $photo ) {
				$row_class[] = 'has-no-image';
			}
			if ( 0 === $index ) {
				$row_class[] = 'is-first';
			}
			?>
			<article id="miki-interview-question-<?php echo esc_attr( (string) ( $index + 1 ) ); ?>" class="<?php echo esc_attr( implode( ' ', $row_class ) ); ?>">
				<div class="miki-interview-question__inner">
					<?php if ( '' !== $interrupt ) : ?>
						<div class="miki-interview-question__interrupt"><?php echo wp_kses_post( wpautop( $interrupt ) ); ?></div>
					<?php endif; ?>
					<div class="miki-interview-question__content">
						<div class="miki-interview-question__text">
							<?php if ( '' !== $question ) : ?>
								<h3><span>Q<?php echo esc_html( (string) ( $index + 1 ) ); ?></span><?php echo esc_html( $question ); ?></h3>
							<?php endif; ?>
							<div class="miki-interview-question__answer"><?php echo wp_kses_post( wpautop( $answer ) ); ?></div>
						</div>
						<?php if ( '' !== $photo ) : ?>
							<figure class="miki-interview-question__media">
								<img src="<?php echo esc_url( $photo ); ?>" alt="<?php echo esc_attr( 'Q' . ( $index + 1 ) . ' ' . $question ); ?>">
								<?php if ( '' !== $caption ) : ?>
									<figcaption><?php echo wp_kses_post( $caption ); ?></figcaption>
								<?php endif; ?>
							</figure>
						<?php endif; ?>
					</div>
				</div>
			</article>
		<?php endforeach; ?>
	</section>

	<?php if ( ! $field_value( 'interview_schedule_hide', false ) && ! empty( $schedule ) ) : ?>
		<section class="miki-interview-schedule" aria-labelledby="miki-schedule-heading">
			<div class="miki-interview-schedule__inner">
				<header class="miki-interview-section-heading">
					<h2 id="miki-schedule-heading"><?php echo esc_html( (string) $field_value( 'interview_schedule_heading_en', 'Daily Schedule' ) ); ?></h2>
					<p><?php echo esc_html( (string) $field_value( 'interview_schedule_heading_ja', 'ある日のスケジュール' ) ); ?></p>
				</header>
				<div class="miki-interview-timeline">
					<?php foreach ( $schedule as $schedule_index => $schedule_row ) : ?>
						<?php $is_accent = (bool) miki_array_value( $schedule_row, 'interview_schedule_accent', false ); ?>
						<article id="miki-interview-schedule-<?php echo esc_attr( (string) ( $schedule_index + 1 ) . '-' . sanitize_title( (string) miki_array_value( $schedule_row, 'interview_schedule_time' ) ) ); ?>" class="miki-interview-timeline__item<?php echo $is_accent ? ' is-accent' : ''; ?>">
							<div class="miki-interview-timeline__time">
								<time><?php echo esc_html( (string) miki_array_value( $schedule_row, 'interview_schedule_time' ) ); ?></time>
								<span><?php echo esc_html( (string) miki_array_value( $schedule_row, 'interview_schedule_label' ) ); ?></span>
							</div>
							<span class="miki-interview-timeline__dot" aria-hidden="true"></span>
							<div class="miki-interview-timeline__card">
								<h3><?php echo esc_html( (string) miki_array_value( $schedule_row, 'interview_schedule_title' ) ); ?></h3>
								<div class="miki-interview-timeline__description"><?php echo wp_kses_post( wpautop( esc_html( $plain_text( miki_array_value( $schedule_row, 'interview_schedule_description' ) ) ) ) ); ?></div>
								<?php if ( '' !== (string) miki_array_value( $schedule_row, 'interview_schedule_note' ) ) : ?>
									<p class="miki-interview-timeline__note"><?php echo nl2br( esc_html( $plain_text( miki_array_value( $schedule_row, 'interview_schedule_note' ) ) ) ); ?></p>
								<?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( '' !== trim( $message_title . $message_body . $message_sign ) ) : ?>
		<section id="miki-interview-message" class="miki-interview-message">
			<p class="miki-interview-message__label"><?php echo esc_html( (string) $field_value( 'interview_message_label', '— Message —' ) ); ?></p>
			<?php if ( '' !== $message_title ) : ?>
				<h2><?php echo nl2br( esc_html( $message_title ) ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $message_body ) : ?>
				<p class="miki-interview-message__body"><?php echo nl2br( esc_html( $message_body ) ); ?></p>
			<?php endif; ?>
			<?php if ( '' !== $message_sign ) : ?>
				<p class="miki-interview-message__signature"><?php echo esc_html( $message_sign ); ?></p>
			<?php endif; ?>
		</section>
	<?php endif; ?>

	<section id="miki-interview-cta" class="miki-interview-cta">
		<div class="miki-interview-cta__inner">
			<div class="miki-interview-cta__copy">
				<h2><?php echo esc_html( (string) $field_value( 'interview_cta_heading', 'もっと、三基食品で働く人を知る。' ) ); ?></h2>
				<p><?php echo nl2br( esc_html( $plain_text( $field_value( 'interview_cta_body', '他の社員のインタビューや、新卒・中途採用の最新情報をご覧いただけます。あなたの“一日”を、私たちと一緒に。' ) ) ) ); ?></p>
			</div>
			<div class="miki-interview-cta__links">
				<?php foreach ( $cta_links as $cta_link ) : ?>
					<?php
					$link_label = (string) miki_array_value( $cta_link, 'interview_cta_link_label' );
					$link_url   = (string) miki_array_value( $cta_link, 'interview_cta_link_url' );
					$link_style = miki_choice_value( miki_array_value( $cta_link, 'interview_cta_link_style', 'primary' ) );
					$link_style = in_array( $link_style, array( 'primary', 'ghost' ), true ) ? $link_style : 'primary';
					if ( '' === $link_label || '' === $link_url ) {
						continue;
					}
					?>
					<a class="miki-interview-cta__link is-<?php echo esc_attr( $link_style ); ?>" href="<?php echo esc_url( $link_url ); ?>"><?php echo esc_html( $link_label ); ?></a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
</main>

<?php get_footer();
