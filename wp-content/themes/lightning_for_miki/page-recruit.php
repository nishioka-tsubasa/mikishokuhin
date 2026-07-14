<?php
/*
 * @copyright     Copyright 2019, Wiz Co., Ltd. (https://www.wiznet.co.jp)
 * @author Kazuyoshi Watanabe
 * Template Name: 採用情報
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
	
	<div class="contents-headblock recruit sub_background">
        <?php
        $header_bg_url = miki_get_acf_image_url( 'header_bg' );
        if ( wp_is_mobile() ) {
            $header_bg_url = miki_get_acf_image_url( 'header_bg_mobile' );
        }
        ?>
		<div class="contents-backblock recruit" style="background-image: url(<?php echo $header_bg_url; ?>)" alt="<?php the_title(); ?>">
            <div class="title">
				<h1 class="recruit-page-tit"><?php the_title(); ?></h1>
            </div>
        </div>
	</div><!--.contents-headblock-->

</div><!--.section.page-header-->

<?php
/*-------------------------------------------*/
/* サイトコンテンツ
/*-------------------------------------------*/
?>
<div class="section siteContent recruit sub_background">
    <div class="container">
        <?php echo get_field( 'overview' ); ?>
        <div class="voice">
            <h3 class="voice-title">社員の声</h3>
        </div>
        <div id="slick">
            <div class="employee-list">
            <?php
                $fields = miki_get_cfs_loop( 'employee_list' );
                foreach ($fields as $field) :
            ?>
            <div class="employee">
                <a href="/recruit/<?php echo miki_array_value( $field, 'employee_link' ); ?>">
                    <div class="employee__belongs">
                        <img src="<?php echo miki_array_value( $field, 'employee_belongs' ); ?>">
                    </div>
                    <div class="employee__img">
                        <img src="<?php echo miki_array_value( $field, 'employee_img' ); ?>">
                    </div>
                    <div class="employee__detail">
                        <div class="employee__detail__name">
                            <h3><?php echo miki_array_value( $field, 'employee_name' ); ?></h3>
                            <div class="employee__detail__name__catch">
                                <?php echo miki_array_value( $field, 'employee_catch' ); ?>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
            </div>
        </div>
        <div class="employee-link_list">       
            <div class="employee-link">
                <a href="/recruit/kitamura">
                    <div class="employee-link__name">
                        喜多村 高雅さんを見る<span>→</span>
                    </div>
                </a>
            </div>
            <div class="employee-link">
                <a href="/recruit/adachi">
                    <div class="employee-link__name">
                        足立 祐介さん<br>足立 翔さんを見る<span>→</span>
                    </div>
                </a>
            </div>
            <div class="employee-link">
                <a href="/recruit/matsushita_m">
                    <div class="employee-link__name">
                        松下 光咲さんを見る<span>→</span>
                    </div>
                </a>
            </div>
            <div class="employee-link">
                <a href="/recruit/matsushita_k">
                    <div class="employee-link__name">
                        松下 知樹さんを見る<span>→</span>
                    </div>
                </a>
            </div>
            <div class="employee-link">
                <a href="/recruit/maeda_a">
                    <div class="employee-link__name">
                        前田 綾子さんを見る<span>→</span>
                    </div>
                </a>
            </div>
            <div class="employee-link">
                <a href="/recruit/ofiji">
                    <div class="employee-link__name">
                        大藤 直也さんを見る<span>→</span>
                    </div>
                </a>
            </div>
        </div>
    </div><!-- [ /.container ] -->
</div><!-- [ /.siteContent ] -->

<div class="section siteContent recruit organization-back">
    <div class="container">
        <?php echo get_field( 'organization' ); ?>
    </div><!-- [ /.container ] -->
</div><!-- [ /.siteContent ] -->

<div class="section siteContent recruit sub_background">
    <div class="container">
        <?php echo get_field( 'event' ); ?>
    </div><!-- [ /.container ] -->
</div><!-- [ /.siteContent ] -->

<div class="section siteContent recruit organization-back">
    <div class="container">
        <?php echo get_field( 'recruitment' ); ?>
    </div><!-- [ /.container ] -->
</div><!-- [ /.siteContent ] -->

<?php get_footer();
