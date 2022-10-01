<?php
/**
 * Class for CSV Import log
 * 
 * Command `import`
 * 
 * @author Krupal Panchal
 */

class CSV_Import {

    public const COMMAND_NAME = 'import';

    public function update() : void {
        WP_CLI::success( 'CSV Imported!!!' );
    }

} // end class

// EOF
