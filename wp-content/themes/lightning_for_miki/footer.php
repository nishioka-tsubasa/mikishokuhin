<?php
/**
 * フッターカスタマイズ
 */

if ( is_active_sidebar( 'footer-upper-widget-1' ) ) : ?>
<div class="section sectionBox siteContent_after">
	<div class="container ">
		<div class="row ">
			<div class="col-md-12 ">
			<?php dynamic_sidebar( 'footer-upper-widget-1' ); ?>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<?php do_action( 'lightning_footer_before' ); ?>

<footer class="section siteFooter">

	<div class="footerMenu">
	   <div class="container">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'Footer',
					'container'      => 'nav',
					'items_wrap'     => '<ul id="%1$s" class="%2$s nav">%3$s</ul>',
					'fallback_cb'    => '',
					'depth'          => 1,
					'before'		 => '<img src="'. get_stylesheet_directory_uri() .'/assets/img/footer_menu_symbol.png" class="footer_menu_symbol">'
				)
			);

			// 会社情報は会社概要ページ(company)より取得
			$company_page_id = miki_get_page_id_by_path( 'company' );
			?>

			<div class="companyInfo">
				<div class="companyInfo-txt">
					<?php lightning_print_headlogo(); ?>
					<div class="companyInfo-txt-box">
						<p class="company-name"><?php echo get_field( 'name', $company_page_id ); ?></p>
						<p>〒<?php echo get_field( 'postcode', $company_page_id ); ?></p>
						<p><?php echo get_field( 'address', $company_page_id ); ?></p>
						<p>TEL <?php echo get_field( 'tel', $company_page_id ); ?></p>
						<p>FAX <?php echo get_field( 'fax', $company_page_id ); ?></p>
					</div>
				</div>
				<div class="companyInfo-copyright">
					<p>Copyright(C) 2019 MIKI Foods All Right Reserved</p>
				</div>
			</div>
		</div>
	</div>

</footer>
<?php do_action( 'lightning_footer_after' ); ?>
<?php do_action( 'lightning_site_footer_after' ); ?>
<?php wp_footer(); ?>
</body>
</html>
