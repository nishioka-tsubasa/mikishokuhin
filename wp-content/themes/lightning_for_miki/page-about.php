<?php
/*
 * @copyright     Copyright 2019, Wiz Co., Ltd. (https://www.wiznet.co.jp)
 * @author Kazuyoshi Watanabe
 * Template Name: イーテーブルとは？
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
</div><!--.section.page-header-->

<?php
/*-------------------------------------------*/
/* サイトコンテンツ
/*-------------------------------------------*/
?>
<div class="section siteContent about">
	<div class="container">
		<div class="row">
			<div class="col mainSection" id="main" role="main">

				<div class="about-title">
					<div class="about-title-logo">
						<?php lightning_print_headlogo(); ?>
					</div>
					<p class="about-title-txt">とは</p>
				</div><!--.about-title-->

				<div class="about-kraftboard">
					<div class="about-kraftboard-block">
						<div class="about-kraftboard-block-txt">
							<p>E-TABLEが提供する食事を通して<br>介護施設で食事を楽しんでもらいたい</p>
						</div>
						<div class="about-kraftboard-block-push">
							<span class="about-kraftboard-block-push-pin"><img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/about-pin.png" alt="ピン"></span>
							<div class="about-kraftboard-block-push-flex">
								<div class="about-kraftboard-block-push-flex-left">
									<table>
										<tbody>
											<tr>
												<td class="about-kraftboard-block-push-flex-left-td-l">
													<p>
														<span class="about-kraftboard-block-push-flex-left-E">
															<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/about-E-logo.png" alt="E">
														</span>
														veryday
													</p>
												</td>
												<td class="about-kraftboard-block-push-flex-left-td-r">
													<p>
														- 毎日 -
													</p>
												</td>
											</tr>
											<tr>
												<td class="about-kraftboard-block-push-flex-left-td-l">
													<p>
														<span class="about-kraftboard-block-push-flex-left-E">
															<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/about-E-logo.png" alt="E">
														</span>
														njoy
													</p>
												</td>
												<td class="about-kraftboard-block-push-flex-left-td-r">
													<p>
														- 楽しい -
													</p>
												</td>
											</tr>
											<tr>
												<td class="about-kraftboard-block-push-flex-left-td-l">
													<p>
														<span class="about-kraftboard-block-push-flex-left-E">
															<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/about-E-logo.png" alt="E">
														</span>
														at
													</p>
												</td>
												<td class="about-kraftboard-block-push-flex-left-td-r">
													<p>
														- 食事を -
													</p>
												</td>
											</tr>
											<tr>
												<td class="about-kraftboard-block-push-flex-left-td-l">
													<p>
														<span class="about-kraftboard-block-push-flex-left-E">
															<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/about-E-logo.png" alt="E">
														</span>
														cology
													</p>
												</td>
												<td class="about-kraftboard-block-push-flex-left-td-r">
													<p>
														- エコに -
													</p>
												</td>
											</tr>
											<tr>
												<td class="about-kraftboard-block-push-flex-left-td-l">
													<p>
														<span class="about-kraftboard-block-push-flex-left-E">
															<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/about-E-logo.png" alt="E">
														</span>
														ndless
													</p>
												</td>
												<td class="about-kraftboard-block-push-flex-left-td-r">
													<p>
														- ずっと -
													</p>
												</td>
											</tr>
										</tbody>
									</table>
								</div><!--.about-kraftboard-block-push-flex-left-->
								<div class="about-kraftboard-block-push-flex-right">
									<div class="about-kraftboard-block-push-flex-right-block">
										<p>このような<br class="br-pc">
										想いから<br>
										E-TABLEは<br class="br-pc">
										生まれました<br>
										</p>
									</div>
								</div><!--.about-kraftboard-block-push-flex-right-->
							</div>
						</div>
					</div>
                </div><!--.about-kraftboard-->
                
                <?php foreach ( $cfs->get('sntnc_loop') as $sntnc_loop ) :
                ?>
                    <div class="about-things">
                        <div class="about-things-img">
                            <p class="about-things-img-title"><?php echo $sntnc_loop['title'] ?></p>
                            <img src="<?php echo $sntnc_loop['image'] ?>" alt="<?php echo $sntnc_loop['title'] ?>">
                        </div>
                        <div class="about-things-txt">
                            <p class="about-things-txt-p"><?php echo $sntnc_loop['txt'] ?></p>
                        </div>
                    </div><!--.about-things-->
                <?php endforeach;
                ?>
			
			</div><!-- [ /.mainSection ] -->
		</div><!-- [ /.row ] -->
	</div><!-- [ /.container ] -->
</div><!-- [ /.siteContent ] -->

<?php get_footer();
