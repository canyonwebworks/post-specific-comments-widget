<?php
/*
 * Plugin Name: Post-Specific Comments Widget (PSCW)
 * Description: A widget that displays formattable recent comments (and even gravatars) from a specific post or page ID or all. Display format is highly customizable with shortcodes and unique CSS tags. 
 * Version: 2.0.1
 * Author: Little Package
 * Donate link: https://www.paypal.me/littlepackage
 * License: GPLv3 or later
 * License URI: http://www.opensource.org/licenses/gpl-license.php
 */

add_action( 'widgets_init', 'pscw_init' );

function pscw_init() {
	register_widget( 'Post_Specific_Comments_Widget' );
}

if ( ! class_exists( 'Post_Specific_Comments_Widget' ) ) :

class Post_Specific_Comments_Widget extends WP_Widget {

	public function __construct() {
		$widget_ops = array(
			'classname' => 'widget_post_specific_comments',
			'description' => __( 'The most recent comments for a specific post', 'post-specific-comments-widget' ),
			'customize_selective_refresh' => true,
		);

		parent::__construct( 'post-specific-comments', __('Post-Specific Comments', 'post-specific-comments-widget' ), $widget_ops );
		$this->alt_option_name = 'widget_post_specific_comments';

		if ( is_active_widget( false, false, $this->id_base ) )
			add_action( 'wp_head', array( $this, 'pscw_recent_comments_style' ) );

		add_action( 'plugins_loaded', array( $this, 'pscw_lang' ));

	}

