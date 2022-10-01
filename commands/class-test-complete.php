<?php
/**
 * Class for testing purpose
 * 
 * Command `test`
 * 
 * @author Krupal Panchal
 */

class Test_Complete {

    public const COMMAND_NAME = 'test';

    /**
     * Command for testing purpose
     * 
     * ## OPTIONS
     * 
     * ## EXAMPLES
     * 
     *      # Complete the test.
     *      $ wp test update
     *      Success: Test Completed!!!
     */
    public function update() : void {
        WP_CLI::success( 'Test Completed!!!' );
    }

} // end class

// EOF