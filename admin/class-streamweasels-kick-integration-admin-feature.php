<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.streamweasels.com
 * @since      1.0.0
 *
 * @package    Streamweasels_Feature_Pro
 * @subpackage Streamweasels_Feature_Pro/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Streamweasels_Feature_Pro
 * @subpackage Streamweasels_Feature_Pro/admin
 * @author     StreamWeasels <admin@streamweasels.com>
 */
class Streamweasels_Kick_Integration_Feature_Admin extends Streamweasels_Kick_Integration_Admin {

	private $plugin_name;
	private $version;
	private $options;
	private $baseOptions;
	private $base;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->baseOptions = get_option( 'swki_options', array() );
		$this->options = get_option( 'swki_options_feature', array() );
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
	public function display_admin_page_feature() {

		add_submenu_page(
			'streamweasels-kick',
			'[Layout] Feature',
			'[Layout] Feature',
			'manage_options',
			'streamweasels-kick-feature',
			array($this, 'swki_showAdmin')
		);		

		$tooltipArray = array(
			'Embed Position'=>'Embed Position <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="feature-embed-position=\'\'"></span>',
			'Controls Background Colour'=>'Controls Background Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="feature-controls-bg-colour=\'\'"></span>',
			'Controls Arrow Colour'=>'Controls Arrow Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="feature-controls-arrow-colour=\'\'"></span>',
			'Skew'=>'Skew <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="feature-skew=\'\'"></span>',
		);		

		register_setting( 'swki_options_feature', 'swki_options_feature', array($this, 'swki_options_validate'));	
		add_settings_section('swki_feature_shortcode_settings', 'Shortcode', false, 'swki_feature_shortcode_fields');		
		add_settings_section('swki_feature_settings', '[Layout] Feature Settings', false, 'swki_feature_fields');
		add_settings_field('swki_feature_embed_position', $tooltipArray['Embed Position'], array($this, 'streamweasels_feature_embed_position_cb'), 'swki_feature_fields', 'swki_feature_settings');	
		add_settings_field('swki_feature_controls_bg_colour', $tooltipArray['Controls Background Colour'], array($this, 'swki_feature_controls_bg_colour_cb'), 'swki_feature_fields', 'swki_feature_settings');	
		add_settings_field('swki_feature_controls_arrow_colour', $tooltipArray['Controls Arrow Colour'], array($this, 'swki_feature_controls_arrow_colour_cb'), 'swki_feature_fields', 'swki_feature_settings');	
		add_settings_field('swki_feature_skew', $tooltipArray['Skew'], array($this, 'swki_feature_skew_cb'), 'swki_feature_fields', 'swki_feature_settings');	
	}

	public function swki_showAdmin() {
		include ('partials/streamweasels-kick-integration-admin-display.php');
	}	   

	public function streamweasels_feature_embed_position_cb() {
		$position = ( isset ( $this->options['swki_feature_embed_position'] ) ) ? $this->options['swki_feature_embed_position'] : '';
		?>
		
		<select id="sw-player-stream-position" name="swki_options_feature[swki_feature_embed_position]">
			<option value="inside" <?php echo selected( $position, 'inside', false ); ?>>Inside</option>
            <option value="above" <?php echo selected( $position, 'above', false ); ?>>Above</option>
            <option value="below" <?php echo selected( $position, 'below', false ); ?>>Below</option>
        </select>
		<p>Choose the position of the embed in your [Layout] Feature.</p>

		<?php
	}

	public function swki_feature_controls_bg_colour_cb() {
		$controlsBgColour = ( isset ( $this->options['swki_feature_controls_bg_colour'] ) ) ? $this->options['swki_feature_controls_bg_colour'] : '';
		?>
		
		<input type="text" id="sw-feature-controls-bg-colour" name="swki_options_feature[swki_feature_controls_bg_colour]" size='40' value="<?php echo $controlsBgColour; ?>" />

		<p>Choose the controls colour of the [Layout] Feature.</p>

		<?php
	}

	public function swki_feature_controls_arrow_colour_cb() {
		$controlsArrowColour = ( isset ( $this->options['swki_feature_controls_arrow_colour'] ) ) ? $this->options['swki_feature_controls_arrow_colour'] : '';
		?>
		
		<input type="text" id="sw-feature-controls-arrow-colour" name="swki_options_feature[swki_feature_controls_arrow_colour]" size='40' value="<?php echo $controlsArrowColour; ?>" />

		<p>Choose the arrow colour of the [Layout] Feature.</p>

		<?php
	}

	public function swki_feature_skew_cb() {
		$skew = ( isset ( $this->options['swki_feature_skew'] ) ) ? $this->options['swki_feature_skew'] : '';
		?>
		
		<input type="hidden" name="swki_options_feature[swki_feature_skew]" value="0"/>
		<input type="checkbox" id="sw-feature" name="swki_options_feature[swki_feature_skew]" value="1" <?php checked( 1, $skew, true ); ?>/>
		<p>Choose to add the 3D skew effect to the Feature layout.</p>
		<?php
	}	

	public function swki_options_validate($input) {
		$new_input = [];
		$options = get_option('swki_options_feature');

		if( isset( $input['swki_feature_embed_position'] ) ) {
			$new_input['swki_feature_embed_position'] = sanitize_text_field( $input['swki_feature_embed_position'] );
		}	
		if( isset( $input['swki_feature_controls_bg_colour'] ) ) {
			$new_input['swki_feature_controls_bg_colour'] = sanitize_text_field( $input['swki_feature_controls_bg_colour'] );
		}	
		if( isset( $input['swki_feature_controls_arrow_colour'] ) ) {
			$new_input['swki_feature_controls_arrow_colour'] = sanitize_text_field( $input['swki_feature_controls_arrow_colour'] );
		}
		if( isset( $input['swki_feature_skew'] ) ) {
			$new_input['swki_feature_skew'] = (int) $input['swki_feature_skew'];
		}		

		return $new_input;
	}


	public function swki_twitch_layout_options_pro( $options ) {
        $options['feature'] = 'Feature';
        return $options;
	}
}