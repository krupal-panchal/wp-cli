<?php
/**
 * Class for testing purpose
 *
 * Command `test`
 *
 * @author Krupal Panchal
 */
class Test_Complete extends WP_CLI_Base {

	public const COMMAND_NAME = 'test';

	/**
	 * Command for testing purpose
	 *
	 * ## OPTIONS
	 *
	 * ## EXAMPLES
	 *
	 * # Complete the test.
	 * $ wp test update-test
	 * Success: Test Completed!!!
	 *
	 * @subcommand update-test
	 *
	 * @return void
	 */
	public function update_test() : void {

		$msg = 'Test Started!';
		$data = gmdate();

		/**
		 * Log the message
		 */
		WP_CLI::log( $msg );

		/**
		 * Show success message after completion
		 */
		WP_CLI::success( 'Test Completed!!!' );
	}

} // end class.

// EOF.
