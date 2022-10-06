## WP CLI Learning

### This is simple WP CLI repo for learning purpose, which I have created.

If you want to learn something more you can simply fork this repo and update according your need.

Add this repo in your theme root, and add below code to your `functions.php` to just load the `wp-cli` directory.

```
/**
 * Add dependency file for WP-CLI
 */
require_once 'wp-cli/class-wp-cli-commands.php';
```

> Check the [wp-cli Handbook](https://make.wordpress.org/cli/handbook/) and [Commands Cookbook](https://make.wordpress.org/cli/handbook/guides/commands-cookbook/) for more details.
