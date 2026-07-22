<?php
/*
 * Template Name: 社員の声一覧
 */

get_header();

$recruit_page = get_page_by_path( 'recruit' );
$recruit_id   = $recruit_page instanceof WP_Post ? $recruit_page->ID : 0;
$employees    = array();

global $cfs;
if ( $recruit_id && is_object( $cfs ) && method_exists( $cfs, 'get' ) ) {
	$employee_field = $cfs->get( 'employee_list', $recruit_id );
	if ( is_array( $employee_field ) ) {
		$employees = $employee_field;
	}
}

if ( empty( $employees ) && $recruit_id && function_exists( 'get_field' ) ) {
	$employee_field = get_field( 'employee_list', $recruit_id );
	if ( is_array( $employee_field ) ) {
		$employees = $employee_field;
	}
}

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

$image_alt = static function ( $image, $fallback = '' ) {
	if ( is_array( $image ) && isset( $image['alt'] ) && '' !== trim( (string) $image['alt'] ) ) {
		return (string) $image['alt'];
	}
	$attachment_id = is_numeric( $image ) ? (int) $image : attachment_url_to_postid( (string) $image );
	if ( $attachment_id ) {
		$alt = trim( (string) get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) );
		if ( '' !== $alt ) {
			return $alt;
		}
	}
	return $fallback;
};

$plain_text = static function ( $value ) {
	$value = preg_replace( '#<br\s*/?>#i', "\n", (string) $value );
	$value = wp_strip_all_tags( html_entity_decode( $value, ENT_QUOTES, 'UTF-8' ) );
	$value = preg_replace( "/\r\n|\r/", "\n", $value );
	$value = preg_replace( "/[ \t]*\n[ \t]*/", "\n", $value );
	$value = preg_replace( "/\n{2,}/", "\n", $value );
	return trim( $value );
};

$normalise_slug = static function ( $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}

	$path = wp_parse_url( $value, PHP_URL_PATH );
	if ( is_string( $path ) && '' !== $path ) {
		$value = $path;
	}

	return sanitize_title( basename( untrailingslashit( $value ) ) );
};

$page_field = static function ( $field_name, $page_id, $fallback = '' ) use ( &$cfs ) {
	$value = '';
	if ( is_object( $cfs ) && method_exists( $cfs, 'get' ) ) {
		$value = $cfs->get( $field_name, $page_id );
	}
	if ( ( '' === $value || null === $value || false === $value ) && function_exists( 'get_field' ) ) {
		$value = get_field( $field_name, $page_id );
	}

	return '' === $value || null === $value || false === $value ? $fallback : $value;
};

$employee_by_slug = array();
foreach ( $employees as $employee ) {
	$employee_slug = $normalise_slug( miki_array_value( $employee, 'employee_link' ) );
	if ( '' !== $employee_slug ) {
		$employee_by_slug[ $employee_slug ] = $employee;
	}
}

$detail_pages = $recruit_id ? get_posts(
	array(
		'post_type'      => 'page',
		'post_status'    => 'publish',
		'post_parent'    => $recruit_id,
		'post__not_in'   => array( get_queried_object_id() ),
		'posts_per_page' => -1,
		'orderby'        => 'menu_order title',
		'order'          => 'ASC',
	)
) : array();

$detail_pages_by_slug = array();
foreach ( $detail_pages as $detail_page ) {
	$detail_pages_by_slug[ $detail_page->post_name ] = $detail_page;
}

// Keep the employee loop's established order, then append newly published child pages.
$ordered_detail_pages = array();
foreach ( array_keys( $employee_by_slug ) as $employee_slug ) {
	if ( isset( $detail_pages_by_slug[ $employee_slug ] ) ) {
		$ordered_detail_pages[] = $detail_pages_by_slug[ $employee_slug ];
		unset( $detail_pages_by_slug[ $employee_slug ] );
	}
}
foreach ( $detail_pages as $detail_page ) {
	if ( isset( $detail_pages_by_slug[ $detail_page->post_name ] ) ) {
		$ordered_detail_pages[] = $detail_page;
		unset( $detail_pages_by_slug[ $detail_page->post_name ] );
	}
}

$voices_per_page = 6;
$voices_page      = isset( $_GET['voices_page'] ) ? max( 1, absint( wp_unslash( $_GET['voices_page'] ) ) ) : 1;
$voices_total     = count( $ordered_detail_pages );
$voices_max_page  = max( 1, (int) ceil( $voices_total / $voices_per_page ) );
$voices_page      = min( $voices_page, $voices_max_page );
$visible_pages    = array_slice( $ordered_detail_pages, ( $voices_page - 1 ) * $voices_per_page, $voices_per_page );

$page_intro = '';
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		$page_intro = trim( (string) get_the_content() );
	}
}
?>

