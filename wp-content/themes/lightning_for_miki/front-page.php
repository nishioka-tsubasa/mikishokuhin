<?php
/**
 * Template Name: トップページカスタマイズ
 */
get_header(); ?>

<?php
$bootstrap = miki_get_bootstrap_type();
if ( $bootstrap == '3' ) {
	$old_file_name[] = 'module_slide.php';
	if ( locate_template( $old_file_name, false, false ) ) {
		locate_template( $old_file_name, true, false );
	} else {
		get_template_part( 'template-parts/slide', 'bs3' );
	}
} else {
	get_template_part( 'template-parts/slide', 'bs4' );
}

$days  = miki_get_cfs_value( 'notice_new_time', 0 ); // お知らせ：NEWを表示させる期間の日数を入力
$today = date_i18n('U');
$args = array(
	'category_name' => 'notice', // お知らせ：カテゴリー
	'posts_per_page' => miki_get_cfs_value( 'notice_display_count', 5 ) // お知らせ：表示させる記事数
);

// お問い合わせ先の電話番号取得のため
$company_page_id = miki_get_page_id_by_path( 'company' );
?>
<div class="section siteContent top sub_background">

	<?php
	/**
	 * 会社情報
	 */
	?>
	<div class="area company-area">
		<?php echo get_field( 'company' ); ?>
	</div>


	<?php
	/**
	 * 事業内容
	 */
	?>
	<div class="area business-area">
		<?php echo get_field( 'business' ); ?>
	</div>


	<?php
	/**
	 * 採用情報
	 */
	?>
	<div class="area recruit-area">
		<?php echo get_field( 'recruitment' ); ?>
	</div>

	<?php
	/**
	 * お知らせ
	 */
	?>
		
	<div class="area notice-area">
		<div class="newslist-content">
			<div class="title">新着情報</div>
			<div class="subtitle">NEWS</div>
			<div class="notice">
				<div class="main-notice">
					<?php query_posts( $args );
					if ( have_posts() ) : ?>
					<table>
						<tbody>
							<?php while ( have_posts() ) : the_post(); ?>
								<tr>
									<td class="new">
										<?php	$total = date( 'U',( $today - get_the_time('U') ) ) / 86400;
												if( $days > $total ){?>
											<span class="notice-new-mark">NEW</span>
										<?php	} ?>
									</td>
									<td class="time"><p class="time-p"><?php the_time('Y.n.j'); ?></p></td>
									<td class="news-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
								</tr>
							<?php endwhile;?>
						</tbody>
					</table>
					<?php else : ?>
						<p>現在お知らせする情報はありません</p>
					<?php endif;
					wp_reset_query(); 
					?>
				</div>
				<a class="notice-link" href="/category/notice/">一覧へ</a>
			</div>
		</div><!--.notice-->
	</div><!--.notice-backblock-->



	<?php do_action( 'lightning_siteContent_prepend' ); ?>
	<div class="container">
		<?php do_action( 'lightning_siteContent_container_prepend' ); ?>
		<div class="row">

			<div class="<?php lightning_the_class_name( 'mainSection' ); ?>">

				<?php
					do_action( 'lightning_home_content_top_widget_area_before' );

					if ( is_active_sidebar( 'home-content-top-widget-area' ) ) :
						dynamic_sidebar( 'home-content-top-widget-area' );
					endif;

					do_action( 'lightning_home_content_top_widget_area_after' );

					if ( apply_filters( 'is_lightning_home_content_display', true ) ) :

						if ( have_posts() ) :

							if ( 'page' == get_option( 'show_on_front' ) ) :

								while ( have_posts() ) :
									the_post();
				?>

									<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
										<div class="entry-body">
											<?php the_content(); ?>
										</div>
										<?php
											wp_link_pages(
												array(
													'before' => '<div class="page-link">' . 'Pages:',
													'after'  => '</div>',
												)
											);
										?>
									</article><!-- [ /#post-<?php the_ID(); ?> ] -->

				<?php 			endwhile;

							else : //if ( 'page' == get_option( 'show_on_front' ) )
				?>

								<div class="postList">

									<?php
										while ( have_posts() ) :
											the_post();

											get_template_part( 'module_loop_post' );

										endwhile;

										the_posts_pagination(
											array(
												'mid_size'           => 1,
												'prev_text'          => '&laquo;',
												'next_text'          => '&raquo;',
												'type'               => 'list',
												'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'lightning' ) . ' </span>',
											)
										);
									?>

								</div><!-- [ /.postList ] -->

				<?php		endif; // if ( 'page' == get_option('show_on_front') )

						else : // if ( have_posts() ) :
				?>

							<div class="well"><p><?php _e( 'No posts.', 'lightning' ); ?></p></div>

				<?php
						endif; // have_post()

					endif; // if ( apply_filters( 'is_lightning_home_top_posts_display', true ) )
				?>

			</div><!-- [ /.mainSection ] -->

			<?php if ( ! lightning_is_frontpage_onecolumn() ) : ?>

				<div class="<?php lightning_the_class_name( 'sideSection' ); ?>">
					<?php get_sidebar(); ?>
				</div><!-- [ /.subSection ] -->

			<?php endif; ?>

		</div><!-- [ /.row ] -->
	</div><!-- [ /.container ] -->
</div><!-- [ /.siteContent ] -->
<?php get_footer();
