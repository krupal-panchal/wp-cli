<?php
/**
 * Register of all WP Commands
 *
 * @author Krupal Panchal
 */
class WP_CLI_Commands {

	/**
	 * @var array Class of all commands.
	 */
	protected array $_commands = [
		Test_Complete::class,
		User_Greeting::class,
		Article_URL_Replace::class,
		Add_Category_Tag::class,
		Post_Check::class,
	];

	/**
	 * Class Constructor
	 */
	public function __construct() {
		$this->_include_files();
		$this->_regiser_commands();
	}

	/**
	 * Method to include command files
	 *
	 * @return void
	 */
	protected function _include_files() : void {

		require_once 'commands/class-wp-cli-base.php';
		require_once 'commands/class-test-complete.php';
		require_once 'commands/class-user-greeting.php';
		require_once 'commands/class-article-url-replace.php';
		require_once 'commands/class-add-category-tag.php';
		require_once 'commands/class-post-check.php';
	}

	/**
	 * Method to register custom commands with WP-CLI
	 *
	 * @return void
	 */
	protected function _regiser_commands() : void {

		// Check if WP-CLI is defined.
		if ( ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			return;
		}

		if ( ! empty( $this->_commands ) ) {
			foreach ( $this->_commands as $command ) {
				WP_CLI::add_command( $command::COMMAND_NAME, $command );
			}
		}
	}

} // end class.

new WP_CLI_Commands();

// EOF.
