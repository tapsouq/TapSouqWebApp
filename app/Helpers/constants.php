<?php

//Shred Constants
define( 'BANNER', 2 );
define( 'INTERSTITIAL', 1 );
define( 'MOBILE', 1 );
define( 'TABLET', 2 );
define( 'PORTRAIT', 1 );
define( 'LANDSCAPE', 2 );

// for User system Module
define( 'DEV_PRIV', 1 );
define( 'ADMIN_PRIV', 2 );
define( 'PENDING_USER', 1 );
define( 'ACTIVE_USER', 2 );
define( 'SUSPEND_USER', 3 );

// for Application module
define( 'ANDROID_PLATFORM', 1 );
define( 'IOS_PLATFORM', 2 );
define( 'PENDING_APP', 1 );
define( 'ACTIVE_APP', 2 );
define( 'DELETED_APP', 3 );

// For Zone Module
define( 'ACTIVE_ZONE', 1 );
define( 'DELETED_ZONE', 2 );

// For Campaign Module
define( 'EVEN_CAMP', 1 );
define( 'FAST_CAMP', 2 );
define( 'RUNNING_CAMP', 1 );
define( 'PAUSED_CAMP', 2 );
define( 'COMPLETED_CAMP', 3 );
define( 'DELETED_CAMP', 4 );

// For Creative Ads Module
define( 'TEXT_AD', 1 );
define( 'IMAGE_AD', 2 );
define( 'RUNNING_AD', 1 );
define( 'PAUSED_AD', 2 );
define( 'COMPLETED_AD', 3 );
define( 'DELETED_AD', 4 );

// For SDK Module
define('REQUEST_ACTION', 1);
define('SHOW_ACTION', 2);
define('CLICK_ACTION', 3);
define('INSTALL_ACTION', 4);

define('PLACEMENT_AD', 1);
define('CREATIVE_AD', 2);

// For SDK URLs
 /* Create device */
define('ADD_PLATFORM', 1);
define('ADD_ADVERTISING_ID', 2);
define('ADD_MANEFACTURER', 3);
define('ADD_MODEL', 4);
define('ADD_OS_VER', 5);
define('ADD_LANG', 6);
define('ADD_COUNTRY', 7);
define('ADD_CITY', 8);
define('ADD_CARRIER', 9);
define('TAPSOUQ_SDK_VER', 10);

 /* Update device */
define("UPDATE_DEVICE_ID", 1);
define("UPDATE_LANG", 2);
define("UPDATE_COUNTRY", 3);
define("UPDATE_OS", 4);
define("UPDATE_MODEL", 5);
define("UPDATE_MANEFACTURER", 6);

 /* sdk-action */
define('DEVICE_ID', 1);
define('ACTION_NAME', 2);
define('REQUEST_ID', 3);
define('PLACEMENT_ID', 4);
define('CREATIVE_ID', 5);

// For Charts module
define('IS_CAMPAIGN', false);
define('NOT_CAMPAIGN', true);
define('IN_DASHBOARD', true);
// For Dashboard module

// For Cron Jobs
define("PENDING_UPDATED", 0);
define("SUCCESS_UPDATED", 1);
define("ERROR_UPDATED", 2);