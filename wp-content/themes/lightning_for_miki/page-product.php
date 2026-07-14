<?php
/*
 * @copyright     Copyright 2019, Wiz Co., Ltd. (https://www.wiznet.co.jp)
 * @author Kazuyoshi Watanabe
 * Template Name: 商品一覧
 */
get_header(); ?>

<div class="product-bg">

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
        
        <div class="contents-headblock product sub_background">
            <div class="contents-backblock product-back">
                <div class="title">
                    <h1><?php the_title(); ?></h1>
                    <p class="subtitle">PRODUCT LIST</p>
                </div>
                <div class="subtext">
                    <p>「詳細へ」をクリックすると<br>三基商事のHPへリンクします。</p>
                </div>
            </div>
        </div><!--.contents-headblock-->

    </div><!--.section.page-header-->

    <?php
    /*-------------------------------------------*/
    /* サイトコンテンツ
    /*-------------------------------------------*/
    ?>
    <div class="section siteContent product sub_background ">

        <div class="container">

            <div class="factory-block">
                <h2>西宮工場</h2>
                <div class="factory-block-about">
                    <h3>主な取扱商品</h3>

                <?php
                    foreach( miki_get_cfs_loop( 'nishinomiya_loop' ) as $nishinomiya_loop ) :
                ?>

                    <div class="factory-block-about-inner">
                        <img src="<?php echo miki_array_value( $nishinomiya_loop, 'nishinomiya_img' ); ?>" alt="<?php echo miki_array_value( $nishinomiya_loop, 'nishinomiya_name' ); ?>">
                        <h4><?php echo miki_array_value( $nishinomiya_loop, 'nishinomiya_name' ); ?></h4>
                        <p><?php echo miki_array_value( $nishinomiya_loop, 'nishinomiya_about' ); ?></p>
                        <a href="<?php echo miki_array_value( $nishinomiya_loop, 'nishinomiya_url' ); ?>" class="detail-button" target="_blank" rel="noopener">詳細へ</a>
                    </div> 

                <?php
                    endforeach;
                ?>

                </div><!-- [ /.factory-block-about ] -->
            </div><!-- [ /.factory-block ] -->

            <?php echo miki_get_cfs_value( 'cosme_html' ); ?>

            <div class="factory-block end-factory-block">
                <h2>山南工場</h2>
                <div class="factory-block-about">
                    <h3>主な取扱商品</h3>

                <?php
                    foreach( miki_get_cfs_loop( 'sannan_loop' ) as $nishinomiya_loop ) :
                ?>

                    <div class="factory-block-about-inner">
                        <img src="<?php echo miki_array_value( $nishinomiya_loop, 'sannan_img' ); ?>" alt="<?php echo miki_array_value( $nishinomiya_loop, 'sannan_name' ); ?>">
                        <h4><?php echo miki_array_value( $nishinomiya_loop, 'sannan_name' ); ?></h4>
                        <p><?php echo miki_array_value( $nishinomiya_loop, 'sannan_about' ); ?></p>
                        <a href="<?php echo miki_array_value( $nishinomiya_loop, 'sannan_url' ); ?>" class="detail-button" target="_blank" rel="noopener">詳細へ</a>
                    </div> 

                <?php
                    endforeach;
                ?>

                </div><!-- [ /.factory-block-about ] -->
            </div><!-- [ /.factory-block ] -->

        </div><!-- [ /.container ] -->

    </div><!-- [ /.siteContent ] -->

</div><!-- [ /.product-bg ] -->
<?php get_footer();
