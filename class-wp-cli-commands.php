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

		// Autoload.
		spl_autoload_register( [ $this, 'wp_cli_command_autoload' ] );

		// Register all Commands.
		$this->_regiser_commands();
	}

	/**
	 * Method to autoload all command class.
	 *
	 * @param string $class Class name.
	 *
	 * @return void
	 */
	public function wp_cli_command_autoload( string $class ) : void {

		$class = str_replace( '_', '-', $class );
		$class = strtolower( $class );

		$get_file = get_template_directory() . "/wp-cli/commands/class-$class.php";

		file_exists( $get_file ) ? require_once $get_file : '';
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