	/**
	 * l10n
	 **/
	public function pscw_lang() {
	
		load_plugin_textdomain( 'post-specific-comments-widget', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

	}

	/**
	 * CSS
	 **/
	public function pscw_recent_comments_style() {
	
		if ( ! current_theme_supports( 'widgets' ) || ! apply_filters( 'show_recent_comments_widget_style', true, $this->id_base ) )
			return;
		?>
		<style type="text/css">.recentcomments a{display:inline !important;padding:0 !important;margin:0 !important;}</style>
	<?php
	
	}

	public function widget( $args, $instance ) {
	
		global $comments, $comment;

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
        }
        
 		extract( $args, EXTR_SKIP );
 		$output = '';
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Recent Comments', 'post-specific-comments-widget' ) : $instance['title'], $instance, $this->id_base );

        $number = empty( $instance['number'] ) ? 5 : $instance['number'];
        $postID = empty( $instance['postID'] ) ? 0 : $instance['postID'];
		
		if ( $postID != 0 ) {
			$comments = get_comments( apply_filters( 'widget_comments_args', array( 'number' => $number, 'post_id' => $postID, 'status' => 'approve', 'post_status' => 'publish') ) );
		} else {
			$comments = get_comments( apply_filters( 'widget_comments_args', array( 'number' => $number, 'status' => 'approve', 'post_status' => 'publish') ) );
		}
		
		$excerpt_length = empty( $instance['excerpt_length'] ) ? 60 : $instance['excerpt_length'];
 		$excerpt_trail = empty( $instance['excerpt_trail'] ) ? '...' : $instance['excerpt_trail'];

		$output .= $before_widget;
		if ( $title ) {
			$output .= $before_title . $title . $after_title;
		}

		$pscw_rand = mt_rand(1,50);
		$output .= '<ul id="pscw-comments-' . $pscw_rand . '" class="pscw">';

		if ( is_array( $comments ) && $comments ) {
		
			// Prime cache for associated posts. (Prime post term cache if we need it for permalinks.)
			$post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
			_prime_post_caches( $post_ids, strpos( get_option( 'permalink_structure' ), '%category%' ), false );

			foreach ( (array) $comments as $comment ) {

				$aRecentComment = get_comment( $comment->comment_ID );

				$aRecentCommentID = get_comment( $comment->comment_post_ID );
				$aRecentCommentTitle = get_the_title( $aRecentCommentID );
				$aRecentCommentAuthor = get_comment_author_link();
				$aRecentCommentDate = get_comment_date( null, $comment->comment_ID );
				                
				$aRecentCommentTxt = trim( mb_substr( strip_tags( apply_filters( 'comment_text', $aRecentComment->comment_content ) ), 0, $excerpt_length ));
				if ( strlen( $aRecentComment->comment_content ) > $excerpt_length ){
					$aRecentCommentTxt .= $excerpt_trail;
				}
	
				if ( $instance['comment_format'] == "author-post" ) {
					$output .=  '<li class="recentcomments pscw-recentcomments">' . sprintf(__('%1$s on %2$s', 'post-specific-comments-widget'), $aRecentCommentAuthor, '<a href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '" class="pscw-link">' . get_the_title($comment->comment_post_ID) . '</a>') . '</li>';
				}

				if ( $instance['comment_format'] == "author-excerpt" ) {
					$output .= '<li class="recentcomments pscw-recentcomments">' . sprintf(__( '<span class="recentcommentsauthor">%1$s</span> said %2$s', 'post-specific-comments-widget' ), $aRecentCommentAuthor, '<a href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '" class="pscw-link">' . $aRecentCommentTxt . '</a>') . '</li>';	
				}

				if ( $instance['comment_format'] == "post-excerpt" ) {
					$output .= '<li class="recentcomments pscw-recentcomments"><a href="' . esc_url( get_comment_link($comment->comment_ID) ) . '" class="pscw-link">' . $aRecentCommentTxt . '</a></li>';	
				}

				if ( $instance['comment_format'] == "excerpt-author" ) {
					$output .= '<li class="recentcomments pscw-recentcomments">' . sprintf(__( '<span class="recentcommentstitle">%1$s</span> - %2$s', 'post-specific-comments-widget' ), '<a href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '" class="pscw-link">' . $aRecentCommentTxt . '</a>', $aRecentCommentAuthor ) . '</li>';	
				}
				
				if ( $instance['comment_format'] == "other-format" ) {
					$other_input = empty( $instance['other_input'] ) ? '' : strip_tags( $instance['other_input'], '<br /><br><p><strong><em>' );
					
					if ( strpos( $other_input, '[AVATAR' ) !== FALSE ) {
					
					    preg_match( '~\[AVATAR ([0-9]{0,4})\]~', $other_input, $avasize);
                        
                        if ( $avasize[1] != '' ) {
                            $avatarsize = $avasize[1];
                        } else {
                            $avatarsize = '32';
                        }
                        $aRecentAvatar = '<a href="' . get_comment_author_url( $aRecentComment ) . '" rel="external nofollow" alt="commenter avatar" target="_blank">' . get_avatar( $comment, $avatarsize ) . '</a>';
					
					}

					$aRecentCommentTitle = '<span class="recentcommentstitle">' . $aRecentCommentTitle . '</span>';
					$aRecentCommentTxt = '<a href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '" class="pscw-link">' . $aRecentCommentTxt . '</a>';
					$output .= '<li class="recentcomments pscw-recentcomments">' . preg_replace( array( '/\[AUTHOR]/','/\[TITLE\]/','/\[EXCERPT\]/','/\[DATE\]/','/\[AVATAR\]|\[AVATAR ([0-9]{0,4})\]/' ), array( $aRecentCommentAuthor, $aRecentCommentTitle, $aRecentCommentTxt, $aRecentCommentDate, $aRecentAvatar ), $other_input ) . '</li>';

				}
			}
 		}
		$output .= '</ul>';
		$output .= $after_widget;

		echo $output;

	}

	public function update( $new_instance, $old_instance ) {
	
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = absint( $new_instance['number'] );
		$instance['postID'] = absint( $new_instance['postID'] );
		$instance['comment_format'] = $new_instance['comment_format'];
		$instance['other_input'] = $new_instance['other_input'];
		$instance['excerpt_length'] = absint( $new_instance['excerpt_length'] );
		$instance['excerpt_trail'] = strip_tags( $new_instance['excerpt_trail'] );

		return $instance;
		
	}

	public function form( $instance ) {
	
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$postID = isset( $instance['postID'] ) ? $instance['postID'] : '';
		$comment_format = isset( $instance['comment_format'] ) ? $instance['comment_format'] : 'author-post';
		$other_input = isset( $instance['other_input'] ) ? $instance['other_input'] : '';

		$excerpt_length = isset( $instance['excerpt_length'] ) ? $instance['excerpt_length'] : '60';
		$excerpt_trail = isset( $instance['excerpt_trail'] ) ? $instance['excerpt_trail'] : '...';
		?>
		
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title', 'post-specific-comments-widget' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e( 'Number of Comments to Show', 'post-specific-comments-widget' ); ?>:</label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

		<p><label for="<?php echo $this->get_field_id('postID'); ?>"><?php _e( 'Post/Page ID Number', 'post-specific-comments-widget' ); ?>:</label>
		<input id="<?php echo $this->get_field_id('postID'); ?>" name="<?php echo $this->get_field_name('postID'); ?>" type="text" value="<?php echo $postID; ?>" size="6" /></p>

		<legend><?php _e('Comment Format', 'post-specific-comments-widget'); ?>:</legend>
		
        <p style="margin-left:20px"><input type="radio" value="author-post" id="authorpost" name="<?php echo $this->get_field_name('comment_format'); ?>" <?php if ( isset( $comment_format ) && $comment_format == "author-post" ) echo "checked"; ?>  aria-label="(Author) on (Post Title)"> <label for="authorpost">(Author) on (Post Title)</label><br />
        <input type="radio" value="author-excerpt" id="authorexcerpt" name="<?php echo $this->get_field_name('comment_format'); ?>" <?php if ( isset( $comment_format ) && $comment_format == "author-excerpt" ) echo "checked"; ?> aria-label="(Author) said (Excerpt)"> <label for="authorexcerpt">(Author) said (Excerpt)</label><br />
        <input type="radio" name="<?php echo $this->get_field_name('comment_format'); ?>" <?php if ( isset( $comment_format ) && $comment_format == "excerpt-author" ) echo "checked"; ?> value="excerpt-author" id="excerptauthor" aria-label="(Excerpt) - (Author)"> <label for="excerptauthor">(Excerpt) - (Author)</label><br />
        <input type="radio" id="postexcerpt" name="<?php echo $this->get_field_name('comment_format'); ?>" <?php if ( isset( $comment_format ) && $comment_format == "post-excerpt" ) echo "checked"; ?> value="post-excerpt" aria-label="(Excerpt)"> <label for="postexcerpt">(Excerpt)</label><br />
        <input type="radio" id="otherformat" name="<?php echo $this->get_field_name('comment_format'); ?>" <?php if ( isset( $comment_format ) && $comment_format == "other-format" ) echo "checked"; ?> value="other-format" aria-label="Other format"> <label for="otherformat">Other format:</label></p>

        <!-- TEXT INPUT FOR OTHER VALUE -->
        <p style="margin-left:30px"><input type="text" name="<?php echo $this->get_field_name('other_input'); ?>" value="<?php if ( isset($other_input) ) { echo $other_input; } else { echo ""; } ?>" id="other_input"><br /><label for="other_input"> Use text &amp; shortcodes, no CSS/JS/HTML except <code>&lt;p&gt;</code>, <code>&lt;br /&gt;</code>, <code>&lt;strong /&gt;</code>, and <code>&lt;em /&gt;</code>.<br />Shortcodes are: <code>[AUTHOR]</code>, <code>[TITLE]</code>, <code>[EXCERPT]</code>, <code>[DATE]</code>, <code>[AVATAR]</code>.<br />Refer to plugin readme.txt file for more info.</label></p>
        
        <p><label for="<?php echo $this->get_field_id( 'excerpt_length' ); ?>"><?php _e( 'Excerpt Length', 'post-specific-comments-widget' ); ?>:</label>
        <input id="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_length' ); ?>" type="text" value="<?php echo $excerpt_length; ?>" size="3" />
        <p><label for="<?php echo $this->get_field_id( 'excerpt_trail' ); ?>"><?php _e( 'Excerpt Trailing', 'post-specific-comments-widget' ); ?>:</label>
        <input style="width: 100px;" id="<?php echo $this->get_field_id( 'excerpt_trail' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_trail' ); ?>" type="text" value="<?php echo $excerpt_trail; ?>" size="3" />
        </p>
        
        <p><?php _e('Please support hard working Wordpress developers with small donations and/or plugin reviews. Thank you!', 'post-specific-comments-widget'); ?></p>

	<?php } // End form()
	
} // End class post_Specific_Comments_Widget

endif; // End if ( class_exists() )