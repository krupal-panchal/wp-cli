<?php
/**
 * Class for adding category according to tag
 *
 * @author Krupal Panchal
 */
class Add_Category_Tag extends WP_CLI_Base {

	public const COMMAND_NAME = 'category';

	/**
	 * @var array Array for mapping href changes.
	 */
	public array $category_mapping = [];

	/**
	 * Command for category update
	 *
	 * <tag>
	 * : slug of tag
	 *
	 * <category>
	 * : slug of category
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
	 * ## EXAMPLES
	 *
	 * # add category according tag.
	 * $ wp category add <tag> <category> --dry-run=false
	 * Success: Category Added!!
	 *
	 * @subcommand add
	 *
	 * @param array $args       Arguments.
	 * @param array $assoc_args Associate arguments.
	 *
	 * @return void
	 */
	public function add( array $args, array $assoc_args ) : void {

		list( $slug ) = $args;

		$post_type = 'post';

		$tag      = $args[0];
		$category = $args[1];

		$tag_id = get_term_by( 'slug', $tag, 'post_tag' )->term_id;
		$cat_id = get_term_by( 'slug', $category, 'category' )->term_id;

		if ( ! tag_exists( $tag ) ) {
			WP_CLI::log( 'Tag doesn\'t exist. Please Add/Check.' );
		}

		if ( ! category_exists( $category ) ) {
			WP_CLI::log( 'Category doesn\'t exist. Please Add/Check.' );
		}

		if ( tag_exists( $tag ) && category_exists( $category ) ) {

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

					$post_id = $posts[ $i ]->ID;

					if ( has_term( $tag_id, 'post_tag', $post_id ) ) {

						if ( ! has_term( $cat_id, 'category', $post_id ) ) {

							$this->category_mapping[] = $post_id;

							$update_count++;

							WP_CLI::log(
								sprintf( ' %d) Post ID: %d -> Category Added.', ( $update_count ), $post_id )
							);

							if ( ! $this->is_dry_run() ) {
								wp_set_post_categories(
									$post_id,
									$cat_id,
									true
								);
							}
						}
					}

					$count++;
					$this->_update_iteration();
				}
				$page++;

			} while ( $posts_count === $this->_batch_size );

			if ( 1 > count( $this->category_mapping ) ) {
				WP_CLI::log( 'No any post found to update category!' );
			} else {
				if ( ! $this->is_dry_run() ) {
					WP_CLI::log( '' );
					$this->_notify_on_done( 'Category added in these posts!' );
				} else {
					WP_CLI::log( '' );
					$this->_notify_on_done( 'Dry run ended - Category will be added in these posts.' );
				}
			}
		}
	}

} // end class.

// EOF.