<main class="miki-voices">
	<section class="miki-voices-hero">
		<div class="miki-voices-hero__inner">
			<p class="miki-voices-hero__eyebrow">RECRUIT / EMPLOYEES' VOICES</p>
			<h1><?php the_title(); ?></h1>
			<div class="miki-voices-hero__lead">
				<?php if ( '' !== $page_intro ) : ?>
					<?php echo wp_kses_post( apply_filters( 'the_content', $page_intro ) ); ?>
				<?php else : ?>
					<p>三基食品で働く社員それぞれの仕事への想いと、日々の挑戦をご紹介します。</p>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="miki-voices-list" aria-label="社員インタビュー一覧">
		<div class="miki-voices-list__inner">
			<?php if ( ! empty( $visible_pages ) ) : ?>
				<div class="miki-voices-grid">
					<?php foreach ( $visible_pages as $employee_page ) : ?>
						<?php
						$employee_slug    = $employee_page->post_name;
						$employee         = isset( $employee_by_slug[ $employee_slug ] ) ? $employee_by_slug[ $employee_slug ] : array();
						$employee_name    = $plain_text( miki_array_value( $employee, 'employee_name' ) );
						$employee_catch   = $plain_text( miki_array_value( $employee, 'employee_catch' ) );
						$employee_image_field   = miki_array_value( $employee, 'employee_img' );
						$employee_belongs_field = miki_array_value( $employee, 'employee_belongs' );
						$employee_image         = $image_url( $employee_image_field );
						$employee_belongs       = $image_url( $employee_belongs_field );
						$employee_belongs_alt   = $image_alt( $employee_belongs_field, '所属' );

						if ( '' === $employee_name ) {
							$employee_name = $plain_text( $page_field( 'interview_profile_name', $employee_page->ID, get_the_title( $employee_page ) ) );
						}
						if ( '' === $employee_catch ) {
							$employee_catch = $plain_text( $page_field( 'interview_hero_title', $employee_page->ID ) );
							$employee_accent = $plain_text( $page_field( 'interview_hero_accent', $employee_page->ID ) );
							if ( '' !== $employee_accent ) {
								$employee_catch = trim( $employee_catch . "\n" . $employee_accent );
							}
						}
						if ( '' === $employee_image ) {
							$employee_image = (string) get_the_post_thumbnail_url( $employee_page, 'full' );
						}
						if ( '' === $employee_image ) {
							$employee_image = $image_url( $page_field( 'interview_profile_image', $employee_page->ID ) );
						}
						if ( '' === $employee_image ) {
							$interview_rows = $page_field( 'interview', $employee_page->ID, array() );
							foreach ( (array) $interview_rows as $interview_row ) {
								$employee_image = $image_url( miki_array_value( $interview_row, 'img' ) );
								if ( '' !== $employee_image ) {
									break;
								}
							}
						}
						$employee_url = get_permalink( $employee_page );
						?>
						<article class="miki-voices-card">
							<a href="<?php echo esc_url( $employee_url ); ?>">
								<div class="miki-voices-card__visual">
									<?php if ( '' !== $employee_image ) : ?>
										<img class="miki-voices-card__portrait" src="<?php echo esc_url( $employee_image ); ?>" alt="<?php echo esc_attr( $employee_name ); ?>">
									<?php endif; ?>
									<?php if ( '' !== $employee_belongs ) : ?>
										<img class="miki-voices-card__belongs" src="<?php echo esc_url( $employee_belongs ); ?>" alt="<?php echo esc_attr( $employee_belongs_alt ); ?>">
									<?php endif; ?>
								</div>
								<div class="miki-voices-card__body">
									<div class="miki-voices-card__meta">
										<p class="miki-voices-card__number">EMPLOYEE VOICE</p>
									</div>
									<h2><?php echo nl2br( esc_html( $employee_name ) ); ?></h2>
									<?php if ( '' !== $employee_catch ) : ?>
										<p class="miki-voices-card__catch"><?php echo nl2br( esc_html( $employee_catch ) ); ?></p>
									<?php endif; ?>
									<span class="miki-voices-card__more">インタビューを見る<span aria-hidden="true">→</span></span>
								</div>
							</a>
						</article>
					<?php endforeach; ?>
				</div>
				<?php if ( 1 < $voices_max_page ) : ?>
					<?php
					$pagination_base = add_query_arg( 'voices_page', 999999999, get_permalink() );
					$pagination_base = str_replace( '999999999', '%#%', $pagination_base );
					$pagination      = paginate_links(
						array(
							'base'      => $pagination_base,
							'format'    => '',
							'current'   => $voices_page,
							'total'     => $voices_max_page,
							'type'      => 'list',
							'prev_text' => '前へ',
							'next_text' => '次へ',
						)
					);
					?>
					<?php if ( $pagination ) : ?>
						<nav class="miki-voices-pagination" aria-label="社員の声一覧のページ送り">
							<?php echo wp_kses_post( $pagination ); ?>
						</nav>
					<?php endif; ?>
				<?php endif; ?>
			<?php else : ?>
				<p class="miki-voices-list__empty">社員情報は準備中です。</p>
			<?php endif; ?>
		</div>
	</section>

	<nav class="miki-voices-footer" aria-label="採用情報の関連リンク">
		<div class="miki-voices-footer__inner">
			<a class="miki-voices-footer__back" href="<?php echo esc_url( home_url( '/recruit/' ) ); ?>">採用情報へ戻る</a>
			<a class="miki-voices-footer__entry" href="<?php echo esc_url( home_url( '/contact/#recruit' ) ); ?>">採用に関するお問い合わせ</a>
		</div>
	</nav>
</main>

<?php get_footer();
