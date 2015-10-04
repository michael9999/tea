<?php 
/*
Plugin Name: Add To Menu
Authors: New Signature
Version: 0.1.0
Tags: Menus
Author URI: http://www.newsignature.com/
*/


define( 'ADD_TO_MENU_PLUGIN_FILE', __FILE__ );


/**
 * Hook to register activation hook
 *
 * Adds capabilities for usage for others to customize.
 */
register_activation_hook( ADD_TO_MENU_PLUGIN_FILE, 'add_to_menu__install');
function add_to_menu__install(){
  global $wp_roles;
  
  $wp_roles->add_cap( 'administrator', 'add_to_menu' );
  $wp_roles->add_cap( 'administrator', 'add_to_menu_settings' );
}




//
// Options page
//
//

/**
 * Hook to register options admin page
 */
add_action( 'admin_menu', 'add_to_menu__menu' );
function add_to_menu__menu() {
  add_options_page( __('Add to menu settings'), __('Add to menu'), 'add_to_menu_settings', 'add-to-menu', 'add_to_menu__options' );
}



/**
 * Hook to admin_init to register settings
 */

function add_to_menu__register_settings(){
  register_setting( 'add-to-menu', 'add_to_menu_post_types_to_handle' );
}
add_action( 'admin_init', 'add_to_menu__register_settings' );



/**
 * Handle the options page
 */
function add_to_menu__options(){
  ?>
  <div class="wrap">
    <h2><?php _e('Add to menu settings'); ?></h2>
    <form method="post" action="options.php">
      <?php settings_fields( 'add-to-menu' ); ?>
      <table cellspacing="0" class="widefat page fixed">
      <thead>
        <tr>
          <th style="width: 5em;">Enabled</th>
          <th>Post type</th>
          <th>Post type description</th>
        </tr>
      </thead>
      
      <tbody>
      
      <?php 
      $checked = get_option('add_to_menu_post_types_to_handle'); 
      
      $post_types = get_post_types( array('public' => true,), 'objects');
      $alternate = true;
      foreach ($post_types as $post_type ): 
      ?>
        <tr <?php echo $alternate? 'class="alternate"': ''; $alternate = !$alternate; ?>>
          <td style="width: 5em;"><input name="add_to_menu_post_types_to_handle[]" id="add_to_menu_post_types_to_handle--<?php echo $post_type->name; ?>" type="checkbox" value="<?php echo $post_type->name; ?>" <?php echo in_array( $post_type->name, $checked )? 'checked="checked"' : ''; ?> /></td>
          <td>
          <label for="add_to_menu_post_types_to_handle--<?php echo $post_type->name; ?>"><?php 
            if(!empty($post_type->menu_icon)){
              echo '<img src="'.$post_type->menu_icon.'" alt="{icon}" style="vertical-align: top;" />';
            } else {
            
            } 
          ?>
          <?php echo $post_type->labels->name; ?></label></td>
          <td><?php echo $post_type->description; ?>&nbsp;</td>
        </tr>
      <?php endforeach; ?>
      </tbody></table>
      
      <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
      </p>

    </form>
  </div>
  <?php
}



//
// Handle the post edit page 'Add to menu' form elements and processing
//
//


/**
 * Hook add_meta_box
 *
 * Used to register meta boxes (on the post edit page)
 */
add_action('add_meta_boxes', 'add_to_menu__add_meta_boxes');
function add_to_menu__add_meta_boxes() {
  // Check if the user can change the menus
  if( current_user_can('edit_theme_options', 'add_to_menu') ){
    $post_types = get_post_types('','names'); 
    $display_on = get_option('add_to_menu_post_types_to_handle'); 
    $post_types = array_intersect( $post_types, $display_on );
    
    foreach ($post_types as $post_type ) {
      add_meta_box('add-to-menu', 
                 __('Add to menu'), 
                 'add_to_menu__add_to_menu_box', 
                 $post_type,
                 'side',
                 'low');
    }
  }
}



/**
 * Create contents of the 'Add to menu' meta box.
 */
function add_to_menu__add_to_menu_box(){
  global $post;
  
  if(isset($edit_post->ancestors)) {
    $active_post = get_post( $edit_post->ancestors[0] );
  } else {
    $active_post = $post;
  }
  
  
  $get_args = array('meta_key' => '_menu_item_object_id', 'meta_value' => $active_post->ID, 'post_status' => 'any' );
  
  $menus = wp_get_nav_menus( array('orderby' => 'name') );
  foreach( $menus as $id => $menu ){
    $has_menu_item = wp_get_nav_menu_items($menu->term_id, $get_args);
    $has_menu_item = count($has_menu_item) > 0;
    
    echo '<p><label><input type="checkbox" '.($has_menu_item? 'checked="checked" disabled="disabled"' : '').' name="add_to_menu['.$menu->term_id.']" /> '.$menu->name.'</label></p>';
    
  }
  
  
}


/**
 * Hook save_post
 *
 * Processes the 'Add to menu' meta box
 */
add_action('save_post', 'add_to_menu__save_post', 20, 2);
function add_to_menu__save_post( $post_id, $edit_post ) {
  // no need for this if it is a nav_menu_item
  if($post->post_type == 'nav_menu_item' || !isset($_POST['add_to_menu'])) {
    return;
  }
  
  // Get the actually post object instead of a revision post
  if(isset($edit_post->ancestors)) {
    $post_id = $edit_post->ancestors[0];
  }
  
  $post = get_post($post_id);
  
  
  // get the menus to add the menu item to
  $menus = $_POST['add_to_menu'];
  unset($_POST['add_to_menu']); // to prevent it getting handled again
  
  
  foreach($menus as $menu_id => $v ){
    
    // Find if the post has a parent post, if so try to put it underneath the parent in the menu
    $parent_id = 0;
    $walk_post = clone $post;
    // If this is the first time the post is being saved (hence being inserted into the DB), 
    // the post_parent is not set for some reason, then grab it from $_POST['parent_id']
    if( empty($walk_post->post_parent) && !empty($_POST['parent_id']) ){
      $walk_post->post_parent = $_POST['parent_id'];
    }
    
    while( $parent_id == 0 && $walk_post->post_parent != 0 ){
      $get_args = array('meta_key' => '_menu_item_object_id', 'meta_value' => $walk_post->post_parent, 'post_status' => 'any' );
      $parent_menu_items = wp_get_nav_menu_items($menu_id, $get_args);
      
      if( count($parent_menu_items) > 0 ){
        $parent_id = $parent_menu_items[0]->ID;
      }
      
      $walk_post = get_post( $walk_post->post_parent );
    }
    
    $menu_item_data = array(
      'menu-item-object' => $post->post_type,
      'menu-item-object-id' => $post_id,
      'menu-item-parent-id' => $parent_id, // the parent menu item I think
      // 'menu-item-position' => 0,
      'menu-item-type' => 'post_type',
      'menu-item-title' => $post->post_title,
      'menu-item-status' => 'draft',
      'menu-item-url' => get_permalink( $post_id ),
    );
    
    wp_update_nav_menu_item( $menu_id, 0, $menu_item_data);
  }
}

