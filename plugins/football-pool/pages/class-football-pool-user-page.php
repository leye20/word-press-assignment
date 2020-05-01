<?php
class Football_Pool_User_Page {
	public function page_content() {
		$user_id = Football_Pool_Utils::get_integer( 'user', 0 );
		// default to the currently logged in user, if no user is given.
		if ( $user_id == 0 ) $user_id = get_current_user_id();
		$user = get_userdata( $user_id );
		
		$output = '';
		
		if ( $user ) {
			$stats = new Football_Pool_Statistics;
			if ( $stats->stats_enabled ) {
				$output .= sprintf( '<div class="statistics" title="%s">', __( 'view all statistics for this user', 'football-pool' ) );
				$output .= sprintf( '<h5>%s</h5>', __( 'Statistics', 'football-pool' ) );
				$output .= sprintf( '<p><a class="statistics" href="%s">%s</a></p>'
									, esc_url(
										add_query_arg(
											array( 'view' => 'user', 'user' => $user->ID ),
											Football_Pool::get_page_link( 'statistics' )
										)
									)
									, __( 'Statistics', 'football-pool' )
							);
				$output .= '</div>';
			}

			$pool = new Football_Pool_Pool();
			$matches = new Football_Pool_Matches();
			$matches->disable_edits();
			
			$output .= sprintf( '<p class="user-page-intro">%s <span class="username">%s</span>.</p>'
								, __( 'Below are all the predictions for', 'football-pool' )
								, $pool->user_name( $user->ID )
						);
			$output = apply_filters( 'footballpool_user_page_html_after_username', $output, $user->ID );
			
			if ( ! $matches->always_show_predictions ) {
				$output .= sprintf( '<p>%s</p>'
									, __( 'Only matches and bonus questions that can\'t be changed are shown here.',
											'football-pool' )
							);
			}
			
			$match_rows = $matches->get_match_info_for_user( $user_id, null );
			if ( Football_Pool_Utils::get_fp_option( 'user_page_show_predictions_only', false ) == true ) {
				// filter out matches without a prediction
				$match_rows = array_filter( $match_rows, array( $this, 'remove_unpredicted_matches' ) );
			}
			if ( Football_Pool_Utils::get_fp_option( 'user_page_show_finished_matches_only', false ) == true ) {
				// filter out matches without an end result
				$match_rows = array_filter( $match_rows, array( $this, 'remove_unfinished_matches' ) );
			}
			
			$result = apply_filters( 'footballpool_user_page_matches', $match_rows, $user_id );
			$filtered_result = apply_filters( 'footballpool_user_page_matches_filtered', $result, $user_id );
			
			$show_actual = Football_Pool_Utils::get_fp_option( 'user_page_show_actual_result', false );
			$is_user_page = true;
			$output .= $matches->print_matches_for_input( $filtered_result, 1, $user_id, $is_user_page, $show_actual );
			
			$pool = new Football_Pool_Pool();
			if ( $pool->has_bonus_questions ) {
				$only_non_linked = true;
				$questions = $pool->get_bonus_questions_for_user( $user_id, null, $only_non_linked );
				
				if ( Football_Pool_Utils::get_fp_option( 'user_page_show_predictions_only', false ) == true ) {
					// filter out questions without an answer
					$questions = array_filter( $questions, array( $this, 'remove_unanswered_questions' ) );
				}
				if ( Football_Pool_Utils::get_fp_option( 'user_page_show_finished_matches_only', false ) == true ) {
					// filter out questions that did not end yet
					$questions = array_filter( $questions, array( $this, 'remove_unfinished_questions' ) );
				}
				
				$questions = apply_filters( 'footballpool_user_page_questions', $questions );
				$questions_output = $pool->print_bonus_question_for_user( $questions );
				if ( $questions_output != '' ) {
					$output .= sprintf( '<h2>%s</h2>', __( 'bonus questions', 'football-pool' ) );
					$output .= $questions_output;
				}
			}
			
			$output = apply_filters( 'footballpool_user_page_html', $output, $result );
		} else {
			$output = sprintf( '<p>%s</p>', __( 'No user selected.', 'football-pool' ) );
		}

		return $output;
	}
	
	// helper functions
	private function remove_unpredicted_matches( $m ) {
		return $m['home_score'] !== NULL || $m['away_score'] !== NULL;
	}
	private function remove_unfinished_matches( $m ) {
		return $m['real_home_score'] !== NULL || $m['real_away_score'] !== NULL;
	}
	private function remove_unanswered_questions( $q ) {
		return $q['answer'] != '';
	}
	private function remove_unfinished_questions( $q ) {
		return $q['score_date'] !== NULL;
	}
}
