<?php
/*-----------------------------------------------------------------------------------*/
/*	Register Sidebars
/*-----------------------------------------------------------------------------------*/

if ( !function_exists( 'thetoi_sidebars_init' ) ) {
    function thetoi_sidebars_init() {
    	register_sidebar(array(
    		'name' => __('Home Page Sidebar', 'zilla'),
    		'description' => __('Widget area for home page', 'zilla'),
    		'id' => 'sidebar-home',
    		'before_widget' => '<div id="%1$s" class="widget %2$s">',
    		'after_widget' => '</div>',
    		'before_title' => '<h3 class="widget-title">',
    		'after_title' => '</h3>',
    	));
    }
}
add_action( 'widgets_init', 'thetoi_sidebars_init' );

?>