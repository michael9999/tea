<?php
/*
Template Name: Home Page Blog Style
*/
?>

<?php get_header(); ?>
            
            <!-- BEGIN #primary .hfeed-->
            <div id="primary" class="hfeed clearfix">           

            <?php 
            if ( get_query_var('paged') ) {
                $paged = get_query_var('paged');
            } elseif ( get_query_var('page') ) {
                $paged = get_query_var('page');
            } else {
                $paged = 1;
            }

            $temp = $wp_query;
            // enable use of more tag on template page  
            global $more; $more = 0;           

                //boucle pour les catégories Tea Time...
                $wp_query= null;            
                $wp_query = new WP_Query( array(
                    'post_type' => 'post',
                    'paged' => $paged,
                    'category__in' => array(3,4,5,6,7,8,13),
                    'posts_per_page' => 1
                ) );
                
                while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
                
                <?php zilla_post_before(); ?>
                <!-- BEGIN .hentry -->
                <div <?php post_class(); ?> id="post-<?php the_ID(); ?>">

                <a class="rub-name" href="/tea-time-selon-the-toi/"><h2>Tea Time selon Thé + toi</h2></a>

                <?php zilla_post_start(); ?>
                
                <?php
                    $format = get_post_format();
                    $format = ($format) ? $format : 'standard';
                                        
                    get_template_part( 'content', $format);
                    
                    if( $format == 'standard' || $format == 'gallery' || $format == 'video' || $format == 'audio' ) {
                        get_template_part( 'content', 'meta' ); 
                    }
                ?>
                                    
                <?php zilla_post_end(); ?>
                <!-- END .hentry-->  
                </div>
                <?php zilla_post_after(); ?>

                <?php endwhile; ?>

                <?php 
                //boucle pour les catégories Actualit(h)és...
                $wp_query= null;            
                $wp_query = new WP_Query( array(
                    'post_type' => 'post',
                    'paged' => $paged,
                    'category__in' => array(14),
                    'posts_per_page' => 1
                ) );
                
                while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
                
                <?php zilla_post_before(); ?>
                <!-- BEGIN .hentry -->
                <div <?php post_class(); ?> id="post-<?php the_ID(); ?>">

                <a class="rub-name" href="/actualithe/"><h2>Actualit(h)és</h2></a>

                <?php zilla_post_start(); ?>
                
                <?php
                    $format = get_post_format();
                    $format = ($format) ? $format : 'standard';
                                        
                    get_template_part( 'content', $format);
                    
                    if( $format == 'standard' || $format == 'gallery' || $format == 'video' || $format == 'audio' ) {
                        get_template_part( 'content', 'meta' ); 
                    }
                ?>
                                    
                <?php zilla_post_end(); ?>
                <!-- END .hentry-->  
                </div>
                <?php zilla_post_after(); ?>

                <?php endwhile; ?>

                <?php 
                //boucle pour les catégories Curiosit(h)és...
                $wp_query= null;            
                $wp_query = new WP_Query( array(
                    'post_type' => 'post',
                    'paged' => $paged,
                    'category__in' => array(12),
                    'posts_per_page' => 1
                ) );
                
                while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
                
                <?php zilla_post_before(); ?>
                <!-- BEGIN .hentry -->
                <div <?php post_class(); ?> id="post-<?php the_ID(); ?>">

                <a class="rub-name" href="/category/curiosithes/"><h2>Curiosit(h)és</h2></a>

                <?php zilla_post_start(); ?>
                
                <?php
                    $format = get_post_format();
                    $format = ($format) ? $format : 'standard';
                                        
                    get_template_part( 'content', $format);
                    
                    if( $format == 'standard' || $format == 'gallery' || $format == 'video' || $format == 'audio' ) {
                        get_template_part( 'content', 'meta' ); 
                    }
                ?>

                <?php zilla_post_end(); ?>
                <!-- END .hentry-->  
                </div>
                <?php zilla_post_after(); ?>

                <?php endwhile; ?>

            <!-- END #primary .hfeed-->
            </div>

        <?php get_sidebar( "home" ); ?>
        </div>

        
        <?php $wp_query = null; $wp_query = $temp;?>
        <?php get_footer(); ?>