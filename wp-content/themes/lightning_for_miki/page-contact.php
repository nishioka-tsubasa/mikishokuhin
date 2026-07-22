<?php
/*
 * Template Name: お問い合わせ
 */
get_header();

/*-------------------------------------------*/
/* Page Header
/*-------------------------------------------*/
?>
<div class="section page-header">
	<?php
	/*-------------------------------------------*/
	/* BreadCrumb
	/*-------------------------------------------*/
	$old_file_name[] = 'module_panList.php';
	if ( locate_template( $old_file_name, false, false ) ) {
		locate_template( $old_file_name, true, false );
	} else {
		get_template_part( 'template-parts/breadcrumb' );
	}
	?>

	<div class="contents-headblock meal contact">
		<div class="contents-backblock meal contact">
		</div><!--.notice-backblock-->
        <div class="contact-header">
            <p><?php the_title(); ?></p>
        </div>
	</div><!--.contents-headblock.meal-->

</div><!--.section.page-header-->

<?php
/*-------------------------------------------*/
/* サイトコンテンツ
/*-------------------------------------------*/
?>
<div class="section siteContent contact">
	<div class="container">
		<div class="row">
			<div class="col mainSection" id="main" role="main">
                <div class="contact-mwwpform-row">
					<div class="contact-title">
						<h1>お問い合わせ</h1>
						<p class="contact-subtitle">CONTACT</p>
					</div>
                    <?php 
                        if ( have_posts() ) {
                            while ( have_posts() ) {
                                the_post();
                                the_content();
                            } // end while(have_posts())
                        } // end if(have_posts())
                    ?>
                    <div class="contact-mwwpform">
                        <?php echo do_shortcode('[mwform_formkey key="157"]');?>
                    </div>
                </div>
			</div><!-- [ /.mainSection ] -->
		</div><!-- [ /.row ] -->
	</div><!-- [ /.container ] -->
</div><!-- [ /.siteContent ] -->

<?php
$page_contact_js = '/assets/js/page-contact.js';
$yubinbango_js    = '/assets/js/yubinbango.js';
?>
<script src="<?php echo esc_url( add_query_arg( 'ver', filemtime( get_stylesheet_directory() . $page_contact_js ), get_stylesheet_directory_uri() . $page_contact_js ) ); ?>"></script>
<script src="<?php echo esc_url( add_query_arg( 'ver', filemtime( get_stylesheet_directory() . $yubinbango_js ), get_stylesheet_directory_uri() . $yubinbango_js ) ); ?>"></script>

<?php get_footer(); ?>
