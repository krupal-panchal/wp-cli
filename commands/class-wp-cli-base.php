<?php
/**
 * Base class for WP-CLI
 *
 * @author Krupal Panchal
 */
class WP_CLI_Base {

    /**
	 * @var bool Dry run.
	 */
	protected bool $_dry_run = true;  // default to true to prevent accidental command runs.

    /**
	 * @var int Batch size of command run.
	 */
	protected int $_batch_size = 60;

    /**
	 * Class constructor
	 *
	 */
	public function __construct() {
	}

    /**
	 * Method to check if current run is dry run or not
	 *
	 * @return bool
	 */
	public function is_dry_run() : bool {
		return (bool) ( true === $this->_dry_run );
	}

    /**
	 * Method to parse global arguments on any CLI command run
	 *
	 * @param array $assoc_args Associate arguments.
	 *
	 * @return void
	 */
	protected function _parse_global_arguments( array $assoc_args = [] ) : void {

        if ( empty( $assoc_args ) ) {
			return;
		}

		$command_truthy_values = [ '', 'yes', 'true', '1' ];

		if ( ! empty( $assoc_args['batch-size'] ) ) {
			$this->_batch_size = (int) $assoc_args['batch-size'];
		}

		if ( isset( $assoc_args['dry-run'] ) ) {
			$this->_dry_run = in_array(
				strtolower( $assoc_args['dry-run'] ),
				$command_truthy_values,
				true
			);
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

} // end class.

// EOF.
