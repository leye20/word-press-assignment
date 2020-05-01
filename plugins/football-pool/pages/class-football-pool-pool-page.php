<?php
class Football_Pool_Pool_Page {
	public function page_content() {
		global $current_user;
		wp_get_current_user();

		$pool = new Football_Pool_Pool();
		$user_is_player = $pool->user_is_player( $current_user->ID );
		$output = '';
		
		if ( $current_user->ID != 0 && $user_is_player ) {
			// save any updates
			$output = $pool->prediction_form_update();
			
			$questions = $pool->get_bonus_questions_for_user( $current_user->ID );
			// determine if there are any questions not linked to a match
			$show_question_form = false;
			if ( $pool->has_bonus_questions ) {
				foreach ( $questions as $question ) {
					if ( $question['match_id'] == 0 ) {
						$show_question_form = true;
						break;
					}
				}
			}
			$matches = new Football_Pool_Matches;
			$result = $matches->get_match_info_for_user( $current_user->ID );
			$result = apply_filters( 'footballpool_page_pool_matches_filter_2', $result, $current_user->ID );
			$filtered_result = apply_filters( 'footballpool_page_pool_matches_filter', $result, $current_user->ID );
			
			$id = Football_Pool_Utils::get_counter_value( 'fp_predictionform_counter' );
			
			$empty_prediction = $matches->first_empty_match_for_user( $current_user->ID );
			if ( $show_question_form && $pool->has_matches ) {
				$output .= sprintf( '<p><a href="#bonus">%s</a> | <a href="#match-%d-%d">%s</a></p>'
									, __( 'Bonus questions', 'football-pool' )
									, $empty_prediction
									, $id
									, __( 'Predictions', 'football-pool' )
							);
			}
			
			$output .= $pool->prediction_form_start( $id );
			
			if ( $pool->has_matches ) {
				$output .= sprintf( '<h2>%s</h2>', __( 'matches', 'football-pool' ) );
				// the matches
				$output .= $pool->prediction_form_matches( $filtered_result, false, $id, 'matches pool-page' );
			}
			
			// the questions
			if ( $show_question_form ) {
				$nr = 1;
				$output .= sprintf( '<h2 id="bonus">%s</h2>', __( 'bonus questions', 'football-pool' ) );
				foreach ( $questions as $question ) {
					if ( $question['match_id'] == 0 ) {
						$output .= $pool->print_bonus_question( $question, $nr++ );
					}
				}
				$output .= $pool->save_button( 'questions pool-page', $id );
			}
			
			$output .= $pool->prediction_form_end();
			$output = apply_filters( 'footballpool_pool_page_html', $output, $result );
		} else {
			$output .= '<p>';
			$output .= sprintf( __( 'You have to be a <a href="%s">registered</a> user and <a href="%s">logged in</a> to play in this pool.', 'football-pool' )
								, wp_registration_url()
								, wp_login_url( get_permalink() )
							);
			$output .= '</p>';
		}
		
		return $output;
	}
	
}
