<?php
class Football_Pool_Utils {
	// XSS safe outputting
	// https://www.owasp.org/index.php/PHP_Security_Cheat_Sheet#XSS_Cheat_Sheet
	public static function xssafe( $data, $encoding = 'UTF-8', $allow_overwrite = FOOTBALLPOOL_ALLOW_HTML ) {
		if ( ! $allow_overwrite ) {
			$data = htmlspecialchars( $data, ENT_QUOTES | ENT_HTML401, $encoding );
		}
		
		return $data;
	}
	public static function xecho( $data, $encoding = 'UTF-8', $allow_overwrite = FOOTBALLPOOL_ALLOW_HTML ) {
		if ( ! $allow_overwrite ) {
			$data = self::xssafe( $data, $encoding );
		}
		
		echo $data;
	}
	// XSS safe outputting
	
	// https://defuse.ca/blog/escaping-string-literals-for-javascript-in-php.html
	public static function js_string_escape( $data ) {
		$safe = "";
		for ( $i = 0; $i < strlen( $data ); $i++ ) {
			if ( ctype_alnum( $data[$i] ) ) {
				$safe .= $data[$i];
			} else {
				$safe .= sprintf( "\\x%02X", ord( $data[$i] ) );
			}
		}
		return $safe;
	}
	
	/**
	 * Gets a single value from the user meta set.
	 *
	 * @param array $user_meta
	 * @param string $key
	 * @param string $default
	 *
	 * @return mixed|string
	 */
	public static function get_user_meta( $user_meta, $key, $default = '' ) {
		$output = $default;
		if ( is_array( $user_meta ) && isset( $user_meta[$key] ) 
			&& is_array( $user_meta[$key] ) && isset( $user_meta[$key][0] ) ) $output = $user_meta[$key][0];
		
		return $output;
	}
	

	/**
	 * Checks if the date is a valid date in the "Y-m-d H:i" format (or other format if the format is given).
	 *
	 * @param string $date the date to check
	 * @param string $format a date format string
	 *
	 * @return bool
	 * @throws Exception
	 */
	public static function is_valid_mysql_date( $date, $format = 'Y-m-d H:i' ) {
		// $d = DateTime::createFromFormat( $format, $date );
		$d = new DateTime();
		preg_match( '/([0-9]{4})-([0-9]{2})-([0-9]{2})\s([0-9]{2}):([0-9]{2})/', $date, $matches );
		if ( $matches && count( $matches ) == 6 ) {
			$d->setDate( (int) $matches[1], (int) $matches[2], (int) $matches[3] );
			$d->setTime( (int) $matches[4], (int) $matches[5] );
		}
		return $d && $d->format( $format ) == $date;
	}


	/**
	 * Helper function to include CSS files in the plugin.
	 *
	 * @param string $file
	 * @param string $handle
	 * @param array $deps
	 * @param bool $forced_exit
	 * @param string $custom_path
	 * @param bool $external
	 * @param null $pages
	 * @param string $version
	 */
	public static function include_css( $file, $handle, $deps = null, $forced_exit = true
										, $custom_path = '', $external = false, $pages = null
										, $version = FOOTBALLPOOL_DB_VERSION ) {
		$external = ( $external === 'external' );
		if ( $external || $custom_path != '' ) {
			$url = $external ? esc_url_raw( $file ) : $file;
			$dir = $custom_path;
		} else {
			$url = FOOTBALLPOOL_PLUGIN_URL . $file;
			$dir = FOOTBALLPOOL_PLUGIN_DIR . $file;
		}
		
		if ( $external || file_exists( $dir ) ) {
			wp_register_style( $handle, $url, $deps, $version );
			wp_enqueue_style( $handle );
		} else {
            if ( $forced_exit ) wp_die( $dir . ' not found' );
		}
	}
	
	/**
	 * Helper function to include JS files in the plugin.
	 *
	 * @param string $file
	 * @param string $handle
	 * @param array $deps
	 * @param bool $forced_exit
	 * @param string $custom_path
	 * @param bool $external
	 * @param null $pages
	 * @param string $version
	 */
	public static function include_js( $file, $handle, $deps = null, $forced_exit = true, $custom_path = ''
								, $external = false, $pages = null, $version = FOOTBALLPOOL_DB_VERSION ) {
		$external = ( $external === 'external' );
		if ( $external || $custom_path != '' ) {
			$url = $external ? esc_url_raw( $file ) : $file;
			$dir = $custom_path;
		} else {
			$url = FOOTBALLPOOL_PLUGIN_URL . $file;
			$dir = FOOTBALLPOOL_PLUGIN_DIR . $file;
		}
		
		if ( $external || file_exists( $dir ) ) {
			wp_register_script( $handle, $url, $deps, $version );
			wp_enqueue_script( $handle );
		} else {
            if ( $forced_exit ) wp_die( $dir . ' not found' );
		}
	}

