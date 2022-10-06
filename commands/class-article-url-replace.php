<?php
/**
 * Class for replace article links
 *
 * @author Krupal Panchal
 */
class Article_URL_Replace {

	public const COMMAND_NAME = 'url';

	/**
	 * @var int Batch size of command run.
	 */
	protected int $_batch_size = 60;

	/**
	 * @var string Source URL.
	 */
	protected string $_source_url = '';

	/**
	 * @var string Target URL.
	 */
	protected string $_target_url = '';

	/**
	 * @var bool Dry run.
	 */
	protected bool $_dry_run = true;  // default to true to prevent accidental command runs.

	/**
	 * Command for replace link
	 *
	 * <source_url>
	 * : Source URL(old or original URL)
	 *
	 * <target_url>
	 * : Target URL(New URL)
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
	 * [--all-links=<all-links>]
	 * : Get articles with changed slug
	 *
	 * ## EXAMPLES
	 *
	 * # User Greeting with name only.
	 * $ wp user greeting User
	 * Success: Hello User!
	 * 
	 * # User Greetings with call.
	 * $ wp user greetings User --call=Mr./Mrs./Ms.
	 * Success: Hello Mr./Mrs./Ms. User!
	 *
	 * @subcommand replace
	 * 
	 * @param array $args
	 * @param array $assoc_args
	 *
	 */
	public function replace( array $args, array $assoc_args ) : void {

		$this->_sub_command_name = __FUNCTION__;

		list( $urls ) = $args;

		$all_links = false;

		if ( ! empty( $assoc_args['all-links'] ) ) {
			$all_links = filter_var( $assoc_args['all-links'], FILTER_VALIDATE_BOOLEAN );
		}

		if ( ! empty( $assoc_args['post-type'] ) ) {
			$post_type = $assoc_args['post-type'];
		} else {
			$post_type = 'post';
		}

		$command_truthy_values = [ '', 'yes', 'true', '1' ];

		if ( isset( $assoc_args['dry-run'] ) ) {
			$this->_dry_run = in_array(
				strtolower( $assoc_args['dry-run'] ),
				$command_truthy_values,
				true
			);
		}

		$this->_source_url = $args[0];
		$this->_target_url = $args[1];

		$this->_notify_on_start();

		$count     = 0;
		$page      = 1;
		$post_args = [
			'post_type'        => $post_type,
			'posts_per_page'   => $this->_batch_size,
			'orderby'          => 'ID',
			'order'            => 'ASC',
			'suppress_filters' => false,
			'post_status'      => [ 'any', 'draft' ],
		];

		do {
			$post_args['paged'] = $page;

			$posts = get_posts( $post_args );

			if ( empty( $posts ) ) {
				break;
			}

			$posts_count = count( $posts );

			for ( $i = 0; $i < $posts_count; $i++ ) {

				$doc = new \DOMDocument();
				$doc->loadHTML( $posts[ $i ]->post_content, LIBXML_NOERROR );

				$anchor_tags = $doc->getElementsByTagName( 'a' );

				$href_change_mapping = [];

				foreach ( $anchor_tags as $index => $a_tag ) {
					$href = $a_tag->getAttribute( 'href' );
					
					$source_url = $this->_source_url;
					$target_url = $this->_target_url;

					$source_match_found = false;

					if ( $href === $source_url ) {
						$source_match_found = true;
					}

					$new_url = str_replace( $source_url, $target_url, $target_url );

					if ( $source_match_found || ( $all_links && $source_url !== $target_url ) ) {

						$href_change_mapping[ $source_url ] = $target_url;
					}
						
					if ( ! empty( $href_change_mapping ) ) {

						$update_count++;

						WP_CLI::log(
							sprintf( ' %d) Post ID: %d ', ( $update_count ), $posts[ $i ]->ID )
						);

						WP_CLI::log( sprintf( ' Slug: %s', $posts[ $i ]->post_name ) );
						WP_CLI::log( sprintf( '------------ -----------' ) );

						$content = $posts[ $i ]->post_content;

						foreach ( $href_change_mapping as $old_link => $new_link ) {

							WP_CLI::log( sprintf( ' Old URL - [%s]', $old_link ) );
							WP_CLI::log( sprintf( ' New URL - [%s]', $new_link ) );
							WP_CLI::log( sprintf( '------------ -----------' ) );
							WP_CLI::log( sprintf( '' ) );

							$content = str_replace( $old_link, $new_link, $content );

							if ( ! $this->is_dry_run() ) {

								wp_update_post(
									array(
										'ID'           => $posts[ $i ]->ID,
										'post_content' => $content,
									),
									true
								);
							}
						}
					}
					$count++;
				}
			}
			$page++;

		} while ( $posts_count === $this->_batch_size );

		if ( ! $this->is_dry_run() ) {
			$this->_notify_on_done( 'URLs are updated!' );
		} else {
			$this->_notify_on_done( 'These URLs will be udpated!' );
		}
	}

	/**
	 * Method to log and notify start of command run
	 *
	 * @return void
	 */
	protected function _notify_on_start() : void {

		$message = sprintf(
			'WP_CLI command has started running on %s',
			wp_parse_url( home_url(), PHP_URL_HOST )
		);

		if ( $this->is_dry_run() ) {
			$message = sprintf( '%s - Dry Run Started', $message );
		}

		WP_CLI::log( '' );
		WP_CLI::log( $message );
		WP_CLI::log( '' );
	}

	/**
	 * Method to log and notify end of command run
	 *
	 * @param string $msg Message to show at the end of command run.
	 *
	 * @return void
	 */
	protected function _notify_on_done( string $msg = '' ) : void {

		if ( empty( $msg ) ) {
			$msg = 'WP-CLI command run completed!';
		}

		WP_CLI::log( $msg );
	}

	/**
	 * Method to check if current run is dry run or not
	 *
	 * @return bool
	 */
	public function is_dry_run() : bool {
		return (bool) ( true === $this->_dry_run );
	}

} // end class.

// EOF.
