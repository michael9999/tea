<?php
/*
Template Name: Evenements-thés
*/
?>

<?php get_header(); ?>

			<!-- BEGIN #primary .hfeed-->
			<div id="primary" class="hfeed">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
				
				<?php zilla_page_before(); ?>
				<!-- BEGIN .hentry -->
				<div <?php post_class() ?> id="post-<?php the_ID(); ?>">
				<?php zilla_page_start(); ?>
				
					<h1 class="entry-title"><?php the_title(); ?></h1>
					
					<!-- BEGIN .entry-content -->
					<div class="entry-content">
						<?php the_content(__('Read more...', 'zilla')); ?>
						<?php wp_link_pages(array('before' => '<p><strong>'.__('Pages:', 'zilla').'</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
					<!-- END .entry-content -->
					</div>
					
					<!-- BEGIN .archive-lists -->
					<div class="archive-lists">
						
						<h3><?php _e('Evenement(h)és', 'zilla') ?></h3>
						
						<ul>
					
						<?php
                                $catPost = get_posts('cat=15&posts_per_page=-1'); //change this
                                   foreach ($catPost as $post) : setup_postdata($post); ?>
                                
                                <a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a>
                                    <?php // the_excerpt(); ?> 
                                
                                  
                                <?php  endforeach;?>
						
						
						</ul>
						
						
					
					<!-- END .archive-lists -->
					</div>
                
                <?php zilla_page_end(); ?>
                <!-- END .hentry-->  
				</div>
				<?php zilla_page_after(); ?>
				
				<?php endwhile; else: ?>

				<!-- BEGIN #post-0-->
				<div id="post-0" <?php post_class() ?>>
				
					<h1 class="entry-title"><?php _e('Error 404 - Not Found', 'zilla') ?></h1>
				
					<!-- BEGIN .entry-content-->
					<div class="entry-content">
						<p><?php _e("Sorry, but you are looking for something that isn't here.", "zilla") ?></p>
					<!-- END .entry-content-->
					</div>
				
				<!-- END #post-0-->
				</div>

			<?php endif; ?>
			<!-- END #primary .hfeed-->
			</div>
				

<?php get_sidebar('page'); ?>

<?php get_footer(); ?>