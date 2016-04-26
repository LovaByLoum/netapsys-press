<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

$host = $_SERVER['HTTP_HOST'];
$appPath = dirname (__FILE__) . DIRECTORY_SEPARATOR;
define("SERVERCONFIG", $host);
define ("BASEPATH", dirname (__FILE__));
require_once( $appPath . 'wp-config' . DIRECTORY_SEPARATOR . SERVERCONFIG . DIRECTORY_SEPARATOR .  "wp-config.php" );