	/**
	 * Checks if WordPress is at least at version $version
	 *
	 * @param string $version
	 *
	 * @return bool
	 */
	public static function wordpress_is_at_least_version( $version ) {
		$wp_ver = explode( '.', get_bloginfo( 'version' ) );
		$version = explode( '.', $version );
		for ( $i = 0; $i <= count( $version ); $i++ )
			if( ! isset( $wp_ver[$i] ) ) array_push( $wp_ver, 0 );
	 
		foreach ( $version as $i => $is_val )
			if ( (int) $wp_ver[$i] < (int) $is_val ) return false;
		
		return true;
	}

	/**
	 * Returns or echoes a string in a code block using the highlight_string() function.
	 *
	 * @param string $str
	 * @param bool $return
	 * @param string $class
	 *
	 * @return string|void
	 */
	public static function highlight_string( $str, $return = false, $class = 'block' ) {
		$highlight = highlight_string( $str, true );
		$highlight = str_ireplace( '<code>', "<code class='{$class}'>", $highlight );
		
		if ( $return === true ) {
			return $highlight;
		} else {
			echo $highlight;
		}
	}
	
	/**
	 * Replaces all placeholders in a string with a value.
	 *
	 * Parameter $input is a string with placeholders surrounded with %%. Parameter $params is an array of format
	 * array( 'placeholder' => 'text', ... ).
	 *
	 * @param string $input
	 * @param array $params
	 * @param string $placeholder_start the char that identifies the start of a placeholder, defaults to '%'
	 * @param string $placeholder_end the char that identifies the end of a placeholder, defaults to '%'
	 *
	 * @return string|string[]
	 */
	public static function placeholder_replace( $input, $params = array()
												, $placeholder_start = FOOTBALLPOOL_TEMPLATE_PARAM_DELIMITER
												, $placeholder_end = FOOTBALLPOOL_TEMPLATE_PARAM_DELIMITER ) {
		if ( count( $params ) > 0 ) {
			foreach ( $params as $key => $val ) {
				$input = str_replace( "{$placeholder_start}{$key}{$placeholder_end}", $val, $input );
			}
		}
		
		return $input;
	}

	/**
	 * Returns HTML for a select box with options.
	 *
	 * @param string $id
	 * @param array $options
	 * @param string|integer $selected_val
	 * @param string $name
	 * @param string $css_class
	 *
	 * @return string
	 */
	public static function select( $id, $options, $selected_val, $name = '', $css_class = '' ) {
		if ( $name === '' ) $name = $id;
		
		$output = sprintf( '<select name="%s" id="%s" class="%s">', $name, $id, $css_class );
		foreach ( $options as $val => $text ) {
			$output .= sprintf('<option value="%s"%s>%s</option>'
								, $val
								, ( $val == $selected_val ) ? ' selected="selected"' : ''
								, $text
						);
		}
		$output .= '</select>';
		
		return $output;
	}
	
	/**
	 * Extract ids from a string ("x", "x-z", "x,y,z").
	 *
	 * We convert everything to integers, so strings will also end up as integer (e.g. "a" will become 0),
	 * but this is in general not a problem as we are looking for DB id's.
	 *
	 * @param string $input
	 *
	 * @return array result is an array of integers
	 */
	public static function extract_ids( $input ) {
		$ids = array();
		// remove all spaces and tabs
		$input = str_replace( array( ' ', "\t" ), '', $input );
		// split for single numbers
		$input = explode( ',', $input );
		foreach ( $input as $part ) {
			// split in case of ranges
			$range = explode( '-', $part );
			if ( count( $range ) === 2 ) {
				// a range: x-y
				$x = (int) $range[0];
				$y = (int) $range[1];
				// always include low number of the range
				$ids[] = $x++;
				// if x is bigger than y (e.g. 5-2) it's not a valid range and we ignore the rest
				// if not, we add every number until we get to the upper boundary y of the range
				while ( $x <= $y ) {
					$ids[] = $x++;
				}
			} else {
				// a single number x
				// or a malformed range like x--y (which will be treated as a single number, returning only x).
				$ids[] = (int) $range[0];
			}
		}
		
		// remove duplicates and return
		return array_keys( array_flip( $ids ) );
	}

