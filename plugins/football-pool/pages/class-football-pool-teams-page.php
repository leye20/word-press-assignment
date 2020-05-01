<?php 
class Football_Pool_Teams_Page {
	public function page_content() {
		$output = '';
		
		$team_id = Football_Pool_Utils::get_integer( 'team' );
		$team = new Football_Pool_Team( $team_id );
		
		// show details for team or show links for all teams
		if ( is_object( $team ) && $team->id != 0 && $team->is_real == 1 && $team->is_active == 1 ) {
			// team details
			$output .= sprintf( '<h3 class="football-pool team name"><a href="%s" title="%s">%s</a></h3>' 
								, $team->link
								, __( 'go to the team site', 'football-pool' )
								, Football_Pool_Utils::xssafe( $team->name )
						);
			
			if ( $team->comments != '' ) {
				$output .= sprintf( '<p class="football-pool team bio">%s</p>'
									, nl2br( Football_Pool_Utils::xssafe( $team->comments ) ) 
								);
			}
			
			$output .= sprintf( '<table class="football-pool teaminfo">
									<tr>
										<th>%s:</th>
										<td><a href="%s">%s</a></td>
									</tr>'
								, __( 'plays in', 'football-pool' )
								, esc_url( add_query_arg( array( 'group' => $team->group_id ), Football_Pool::get_page_link('groups') ) )
								, Football_Pool_Utils::xssafe( $team->group_name )
						);
						
			// show venues?
			if ( Football_Pool_Utils::get_fp_option( 'show_venues_on_team_page', true ) ) {
				$stadiums = $team->get_stadiums();
				if ( is_array( $stadiums ) && count( $stadiums ) > 0 ) {
					$output .= sprintf( '<tr><th>%s:</th><td><ol class="football-pool stadium-list">'
										, __( 'venues', 'football-pool' )
								);
					$stadium_page = Football_Pool::get_page_link( 'stadiums' );
					while ( $stadium = array_shift( $stadiums ) ) {
						$output .= sprintf( '<li><a href="%s">%s</a></li>'
											, esc_url( add_query_arg( array( 'stadium' => $stadium->id ), $stadium_page ) )
											, Football_Pool_Utils::xssafe( $stadium->name )
									);
					}
					$output .= '</ol></td></tr>';
				}
			}
			
			$output .= sprintf( '<tr><th valign="top">%s:</th>', __( 'photo', 'football-pool' ) );
			$output .= sprintf( '<td>%s</td></tr>', $team->HTML_thumb() );
			$output .= '</table>';
			
			// the games for this team
			$plays = $team->get_plays();
			if ( count( $plays ) > 0 ) {
				$matches = new Football_Pool_Matches;
				$output .= sprintf( '<h4>%s</h4>', __( 'matches', 'football-pool' ) );
				$output .= $matches->print_matches( $plays, 'page team-page' );
			}
			
			$output .= sprintf( '<p><a href="%s">%s</a></p>'
								, get_page_link()
								, __( 'view all teams', 'football-pool' ) 
						);
		} else {
			// show all teams
			$teams = new Football_Pool_Teams();
			$output .= '<p><ol class="football-pool team-list">';
			$all_teams = $teams->get_teams();
			$output .= $teams->print_lines( $all_teams );
			$output .= '</ol></p>';
		}
		
		return apply_filters( 'footballpool_teams_page_html', $output, $team );
	}
}
