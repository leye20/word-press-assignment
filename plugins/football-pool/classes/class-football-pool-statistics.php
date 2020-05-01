<?php
class Football_Pool_Statistics {
	public $data_available = false;
	public $stats_visible = false;
	public $stats_enabled = false;
	
	public function __construct() {
		$this->data_available = $this->check_data();
		
		if ( Football_Pool_Utils::get_fp_option( 'simple_calculation_method' ) == 0 ) {
			$chart = new Football_Pool_Chart;
			$this->stats_enabled = $chart->stats_enabled;
		} else {
			// With simple calculation method, the stats are always disabled
			$this->stats_enabled = false;
		}
	}
	
	public function page_content() {
		$output = new Football_Pool_Statistics_Page();
		return $output->page_content();
	}
	
	private function check_data( $match = 0 ) {
		global $wpdb;
		$prefix = FOOTBALLPOOL_DB_PREFIX;
		$pool = new Football_Pool_Pool();
		$scorehistory = $pool->get_score_table();
		
		$ranking_id = FOOTBALLPOOL_RANKING_DEFAULT;
		$single_match = ( $match > 0 ) ? '' : '--';
		$sql = $wpdb->prepare( sprintf( "SELECT COUNT( * ) FROM {$prefix}{$scorehistory} 
								WHERE ranking_id = %%d %s AND type = 0 AND source_id = %%d", $single_match )
								, $ranking_id, $match
							);
		$num = $wpdb->get_var( $sql );
		
		return ( $num > 0 );
	}
	
	public function data_available_for_match( $match ) {
		return $this->check_data( $match );
	}
	
	public function get_user_info( $user ) {
		return get_userdata( $user );
	}
	
	public function show_user_info( $user ) {
		if ( $user ) {
			$pool = new Football_Pool_Pool();
			$output = sprintf( '<h1>%s</h1>', $pool->user_name( $user->ID ) );
			$this->stats_visible = true;
		} else {
			$output = sprintf( '<p>%s</p>', __( 'User unknown.', 'football-pool' ) );
			$this->stats_visible = false;
		}
		
		return $output;
	}
	
	public function show_match_info( $info ) {
		$output = '';
		$this->stats_visible = false;
		$matches = new Football_Pool_Matches;
		
		if ( count( $info ) > 0 ) {
			if ( $matches->always_show_predictions || $info['match_is_editable'] == false ) {
				$output .= sprintf( '<h2>%s - %s'
									, Football_Pool_Utils::xssafe( $info['home_team'] )
									, Football_Pool_Utils::xssafe( $info['away_team'] )
								);
				if ( is_integer( $info['home_score'] ) && is_integer( $info['away_score'] ) ) {
					$output .= sprintf( ' (%d - %d)', $info['home_score'], $info['away_score'] );
				}
				$output .= '</h2>';
				$output .= sprintf( '<h3 class="stadium-name">%s</h3>', Football_Pool_Utils::xssafe( $info['stadium_name'] ) );
				$this->stats_visible = true;
			} else {
				$output .= sprintf( '<h2>%s - %s</h2>'
									, Football_Pool_Utils::xssafe( $info['home_team'] )
									, Football_Pool_Utils::xssafe( $info['away_team'] )
								);
				$output .= sprintf( '<h3 class="stadium-name">%s</h3>', Football_Pool_Utils::xssafe( $info['stadium_name'] ) );
				$output .= sprintf( '<p>%s</p>', __( 'This data is not (yet) available.', 'football-pool' ) );
			}
		} else {
			$output .= sprintf( '<p>%s</p>', __( 'This data is not (yet) available.', 'football-pool' ) );
		}
		
		$output = apply_filters( 'footballpool_statistics_show_match_info', $output, $info );
		return $output;
	}
	
	public function show_bonus_question_info( $question ) {
		$output = '';
		$pool = new Football_Pool_Pool();
		$info = $pool->get_bonus_question_info( $question );
		if ( $info ) {
			$points = $info['points'] == 0 ? __( 'variable', 'football-pool' ) : $info['points'];
			$output .= sprintf( '<h2>%s</h2>', Football_Pool_Utils::xssafe( $info['question'] ) );
			$output .= sprintf( '<p class="question-info"><span class="question-points">%s: %s</span>'
								, __( 'points', 'football-pool' )
								, $points
							);
			if ( $pool->always_show_predictions || ! $info['question_is_editable'] ) {
				$this->stats_visible = true;
				if ( ! $info['question_is_editable'] ) {
					$output .= sprintf( '<br /><span class="question-answer">%s: %s</span>'
									, __( 'answer', 'football-pool' )
									, Football_Pool_Utils::xssafe( $info['answer'] )
								);
				}
			} else {
				$output .= sprintf( '<p>%s</p>', __( 'This data is not (yet) available.', 'football-pool' ) );
				$this->stats_visible = false;
			}
			$output .= '</p>';
		} else {
			$output .= sprintf( '<p>%s</p>', __( 'This data is not (yet) available.', 'football-pool' ) );
			$this->stats_visible = false;
		}
		
		return $output;
	}
	
	public function show_answers_for_bonus_question( $id ) {
		$pool = new Football_Pool_Pool();
		$info = $pool->get_bonus_question_info( $id );
		
		$show_answer_status = ( $info && $info['score_date'] != null );
		
		$answers = $pool->get_bonus_question_answers_for_users( $id );
		$rows = apply_filters( 'footballpool_statistics_bonusquestion', $answers );
		
		$output = sprintf( '<table class="statistics prediction-table-questions">
							<tr><th>%s</th><th>%s</th><th class="correct">%s</th></tr>'
							, __( 'user', 'football-pool' )
							, __( 'answer', 'football-pool' )
							, __( 'correct?', 'football-pool' )
				);
		
		$userpage = Football_Pool::get_page_link( 'user' );
		
		$class = $title = '';
		foreach ( $rows as $answer ) {
			if ( $show_answer_status ) {
				if ( $answer['correct'] == 1 ) {
					// $class = 'correct fp-icon-checkmark-circle';
					$class = 'correct fp-icon-checkmark';
					$title = __( 'correct answer', 'football-pool' );
				} else {
					// $class = 'wrong fp-icon-cancel-circle';
					$class = 'wrong fp-icon-close';
					$title = __( 'wrong answer', 'football-pool' );
				}
			}
			$output .= sprintf( '<tr><td><a href="%s">%s</a></td><td>%s</td>'
								, esc_url( add_query_arg( array( 'user' => $answer['user_id'] ), $userpage ) )
								, $answer['name']
								, Football_Pool_Utils::xssafe( $answer['answer'], null, false )
						);
			$output .= sprintf( '<td class="score"><span class="score %s" title="%s"></span></td></tr>'
								, $class
								, $title
						);
		}
		$output .= '</table>';
		
		return apply_filters( 'footballpool_statistics_bonusquestion_html', $output, $answers );
	}
	
	public function show_predictions_for_match( $match_info ) {
		global $wpdb;
		$prefix = FOOTBALLPOOL_DB_PREFIX;
		
		$pool = new Football_Pool_Pool();
		
		$sql = "SELECT
					m.home_team_id, m.away_team_id, 
					p.home_score, p.away_score, p.has_joker, u.ID AS user_id, u.display_name AS user_name ";
		if ( $pool->has_leagues ) $sql .= ", l.id AS league_id ";
		$sql .= "FROM {$prefix}matches m 
				LEFT OUTER JOIN {$prefix}predictions p 
					ON ( p.match_id = m.id AND m.id = %d ) 
				RIGHT OUTER JOIN {$wpdb->users} u 
					ON ( u.ID = p.user_id ) ";
		if ( $pool->has_leagues ) {
			$sql .= "INNER JOIN {$prefix}league_users lu ON ( u.ID = lu.user_id )
					INNER JOIN {$prefix}leagues l ON ( l.id = lu.league_id ) ";
		} else {
			$sql .= "LEFT OUTER JOIN {$prefix}league_users lu ON ( lu.user_id = u.ID ) ";
			$sql .= "WHERE ( lu.league_id <> 0 OR lu.league_id IS NULL ) ";
		}
		$sql .= "ORDER BY u.display_name ASC";
		$sql = $wpdb->prepare( $sql, $match_info['id'] );
		
		$predictions = $wpdb->get_results( $sql, ARRAY_A );
		$rows = apply_filters( 'footballpool_statistics_matchpredictions', $predictions, $match_info );
		
		$output = '';
		if ( count( $rows ) > 0 ) {
			// define templates
			$template_start = sprintf( '<table class="matchinfo statistics">
									<tr><th class="username">%s</th>
									<th colspan="%d">%s</th><th>%s</th></tr>',
									__( 'name', 'football-pool' ),
									( $pool->has_jokers ? 4 : 3 ),
									__( 'prediction', 'football-pool' ),
									__( 'score', 'football-pool' )
								);
			$template_start = apply_filters( 'footballpool_matchpredictions_template_start'
											, $template_start, $match_info );
			
			$template_end = '</table>';
			$template_end = apply_filters( 'footballpool_matchpredictions_template_end'
											, $template_end, $match_info );
			
			$row_template = '<tr class="%current_user_css_class%">
								<td><a href="%user_url%">%user_name%</a></td>
								<td class="home">%home_score%</td>
								<td class="match-hyphen">-</td>
								<td class="away">%away_score%</td>';
			if ( $pool->has_jokers ) {
				$row_template .= '<td title="%joker_title_text%"><span class="nopointer %joker_css_class%"></span></td>';
			}
			$row_template .= '<td class="score">%score%</td></tr>';
			
			$row_template = apply_filters( 'footballpool_matchpredictions_row_template'
											, $row_template, $match_info );
			
			// define the start and end template params
			$template_params = array();
			$template_params = apply_filters( 'footballpool_matchpredictions_template_params'
											, $template_params, $match_info );
			
			// start output
			$output .= Football_Pool_Utils::placeholder_replace( $template_start, $template_params );
			
			$current_user_id = get_current_user_id();
			$userpage = Football_Pool::get_page_link( 'user' );
			foreach ( $rows as $row ) {
				// set the params for this row
				$row_params = array();
				$row_params['user_name'] = $pool->user_name( $row['user_id'] );
				$row_params['user_url'] = esc_url( add_query_arg( array( 'user' => $row['user_id'] ), $userpage ) );
				$row_params['current_user_css_class'] = ( (int) $row['user_id'] === $current_user_id ? 'currentuser' : '' );
				$row_params['home_score'] = $row['home_score'];
				$row_params['away_score'] = $row['away_score'];
				if ( $row['has_joker'] == 1 ) {
					$row_params['joker_title_text'] = _x( 'joker set', 'to indicate that a user has set a joker for this match'
														, 'football-pool' );
					$row_params['joker_css_class'] = 'fp-joker';
				} else {
					$row_params['joker_title_text'] = '';
					$row_params['joker_css_class'] = 'fp-nojoker';
				}
				$score = $pool->calc_score(
									$match_info['home_score'], 
									$match_info['away_score'], 
									$row['home_score'], 
									$row['away_score'], 
									$row['has_joker'],
									$match_info['id'],
									$row['user_id']
								);
				$row_params['score'] = $score['score'];
				
				$row_params = apply_filters( 'footballpool_matchpredictions_row_params'
											, $row_params, $match_info, $row['user_id'] );
				
				// output the row
				$output .= Football_Pool_Utils::placeholder_replace( $row_template, $row_params );
			}
			
			$output .= Football_Pool_Utils::placeholder_replace( $template_end, $template_params );
		}
		
		return apply_filters( 'footballpool_statistics_matchpredictions_html', $output, $predictions );
	}
	
}
