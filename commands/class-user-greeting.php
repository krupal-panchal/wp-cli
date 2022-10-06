<?php
/**
 * Class for User greetings
 *
 * @author Krupal Panchal
 */
class User_Greeting extends WP_CLI_Base {

	public const COMMAND_NAME = 'user';

	/**
	 * Command for User greetings
	 *
	 * ## OPTIONS
	 *
	 * <user_name>...
	 * : Name of user
	 *
	 * [--call=<call>]
	 * : Call of user
	 * ---
	 * default: Mr.
	 * options:
	 * 	- Mr.
	 * 	- Ms.
	 * 	- Mrs.
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 * # User Greeting with name only.
	 * $ wp user greeting <user>
	 * Success: Hello <User>!
	 *
	 * # User Greetings with call.
	 * $ wp user greetings User --call=Mr./Mrs./Ms.
	 * Success: Hello Mr./Mrs./Ms. <User>!
	 *
	 * @subcommand greeting
	 *
	 * @param array $args       Arguments.
	 * @param array $assoc_args Associate arguments.
	 *
	 * @return void
	 */
	public function greeting( array $args, array $assoc_args ) : void {

		list( $name ) = $args;

		$name = implode( ' ', $args );

		if ( ! empty( $assoc_args['call'] ) ) {
			$call = $assoc_args['call'];
		}

		WP_CLI::success( "Hello, {$call} {$name}!" );
	}

} // end class.

// EOF.
