<?php
/*
Plugin Name: Dealership popular posts
Description: Dealership popular posts plugin
*/


/* Start Adding Functions Below this Line */

class popular_posts_as_headings extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_popular_posts_as_headings', 'description' => __( "Popular posts as headings" ) );
		parent::__construct('popular_posts_as_headings', __('Popular posts as headings.'), $widget_ops);
	}
  
	function widget( $args, $instance ) {
		extract( $args );

		/** This filter is documented in wp-includes/default-widgets.php */
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Popular posts' ) : $instance['title'], $instance, $this->id_base );

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;


			$popularpost = new WP_Query( array( 'posts_per_page' => 4, 'meta_key' => 'wpb_post_views_count', 'orderby' => 'meta_value_num', 'order' => 'DESC'  ) );
			
			while ( $popularpost->have_posts() ) : $popularpost->the_post();

?>
				<h6 class="popular posts">
					<a href="
						<?php the_permalink(); ?>"><?php the_title(); ?>
					</a>
				</h6>
			<?php endwhile;
      echo $after_widget;
		}

		

  function wpb_set_post_views($postID) {
      $count_key = 'wpb_post_views_count';
      $count = get_post_meta($postID, $count_key, true);
      if($count==''){
          $count = 0;
          delete_post_meta($postID, $count_key);
          add_post_meta($postID, $count_key, '0');
      }else{
          $count++;
          update_post_meta($postID, $count_key, $count);
      }
  }
  
  //To keep the count accurate, lets get rid of prefetching
  function rid_prefectching() {
    remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
  }

  function wpb_track_post_views ($post_id) {
      if ( !is_single() ) return;
      if ( empty ( $post_id) ) {
          global $post;
          $post_id = $post->ID;    
      }
      wpb_set_post_views($post_id);
  }  
  function add_wpb_track_post_views() {
    add_action( 'wp_head', 'wpb_track_post_views');
  }

  function wpb_get_post_views($postID){
      $count_key = 'wpb_post_views_count';
      $count = get_post_meta($postID, $count_key, true);
      if($count==''){
          delete_post_meta($postID, $count_key);
          add_post_meta($postID, $count_key, '0');
          return "0 View";
      }
      return $count.' Views';
  }
  
	function form( $instance ) {
		//Defaults
		$instance = wp_parse_args( (array) $instance, array( 'title' => '') );
		$title = esc_attr( $instance['title'] );
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
  <?php	
  }

}

// Register and load the widget
function ppah_load_widget() {
  register_widget( 'popular_posts_as_headings' );
}
add_action( 'widgets_init', 'ppah_load_widget' );

/* Stop Adding Functions Below this Line */
?>