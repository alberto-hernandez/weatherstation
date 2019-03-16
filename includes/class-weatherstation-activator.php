<?php

/**
 * Fired during plugin activation
 *
 * @link       www.cksc.es
 * @since      1.0.0
 *
 * @package    Weatherstation
 * @subpackage Weatherstation/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Weatherstation
 * @subpackage Weatherstation/includes
 * @author     Alberto Hernandez Acosta <albherna@gmail.com>
 */
class Weatherstation_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'weatherstation';

		$sql = "CREATE TABLE IF NOT EXISTS " . $table_name .
	       "(
    		id INTEGER (11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			time        DATETIME,
			r_temperature DECIMAL(2,1),
    		r_windspeed   INT,
    		r_windgust    INT,
    		r_wind_direction DECIMAL(2,1),
			r_barometric_pressure DECIMAL(2,1),
			c_windspeed_kt INT,
			c_windgust_kt	INT,
			c_windspeed_bft	INT,
			c_windgust_bft	INT,
			c_wind_direction CHAR(5)
    	    );";
    	    
	    //upgrade contiene la función dbDelta la cuál revisará si existe la tabla o no
	    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	    
	    //creamos la tabla
		dbDelta($sql);
		
		$token = sha1(time()) .  sha1(time(-100)) . sha1(time(+200));
		add_option('weatherstation_token', $token);
	}

}
