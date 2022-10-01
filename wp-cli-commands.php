<?php
/**
 * Register of all WP Commands
 * 
 * @author  Krupal Panchal
 */

class WP_CLI_Commands {

    protected array $_commands = [
        Test_Complete::class,
        CSV_Import::class,
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
     */
    protected function _include_files() : void {

        require_once 'commands/class-test-complete.php';
        require_once 'commands/class-csv-import.php';

    }

    /**
     * Method to register custom commands with WP-CLI
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

} // end class

$wp_cli_obj = new WP_CLI_Commands();

// EOF
