<?php
/*
 * @copyright     Copyright 2019, Wiz Co., Ltd. (https://www.wiznet.co.jp)
 * @author Kazuyoshi Watanabe
 * Template Name: 採用情報（子ページ）
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
	
	<div class="contents-headblock recruit detail">
		<div class="contents-backblock recruit detail">
            <?php echo get_field( 'header_title' ); ?>
        </div>
	</div><!--.contents-headblock-->

</div><!--.section.page-header-->

<?php
/*-------------------------------------------*/
/* サイトコンテンツ
/*-------------------------------------------*/
?>
<div class="section siteContent recruit sub_background ">
    <div class="recruit-interview">
        <?php
            $interview_detail_number = 0;
            foreach( $cfs->get('interview') as $interview ) :
                $interview_detail_number = $interview_detail_number + 1;
        ?>
                <div class="recruit-interview-block <?php echo key($interview['bg']); ?> <?php echo key($interview['position']); ?>">
                    <div class="container">
                        <?php if ( $interview['interrupt'] ) : ?>
                            <h2><?php echo $interview['interrupt']; ?></h2>
                        <?php endif; ?>
                        <div class="interview-section">
                            <?php if ( $interview['question'] ) : ?>
                                <div class="interview-detail">
                                    <h3><span class="interview-detail-number">Q<?php echo $interview_detail_number; ?></span><?php echo $interview['question']; ?></h2>
                                    <?php
                                    if ( wp_is_mobile() ) :
                                    ?>
                                    <?php if ( $interview['img'] ) : ?>
                                        <div class="interview-image">
                                            <div class="interview-photo">
                                                <img src="<?php echo $interview['img']; ?>" alt="Q<?php echo $interview_detail_number; ?>写真">
                                            </div>
                                            <div class="interview-photo-detail">
                                                <?php echo $interview['img-detail']; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php
                                    endif;
                                    ?>
                                    <p><?php echo $interview['answer']; ?></p>
                                </div>
                            <?php endif; ?>
                            <?php
                            if ( !wp_is_mobile() ) :
                            ?>
                            <?php if ( $interview['img'] ) : ?>
                                <div class="interview-image">
                                    <div class="interview-photo">
                                        <img src="<?php echo $interview['img']; ?>" alt="Q<?php echo $interview_detail_number; ?>写真">
                                    </div>
                                    <div class="interview-photo-detail">
                                        <?php echo $interview['img-detail']; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div> 
                    </div><!-- [ /.container ] -->
                </div><!-- [ /.recruit-interview-block ] -->
        <?php
            endforeach;
        ?>
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
        <div class="recruit-back"><a href="../../recruit">採用情報へ戻る</a></div>
    </div>
</div><!-- [ /.siteContent ] -->

<?php get_footer();
