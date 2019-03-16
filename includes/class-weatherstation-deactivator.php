<?php

/**
 * Fired during plugin deactivation
 *
 * @link       www.cksc.es
 * @since      1.0.0
 *
 * @package    Weatherstation
 * @subpackage Weatherstation/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Weatherstation
 * @subpackage Weatherstation/includes
 * @author     Alberto Hernandez Acosta <albherna@gmail.com>
 */
class Weatherstation_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'weatherstation';

		//$sql = "DROP TABLE IF EXISTS " . $table_name;
		//$wpdb->query($sql);
		
		delete_option('weatherstation_token');
	}

}