	/**
	 * Returns an int and stores the value + 1 in the WP cache.
	 *
	 * @param string $cache_key
	 *
	 * @return bool|int|mixed
	 */
	public static function get_counter_value( $cache_key ) {
		$id = wp_cache_get( $cache_key );
		if ( $id === false ) {
			$id = 1;
		}
		wp_cache_set( $cache_key, $id + 1 );
		
		return $id;
	}

	/**
	 * Accepts a date in Y-m-d H:i format and changes it to UTC.
	 *
	 * @param string $date_string
	 * @param string $date_format
	 *
	 * @return string
	 */
	public static function gmt_from_date( $date_string, $date_format = 'Y-m-d H:i' ) {
		if ( strlen( $date_string ) === strlen( '0000-00-00 00:00' ) ) $date_string .= ':00';
		return $date_string !== '' ? get_gmt_from_date( $date_string, $date_format ) : '';
	}
	
	/**
	 * Accepts a date in Y-m-d H:i format and changes it to local time according to WP's timezone setting.
	 *
	 * @param string $date_string
	 * @param string $date_format
	 *
	 * @return string
	 */
	public static function date_from_gmt( $date_string, $date_format = 'Y-m-d H:i' ) {
		if ( strlen( $date_string ) === strlen( '0000-00-00 00:00' ) ) $date_string .= ':00';
		return $date_string !== '' ? get_date_from_gmt( $date_string, $date_format ) : '';
	}

	/**
	 * Returns the full url of the current script.
	 *
	 * @return string
	 */
	public static function full_url() {
		$s = empty( $_SERVER['HTTPS'] ) ? '' : ( ( $_SERVER['HTTPS'] == 'on' ) ? 's' : '' );
		$protocol = substr( strtolower( $_SERVER['SERVER_PROTOCOL'] ), 0, strpos( strtolower( $_SERVER['SERVER_PROTOCOL'] ), '/' ) ) . $s;
		$port = ( $_SERVER['SERVER_PORT'] == '80' ) ? '' : ( ':' . $_SERVER['SERVER_PORT'] );
		return $protocol . '://' . $_SERVER['SERVER_NAME'] . $port . $_SERVER['REQUEST_URI'];
	}

	/**
	 * Updates a value in the football pool options array that's stored in wp_options.
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public static function set_fp_option( $key, $value ) {
		self::update_fp_option( $key, $value );
	}

	/**
	 * Updates a value in the football pool options array that's stored in wp_options.
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param string|mixed $overwrite defaults to overwriting the value for an existing key
	 */
	public static function update_fp_option( $key, $value, $overwrite = 'overwrite' ) {
		$options = get_option( FOOTBALLPOOL_OPTIONS, array() );
		if ( ! isset( $options[$key] ) || ( isset( $options[$key] ) && $overwrite === 'overwrite' ) ) {
			$options[$key] = $value;
			update_option( FOOTBALLPOOL_OPTIONS, $options );
		}
	}

	/**
	 * Returns a value from the football pool options array that's stored in wp_options.
	 *
	 * @param string $key
	 * @param string $default
	 * @param string $type
	 *
	 * @return int|string
	 */
	public static function get_fp_option( $key, $default = '', $type = 'text' ) {
		$options = get_option( FOOTBALLPOOL_OPTIONS, array() );
		$value = isset( $options[$key] ) ? $options[$key] : $default;
		if ( $type === 'int' || $type === 'integer' ) {
			if ( ! is_numeric( $value ) ) $value = $default;
			$value = (int) $value;
		}
		return $value;
	}
	
	/**
	 * @param array|mixed|string $value
	 *
	 * @return array|mixed|string
	 */
	public static function safe_stripslashes( $value ) {
		// damn you, magic quotes!
		// and damn you, WP, for not telling me about wp_magic_quotes()!
		// http://kovshenin.com/2010/wordpress-and-magic-quotes/

		// return get_magic_quotes_gpc() ? stripslashes( $value ) : $value;
		if ( is_array( $value) )
			return stripslashes_deep( $value );
		else
			return stripslashes( $value );
	}

	/**
	 * @param array|mixed|string $value
	 *
	 * @return array|mixed|string
	 */
	public static function safe_stripslashes_deep( $value ) {
		// return get_magic_quotes_gpc() ? stripslashes_deep( $value ) : $value;
		return stripslashes_deep( $value );
	}
	
	// String GET and POST

	/**
	 * @param string $key
	 * @param string $default
	 *
	 * @return string
	 */
	public static function request_str( $key, $default = '' ) {
		return self::request_string( $key, $default );
	}

