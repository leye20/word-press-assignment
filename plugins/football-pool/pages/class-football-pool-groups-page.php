<?php
class Football_Pool_Groups_Page {
	public function page_content() {
		$group_id = Football_Pool_Utils::get_string( 'group' );
		
		$groups = new Football_Pool_Groups;
		$output = $groups->print_group_standing( $group_id );
		
		if ( $group_id ) {
			// the games for this group
			$output .= sprintf( '<h2 style="clear: both;">%s</h2>'
								, __( 'matches in the group stage', 'football-pool' ) 
						);
			$plays = $groups->get_plays( $group_id );
			
			$matches = new Football_Pool_Matches;
			$output .= $matches->print_matches( $plays, 'page group-page' );
			
			$group_names = $groups->get_group_names();
			if ( count( $group_names ) > 1 ) {
				$output .= sprintf( '<p style="clear: both;"><a href="%s">%s</a></p>'
									, get_page_link()
									, __( 'view all groups', 'football-pool' )
							);
			}
		}
		
		return apply_filters( 'footballpool_groups_page_html', $output );
	}
}
