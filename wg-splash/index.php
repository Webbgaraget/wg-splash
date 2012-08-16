<?php
/*
Plugin Name: WG Splash
Description: Shows a splash page for new visitors. This plugin uses cookies to detect whether a visitor is new or returing.
Version: 0.0.1
Author: Webbgaraget
Author URI: http://webbgaraget.se/
License: MIT
*/

include_once( 'class-wg-splash.php' );
include_once( 'class-wg-splash-settings.php' );

new WG_Splash();

