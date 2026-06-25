<?php
/*
 * Plugin Name: Post-Specific Comments Widget (PSCW)
 * Description: A widget that displays recent comments from a *specific post or page* (or all). Display format is highly customizable with shortcodes, hooks, and unique CSS tags, and includes gravatars. 
 * Version: 2.1.1
 * Author: Little Package
 * Donate link: https://www.paypal.me/littlepackage
 * License: GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * 
 * Text Domain: post-specific-comments-widget
 * Domain path: lang
 *
 * Copyright 2013-2020 Little Package 
 *		
 *     This file is part of Post Specific Comments Widget, a plugin for WordPress.
 *     If it benefits you, please "buy me a coffee" 
 *
 * 	   https://www.paypal.me/littlepackage   or/and
 *
 * 	   leave a nice review at:
 *
 * 	   https://wordpress.org/support/view/plugin-reviews/post-specific-comments-widget?filter=5
 *
 *     Thank you.
 *
 *     Post Specific Comments Widget is free software: You can redistribute
 *     it and/or modify it under the terms of the GNU General Public
 *     License as published by the Free Software Foundation, either
 *     version 3 of the License, or (at your option) any later version.
 *     
 *     This plugin is distributed in the hope that it will
 *     be useful, but WITHOUT ANY WARRANTY; without even the
 *     implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
 *     PURPOSE. See the GNU General Public License for more details.
 *
 *     You should have received a copy of the GNU General Public License
 *     along with WordPress. If not, see <http://www.gnu.org/licenses/>.
 *
 */
defined( 'ABSPATH' ) || exit;
        
