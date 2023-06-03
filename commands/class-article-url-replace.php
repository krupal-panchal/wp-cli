<?php
/**
 * Class for replace article links
 *
 * @author Krupal Panchal
 */
class Article_URL_Replace extends WP_CLI_Base {

	public const COMMAND_NAME = 'url';

	/**
	 * @var string Source URL.
	 */
	protected string $_source_url = '';

	/**
	 * @var string Target URL.
	 */
	protected string $_target_url = '';

	/**
	 * @var array Array for mapping href changes.
	 */
	protected array $_href_change_mapping = [];

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
	 * # replace URL within post content.
	 * $ wp url replace <source_url> <target_url> --dry-run=false
	 * Success: URLs updated!!
	 *
	 * @subcommand replace
	 *
	 * @param array $args       Arguments.
	 * @param array $assoc_args Associate arguments.
	 */
	public function replace( array $args, array $assoc_args ) : void {

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

		// Parse the global arguments.
		$this->_parse_global_arguments( $assoc_args );

		$this->_source_url = trailingslashit( $args[0] );
		$this->_target_url = trailingslashit( $args[1] );

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

		do {
			$post_args['paged'] = $page;

			$posts = get_posts( $post_args );

			if ( empty( $posts ) ) {
				break;
			}

			$posts_count = count( $posts );

			for ( $i = 0; $i < $posts_count; $i++ ) {

				$doc          = new \DOMDocument();
				$post_content = $posts[ $i ]->post_content;

				if ( ! empty( $post_content ) ) {

					$doc->loadHTML( $post_content, LIBXML_NOERROR );

					$anchor_tags = $doc->getElementsByTagName( 'a' );

					foreach ( $anchor_tags as $index => $a_tag ) {
						$href = trailingslashit( $a_tag->getAttribute( 'href' ) );

						$source_url = $this->_source_url;
						$target_url = $this->_target_url;

						if ( $href === $source_url ) {
							$new_url = str_replace( $source_url, $target_url, $target_url );

							if ( $source_url !== $new_url ) {
								$this->_href_change_mapping[ $source_url ] = $new_url;
							}

							if ( ! empty( $this->_href_change_mapping ) ) {

								$update_count++;

								WP_CLI::log(
									sprintf( ' %d) Post ID: %d ', ( $update_count ), $posts[ $i ]->ID )
								);

								WP_CLI::log( sprintf( ' Slug: %s', $posts[ $i ]->post_name ) );
								WP_CLI::log( sprintf( '------------ -----------' ) );

								$content = $posts[ $i ]->post_content;

								foreach ( $this->_href_change_mapping as $old_link => $new_link ) {

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
						}

						$count++;
						$this->_update_iteration();
					}
				}
			}
			$page++;

		} while ( $posts_count === $this->_batch_size );

		if ( empty( $this->_href_change_mapping ) ) {
			WP_CLI::log( 'No any post found to replace given URL!' );
		} else {
			if ( ! $this->is_dry_run() ) {
				$this->_notify_on_done( 'URLs updated!' );
			} else {
				$this->_notify_on_done( 'Dry run ended - These URLs will be udpated!' );
			}
		}

	}
} // end class.

// EOF.
