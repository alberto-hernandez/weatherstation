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
class Weatherstation_Register {

	private $db;

	private $token;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
		$this->table_name = $wpdb->prefix . 'weatherstation';
		$this->token = get_option('weatherstation_token');
	}

	/**
	 * 
	 */
	private function toBft ($value)
	{
		if ($value <= 1)
			return  0;
		elseif ($value <= 5)
			return 1;
		elseif ($value <= 11)
			return 2;
		elseif ($value <= 19)
			return 3;
		elseif ($value <= 28)
			return 4;
		elseif ($value <= 38)
			return 5;
		elseif ($value <= 49)
			return 6;
		elseif ($value <= 61)
			return 7;
		elseif ($value <= 74)
			return 8;
		elseif ($value <= 88)
			return 9;
		elseif ($value <= 102)
			return 10;
		elseif ($value <= 117)
			return 11;
		else
			return 12;
	}


	function compute_derivates ($signal) {
		// convert from windspeed in km/h to knots
		$signal['c_ws'] = $signal['ws'] / 1.852;
		$signal['c_wg'] = $signal['wg'] / 1.852;

		$signal['c_ws_bft'] = $this->toBft ($signal['ws']);
		$signal['c_wg_bft'] = $this->toBft ($signal['wg']);

		$direction = 'N';
		if ($signal['wd'] < 11.25)
			$direction = 'N';
		elseif ($signal['wd'] < 33.75)
			$direction = 'NNE';
		elseif ($signal['wd'] <= 56.25)
			$direction = 'NE';
		elseif ($signal['wd'] <= 78.75)
			$direction = 'ENE';
		elseif ($signal['wd'] <= 101.25)
			$direction = 'E';
		elseif ($signal['wd'] <= 123.75)
			$direction = 'ESE';
		elseif ($signal['wd'] <= 146.25)
			$direction = 'SE';
		elseif ($signal['wd'] <= 168.75)
			$direction = 'SSE';
		elseif ($signal['wd'] <= 191.25)
			$direction = 'S';
		elseif ($signal['wd'] <= 213.75)
			$direction = 'SSO';
		elseif ($signal['wd'] <= 236.5)
			$direction = 'SO';
		elseif ($signal['wd'] <= 258.75)
			$direction = 'OSO';
		elseif ($signal['wd'] <= 281.25)
			$direction = 'O';
		elseif ($signal['wd'] <= 303.75)
			$direction = 'ONO';
		elseif ($signal['wd'] <= 326.25)
			$direction = 'NO';
		elseif ($signal['wd'] <= 348.75)
			$direction = 'NNO';
		else
			$direction = 'N';
		$signal['c_wd'] = $direction;

		return $signal;
	}

	
	private function registerNewMeasure ($raw_signal) {
		$signal = $this->compute_derivates ($raw_signal);

		$this->db->insert( 
			$this->table_name, 
			array( 
				'time' => $signal['dt'], 
				'r_temperature' => $signal['t'], 
				'r_windspeed' => $signal['ws'], 
				'r_windgust' => $signal['wg'], 
				'r_wind_direction' => $signal['wd'], 
				'r_barometric_pressure' => $signal['bp'], 
				'c_windspeed_kt' => $signal['c_ws'], 
				'c_windgust_kt' => $signal['c_wg'], 
				'c_windspeed_bft' => $signal['c_ws_bft'],
				'c_windgust_bft' => $signal['c_wg_bft'],
				'c_wind_direction' => $signal['c_wd'],
			), 
			array( 
				'%s', 
				'%f', 
				'%d', 
				'%d', 
				'%f', 
				'%f', 
				'%d', 
				'%d', 
				'%d', 
				'%d', 
				'%s'
			) 
		);
	}



	public function addMeasure($request)
	{
		//global $weatherStation;
		$res = $request->get_params();
		$token = $res['token'];
		$response = new WP_REST_Response();
		
		if (! isset($token))
		{
			$response->set_status( 401 );
			return $response;
		}
		
		if ($token != $this->token)
		{
			$response->set_status( 401 );
			return $response;
		}
		
		foreach ($res['ws'] as $signal)
		{
			$this->registerNewMeasure($signal);
		}
		
		// Add a custom status code
		$response->set_status( 201 );
		return $response;
	}


}
