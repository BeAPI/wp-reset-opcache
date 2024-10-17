<?php
/*
Plugin Name: WP CLI Reset Opcache
Description: Reset opcache via WP-CLI or HTTP request.
Version: 1.0.2
Author: Be API technical team
License: MIT

---

 Copyright 2024 Be API Technical team (humans@beapi.fr)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if ( ! defined( 'RESET_OPCACHE_SECRET' ) ) {
	defined( 'RESET_OPCACHE_SECRET', '' );
}

new \BEAPI\Clear_Opcache\Clear_Opcache();
