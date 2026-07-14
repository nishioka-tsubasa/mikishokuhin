<?php
/*
 * @copyright     Copyright 2019, Wiz Co., Ltd. (https://www.wiznet.co.jp)
 * @author Kazuyoshi Watanabe
 * Template Name: 事業内容
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
	
	<div class="contents-headblock business">
        <?php
        $header_bg_url = miki_get_acf_image_url( 'header_bg' );
        if ( wp_is_mobile() ) {
            $header_bg_url = miki_get_acf_image_url( 'header_bg_mobile' );
        }
        ?>
		<div class="contents-backblock business" style="background-image: url(<?php echo $header_bg_url; ?>)" alt="<?php the_title(); ?>">
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
<div class="section siteContent business sub_background ">
    
    <div class="area notice-area overview-area">
        <?php echo get_field( 'overview' ); ?>
    </div>

    <div class="area department-intro">
		<div class="title">部門紹介</div>
		<div class="subtitle">DEPARTMENT INTRODUCTION</div>

        <div class="tab-wrap">
            <input id="tab-overview" type="radio" name="TAB" class="tab-switch" checked="checked" /><label style="display:none" class="tab-label" for="tab-overview">部門一覧</label>
            <div class="tab-content">
        	    <div class="dept-all">
					 <?php echo get_field( 'dept-all' ); ?>
        	    </div>
            </div>
            <!-- <input id="tab-production" type="radio" name="TAB" class="tab-switch" /><label class="tab-label" for="tab-production">生産部門</label>
            <div class="tab-content">
        	    <div class="dept-production">
					 <?php echo get_field( 'dept-production' ); ?>
        	    </div>
            </div>
            <input id="tab-quality" type="radio" name="TAB" class="tab-switch" /><label class="tab-label" for="tab-quality">品質管理<br>部門</label>
            <div class="tab-content">
        	    <div class="dept-quality">
					 <?php echo get_field( 'dept-quality' ); ?>
        	    </div>
            </div> -->
        </div>
    </div>

</div><!-- [ /.siteContent ] -->

<?php get_footer();
