<?php
/**
 * Plugin Name: Simple Recent Posts
 * Description: A plugin that has a simple widget to display recent posts. Minimal customization includes: title of widget area, number of posts to display and option to link to all posts.
 * Version: 0.3
 * Author: Rian Rainey
 * Author URI: http://www.rianrainey.com
 */

class SimpleRecentPosts extends WP_Widget
{
  
  function SimpleRecentPosts()
  {
    // Description that shows up below Widget Title in list of widgets
    $widget_ops = array('classname' => 'SimpleRecentPosts', 'description' => 'Displays a simple list of recent posts' );
    
    // Title that appears in Widget List after activated
    $this->WP_Widget('SimpleRecentPosts', 'Simple Recent Posts', $widget_ops);
  }
  
  /**
   * Widget Form with all the form elements
   */
  function form($instance)
    {
  		$instance       = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '' , 'num_posts' => '') );
  		
  		$title          = strip_tags($instance['title']);  // Stripe any html/js/css tags
  		$text           = esc_textarea($instance['text']); // Escape potentially dangerous code
  		$num_posts      = $instance['num_posts'];
  		$view_all_text  = esc_textarea( $instance['view_all_text'] );
  		$excerpt_length  = esc_textarea( $instance['excerpt_length'] );
  		$read_more_text  = esc_textarea( $instance['read_more_text'] );
      ?>

  		<p>
  		  <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
  		  <input 
  		    class="widefat" 
  		    id="<?php echo $this->get_field_id('title'); ?>" 
  		    name="<?php echo $this->get_field_name('title'); ?>" 
  		    type="text" 
  		    value="<?php echo esc_attr($title); ?>" />
  		</p>
  		
      <!-- <label for="<?php //echo $this->get_field_id('text'); ?>"><?php //_e('Text:'); ?></label>
      <textarea class="widefat" rows="4" cols="20" id="<?php //echo $this->get_field_id('text'); ?>" name="<?php //echo $this->get_field_name('text'); ?>"><?php //echo $text; ?>
      </textarea> -->
      
      <p>
  		  <label for="<?php echo $this->get_field_id('num_posts'); ?>"><?php _e('Number of Posts to Display:'); ?></label>
  		  <input 
  		    id="<?php echo $this->get_field_id('num_posts'); ?>" 
  		    name="<?php echo $this->get_field_name('num_posts'); ?>" 
  		    type="text" 
  		    size="3"
  		    value="<?php echo $num_posts; ?>" />
  		</p>

  		<p>
  		  <input 
  		    id="<?php echo $this->get_field_id('view_all_cb'); ?>" 
  		    name="<?php echo $this->get_field_name('view_all_cb'); ?>" 
  		    type="checkbox" <?php checked(isset($instance['view_all_cb']) ? $instance['view_all_cb'] : 0); ?> />&nbsp;
  		    
  		    <label for="<?php echo $this->get_field_id('view_all_cb'); ?>"><?php _e('Include link to view all posts'); ?></label>
  		</p>
  		
  		<p>
		    <label for="<?php echo $this->get_field_id('view_all_text'); ?>"><?php _e("'View All Posts' Text:"); ?></label>

		    <input 
  		    id="<?php echo $this->get_field_id('view_all_text'); ?>" 
  		    class="widefat"
  		    name="<?php echo $this->get_field_name('view_all_text'); ?>" 
  		    type="text" 
  		    value="<?php echo $view_all_text; ?>"/>    		  
  		</p>
  		
  		<p>
		    <label for="<?php echo $this->get_field_id('excerpt_length'); ?>"><?php _e("Post excerpt length in characters:"); ?></label>

		    <input 
  		    id="<?php echo $this->get_field_id('excerpt_length'); ?>" 
  		    class="widefat"
  		    name="<?php echo $this->get_field_name('excerpt_length'); ?>" 
  		    type="text" 
  		    value="<?php echo $excerpt_length; ?>"/>    		  
  		</p>
  		
  		<p>
		    <label for="<?php echo $this->get_field_id('read_more_text'); ?>"><?php _e("Post's 'Read More' text:"); ?></label>

		    <input 
  		    id="<?php echo $this->get_field_id('read_more_text'); ?>" 
  		    class="widefat"
  		    name="<?php echo $this->get_field_name('read_more_text'); ?>" 
  		    type="text" 
  		    value="<?php echo $read_more_text; ?>"/>    		  
  		</p>
    		
      <?php
      
    }
  
  /**
   * Save/Update all widget form elements
   */
  function update($new_instance, $old_instance)
  {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		
		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) ); // wp_filter_post_kses() expects slashed
		
		$instance['num_posts']        = $new_instance['num_posts'];		
		$instance['view_all_cb']      = isset($new_instance['view_all_cb']);		
		$instance['view_all_text']    = $new_instance['view_all_text'];		
		$instance['excerpt_length']   = $new_instance['excerpt_length'];				
		$instance['read_more_text']   = $new_instance['read_more_text'];				
		
		return $instance;
  }

  /**
   * Output where the widget is placed in the template
   */
  function widget($args, $instance)
  {
    extract($args);
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$text = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance );
		$num_posts        = $instance['num_posts'];
		$view_all         = $instance['view_all'];
		$view_all_text    = $instance['view_all_text'];
		$excerpt_length   = $instance['excerpt_length'];
		$read_more_text   = $instance['read_more_text'];
		
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; } ?>
			<div class="textwidget"><?php echo !empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?></div>
		<?php
		( !empty($num_posts) && ($num_posts > 0) ) ? $amount = $num_posts : $amount = 3;
		
		query_posts("posts_per_page=$amount&orderby=desc");
    
    if (have_posts()) : 
    	
    	while (have_posts()) : the_post(); 
    		echo "<div class='post'><span class='post_title'><a href='". get_permalink() ."'>". get_the_title() . "</a></span>";
    		echo "<div class='clear'></div>";
    		$new_excerpt = $this->output_custom_length_string( get_the_excerpt() , $excerpt_length );
    		echo "<p class='post_excerpt'>" . $new_excerpt . "</p>";
    		echo "<div class='clear'></div>";
    		echo "<a href='" . get_permalink() . "' class='read_more_button' title='Read More'>" . $read_more_text . "</a>";
        
    		echo "</div>";	
    	endwhile;
    	
    endif; 
    wp_reset_query();
    
    if( !empty($view_all) ) {
      if( get_option('show_on_front') == 'page') { // posts must be somewhere else
      	$posts_page_id = get_option( 'page_for_posts');
      	$posts_page_url = get_page_uri($posts_page_id);

        echo "<div class='view-all-posts'>";
        echo "<a href=/" . $posts_page_url . ">" . $view_all_text . "</a>";
        echo "</div>";
      }
      
    }

		echo $after_widget;
		
  }

  
  /**
   * Output the most recent posts
   * @param string  $section_title Overall title to the section
   * @param array   $category the numerical id(s) of the category(ies) to display
   * @param integer $amount the number of posts to return
   * @param boolean $random if the query should retrieve posts in a random order, descending otherwise
   */
  function srp_output_recent_posts($section_title = "Recent Posts", $category = 1, $amount = 3, $random = "FALSE") {
    $args = array(
      'post_type'       => 'post',
      'posts_per_page'  => $amount,
      'cat'             => $category,
    );

    $random ? $args['orderby'] = "rand" : $args['order'] = "desc"; // add order criteria

    $posts_q = new WP_Query($args);
    ?>

    <?php while($posts_q->have_posts()) : $posts_q->the_post(); ?>
      <div class="sidebar-title"><?php print $section_title; ?></div>
  		<div class="news-story">
  			<div class="news-title"><?php the_title(); ?></div>
  			<div class="news-blurb"><?php print output_custom_length_string(get_the_excerpt(), 85); ?></div>
  			<div class="news-btn"><a href="<?php the_permalink(); ?>" id="small-white-btn" title="Read More">read more</a></div>
  		</div>
    <?php endwhile; ?>

    <?php wp_reset_query(); ?>

    <?php
  }

  /**
   * Output dynamic length of code
   */
  function output_custom_length_string($string = NULL, $limit = 0) {
    if($string && ($limit > 0) ) {
      // Trim down to given length
      $newstring = substr($string, 0, $limit);
      if(strlen($string) > strlen($newstring)) $newstring .= ' ...';

      $string = $newstring;
    }
    return $string;
  }
  
  function say_hello() {
    print "Hello World!!!";
  }
}



add_action( 'widgets_init', create_function('', 'return register_widget("SimpleRecentPosts");') );?>
