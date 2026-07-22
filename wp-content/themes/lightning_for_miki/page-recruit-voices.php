<?php
/*
 * Template Name: 社員の声一覧
 */

get_header();

$recruit_page = get_page_by_path( 'recruit' );
$recruit_id   = $recruit_page instanceof WP_Post ? $recruit_page->ID : 0;
$employees    = array();

if ( $recruit_id && function_exists( 'get_field' ) ) {
	$employee_field = get_field( 'employee_list', $recruit_id );
	if ( is_array( $employee_field ) ) {
		$employees = $employee_field;
	}
}

if ( empty( $employees ) ) {
	global $cfs;
	if ( $recruit_id && is_object( $cfs ) && method_exists( $cfs, 'get' ) ) {
		$employee_field = $cfs->get( 'employee_list', $recruit_id );
		if ( is_array( $employee_field ) ) {
			$employees = $employee_field;
		}
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

$plain_text = static function ( $value ) {
	$value = preg_replace( '#<br\s*/?>#i', "\n", (string) $value );
	return trim( wp_strip_all_tags( html_entity_decode( $value, ENT_QUOTES, 'UTF-8' ) ) );
};

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
			<?php if ( ! empty( $employees ) ) : ?>
				<div class="miki-voices-grid">
					<?php foreach ( $employees as $employee ) : ?>
						<?php
						$employee_slug    = trim( (string) miki_array_value( $employee, 'employee_link' ), '/' );
						$employee_name    = $plain_text( miki_array_value( $employee, 'employee_name' ) );
						$employee_catch   = $plain_text( miki_array_value( $employee, 'employee_catch' ) );
						$employee_image   = $image_url( miki_array_value( $employee, 'employee_img' ) );
						$employee_belongs = $image_url( miki_array_value( $employee, 'employee_belongs' ) );
						if ( '' === $employee_slug || '' === $employee_name ) {
							continue;
						}
						$employee_url = home_url( '/recruit/' . $employee_slug . '/' );
						?>
						<article class="miki-voices-card">
							<a href="<?php echo esc_url( $employee_url ); ?>">
								<div class="miki-voices-card__visual">
									<?php if ( '' !== $employee_belongs ) : ?>
										<img class="miki-voices-card__belongs" src="<?php echo esc_url( $employee_belongs ); ?>" alt="">
									<?php endif; ?>
									<?php if ( '' !== $employee_image ) : ?>
										<img class="miki-voices-card__portrait" src="<?php echo esc_url( $employee_image ); ?>" alt="<?php echo esc_attr( $employee_name ); ?>">
									<?php endif; ?>
								</div>
								<div class="miki-voices-card__body">
									<p class="miki-voices-card__number">EMPLOYEE VOICE</p>
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
