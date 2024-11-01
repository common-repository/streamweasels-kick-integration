<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.streamweasels.com
 * @since      1.0.0
 *
 * @package    Streamweasels_vods_Pro
 * @subpackage Streamweasels_vods_Pro/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Streamweasels_vods_Pro
 * @subpackage Streamweasels_vods_Pro/admin
 * @author     StreamWeasels <admin@streamweasels.com>
 */
class Streamweasels_Kick_Integration_Vods_Admin extends Streamweasels_Kick_Integration_Admin {

	private $plugin_name;
	private $version;
	private $options;
	private $baseOptions;
	private $base;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->baseOptions = get_option( 'swki_options', array() );
		$this->options = get_option( 'swki_options_vods', array() );
		$this->base = '';
		if (in_array('streamweasels-base/streamweasels.php', apply_filters('active_plugins', get_option('active_plugins')))){ 
			$this->base = '/streamweasels-base';
		}		
		if (in_array('streamweasels-twitch-integration-pro/streamweasels.php', apply_filters('active_plugins', get_option('active_plugins')))){ 
			$this->base = '/streamweasels-twitch-integration-pro';
		}
		if (in_array('streamweasels-twitch-integration/streamweasels.php', apply_filters('active_plugins', get_option('active_plugins')))){ 
			$this->base = '/streamweasels-twitch-integration';
		}
	}

	/**
	 * Register the admin page.
	 *
	 * @since    1.0.0
	 */
	public function display_admin_page_vods() {

		add_submenu_page(
			'streamweasels-kick',
			'[Layout] Vods',
			'[Layout] Vods',
			'manage_options',
			'streamweasels-kick-vods',
			array($this, 'swki_showAdmin')
		);		

		$tooltipArray = array(
			'Clip Period'=>'Clip Period <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="vods-clip-period=\'\'"></span>',
            'Column Count'=>'Column Count <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="vods-column-count=\'\'"></span>',
            'Column Spacing'=>'Column Spacing <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="vods-column-spacing=\'\'"></span>'
		);		

		register_setting( 'swki_options_vods', 'swki_options_vods', array($this, 'swki_options_validate'));	
		add_settings_section('swki_vods_shortcode_settings', 'Shortcode', false, 'swki_vods_shortcode_fields');		
		add_settings_section('swki_vods_settings', '[Layout] Vods Settings', false, 'swki_vods_fields');
		add_settings_field('swki_vods_clip_period', $tooltipArray['Clip Period'], array($this, 'swki_vods_clip_period_cb'), 'swki_vods_fields', 'swki_vods_settings');
		add_settings_field('swki_vods_column_count', $tooltipArray['Column Count'], array($this, 'swki_vods_column_count_cb'), 'swki_vods_fields', 'swki_vods_settings');			
        add_settings_field('swki_vods_column_spacing', $tooltipArray['Column Spacing'], array($this, 'swki_vods_column_spacing_cb'), 'swki_vods_fields', 'swki_vods_settings');	
	}

	public function swki_showAdmin() {
		include ('partials/streamweasels-kick-integration-admin-display.php');
	}	   

	public function swki_vods_clip_period_cb() {
		$clipPeriod = ( isset ( $this->options['swki_vods_clip_period'] ) ) ? $this->options['swki_vods_clip_period'] : '';
		?>
		
		<select id="sw-vods-clip-period" name="swki_options_vods[swki_vods_clip_period]">
			<option value="all" <?php echo selected( $clipPeriod, 'all', false ); ?>>All</option>
			<option value="day" <?php echo selected( $clipPeriod, 'day', false ); ?>>Day</option>
            <option value="week" <?php echo selected( $clipPeriod, 'week', false ); ?>>Week</option>
            <option value="month" <?php echo selected( $clipPeriod, 'month', false ); ?>>Month</option>
        </select>
		<p>Choose the Clip Period. This field only works when CLIPS is selected above.</p>
		<?php
	}	

	public function swki_vods_column_count_cb() {
		$columns = ( isset ( $this->options['swki_vods_column_count'] ) ) ? $this->options['swki_vods_column_count'] : '4';
		?>
		
		<input id="sw-tile-column-count" type="text" name="swki_options_vods[swki_vods_column_count]" value="<?php echo esc_html($columns); ?>">
		<span class="range-bar-value"></span>		
		<p>Choose the number of columns for your [Layout] Vods.</p>

		<?php
	}	

	public function swki_vods_column_spacing_cb() {
		$columnSpacing = ( isset ( $this->options['swki_vods_column_spacing'] ) ) ? $this->options['swki_vods_column_spacing'] : '5';
		?>
		
		<input id="sw-tile-column-spacing" type="text" name="swki_options_vods[swki_vods_column_spacing]" value="<?php echo esc_html($columnSpacing); ?>">
		<span class="range-bar-value"></span>	
		<p>Choose the space between columns for your [Layout] Vods.</p>


		<?php
	}

	public function swki_options_validate($input) {
		$new_input = [];
		$options = get_option('swki_options_vods');	

		if( isset( $input['swki_vods_clip_period'] ) ) {
			$new_input['swki_vods_clip_period'] = sanitize_text_field( $input['swki_vods_clip_period'] );
		}		

		if( isset( $input['swki_vods_column_count'] ) ) {
			$new_input['swki_vods_column_count'] = sanitize_text_field( $input['swki_vods_column_count'] );
		}
		
		if( isset( $input['swki_vods_column_spacing'] ) ) {
			$new_input['swki_vods_column_spacing'] = sanitize_text_field( $input['swki_vods_column_spacing'] );
		}		

		return $new_input;
	}
}