if ( ! class_exists( 'Post_Specific_Comments_Widget' ) ) :

    class Post_Specific_Comments_Widget extends WP_Widget {

        public function __construct() {
            $widget_ops = array(
                'classname' => 'widget_post_specific_comments',
                'description' => __( 'The most recent comments for a specific post', 'post-specific-comments-widget' ),
                'customize_selective_refresh' => true,
            );
            parent::__construct( 'post-specific-comments', __( 'Post-Specific Comments', 'post-specific-comments-widget' ), $widget_ops );

        }

        /**
         * l10n
         **/
        public static function pscw_load_plugin_textdomain() {
    
            load_plugin_textdomain( 'post-specific-comments-widget', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

        }

        /**
         * Echoes the widget content.
         *
         * @param array $args     Display arguments including 'before_title', 'after_title',
         *                        'before_widget', and 'after_widget'.
         * @param array $instance The settings for the particular instance of the widget.
         */
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
            $output .= '<ul id="pscw-comments-' . $pscw_rand . '" class="pscw pscw-list">';

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
                    $aRecentAvatar = '';
        
                    $aRecentCommentTxt = trim( mb_substr( strip_tags( apply_filters( 'comment_text', $aRecentComment->comment_content ) ), 0, $excerpt_length ) );
                    
                    if ( strlen( $aRecentComment->comment_content ) > $excerpt_length ) {
                        $aRecentCommentTxt .= $excerpt_trail;
                    }
    
                    if ( $instance['comment_format'] == "author-post" ) {
                        $output .=  '<li class="recentcomments pscw-recentcomments">' . sprintf( __( '%1$s on %2$s', 'post-specific-comments-widget' ), $aRecentCommentAuthor, '<a href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '" class="pscw-link">' . get_the_title( $comment->comment_post_ID ) . '</a>') . '</li>';
                    }

                    if ( $instance['comment_format'] == "author-excerpt" ) {
                        $output .= '<li class="recentcomments pscw-recentcomments">' . sprintf( __( '<span class="recentcommentsauthor">%1$s</span> said %2$s', 'post-specific-comments-widget' ), $aRecentCommentAuthor, '<a href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '" class="pscw-link">' . $aRecentCommentTxt . '</a>') . '</li>';	
                    }

                    if ( $instance['comment_format'] == "post-excerpt" ) {
                        $output .= '<li class="recentcomments pscw-recentcomments"><a href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '" class="pscw-link">' . $aRecentCommentTxt . '</a></li>';	
                    }

                    if ( $instance['comment_format'] == "excerpt-author" ) {
                        $output .= '<li class="recentcomments pscw-recentcomments">' . sprintf( __( '<span class="recentcommentstitle">%1$s</span> &ndash; %2$s', 'post-specific-comments-widget' ), '<a href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '" class="pscw-link">' . $aRecentCommentTxt . '</a>', $aRecentCommentAuthor ) . '</li>';	
                    }
                
                    if ( $instance['comment_format'] == "other-format" ) {
                        $other_input = empty( $instance['other_input'] ) ? '' : $instance['other_input'];
                 
                        if ( strpos( $other_input, '[AVATAR' ) !== FALSE ) {
                   
                            preg_match( '~\[AVATAR ([0-9]{0,4})\]~', $other_input, $avasize );
                        
                            if ( ! empty( $avasize[1] ) ) {
                                $avatarsize = $avasize[1];
                            } else {
                                $avatarsize = '32';
                            }
                            $aRecentAvatar = '<a href="' . get_comment_author_url( $aRecentComment ) . '" rel="external nofollow" alt="commenter avatar" target="_blank">' . get_avatar( $comment, $avatarsize ) . '</a>';
                            $aRecentAvatar = apply_filters( 'pscw_filter_avatar', $aRecentAvatar, $comment );

                        }

                        $aRecentCommentTitle = '<span class="recentcommentstitle pscw-recentcommentstitle">' . $aRecentCommentTitle . '</span>';
                        $aRecentCommentTxt = '<a href="' . esc_url( get_comment_link( $comment->comment_ID ) ) . '" class="pscw-link">' . $aRecentCommentTxt . '</a>';
                        $output .= '<li class="recentcomments pscw-recentcomments">' . preg_replace( array( '/\[AUTHOR]/','/\[TITLE\]/','/\[EXCERPT\]/','/\[DATE\]/','/\[AVATAR\]|\[AVATAR ([0-9]{0,4})\]/' ), array( $aRecentCommentAuthor, $aRecentCommentTitle, $aRecentCommentTxt, $aRecentCommentDate, $aRecentAvatar ), $other_input ) . '</li>';

                    }
                }
            }
            $output .= '</ul>';
            $output .= $after_widget;
            
            $output = apply_filters( 'pscw_filter_output', $output, $instance );

            echo $output;

        }

        /**
         * Updates a particular instance of a widget.
         *
         * This function should check that `$new_instance` is set correctly. The newly-calculated
         * value of `$instance` should be returned. If false is returned, the instance won't be
         * saved/updated.
         *
         * @param array $new_instance New settings for this instance as input by the user via
         *                            WP_Widget::form().
         * @param array $old_instance Old settings for this instance.
         * @return array Settings to save or bool false to cancel saving.
         */
        public function update( $new_instance, $old_instance ) {
    
            $instance = $old_instance;
            $instance['title'] = sanitize_text_field( $new_instance['title'] );
            $instance['number'] = $new_instance['number'];
            $instance['postID'] = $new_instance['postID'];
            $instance['comment_format'] = $new_instance['comment_format'];            
            $instance['other_input'] = strip_tags( $new_instance['other_input'], '<br /><br><p><strong><em><ul><li>' );
            $instance['excerpt_length'] = $new_instance['excerpt_length'];
            $instance['excerpt_trail'] = sanitize_text_field( $new_instance['excerpt_trail'] );

            return $instance;
        
        }

        /**
         * Outputs the settings update form.
         *
         * @param array $instance Current settings.
         * @return string Default return is 'noform'.
         */
        public function form( $instance ) {
    
            $title = isset( $instance['title'] ) ? $instance['title'] : '';
            $number = isset( $instance['number'] ) ? $instance['number'] : 5;
            $postID = isset( $instance['postID'] ) ? $instance['postID'] : '';
            $comment_format = isset( $instance['comment_format'] ) ? $instance['comment_format'] : 'author-post';
            $other_input = isset( $instance['other_input'] ) ? $instance['other_input'] : '';
            $excerpt_length = isset( $instance['excerpt_length'] ) ? $instance['excerpt_length'] : '60';
            $excerpt_trail = isset( $instance['excerpt_trail'] ) ? $instance['excerpt_trail'] : '...';
            ?>

            <!-- WIDGET DISPLAY TITLE -->
            <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Display Title', 'post-specific-comments-widget' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
            <!-- # COMMENTS TO SHOW -->
            <p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php esc_html_e( 'Number of Comments to Show', 'post-specific-comments-widget' ); ?>:</label>
            <input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="number" value="<?php echo $number; ?>" /></p>
            <!-- POST ID -->
            <p><label for="<?php echo $this->get_field_id( 'postID' ); ?>"><?php esc_html_e( 'Post/Page ID Number', 'post-specific-comments-widget' ); ?>:</label>
            <input id="<?php echo $this->get_field_id( 'postID' ); ?>" name="<?php echo $this->get_field_name( 'postID' ); ?>" type="number" value="<?php echo $postID; ?>" /></p>

            <!-- COMMENT FORMAT -->
            <fieldset>
                <legend><?php esc_html_e( 'Comment Format', 'post-specific-comments-widget' ); ?>:</legend>
                <p style="margin-left:10px">
                    <input type="radio" id="<?php echo $this->get_field_id( 'authorpost' ); ?>" name="<?php echo $this->get_field_name( 'comment_format' ); ?>" value="author-post" <?php checked( $comment_format, "author-post" ); ?>><label for="<?php echo $this->get_field_id( 'authorpost' ); ?>"> <?php esc_html_e( '(Author) on (Post Title)', 'post-specific-comments-widget' ); ?></label><br />
                    <input type="radio" id="<?php echo $this->get_field_id( 'authorexcerpt' ); ?>" name="<?php echo $this->get_field_name( 'comment_format' ); ?>" value="author-excerpt" <?php checked( $comment_format, "author-excerpt" ); ?>><label for="<?php echo $this->get_field_id( 'authorexcerpt' ); ?>"> <?php esc_html_e( '(Author) said (Excerpt)', 'post-specific-comments-widget' ); ?></label><br />
                    <input type="radio" id="<?php echo $this->get_field_id( 'excerptauthor' ); ?>" name="<?php echo $this->get_field_name( 'comment_format' ); ?>" value="excerpt-author" <?php checked( $comment_format, "excerpt-author" ); ?>><label for="<?php echo $this->get_field_id( 'excerptauthor' ); ?>"> <?php esc_html_e( '(Excerpt) &ndash; (Author)', 'post-specific-comments-widget' ); ?></label><br />
                    <input type="radio" id="<?php echo $this->get_field_id( 'postexcerpt' ); ?>" name="<?php echo $this->get_field_name( 'comment_format' ); ?>" value="post-excerpt" <?php checked( $comment_format, "post-excerpt" ); ?>><label for="<?php echo $this->get_field_id( 'postexcerpt' ); ?>"> <?php esc_html_e( '(Excerpt)', 'post-specific-comments-widget' ); ?></label><br />
                    <input type="radio" id="<?php echo $this->get_field_id( 'otherformat' ); ?>" name="<?php echo $this->get_field_name( 'comment_format' ); ?>" value="other-format" <?php checked( $comment_format, "other-format" ); ?>><label for="<?php echo $this->get_field_id( 'otherformat' ); ?>"> <?php esc_html_e( 'Other format:', 'post-specific-comments-widget' ); ?></label>
                </p>
            </fieldset>

            <!-- TEXT INPUT FOR OTHER VALUE -->
            <div style="margin-left:20px"><input class="widefat" type="text" name="<?php echo $this->get_field_name( 'other_input' ); ?>" value="<?php if ( isset( $other_input ) ) { echo $other_input; } else { echo ""; } ?>" id="<?php echo $this->get_field_id( 'other_input' ); ?>"><br />
                <label for="<?php echo $this->get_field_id( 'other_input' ); ?>"><small><?php esc_html_e( 'Use text &amp; shortcodes, no CSS/JS/HTML except &lt;p&gt;, &lt;br /&gt;, &lt;strong&gt;, &lt;ul&gt;, &lt;li&gt;, and &lt;em&gt;.', 'post-specific-comments-widget' ); ?><br />
                    <?php esc_html_e( 'Shortcodes are: [AUTHOR], [TITLE], [EXCERPT], [DATE], [AVATAR].', 'post-specific-comments-widget' ); ?><br />
                    <?php esc_html_e( 'Refer to plugin readme.txt file for more info.', 'post-specific-comments-widget' ) ?></small></label>
            </div>
            <!-- EXCERPT LENGTH -->
            <p><label for="<?php echo $this->get_field_id( 'excerpt_length' ); ?>"><?php esc_html_e( 'Excerpt Length', 'post-specific-comments-widget' ); ?>:</label>
            <input id="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_length' ); ?>" type="number" value="<?php echo $excerpt_length; ?>" />
            <!-- EXCERPT TRAILING CHARS -->
            <p><label for="<?php echo $this->get_field_id( 'excerpt_trail' ); ?>"><?php esc_html_e( 'Excerpt Trailing', 'post-specific-comments-widget' ); ?>:</label>
            <input id="<?php echo $this->get_field_id( 'excerpt_trail' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_trail' ); ?>" type="text" value="<?php echo $excerpt_trail; ?>" size="8" />
            </p>
        
            <p><small><?php echo sprintf( __( 'Please support hard working Wordpress developers with <a href="%1$s" target="_blank" rel="noopener">small donations</a> and/or <a href="%2$s" target="_blank" rel="noopener">plugin reviews</a>. Thank you!', 'post-specific-comments-widget' ), 'https://www.paypal.me/littlepackage', 'https://wordpress.org/support/view/plugin-reviews/post-specific-comments-widget?filter=5' ); ?></small></p>

        <?php } // End form()
    
    } // End class post_Specific_Comments_Widget

endif; // End if ( class_exists() )

function pscw_init() {
	register_widget( 'Post_Specific_Comments_Widget' );
}
add_action( 'widgets_init', 'pscw_init' );

add_action( 'plugins_loaded', array( 'Post_Specific_Comments_Widget', 'pscw_load_plugin_textdomain' ) );
