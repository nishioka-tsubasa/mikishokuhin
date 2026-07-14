<?php
/*
 * @copyright     Copyright 2019, Wiz Co., Ltd. (https://www.wiznet.co.jp)
 * @author Kazuyoshi Watanabe
 * Template Name: 会社概要
 */
get_header(); ?>

<?php
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
	
	<div class="contents-headblock company">
		<?php
        $header_bg_url = miki_get_acf_image_url( 'header_bg' );
        if ( wp_is_mobile() ) {
            $header_bg_url = miki_get_acf_image_url( 'header_bg_mobile' );
        }
        ?>
		<div class="contents-backblock company" style="background-image: url(<?php echo $header_bg_url; ?>)" alt="<?php the_title(); ?>">
            <div class="title">
				<h1><?php the_title(); ?></h1>
            </div>
        </div>
	</div><!--.contents-headblock-->

</div><!--.section.page-header-->

<?php
/*-------------------------------------------*/
/* サイトコンテンツ
/*-------------------------------------------*/
?>
<div class="section siteContent company sub_background ">
    
    <div class="area">

        <div class="tab-wrap">
            <input id="tab-overview" type="radio" name="TAB" class="tab-switch"<?php echo isset( $_GET['page'] ) && $_GET['page'] == 'history' ? '' : ' checked="checked"' ;?> />
			<label class="tab-label" for="tab-overview">会社概要</label>
            <div class="tab-content">
        	    <div class="overview">
        	        <h3>会社概要</h3>
        	        <div class="column">
        	            <div class="column-title">会社名（法人名）</div><div class="column-text"><?php echo get_field( 'name' ); ?></div>
        			</div>
        	        <div class="column">
        	            <div class="column-title">代表者</div><div class="column-text"><?php echo get_field( 'president' ); ?></div>
        			</div>
        	        <div class="column">
        	            <div class="column-title">所在地</div><div class="column-text"><?php echo get_field( 'address' ); ?></div>
        			</div>
        	        <div class="column">
        	            <div class="column-title">電話番号</div><div class="column-text"><?php echo get_field( 'tel' ); ?></div>
        			</div>
        	        <div class="column">
        	            <div class="column-title">設立</div><div class="column-text"><?php echo get_field( 'establishment' ); ?></div>
        			</div>
        	        <div class="column">
        	            <div class="column-title">事業内容</div><div class="column-text"><?php echo get_field( 'business' ); ?></div>
        			</div>
        	        <div class="column">
        	            <div class="column-title">資本金</div><div class="column-text"><?php echo get_field( 'capital' ); ?></div>
        			</div>
        	        <div class="column">
        	            <div class="column-title">従業員数</div><div class="column-text"><?php echo get_field( 'employee' ); ?></div>
        			</div>
        	        <div class="column">
        	            <div class="column-title">許可・登録・免許</div><div class="column-text row"><?php echo get_field( 'license' ); ?></div>
        			</div>
        	        <h3>工場</h3>
        	        <div class="block factory">
        					<img src="<?php echo miki_get_acf_image_url( 'factory-nishinomiya-image' ); ?>" alt="<?php echo miki_get_acf_image_alt( 'factory-nishinomiya-image' ); ?>">
        					<div class="factory-detail">
        						<div class="factory-name">
        							西宮工場
        						</div>
        						<div class="factory-description">
        							<?php echo get_field( 'factory-nishinomiya-description' ); ?>
        						</div>
        						<div class="factory-address">
        							【所在地】<br>
        							<?php echo get_field( 'factory-nishinomiya-address' ); ?>
        							<a class="factory-link" href="<?php echo get_field( 'factory-nishinomiya-map' ); ?>" target="_blank" rel="noopener">MAP</a>
        						</div>
        						<div class="factory-access">
        							<?php if ( get_field( 'factory-nishinomiya-access' ) ) { ?>【交通アクセス】<br><?php } ?>
        							<?php echo get_field( 'factory-nishinomiya-access' ); ?>
        						</div>
        					</div>
        			</div>
        	        <div class="block factory sannan">
        				    <img src="<?php echo miki_get_acf_image_url( 'factory-sannan-image' ); ?>" alt="<?php echo miki_get_acf_image_alt( 'factory-sannan-image' ); ?>">
        					<div class="factory-detail sannan-factory">
        						<div class="factory-name">
        							山南工場
        						</div>
        						<div class="factory-description">
        							<?php echo get_field( 'factory-sannan-description' ); ?>
        						</div>
        						<div class="factory-address">
        							【所在地】<br>
        							<?php echo get_field( 'factory-sannan-address' ); ?>
        							<a class="factory-link" href="<?php echo get_field( 'factory-sannan-map' ); ?>" target="_blank" rel="noopener">MAP</a>
        						</div>
        						<div class="factory-access">
        							<?php if ( get_field( 'factory-sannan-access' ) ) { ?>【交通アクセス】<br><?php } ?>
        							<?php echo get_field( 'factory-sannan-access' ); ?>
        						</div>
        					</div>
        			</div>
        	    </div>
            </div>
            <input id="tab-history" type="radio" name="TAB" class="tab-switch"<?php echo isset( $_GET['page'] ) && $_GET['page'] == 'history' ? ' checked="checked"' : '' ;?> />
			<label class="tab-label" for="tab-history">理念・会社沿革</label>
            <div class="tab-content">
        		<div class="history">
        			<h3>経営方針</h3>
        	        <div class="block">
        				<?php echo get_field( 'management-policy' ); ?>
        			</div>
        	        <h3>企業理念</h3>
        	        <div class="block">
        				<?php echo get_field( 'corporate-philosophy', $post_id,false ); ?>
					</div>
					<div class="mikishokuhin-history">
        	        	<h3>会社沿革</h3>
        	        	<div class="block block-history">
        					<?php echo get_field( 'corporate-history' ); ?>
						</div>
					</div>
        	    </div>
            </div>
        </div>
    </div>
</div><!-- [ /.siteContent ] -->

<?php get_footer();
