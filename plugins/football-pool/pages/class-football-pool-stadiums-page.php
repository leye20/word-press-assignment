<?php
class Football_Pool_Stadiums_Page {
	public function page_content() {
		$output = '';
		$stadiums = new Football_Pool_Stadiums;

		$stadium_id = Football_Pool_Utils::get_string( 'stadium' );

		$stadium = $stadiums->get_stadium_by_id( $stadium_id );
		if ( is_object( $stadium ) ) {
			// show details for stadium
			$output .= sprintf( '<h1>%s</h1>', Football_Pool_Utils::xssafe( $stadium->name ) );
			
			if ( $stadium->comments != '' ) {
				$output .= sprintf( '<p class="stadium bio">%s</p>'
									, nl2br( Football_Pool_Utils::xssafe( $stadium->comments ) ) 
								);
			}
			
			$output .= sprintf( '<p>%s</p>', $stadium->HTML_image() );

			// the games played in this stadium
			$plays = $stadium->get_plays();
			if ( count( $plays ) > 0 ) {
				$output .= sprintf( '<h4>%s</h4>', __( 'matches', 'football-pool' ) );
				$matches = new Football_Pool_Matches;
				$output .= $matches->print_matches( $plays, 'page stadium-page' );
			}
			
			$output .= sprintf( '<p><a href="%s">%s</a></p>'
								, get_page_link()
								, __( 'view all venues', 'football-pool' )
						);
		} else {
			// show all stadiums
			$output .= '<p><ol class="football-pool stadium-list">';
			$all_stadiums = $stadiums->get_stadiums();
			$output .= $stadiums->print_lines( $all_stadiums );
			$output .= '</ol></p>';
		}
		
		return apply_filters( 'footballpool_stadiums_page_html', $output, $stadium );
	}
}