	/**
	 * @param string $key
	 * @param string $default
	 *
	 * @return string
	 */
	public static function request_string( $key, $default = '' ) {
		return ( $_POST ? self::post_string( $key, $default ) : self::get_string( $key, $default ) );
	}

	/**
	 * @param string $key
	 * @param string $default
	 *
	 * @return string
	 */
	public static function get_str( $key, $default = '' ) {
		return self::get_string( $key, $default );
	}

	/**
	 * @param string $key
	 * @param string $default
	 *
	 * @return string
	 */
	public static function get_string( $key, $default = '' ) {
		return ( isset( $_GET[$key] ) ? self::safe_stripslashes( $_GET[$key] ) : $default );
	}

	/**
	 * @param string $key
	 * @param string $default
	 *
	 * @return string
	 */
	public static function post_str( $key, $default = '' ) {
		return self::post_string( $key, $default );
	}

	/**
	 * @param string $key
	 * @param string $default
	 *
	 * @return string
	 */
	public static function post_string( $key, $default = '' ) {
		return ( isset( $_POST[$key] ) ? self::safe_stripslashes( $_POST[$key] ) : $default );
	}
	
	// Integer GET and POST

	/**
	 * @param string $key
	 * @param int $default
	 *
	 * @return int
	 */
	public static function request_int( $key, $default = 0 ) {
		return self::request_integer( $key, $default );
	}

	/**
	 * @param string $key
	 * @param int $default
	 *
	 * @return int
	 */
	public static function request_integer($key, $default = 0) {
		return ( $_POST ? self::post_integer( $key, $default ) : self::get_integer( $key, $default ) );
	}

	/**
	 * @param string $key
	 * @param int $default
	 *
	 * @return int
	 */
	public static function get_integer( $key, $default = 0 ) {
		return ( isset( $_GET[$key] ) && is_numeric( $_GET[$key] )? (integer) $_GET[$key] : $default );
	}

	/**
	 * @param string $key
	 * @param int $default
	 *
	 * @return int
	 */
	public static function get_int( $key, $default = 0 ) {
		return self::get_integer( $key, $default );
	}

	/**
	 * @param string $key
	 * @param int $default
	 *
	 * @return int
	 */
	public static function post_integer( $key, $default = 0 ) {
		return ( isset( $_POST[$key] ) && is_numeric( $_POST[$key] )? (integer) $_POST[$key] : $default );
	}

	/**
	 * @param string $key
	 * @param int $default
	 *
	 * @return int
	 */
	public static function post_int( $key, $default = 0 ) {
		return self::post_integer( $key, $default );
	}
	
	// Array of integers GET and POST

	/**
	 * @param string $key
	 * @param array $default
	 *
	 * @return array
	 */
	public static function request_int_array( $key, $default = array() ) {
		return self::request_integer_array( $key, $default );
	}

	/**
	 * @param string $key
	 * @param array $default
	 *
	 * @return array
	 */
	public static function request_integer_array( $key, $default = array() ) {
		if ( $_POST ) {
			return self::post_integer_array( $key, $default );
		} else {
			return self::get_integer_array( $key, $default );
		}
	}

	/**
	 * @param string $key
	 * @param array $default
	 *
	 * @return array
	 */
	public static function get_intArray( $key, $default = array() ) {
		return self::get_integer_array( $key, $default );
	}

	/**
	 * @param string $key
	 * @param array $default
	 *
	 * @return array
	 */
	public static function get_integer_array( $key, $default = array() ) {
		$arr = array();
		if ( isset( $_GET[$key] ) && is_array( $_GET[$key] ) ) {
			$get = $_GET[$key];
			foreach ( $get as $str ) $arr[] = (int) $str;
		} else {
			$arr = $default;
		}
		
		return $arr;
	}

	/**
	 * @param string $key
	 * @param array $default
	 *
	 * @return array
	 */
	public static function post_int_array( $key, $default = array() ) {
		return self::post_integer_array( $key, $default );
	}

	/**
	 * @param string $key
	 * @param array $default
	 *
	 * @return array
	 */
	public static function post_integer_array( $key, $default = array() ) {
		$arr = array();
		if ( isset( $_POST[$key] ) && is_array( $_POST[$key] ) ) {
			$post = $_POST[$key];
			foreach ( $post as $str ) $arr[] = (int) $str;
		} else {
			$arr = $default;
		}
		
		return $arr;
	}
	
	// Array of strings GET and POST

	/**
	 * @param string $key
	 * @param array $default
	 *
	 * @return array
	 */
	public static function request_str_array( $key, $default = array() ) {
		return self::request_string_array( $key, $default );
	}

