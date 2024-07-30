# wp-cli-reset-opcache

Allows to clear opcache, on all servers on which your site is load-balanced.

## Installing

Before installing this package you need to define a constant named `RESET_OPCACHE_SECRET` (with a string of your choosing).
Installing this package requires WP-CLI v2.10.0 or greater. Update to the latest stable release with `wp cli update`.

Once you've done so, you can install this package with `wp package install beapi/wp-cli-reset-opcache` You also can update your package with `wp package update`

## Usage

`wp clear-opcache --host=myhost.com --servers=www-01a,www-01b`

## Credits 

Based on https://github.com/wearerequired/wp-cli-clear-opcache