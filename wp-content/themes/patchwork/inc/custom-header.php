<?php
/**
 * Sample implementation of the Custom Header feature
 * http://codex.wordpress.org/Custom_Headers
 *
 * You can add an optional custom header image to header.php like so ...

	<?php $header_image = get_header_image();
	if ( ! empty( $header_image ) ) { ?>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
			<img src="<?php header_image(); ?>" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="" />
		</a>
	<?php } // if ( ! empty( $header_image ) ) ?>

 *
 * @package Patchwork
 * @since patchwork 1.0
 */

/**
 * Setup the WordPress core custom header feature.
 *
 * Use add_theme_support to register support for WordPress 3.4+
 * as well as provide backward compatibility for previous versions.
 * Use feature detection of wp_get_theme() which was introduced
 * in WordPress 3.4.
 *
 * @uses patchwork_header_style()
 * @uses patchwork_admin_header_style()
 * @uses patchwork_admin_header_image()
 *
 * @package Patchwork
 */
function patchwork_custom_header_setup() {

	$defaults = patchwork_get_layout_defaults();

	$args = array(
		'default-image'          => '',
		'default-text-color'     => $defaults['default-color'],
		'width'                  => 650,
		'height'                 => 150,
		'flex-height'            => true,
		'wp-head-callback'       => 'patchwork_header_style',
		'admin-head-callback'    => 'patchwork_admin_header_style',
		'admin-preview-callback' => 'patchwork_admin_header_image',
	);

	$args = apply_filters( 'patchwork_custom_header_args', $args );

	if ( function_exists( 'wp_get_theme' ) ) {
		add_theme_support( 'custom-header', $args );
	} else {
		// Compat: Versions of WordPress prior to 3.4.
		define( 'HEADER_TEXTCOLOR',    $args['default-text-color'] );
		define( 'HEADER_IMAGE',        $args['default-image'] );
		define( 'HEADER_IMAGE_WIDTH',  $args['width'] );
		define( 'HEADER_IMAGE_HEIGHT', $args['height'] );
		add_custom_image_header( $args['wp-head-callback'], $args['admin-head-callback'], $args['admin-preview-callback'] );
	}
}
add_action( 'after_setup_theme', 'patchwork_custom_header_setup' );

/**
 * Shiv for get_custom_header().
 *
 * get_custom_header() was introduced to WordPress
 * in version 3.4. To provide backward compatibility
 * with previous versions, we will define our own version
 * of this function.
 *
 * @return stdClass All properties represent attributes of the curent header image.
 *
 * @package Patchwork
 * @since patchwork 1.1
 */

if ( ! function_exists( 'get_custom_header' ) ) {
	function get_custom_header() {
		return (object) array(
			'url'           => get_header_image(),
			'thumbnail_url' => get_header_image(),
			'width'         => HEADER_IMAGE_WIDTH,
			'height'        => HEADER_IMAGE_HEIGHT,
		);
	}
}

if ( ! function_exists( 'patchwork_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog
 *
 * @see patchwork_custom_header_setup().
 *
 * @since patchwork 1.0
 */
function patchwork_header_style() {

	// If no custom options for text are set, let's bail
	// get_header_textcolor() options: HEADER_TEXTCOLOR is default, hide text (returns 'blank') or any hex value
	if ( HEADER_TEXTCOLOR == get_header_textcolor() )
		return;
	// If we get this far, we have custom styles. Let's do this.
	?>
	<style type="text/css">
	<?php
		// Has the text been hidden?
		if ( 'blank' == get_header_textcolor() ) :
	?>
		.site-title,
		.site-description {
			position: absolute !important;
			clip: rect(1px 1px 1px 1px); /* IE6, IE7 */
			clip: rect(1px, 1px, 1px, 1px);
		}
	<?php
		// If the user has set a custom color for the text use that
		else :
	?>
		.site-title a,
		.site-description {
			color: #<?php echo get_header_textcolor(); ?> !important;
		}
	<?php endif; ?>
	</style>
	<?php
}
endif; // patchwork_header_style


/**
 * Enqueue custom header fonts
 */
function patchwork_header_fonts( $hook_suffix ) {

	if ( $hook_suffix != 'appearance_page_custom-header' )
		return;

	wp_enqueue_style( 'googlefonts', 'http://fonts.googleapis.com/css?family=Sail|Cabin' );

}

add_action( 'admin_enqueue_scripts', 'patchwork_header_fonts' );

if ( ! function_exists( 'patchwork_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * @see patchwork_custom_header_setup().
 *
 * @since patchwork 1.0
 */
function patchwork_admin_header_style() {
?>
	<style type="text/css">
	.appearance_page_custom-header #headimg {
		border: none;
	}
	#headimg h1,
	#desc {
	}
	#headimg h1 {
		text-align: center;
	}
	#headimg h1 a {
		border-bottom: 0px none;
		font-family: Sail, serif;
		font-size: 48px;
		line-height: 48px;
		margin: 0;
		text-decoration: none;
	}
	#desc {
		text-align: center;
		font-family: Cabin, Helvetica, sans-serif;
		font-size: 16px;
		margin: 0;
	}
	#headimg img {
		display: block;
		margin: 0 auto;
	}
	</style>
<?php
}
endif; // patchwork_admin_header_style

if ( ! function_exists( 'patchwork_admin_header_image' ) ) :
/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * @see patchwork_custom_header_setup().
 *
 * @since patchwork 1.0
 */
function patchwork_admin_header_image() { ?>
	<div id="headimg">
		<?php $header_image = get_header_image();
		if ( ! empty( $header_image ) ) : ?>
			<img src="<?php echo esc_url( $header_image ); ?>" alt="" />
		<?php endif; ?>
		<?php
		if ( 'blank' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) || '' == get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) )
			$style = ' style="display:none;"';
		else
			$style = ' style="color:#' . get_theme_mod( 'header_textcolor', HEADER_TEXTCOLOR ) . ';"';
		?>
		<h1><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<div id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
	</div>
<?php }
endif; // patchwork_admin_header_image