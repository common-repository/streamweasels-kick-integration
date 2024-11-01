<?php

class Streamweasels_Kick_Integration_Wall_Admin extends Streamweasels_Kick_Integration_Admin {

    /**
     * The admin-specific functionality of the plugin.
     *
     * @link       https://www.streamweasels.com
     * @since      1.6.0
     *
     * @package    Streamweasels_Wall_Pro
     * @subpackage Streamweasels_Wall_Pro/admin
     */

	 private $options;
	 private $plugin_name;
	 private $version;

     public function __construct( $plugin_name, $version ) {

		$this->options = get_option( 'swki_options_wall', array() );
		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}     


    public function display_admin_page_wall() {

        add_submenu_page(
            'streamweasels-kick',
            '[Layout] Wall',
            '[Layout] Wall',
            'manage_options',
            'streamweasels-kick-wall',
            array($this, 'swki_showAdmin')
        );		

        $tooltipArray = array(
            'Column Count'=>'Column Count <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="wall-column-count=\'\'"></span>',
            'Column Spacing'=>'Column Spacing <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="wall-column-spacing=\'\'"></span>',
        );		

        register_setting( 'swki_options_wall', 'swki_options_wall', array($this, 'swki_options_validate'));	
        add_settings_section('swki_wall_settings', '[Layout] Wall Settings', false, 'swki_wall_fields');
        // Shortcode Settings section
        add_settings_section('swki_wall_shortcode_settings', 'Shortcode', false, 'swki_wall_shortcode_fields');		
        add_settings_field('swki_wall_column_count', $tooltipArray['Column Count'], array($this, 'swki_wall_column_count_cb'), 'swki_wall_fields', 'swki_wall_settings');			
        add_settings_field('swki_wall_column_spacing', $tooltipArray['Column Spacing'], array($this, 'swki_wall_column_spacing_cb'), 'swki_wall_fields', 'swki_wall_settings');
    } 

	public function swki_showAdmin() {
		include ('partials/streamweasels-kick-integration-admin-display.php');
	}	    

	public function swki_wall_column_count_cb() {
		$columns = ( isset ( $this->options['swki_wall_column_count'] ) ) ? $this->options['swki_wall_column_count'] : '4';
		?>
		
		<input id="sw-tile-column-count" type="text" name="swki_options_wall[swki_wall_column_count]" value="<?php echo esc_html($columns); ?>">
		<span class="range-bar-value"></span>		
		<p>Choose the number of columns for your [Layout] Wall.</p>

		<?php
	}	

	public function swki_wall_column_spacing_cb() {
		$columnSpacing = ( isset ( $this->options['swki_wall_column_spacing'] ) ) ? $this->options['swki_wall_column_spacing'] : '5';
		?>
		
		<input id="sw-tile-column-spacing" type="text" name="swki_options_wall[swki_wall_column_spacing]" value="<?php echo esc_html($columnSpacing); ?>">
		<span class="range-bar-value"></span>	
		<p>Choose the space between columns for your [Layout] Wall.</p>


		<?php
	}

	public function swki_options_validate($input) {
		$new_input = [];
		$options = get_option('swki_options_wall');
		if( isset( $input['swki_wall_stream_position'] ) ) {
			$new_input['swki_wall_stream_position'] = sanitize_text_field( $input['swki_wall_stream_position'] );
		}	

		if( isset( $input['swki_wall_column_count'] ) ) {
			$new_input['swki_wall_column_count'] = sanitize_text_field( $input['swki_wall_column_count'] );
		}
		
		if( isset( $input['swki_wall_column_spacing'] ) ) {
			$new_input['swki_wall_column_spacing'] = sanitize_text_field( $input['swki_wall_column_spacing'] );
		}
		return $new_input;
	}	     

}