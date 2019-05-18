<?php

get_header( );
global $TLPfoodmenu;

	while ( have_posts() ) : the_post();
	$image_area="tlp-col-md-5 tlp-col-lg-5 tlp-col-sm-6 paddingl0";
	$content_area="tlp-col-md-7 tlp-col-lg-7 tlp-col-sm-6 paddingr0";
	$settings = get_option( $TLPfoodmenu->options['settings'] );
	?>
	<main id="main" class="site-main" rol="main">
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="container">
				<div class="row tlp-food-menu-detail">
                    <?php
		            if(empty($settings['general']['hide_image'])) {
                    ?>
					<div class="<?php echo $image_area?>">
						<?php
				      		if(has_post_thumbnail()){
				      			$imgThum = get_the_post_thumbnail( get_the_ID(), array( 500, 500 ) );
				      			$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
				      			$imgFull = $image[0];
				      		}else{
								$imgThum = "<img src='".$TLPfoodmenu->assetsUrl .'images/demo-100x100.png'."' alt='".get_the_title()."' />";
								$imgFull = $TLPfoodmenu->assetsUrl .'images/demo-100x100.png';
				      		}
				      		echo "<a href='$imgFull' class='tlp-colorbox'>".$imgThum."</a>";
						?>
						</div>
                        <?php }else{
			            $content_area = "tlp-col-sm-12";
                    } ?>
						<div class="<?php echo $content_area?>">
							<?php
								$gTotal = $TLPfoodmenu->getPriceWithLabel();
							?>
							<h2><?php the_title(); ?></h2>
                            <?php if(empty($settings['general']['hide_price'])){ ?>
							<div class="offers">
								<p class="price">
									<span class="amount"><?php echo $gTotal; ?></span>
								</p>
							</div>
                            <?php } ?>
							<div class="fm-details entry">
								<?php
									the_content();
								?>
							</div> 
						</div>
						
				</div> <!-- fm-row  -->

				<div class="fm-Extra">

				</div><!-- fm-Extra  -->

			</div> <!-- single-food  -->
		</article>
	</main>
	<?php
	endwhile;
get_footer( );
