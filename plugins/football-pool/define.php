<?php
global $wpdb;

// admin screen options (defaults per page)
if ( ! defined( 'FOOTBALLPOOL_ADMIN_USERS_PER_PAGE' ) ) define( 'FOOTBALLPOOL_ADMIN_USERS_PER_PAGE', 20 );
if ( ! defined( 'FOOTBALLPOOL_ADMIN_MATCHES_PER_PAGE' ) ) define( 'FOOTBALLPOOL_ADMIN_MATCHES_PER_PAGE', 50 );
if ( ! defined( 'FOOTBALLPOOL_ADMIN_USER_ANWERS_PER_PAGE' ) ) define( 'FOOTBALLPOOL_ADMIN_USER_ANWERS_PER_PAGE', 50 );

// database and path constants
if ( ! defined( 'FOOTBALLPOOL_DB_PREFIX' ) ) define( 'FOOTBALLPOOL_DB_PREFIX', 'pool_' . $wpdb->prefix );
if ( ! defined( 'FOOTBALLPOOL_OPTIONS' ) ) define( 'FOOTBALLPOOL_OPTIONS', 'footballpool_plugin_options' );

if ( ! defined( 'FOOTBALLPOOL_PLUGIN_URL' ) ) define( 'FOOTBALLPOOL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
if ( ! defined( 'FOOTBALLPOOL_PLUGIN_DIR' ) ) define( 'FOOTBALLPOOL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FOOTBALLPOOL_PLUGIN_NAME', 'Football Pool' );

if ( ! defined( 'FOOTBALLPOOL_HIGHCHARTS_API' ) ) define( 'FOOTBALLPOOL_HIGHCHARTS_API', '/highcharts-js/highcharts.js' );

define( 'FOOTBALLPOOL_ASSETS_URL', FOOTBALLPOOL_PLUGIN_URL . 'assets/' );
if ( ! defined( 'FOOTBALLPOOL_ERROR_LOG' ) ) define( 'FOOTBALLPOOL_ERROR_LOG', FOOTBALLPOOL_PLUGIN_DIR . '_error_log.txt' );

$upload_dir = wp_upload_dir();
define( 'FOOTBALLPOOL_UPLOAD_DIR', trailingslashit( $upload_dir['basedir'] ) . trailingslashit( 'football-pool' ) );
define( 'FOOTBALLPOOL_UPLOAD_URL', trailingslashit( $upload_dir['baseurl'] ) . trailingslashit( 'football-pool' ) );

// pool
if ( ! defined( 'FOOTBALLPOOL_DEFAULT_SEASON' ) ) define( 'FOOTBALLPOOL_DEFAULT_SEASON', 1 );

// leagues
if ( ! defined( 'FOOTBALLPOOL_LEAGUE_ALL' ) ) define( 'FOOTBALLPOOL_LEAGUE_ALL', 1 );
if ( ! defined( 'FOOTBALLPOOL_LEAGUE_DEFAULT' ) ) define( 'FOOTBALLPOOL_LEAGUE_DEFAULT', 3 );

// scorehistory
if ( ! defined( 'FOOTBALLPOOL_CALC_STEPSIZE_MATCH' ) ) define( 'FOOTBALLPOOL_CALC_STEPSIZE_MATCH', 400 );
if ( ! defined( 'FOOTBALLPOOL_CALC_STEPSIZE_QUESTION' ) ) define( 'FOOTBALLPOOL_CALC_STEPSIZE_QUESTION', 400 );
if ( ! defined( 'FOOTBALLPOOL_CALC_STEPSIZE_SCORE' ) ) define( 'FOOTBALLPOOL_CALC_STEPSIZE_SCORE', 400 );
if ( ! defined( 'FOOTBALLPOOL_CALC_STEPSIZE_RANKING' ) ) define( 'FOOTBALLPOOL_CALC_STEPSIZE_RANKING', 400 );

define( 'FOOTBALLPOOL_SCORE_TABLE1_FORMAT', 'scorehistory_s%s_t1' );
define( 'FOOTBALLPOOL_SCORE_TABLE2_FORMAT', 'scorehistory_s%s_t2' );
if ( ! defined( 'FOOTBALLPOOL_RANKING_CALCULATION_NOAJAX' ) ) define( 'FOOTBALLPOOL_RANKING_CALCULATION_NOAJAX', false );
if ( ! defined( 'FOOTBALLPOOL_FORCE_CALCULATION' ) ) define( 'FOOTBALLPOOL_FORCE_CALCULATION', false );
define( 'FOOTBALLPOOL_RANKING_AUTOCALCULATION', 1 );
define( 'FOOTBALLPOOL_RANKING_DEFAULT', 1 );
define( 'FOOTBALLPOOL_TYPE_MATCH', 0 );
define( 'FOOTBALLPOOL_TYPE_QUESTION', 1 );

// matches and scores
define( 'FOOTBALLPOOL_MAXPERIOD', 15 * MINUTE_IN_SECONDS );
define( 'FOOTBALLPOOL_JOKERMULTIPLIER', 2 );
define( 'FOOTBALLPOOL_FULLPOINTS', 5 ); // 3
define( 'FOOTBALLPOOL_TOTOPOINTS', 2 ); // 2
define( 'FOOTBALLPOOL_GOALPOINTS', 0 ); // 1
define( 'FOOTBALLPOOL_DIFFPOINTS', 0 ); // bonus points for correct goal difference
                                        // (e.g. match result is 4-0 and prediction is 6-2)

// matches csv import and export
if ( ! defined( 'FOOTBALLPOOL_CSV_DELIMITER' ) ) define( 'FOOTBALLPOOL_CSV_DELIMITER', ';' );
if ( ! defined( 'FOOTBALLPOOL_CSV_UPLOAD_DIR' ) ) define( 'FOOTBALLPOOL_CSV_UPLOAD_DIR', trailingslashit( FOOTBALLPOOL_UPLOAD_DIR . 'schedules' ) );

// groups page
define( 'FOOTBALLPOOL_GROUPS_PAGE_DEFAULT_MATCHTYPE', 1 );
if ( ! defined( 'FOOTBALLPOOL_TEAM_POINTS_WIN' ) ) define( 'FOOTBALLPOOL_TEAM_POINTS_WIN', 3 );
if ( ! defined( 'FOOTBALLPOOL_TEAM_POINTS_DRAW' ) ) define( 'FOOTBALLPOOL_TEAM_POINTS_DRAW', 1 );

// predictions
define( 'FOOTBALLPOOL_DEFAULT_JOKERS', 1 );

// date and time formats (http://php.net/manual/en/function.date.php)
if ( ! defined( 'FOOTBALLPOOL_TIME_FORMAT' ) ) define( 'FOOTBALLPOOL_TIME_FORMAT', 'H:i' );
if ( ! defined( 'FOOTBALLPOOL_DATE_FORMAT' ) ) define( 'FOOTBALLPOOL_DATE_FORMAT', 'Y-m-d' );
if ( ! defined( 'FOOTBALLPOOL_MATCH_DATE_FORMAT' ) ) define( 'FOOTBALLPOOL_MATCH_DATE_FORMAT', 'M d, Y' );
if ( ! defined( 'FOOTBALLPOOL_MATCH_DAY_FORMAT' ) ) define( 'FOOTBALLPOOL_MATCH_DAY_FORMAT', 'l' );

// others
if ( ! defined( 'FOOTBALLPOOL_ALLOW_HTML' ) ) define( 'FOOTBALLPOOL_ALLOW_HTML', false );
if ( ! defined( 'FOOTBALLPOOL_CHANGE_STATS_TITLE' ) ) define( 'FOOTBALLPOOL_CHANGE_STATS_TITLE', true );
define( 'FOOTBALLPOOL_DEFAULT_PAGINATION_PAGE_SIZE', 20 );
define( 'FOOTBALLPOOL_SHOUTBOX_MAXCHARS', 150 );
if ( ! defined( 'FOOTBALLPOOL_SHOUTBOX_DOUBLE_POST_INTERVAL' ) ) define( 'FOOTBALLPOOL_SHOUTBOX_DOUBLE_POST_INTERVAL', HOUR_IN_SECONDS );	// time allowed between two (same) shoutbox messages from one user (in seconds)
define( 'FOOTBALLPOOL_DONATE_LINK', 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=MA9S7MSZRN3YJ'); // https://www.paypal.com/cgi-bin/webscr?cmd=_button-management
define( 'FOOTBALLPOOL_MATCH_SORT', 0 ); // date asc
if ( ! defined( 'FOOTBALLPOOL_NO_AVATAR' ) ) define( 'FOOTBALLPOOL_NO_AVATAR', true ); // set to false if you want to show avatars in the ranking
if ( ! defined( 'FOOTBALLPOOL_SMALL_AVATAR' ) ) define( 'FOOTBALLPOOL_SMALL_AVATAR', 18 ); // size in px
if ( ! defined( 'FOOTBALLPOOL_MEDIUM_AVATAR' ) ) define( 'FOOTBALLPOOL_MEDIUM_AVATAR', 28 ); // size in px
if ( ! defined( 'FOOTBALLPOOL_LARGE_AVATAR' ) ) define( 'FOOTBALLPOOL_LARGE_AVATAR', 36 ); // size in px
if ( ! defined( 'FOOTBALLPOOL_TEMPLATE_PARAM_DELIMITER' ) ) define( 'FOOTBALLPOOL_TEMPLATE_PARAM_DELIMITER', '%' );
if ( ! defined( 'FOOTBALLPOOL_CONTENT_FILTER_PRIORITY' ) ) define( 'FOOTBALLPOOL_CONTENT_FILTER_PRIORITY', 50 );
if ( ! defined( 'FOOTBALLPOOL_REDIRECT_FILTER_PRIORITY' ) ) define( 'FOOTBALLPOOL_REDIRECT_FILTER_PRIORITY', 50 );

// cache
define( 'FOOTBALLPOOL_CACHE_MATCHES', 'fp_match_info' );
define( 'FOOTBALLPOOL_CACHE_QUESTIONS', 'fp_bonus_question_info' );
define( 'FOOTBALLPOOL_CACHE_TEAMS', 'fp_teams_info' );
define( 'FOOTBALLPOOL_CACHE_LEAGUES_ALL', 'fp_get_leagues_all' );
define( 'FOOTBALLPOOL_CACHE_LEAGUES_USERDEFINED', 'fp_get_leagues_user_defined' );
define( 'FOOTBALLPOOL_CACHE_RANKINGS_ALL', 'fp_get_rankings_all' );
define( 'FOOTBALLPOOL_CACHE_RANKINGS_USERDEFINED', 'fp_get_rankings_user_defined' );

// nonces
define( 'FOOTBALLPOOL_NONCE_CSV', 'football-pool-csv-download' );
define( 'FOOTBALLPOOL_NONCE_ADMIN', 'football-pool-admin' );
define( 'FOOTBALLPOOL_NONCE_SCORE_CALC', 'football-pool-score-calculation' );
define( 'FOOTBALLPOOL_NONCE_BLOG', 'football-pool-blog' );
define( 'FOOTBALLPOOL_NONCE_FIELD_BLOG', '_footballpool_wpnonce' );
define( 'FOOTBALLPOOL_NONCE_SHOUTBOX', 'football-pool-shoutbox' );
define( 'FOOTBALLPOOL_NONCE_FIELD_SHOUTBOX', '_footballpool_shoutbox_wpnonce' );

// WP constants
if ( function_exists( 'wp_enqueue_media' ) ) {
	define( 'FOOTBALLPOOL_WP_MEDIA', true );
} else {
	define( 'FOOTBALLPOOL_WP_MEDIA', false );
}

// debug
// define( 'FOOTBALLPOOL_DEBUG_FORCE', 'file' );
define( 'FOOTBALLPOOL_DEBUG_EMAIL', 'wordpressfootballpool@gmail.com' );
if ( defined( 'FOOTBALLPOOL_LOCAL_MODE' ) ) {
	define( 'FOOTBALLPOOL_ENABLE_DEBUG', true );
	error_reporting( -1 );
	$wpdb->show_errors();
	// http://wordpress.org/support/topic/scheduled-posts-still-not-working-in-282#post-1175405
	// define( 'ALTERNATE_WP_CRON', true );
	if ( ! class_exists( 'ChromePhp' ) && file_exists( FOOTBALLPOOL_PLUGIN_DIR . '_dev/ChromePhp.php' ) ) 
		require_once FOOTBALLPOOL_PLUGIN_DIR . '_dev/ChromePhp.php';
} else {
	define( 'FOOTBALLPOOL_ENABLE_DEBUG', false );
}
