# wp-cli-reset-opcache

Allows to clear opcache, on all servers on which your site is load-balanced.

## Installing

Installing this package requires WP-CLI v2.10.0 or greater. Update to the latest stable release with `wp cli update`.

Once you've done so, you can install this package with wp package install `BeAPI/wp-cli-reset-opcache` You also can update your package with `wp package update`

## Usage

`wp clear-opcache --host=myhost.com --servers=www-01a,www-01b`

## Credits 

Based on https://github.com/wearerequired/wp-cli-clear-opcache