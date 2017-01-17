<?php
/**
 * The contact template file.
 *
 * Template name: Contact
 *
 * @package Author
 * @since Author 1.0
 */

get_header(); ?>

<div id="content-wrap" class="clearfix">
	<!-- excerpt slider -->
	<?php get_template_part( 'template-slider' ); ?>

	<div id="content">
		<!-- post navigation -->
		<?php get_template_part( 'template-title' ); ?>

		<div class="post-wrap">
			<!-- load the posts -->
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

				<div <?php post_class('post'); ?>>
					<div class="box">

						<div class="frame">
							<div class="title-wrap">
								<h1 class="entry-title"><?php the_title(); ?></h1>
							</div><!-- title wrap -->

							<div class="post-content">

								<?php the_content( __( 'Read More', 'author' ) ); ?>

								<div class="comment-form" id="contact-form-wrapper">
									<form action="<?php echo get_permalink(); ?>" method="post" id="contact-form">
										<p class="comment-form-author">
											<label for="author">Name <span class="required">*</span></label>
											<input id="author" name="namee" type="text" size="30" maxlength="245" aria-required="true" required="required">
										</p>
										<p class="comment-form-email">
											<label for="email">Email <span class="required">*</span></label>
											<input id="email" name="_replyto" type="text" size="30" maxlength="100" aria-describedby="email-notes" aria-required="true" required="required">
										</p>
										<p class="comment-form-comment">
											<label for="comment">Message <span class="required">*</span></label>
											<textarea id="comment" name="commente" cols="45" rows="8" maxlength="65525" aria-required="true" required="required"></textarea>
										</p>
										<p class="form-submit">
											<input type="hidden" name="_subject" value="New message sent from the blog"/>
											<input type="text" name="_gotcha" style="display:none">
											<input name="submit" type="submit" id="contact-form-submit" value="Send">
										</p>
									</form>
								</div>

								<div class="pagelink">
									<?php wp_link_pages(); ?>
								</div>
							</div><!-- post content -->
						</div><!-- frame -->

						<!-- post meta -->
						<?php get_template_part( 'template-meta' ); ?>
					</div><!-- box -->
				</div><!-- post-->

			<?php endwhile; ?>
		</div><!-- post wrap -->

		<!-- post navigation -->
		<?php get_template_part( 'template-nav' ); ?>

		<?php else: ?>
	</div><!-- content -->

<?php endif; ?>
	<!-- end posts -->
</div><!--content-->

<!-- load the sidebar -->
<?php get_sidebar(); ?>
</div><!-- content wrap -->

<!-- load footer -->
<?php get_footer();
