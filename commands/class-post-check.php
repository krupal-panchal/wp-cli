<?php
/**
 * Class for Post Check
 *
 * @author Krupal Panchal
 */
class Post_Check extends WP_CLI_Base {

    public const COMMAND_NAME = 'post';

    /**
	 * Command for Post check
	 *
	 * ## OPTIONS
	 *
	 * [--dry-run]
	 * : Whether to do a dry run or not.
	 * ---
	 * default: true
	 * options:
	 *  - false
	 *  - true
	 *
	 * [--post-type=<post-type>]
	 * : Post type of article
	 *
	 * ## EXAMPLES
	 * 
	 * # Get Post Title.
	 * $ wp post get-title --dry-run=false
	 * Success: URLs updated!!
	 *
	 * @subcommand get-title
	 *
	 * @param array $args       Arguments.
	 * @param array $assoc_args Associate arguments.
	 */
	public function get_title( array $args, array $assoc_args ) : void {
        
        if ( ! empty( $assoc_args['post-type'] ) ) {
			$post_type = $assoc_args['post-type'];
		} else {
			$post_type = 'post';
		}

        // Parse the global arguments.
		$this->_parse_global_arguments( $assoc_args );

        $this->_notify_on_start();

        $count     = 0;
		$page      = 1;
		$post_args = [
            'post_type'        => $post_type,
			'posts_per_page'   => $this->_batch_size,
			'orderby'          => 'ID',
			'order'            => 'ASC',
			'suppress_filters' => false,
			'post_status'      => [ 'any' ],
		];
        
        $update_count = 0;

		do {

            $post_args['paged'] = $page;

			$posts = get_posts( $post_args );

			if ( empty( $posts ) ) {
				break;
			}

			$posts_count = count( $posts );

			for ( $i = 0; $i < $posts_count; $i++ ) {

                $count++;

                WP_CLI::log(
                    sprintf(
                        '%d) Title - %s',
                        $count,
                        $posts[ $i ]->post_title
                    )
                );

                $this->_update_iteration();
            }

            $page++;
            
        } while ( $posts_count === $this->_batch_size );
    }

    /* if ( ! $this->is_dry_run() ) {
        $this->_notify_on_done( 'Post titles!' );
    } else {
        $this->_notify_on_done( 'Dry run ended - These are the post titles.' );
    } */

} // End Class.