	/**
	 * @param string $key
	 * @param array $default
	 *
	 * @return array
	 */
	public static function request_string_array( $key, $default = array() ) {
		return ( $_POST ? self::post_string_array( $key, $default ) : self::get_string_array( $key, $default ) );
	}

	/**
	 * @param string $key
	 * @param array $default
	 *
	 * @return array
	 */
	public static function get_str_array( $key, $default = array() ) {
		return self::get_string_array( $key, $default );
	}

	/**
	 * @param string $key
	 * @param array $default
	 *
	 * @return array
	 */
	public static function get_string_array( $key, $default = array() ) {
		return ( isset( $_GET[$key] ) && is_array( $_GET[$key] ) ? self::safe_stripslashes_deep( $_GET[$key] ) : $default );
	}

	/**
	 * @param string $key
	 * @param array $default
	 *
	 * @return array
	 */
	public static function post_str_array( $key, $default = array() ) {
		return self::post_string_array( $key, $default );
	}

	/**
	 * @param string $key
	 * @param array $default
	 *
	 * @return array
	 */
	public static function post_string_array( $key, $default = array() ) {
		return ( isset( $_POST[$key] ) && is_array( $_POST[$key] ) ? self::safe_stripslashes_deep( $_POST[$key] ) : $default );
	}
	
	/**
	 * Debug function, but defaults to file log instead of echoing the debug info.
	 *
	 * @param mixed $var
	 * @param string $type
	 * @param int $sleep
	 */
	public static function debugf( $var, $type = 'file', $sleep = 0 ) {
		self::debug( $var, $type, $sleep );
	}
	
	/**
	 * Debug function, but defaults to mail log instead of echoing the debug info and pauses by default for 100ms.
	 *
	 * @param mixed $var
	 * @param string $type
	 * @param int $sleep
	 */
	public static function debugm( $var, $type = 'mail', $sleep = 100 ) {
		self::debug( $var, $type, $sleep );
	}
	
	/**
	 * Print information about a variable in a human readable way.
	 * If argument sleep is set, the execution will halt after the debug for the given amount of micro seconds
	 * (one micro second = one millionth of a second).
	 *
	 * @param mixed $var
	 * @param string $type
	 * @param int $sleep
	 *
	 * @return string|void
	 */
	public static function debug( $var, $type = 'echo', $sleep = 0 ) {
		if ( defined( 'FOOTBALLPOOL_DEBUG_FORCE' ) ) {
			$type = FOOTBALLPOOL_DEBUG_FORCE;
		} else {
			if ( ! FOOTBALLPOOL_ENABLE_DEBUG ) return;
		}
		
		$type = str_replace( array( 'only', 'just', ' ', '-' ), '', $type );
		
		if ( $type === 'once' || ( is_array( $type ) && $type[0] === 'once' ) ) {
			$type = isset( $type[1] ) ? $type[1] : 'echo';
			
			$cache_key = 'fp_debug';
			$i = wp_cache_get( $cache_key );
			if ( false === $i ) {
				$i = 1;
				wp_cache_set( $cache_key, $i );
			} else {
				$i++;
			}
			
			if ( $i > 1 ) return;
		}
		
		$pre  = "<pre style='border: 1px solid;'>";
		$pre .= "<div style='padding:2px;color:#fff;background-color:#000;'>debug</div><div style='padding:2px;'>";
		$post = "</div></pre>";
		switch ( $type ) {
			case 'mail':
			case 'email':
			case 'e-mail':
				$subject = date('D d/M/Y H:i P') . ': error log';
				$message = var_export( $var, true );
				wp_mail( FOOTBALLPOOL_DEBUG_EMAIL, $subject, $message );
				break;
			case 'log':
			case 'file':
				$pre  = "[" . date('D d/M/Y H:i P') . "]\n";
				$post = "\n-----------------------------------------------\n";
				if ( defined( 'FOOTBALLPOOL_ERROR_LOG' ) ) {
					if ( ! file_exists( FOOTBALLPOOL_ERROR_LOG ) ) {
						file_put_contents( FOOTBALLPOOL_ERROR_LOG, "{$pre}errorlog created{$post}" );
					}
					error_log( $pre . var_export( $var, true ) . $post, 3, FOOTBALLPOOL_ERROR_LOG );
				}
				break;
			case 'output':
			case 'return':
				return $pre . var_export( $var, true ) . $post;
				break;
			case 'echo':
			default:
				echo $pre;
				var_dump( $var );
				echo $post;
		}
		
		if ( is_int( $sleep ) && $sleep > 0 ) usleep( $sleep );
	}
	
}
