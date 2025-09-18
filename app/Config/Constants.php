<?php

/*
 | --------------------------------------------------------------------
 | App Namespace
 | --------------------------------------------------------------------
 |
 | This defines the default Namespace that is used throughout
 | CodeIgniter to refer to the Application directory. Change
 | this constant to change the namespace that all application
 | classes should use.
 |
 | NOTE: changing this will require manually modifying the
 | existing namespaces of App\* namespaced-classes.
 */
defined('APP_NAMESPACE') || define('APP_NAMESPACE', 'App');

/*
 | --------------------------------------------------------------------------
 | Composer Path
 | --------------------------------------------------------------------------
 |
 | The path that Composer's autoload file is expected to live. By default,
 | the vendor folder is in the Root directory, but you can customize that here.
 */
defined('COMPOSER_PATH') || define('COMPOSER_PATH', ROOTPATH . 'vendor/autoload.php');

/*
 |--------------------------------------------------------------------------
 | Timing Constants
 |--------------------------------------------------------------------------
 |
 | Provide simple ways to work with the myriad of PHP functions that
 | require information to be in seconds.
 */
defined('SECOND') || define('SECOND', 1);
defined('MINUTE') || define('MINUTE', 60);
defined('HOUR')   || define('HOUR', 3600);
defined('DAY')    || define('DAY', 86400);
defined('WEEK')   || define('WEEK', 604800);
defined('MONTH')  || define('MONTH', 2_592_000);
defined('YEAR')   || define('YEAR', 31_536_000);
defined('DECADE') || define('DECADE', 315_360_000);

/*
 | --------------------------------------------------------------------------
 | Exit Status Codes
 | --------------------------------------------------------------------------
 |
 | Used to indicate the conditions under which the script is exit()ing.
 | While there is no universal standard for error codes, there are some
 | broad conventions.  Three such conventions are mentioned below, for
 | those who wish to make use of them.  The CodeIgniter defaults were
 | chosen for the least overlap with these conventions, while still
 | leaving room for others to be defined in future versions and user
 | applications.
 |
 | The three main conventions used for determining exit status codes
 | are as follows:
 |
 |    Standard C/C++ Library (stdlibc):
 |       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
 |       (This link also contains other GNU-specific conventions)
 |    BSD sysexits.h:
 |       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
 |    Bash scripting:
 |       http://tldp.org/LDP/abs/html/exitcodes.html
 |
 */
defined('EXIT_SUCCESS')        || define('EXIT_SUCCESS', 0);        // no errors
defined('EXIT_ERROR')          || define('EXIT_ERROR', 1);          // generic error
defined('EXIT_CONFIG')         || define('EXIT_CONFIG', 3);         // configuration error
defined('EXIT_UNKNOWN_FILE')   || define('EXIT_UNKNOWN_FILE', 4);   // file not found
defined('EXIT_UNKNOWN_CLASS')  || define('EXIT_UNKNOWN_CLASS', 5);  // unknown class
defined('EXIT_UNKNOWN_METHOD') || define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     || define('EXIT_USER_INPUT', 7);     // invalid user input
defined('EXIT_DATABASE')       || define('EXIT_DATABASE', 8);       // database error
defined('EXIT__AUTO_MIN')      || define('EXIT__AUTO_MIN', 9);      // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      || define('EXIT__AUTO_MAX', 125);    // highest automatically-assigned error code

/*
 | --------------------------------------------------------------------------
 | Maintio Application Constants
 | --------------------------------------------------------------------------
 |
 | Application-specific constants for the Maintio system
 */

// Dashboard-spezifische Konstanten
defined('DASHBOARD_RECENT_WORK_ORDERS_LIMIT') || define('DASHBOARD_RECENT_WORK_ORDERS_LIMIT', 5);
defined('DASHBOARD_UPCOMING_TASKS_LIMIT') || define('DASHBOARD_UPCOMING_TASKS_LIMIT', 5);
defined('DASHBOARD_OVERDUE_WORK_ORDERS_LIMIT') || define('DASHBOARD_OVERDUE_WORK_ORDERS_LIMIT', 10);
defined('DASHBOARD_DUE_SOON_WORK_ORDERS_LIMIT') || define('DASHBOARD_DUE_SOON_WORK_ORDERS_LIMIT', 10);
defined('DASHBOARD_DUE_SOON_DAYS') || define('DASHBOARD_DUE_SOON_DAYS', 7);
defined('DASHBOARD_UPCOMING_MAINTENANCE_DAYS') || define('DASHBOARD_UPCOMING_MAINTENANCE_DAYS', 14);

// Search-spezifische Konstanten
defined('SEARCH_MIN_QUERY_LENGTH') || define('SEARCH_MIN_QUERY_LENGTH', 2);
defined('SEARCH_DEFAULT_LIMIT') || define('SEARCH_DEFAULT_LIMIT', 50);

// Work Order-spezifische Konstanten
defined('WORK_ORDER_DEFAULT_CREATED_BY') || define('WORK_ORDER_DEFAULT_CREATED_BY', 1);
defined('WORK_ORDER_SEARCH_LIMIT') || define('WORK_ORDER_SEARCH_LIMIT', 20);

// Validation-spezifische Konstanten
defined('WORK_ORDER_TITLE_MAX_LENGTH') || define('WORK_ORDER_TITLE_MAX_LENGTH', 200);
defined('WORK_ORDER_DESCRIPTION_MAX_LENGTH') || define('WORK_ORDER_DESCRIPTION_MAX_LENGTH', 1000);
defined('ASSET_NAME_MAX_LENGTH') || define('ASSET_NAME_MAX_LENGTH', 200);
defined('USER_NAME_MAX_LENGTH') || define('USER_NAME_MAX_LENGTH', 100);

// Preventive Maintenance-spezifische Konstanten
defined('PM_DEFAULT_LEAD_TIME_DAYS') || define('PM_DEFAULT_LEAD_TIME_DAYS', 7);
defined('PM_SCHEDULE_NAME_MAX_LENGTH') || define('PM_SCHEDULE_NAME_MAX_LENGTH', 200);

// Report-spezifische Konstanten
defined('REPORT_DEFAULT_PAGE_LENGTH') || define('REPORT_DEFAULT_PAGE_LENGTH', 25);
defined('REPORT_PDF_MAX_TEXT_LENGTH') || define('REPORT_PDF_MAX_TEXT_LENGTH', 20);
defined('REPORT_CSV_SEPARATOR') || define('REPORT_CSV_SEPARATOR', ';');

// Cache-spezifische Konstanten (für zukünftige Implementierung)
defined('CACHE_DASHBOARD_STATS_TTL') || define('CACHE_DASHBOARD_STATS_TTL', 300); // 5 Minuten
defined('CACHE_ASSET_STATS_TTL') || define('CACHE_ASSET_STATS_TTL', 600); // 10 Minuten
