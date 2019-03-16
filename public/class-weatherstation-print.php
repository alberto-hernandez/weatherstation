<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       www.cksc.es
 * @since      1.0.0
 *
 * @package    Weatherstation
 * @subpackage Weatherstation/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Weatherstation
 * @subpackage Weatherstation/public
 * @author     Alberto Hernandez Acosta <albherna@gmail.com>
 */
class Weatherstation_ShowMeasures {

	private $db;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
		$this->table_name = $wpdb->prefix . 'weatherstation';
	}


	function weatherstation ($atts = array())
	{
		ob_start();

		$args = shortcode_atts( array(
			'number_of_measures' => 144,
			'since' => 'yesterday',),
			$atts
		);

		$since = 1;
		if ($args['since'] == 'a_week_ago')
		{
			$since = 7;
		}
	
		// Prepared SQL
		$statement = $this->db->prepare("SELECT * FROM " . $this->table_name . 
										" WHERE time > subdate(current_date, %d) ORDER BY time DESC LIMIT %d", 
										$since, 
										$args['number_of_measures']);
		//$statement->bind_param("ii", $since, $args['number_of_measures']);
	    $measures = $this->db->get_results($statement);
	    
	    echo '<table class="weatherstation-table"><tr>';
	    echo '<th>time</th>';
	    echo '<th>wind</th>';
	    echo '<th>gusts</th>';
	    echo '<th>direction</th>';
	    echo '<th>temperature</th>';
	    
	    foreach ($measures as $measure)
	    {
	        echo '<tr class="weatherstation-row" >';
			echo '<td>' . $measure->time . '</td>';
			
	        echo '<td class="windspeed knt' . $measure->c_windspeed_kt . '"><strong>' . $measure->c_windspeed_kt . ' kts</strong>';
			echo '<span class="bft"> (' . $measure->c_windspeed_bft . ' Bft,';
			echo $measure->r_windspeed . ' km/h)</span></td>';

			echo '<td class="windspeed knt' . $measure->c_windgust_bft . '"><strong>' . $measure->c_windgust_kt . ' kts</strong>';
			echo '<span class="bft"> (' . $measure->c_windgust_bft . ' Bft, ';
			echo $measure->r_windgust . ' km/h)</span></td>';

			echo '<td><div class="orientation"><span>' . $measure->c_wind_direction . '</span>'
					  . '<div class="direction direction2 direction-' . strtolower ($measure->c_wind_direction) . '"></div></div></td>';
	        echo '<td class="temperature">' . $measure->r_temperature . ' ºC</td>';
	        echo '</tr>';
	    }
		echo '</table>';
		
		$output = ob_get_contents();
		ob_end_clean();
 
		return $output;
    }

}
