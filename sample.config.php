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
define("SHOP_EMAIL_FROM", "test@example.com");
define ("SHOP_EMAIL_REPLYTO", "test@example.com");
define("SHOP_NAME", "Tapeshop");
define("SHOP_EMAIL_SUBJECT", "Tapeshop order");
define("SHOP_URL", "tapshop.example.com");

/*
 * SMTP Server configuration
 */
define("SMTP_HOST", "smtp.example.com");
define("SMTP_PORT", 25);
define("SMTP_USER", "smtp-user");
define("SMTP_PASSWORD", "secret");

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

/*
 * Paypal settings 
 */
define('PAYPAL_EMAIL', ''); // your paypal email.
define('PAYPAL_MERCHANT_ID', '');