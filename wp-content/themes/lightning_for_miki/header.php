<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
global $lightning_theme_options;
$lightning_theme_options = get_option( 'lightning_theme_options' );
?>
<?php wp_head(); ?>

</head>
<body <?php body_class(); ?>>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
} else {
	do_action( 'wp_body_open' );
}
do_action( 'lightning_header_before' );
?>
<header class="<?php lightning_the_class_name( 'header' ); ?>">
	<link rel='stylesheet' href='<?php echo get_stylesheet_directory_uri(); ?>/assets/css/common.css' type='text/css' media='all' />
	<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/slick.css" media="all" />
	<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/css/slick-theme.css" media="all" />
	<?php do_action( 'lightning_header_prepend' ); ?>
	<div class="container siteHeadContainer">
		<div class="navbar-header">
			<?php
			if ( is_front_page() ) {
				$title_tag = 'h1';
			} else {
				$title_tag = 'p';
			}
			?>
			<<?php echo $title_tag; ?> class="navbar-brand siteHeader_logo">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span><?php lightning_print_headlogo(); ?></span>
			</a>
			</<?php echo $title_tag; ?>>
			<?php do_action( 'lightning_header_logo_after' ); ?>
			<?php
			$args  = array(
				'theme_location' => 'Header',
				'container'      => 'nav',
				'items_wrap'     => '<ul id="%1$s" class="%2$s ' . lightning_get_the_class_name( 'nav_menu_header' ) . '">%3$s</ul>',
				'fallback_cb'    => '',
				'echo'           => false,
				'walker'         => new description_walker(),
			);
			$gMenu = wp_nav_menu( $args );
		?>
		</div>

		<?php
		if ( $gMenu ) {
			echo '<div id="gMenu_outer" class="gMenu_outer">';
			echo $gMenu;
			echo '</div>';
		}
		?>
	</div>
	<?php do_action( 'lightning_header_append' ); ?>
	
	<script>
	  (function(d) {
	    var config = {
	      kitId: 'ltw0lgl',
	      scriptTimeout: 3000,
	      async: true
	    },
	    h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src='https://use.typekit.net/'+config.kitId+'.js';tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
	  })(document);
	</script>
	
</header>
<?php do_action( 'lightning_header_after' ); ?>
