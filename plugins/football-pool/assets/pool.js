/**
 * pool.js
 *
 * @preserve Copyright 2020 Antoine Hurkmans
 * This is part of the Football Pool WordPress plugin.
 * See https://wordpress.org/plugins/football-pool/ for details and license.
 */

// minified with http://closure-compiler.appspot.com/home 

jQuery( document ).ready( function() {
	// colorbox
	jQuery( ".fp-lightbox" ).colorbox( {
		transition		: 'elastic',
		speed			: 400
	} );
} );

var FootballPool = ( function ( $ ) {
	
	var i18n = FootballPool_i18n;
	
	function change_joker( id ) {
		var joker_val = 0;
		var clicked_joker = $( "#" + id );
		
		if ( clicked_joker.hasClass( 'fp-joker' ) ) {
			// joker already set on this match, so remove it
			clicked_joker.removeClass( "fp-joker" ).addClass( "fp-nojoker" );
		} else {
			// joker not set, so we determine the new joker value
			joker_val = id.substring( id.indexOf( '_' ) + 1 );
			joker_val = joker_val.substring( 0, joker_val.indexOf( '_' ) );
			// remove old joker
			$( ".fp-joker" ).removeClass( "fp-joker" ).addClass( "fp-nojoker" );
			// set new joker
			clicked_joker.removeClass( "fp-nojoker" ).addClass( "fp-joker" );
		}
		// update the joker input
		var the_form = clicked_joker.closest( 'form' );
		$( "input[name*='_joker']", the_form ).val( joker_val );
	}
	
	function update_chars( id, chars ) {
		var length = $( "#" + id ).val().length;
		var remaining = chars - length;
		$( "#" + id ).parent().find( "span span" ).replaceWith( "<span>" + remaining + "</span>" );
	}
	
	function do_countdown( el, extra_text, year, month, day, hour, minute, second, format_type, format_string ) {
		var date_to = new Date( year, month-1, day, hour, minute, second ).getTime();
		var date_now = new Date().getTime();
		var diff = Math.abs( Math.round( ( date_to - date_now ) / 1000 ) );
		
		var pre = '', post = '', counter = '';
		var d = 0, h = 0, m = 0, s = 0;
		var days, hrs, min, sec;
		
		if ( format_string === '' || format_string === null ) {
			format_string = '{d} {days}, {h} {hrs}, {m} {min}, {s} {sec}';
		}
		
		if ( extra_text === null ) {
			extra_text = {
							'pre_before' : i18n.count_pre_before, 
							'post_before' : i18n.count_post_before, 
							'pre_after' : i18n.count_pre_after, 
							'post_after' : i18n.count_post_after
						}
		}
		
		if ( date_to < date_now ) {
			pre = extra_text['pre_after'];
			post = extra_text['post_after'];
		} else {
			pre = extra_text['pre_before'];
			post = extra_text['post_before'];
		}
		
		switch ( format_type ) {
			case 1: // only seconds
				s = diff;
				break;
			case 2: // days, hours, minutes, seconds
			case 4: // days, hours, minutes
				switch ( true ) {
					case diff > 86400:
						d = Math.floor( diff / 86400 );
						diff -= d * 86400;
					case diff > 3600:
						h = Math.floor( diff / 3600 );
						diff -= h * 3600;
					case diff > 60:
						m = Math.floor( diff / 60 );
						diff -= m * 60;
					default:
						s = diff;
				}
				break;
			case 3: // hours, minutes, seconds
			case 5: // hours, minutes
				switch ( true ) {
					case diff > 3600:
						h = Math.floor( diff / 3600 );
						diff -= h * 3600;
					case diff > 60:
						m = Math.floor( diff / 60 );
						diff -= m * 60;
					default:
						s = diff;
				}
				break;
		}
		
		// Set the correct texts for the day, hour, minute and second value
		days = ( d == 1 ? i18n.count_day : i18n.count_days );
		hrs  = ( h == 1 ? i18n.count_hour : i18n.count_hours );
		min  = ( m == 1 ? i18n.count_minute : i18n.count_minutes );
		sec  = ( s == 1 ? i18n.count_second : i18n.count_seconds );
		
		// Replace the number and text placeholders in the format string
		counter = format_string.replace( '{d}', d ).replace( '{days}', days )
								.replace( '{h}', h ).replace( '{hrs}', hrs )
								.replace( '{m}', m ).replace( '{min}', min )
								.replace( '{s}', s ).replace( '{sec}', sec );
		
		$( el ).text( pre + counter + post );
	}
	
	// based on http://www.frequency-decoder.com/2006/07/20/correctly-calculating-a-date-suffix
	// suffixes must be an array of format ["th", "st", "nd", "rd", "th"];
	function ordinal_suffix( d ) {
		var suffix = '';
		var suffixes = arguments[1] || ["th", "st", "nd", "rd", "th"];
		d = String( d );
		if ( d.substr( -( Math.min( d.length, 2 ) ) ) > 3 && d.substr( -( Math.min( d.length, 2 ) ) ) < 21 ) {
			suffix = suffixes[0];
		} else {
			suffix = suffixes[Math.min( Number( d ) % 10, 4 )];
		}
		return suffix;
	}

	function add_ordinal_suffix( d ) {
		return d + ordinal_suffix( d, arguments[1] );
	}

	function charts_user_toggle() {
		$( "input:checkbox", ".user-selector ol" ).bind( "click", function() {
			$( this ).parent().parent().toggleClass( "selected" );
		} );
	}
	
	function set_max_answers( id, max ) {
		var question = "#q" + id;
		
		// check onload
		check_max_answers( id, max );
		
		// and set the click action
		$( question + " :checkbox" ).click( function() {
			check_max_answers( id, max )
		} );
	}

	function check_max_answers( id, max ) {
		var question = "#q" + id;
		if( $( question + " :checkbox:checked" ).length >= max ) {
			$( question + " :checkbox:not(:checked)" ).attr( "disabled", "disabled" );
		} else {
			$( question + " :checkbox" ).removeAttr( "disabled" );
		}
	}
	
	return {
		// public methods
		add_ordinal_suffix: add_ordinal_suffix,
		change_joker: change_joker,
		update_chars: update_chars,
		countdown: do_countdown,
		charts_user_toggle: charts_user_toggle,
		set_max_answers: set_max_answers
	};

} )( jQuery );
