<?php
/**
 * Database Details
 */
define( "DB_HOSTNAME", "localhost" );
define( "DB_USERNAME", "root" );
define( "DB_PASSWORD", "" );
define( "DB_NAME", "tapeshop" );
define( "DB_PROVIDER", "mysql" );

/*
 * Template to use
 */
define('VIEW', 'basic');

/*
 * Email
 */
define( "SHOPADRESS", "test@example.com");
define ("SUPPORTADRESS", "test@example.com");

/*
 * FPDF
 */
define('FPDF_FONTPATH','assets/fonts/');

/*
 * Example Image when no Article Image was passed.
 */
define('ITEM_PLACEHOLDER','assets/img/placeholder.jpg');

/*
 * Order number prefix
 */
define('ORDER_PREFIX', 'TS-');

/*
 * Sofortüberweisung config key
 */
define('SOFORT_CONFIG', ''); //your configkey or userid:projektid:apikey
define('SOFORT_REASON', 'Tapeshop order');

