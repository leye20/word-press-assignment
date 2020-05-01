<?php
/**
 * Executes functions in the Football Pool plugin.
 */

WP_CLI::add_command( 'football-pool', 'Football_Pool_CLI_Command' );

class Football_Pool_CLI_Command extends WP_CLI_Command {
	/**
	 * Calculates the user ranking.
	 *
	 * ## OPTIONS
	 *
	 * [--force-calculation]
	 * : To force a calculation (see help page in plugin for extra info).
	 *
	 * [--normal-calculation]
	 * : To force the normal method of a calculation (regardless of plugin setting).
	 *
	 * [--simple-calculation]
	 * : To force the simple method of a calculation (regardless of plugin setting, see help page in plugin for extra info).
	 *
	 * [--output-time-only]
	 * : Only output the time the calculation took to complete (H:m:i).
	 *   Will only output time when the calculation ends without warning or error.
	 *
	 * ## EXAMPLES
	 *
	 *     wp football-pool calc
	 *
	 *     wp football-pool calc --force-calculation
	 *
	 * @alias calculate
	 */
	public function calc( $args, $assoc_args ) {
		$time_start = microtime( true );
		
		$output_time_only = ( isset( $assoc_args['output-time-only'] ) 
								&& $assoc_args['output-time-only'] === true );
		$force_calculation = ( isset( $assoc_args['force-calculation'] ) 
								&& $assoc_args['force-calculation'] === true ) ? 1 : 0;
		$normal_calculation = ( isset( $assoc_args['normal-calculation'] ) 
								&& $assoc_args['normal-calculation'] === true ) ? 1 : 0;
		$simple_calculation = ( isset( $assoc_args['simple-calculation'] ) 
								&& $assoc_args['simple-calculation'] === true ) ? 1 : 0;
								
		if ( $normal_calculation ) Football_Pool_Utils::set_fp_option( 'simple_calculation_method', 0 );
		if ( $simple_calculation ) Football_Pool_Utils::set_fp_option( 'simple_calculation_method', 1 );
		
		$calc_args = array(
			'force_calculation' => $force_calculation,
			'iteration' => 0,
		);
		
		$calc_args = Football_Pool_Admin_Score_Calculation::process( true, $calc_args );
		extract( $calc_args, EXTR_OVERWRITE );
		
		if ( ! $output_time_only ) {
			/** @var integer $total_iterations */
			$progress = \WP_CLI\Utils\make_progress_bar( 'Calculating scores', $total_iterations );
			// already tick one because we did the prepare step
			$progress->tick();
		}
		// loop through rest of steps
		/** @var integer $completed */
		/** @var boolean $error */
		while ( $completed !== 1 && $error === false ) {
			$calc_args = Football_Pool_Admin_Score_Calculation::process( true, $calc_args );
			extract( $calc_args, EXTR_OVERWRITE );
			if ( ! $output_time_only ) $progress->tick();
		}
		// on finish
		if ( $completed === 1 && ! $output_time_only ) $progress->finish();

		/** @var string $message */
		if ( $error !== false ) {
			WP_CLI::error( $message );
		} else {
			if ( isset( $message_type ) && $message_type === 'warning' ) {
				WP_CLI::warning( $message );
			} else {
				if ( $output_time_only ) {
					$time_end = microtime( true );
					$time = $time_end - $time_start;
					$hours = floor( $time / ( HOUR_IN_SECONDS ) );
					$time -= $hours * ( HOUR_IN_SECONDS );
					$minutes = floor( $time / MINUTE_IN_SECONDS );
					$time -= $minutes * MINUTE_IN_SECONDS;
					$seconds = floor( $time );
					WP_CLI::log( sprintf( "%02d:%02d:%02d", $hours, $minutes, $seconds ) );
				} else {
					WP_CLI::success( $message );
				}
			}
		}
	}
}
