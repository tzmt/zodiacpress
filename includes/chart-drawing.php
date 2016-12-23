<?php
/**
 * Functions related to the chart drawing
 *
 * @package     ZodiacPress
 * @copyright   Copyright (c) 2016, Isabel Castillo
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Returns the chart drawing image element
 */
function zp_get_chart_drawing( $default, $arg, $chart, $colors = '' ) {
	$i18n = array(
		'hypothetical'	=> __( 'Hypothetical', 'zodiacpress' ),
		'time'			=> __( 'Time: 12:00pm', 'zodiacpress' )
	);

	if ( empty( $colors ) ) {
		$customizer_settings = ZP_Customize::get_settings();
	} else {
		// incorporate live customizer colors
		$customizer_settings = ZP_Customize::merge_settings( $colors );
	}

	$custom = rawurlencode( serialize( $customizer_settings ) );
	$i = rawurlencode( serialize( $i18n ) );
	$l = rawurlencode( serialize( $chart->planets_longitude ) );
	$s = rawurlencode( serialize( $chart->planets_speed ) );
	$c = rawurlencode( serialize( $chart->cusps ) );
	$u = urlencode( serialize( $chart->unknown_time ) );

	$out = '<img src="' . ZODIACPRESS_URL . 'image.php?zpl=' . $l . '&zps=' . $s . '&zpc=' . $c . '&zpi=' . $i . '&zpcustom=' . $custom . '&zpu=' . $u . '" class="zp-chart-drawing" alt="chart drawing" />';

	return $out;
}

/**
 * Insert chart drawing in the Birth Report, if enabled.
 */
function zp_report_append_drawing( $default, $arg, $chart ) {
	$report_variation = is_array( $arg ) ? $arg['zp-report-variation'] : $arg;

	if ( 'birthreport' == $report_variation )  {
		$default .= zp_get_chart_drawing( $default, $arg, $chart );
	}

	return $default;
}

/**
 * Get a test sample chart drawing
 * @param array $colors The current customizer preview color settings
 */
function zp_get_sample_chart_drawing( $colors = false ) {
	// Chart data for Steve Jobs
	$chart = ZP_Chart::get_instance( array(
		'name'					=> 'Steve Jobs',
		'month'					=> '2',
		'day'					=> '24',
		'year'					=> '1955',
		'hour'					=> '19',
		'minute'				=> '15',
		'geo_timezone_id'		=> 'America/Los_Angeles',
		'place'					=> 'San Francisco, California, United States',
		'zp_lat_decimal'		=> '37.77493',
		'zp_long_decimal'		=> '-122.41942',
		'zp_offset_geo'			=> '-8',
		'action'				=> 'zp_birthreport',
		'zp-report-variation'	=> 'birthreport',
		'unknown_time'			=> '',
		'house_system'			=> false,
		'sidereal'				=> false
	) );
	return zp_get_chart_drawing( '', '', $chart, $colors );
}

/*
 * Hook into the birth report to insert the chart drawing, if enabled.
 */
function zp_insert_chart_drawing() {
	$zp_options = get_option( 'zodiacpress_settings' );
	if ( isset( $zp_options['add_drawing_to_birthreport'] ) ) {
		if ( 'top' == $zp_options['add_drawing_to_birthreport'] ) {
			add_filter( 'zp_report_header', 'zp_report_append_drawing', 20, 3 );
		} elseif ( 'bottom' == $zp_options['add_drawing_to_birthreport'] ) {
			add_filter( 'zp_report_aspects', 'zp_report_append_drawing', 10, 3 );
		}
	}
}
add_action( 'plugins_loaded', 'zp_insert_chart_drawing' );