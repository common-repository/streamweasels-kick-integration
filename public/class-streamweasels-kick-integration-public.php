<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.streamweasels.com
 * @since      1.0.0
 *
 * @package    Streamweasels_Kick_Integration
 * @subpackage Streamweasels_Kick_Integration/public
 */
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Streamweasels_Kick_Integration
 * @subpackage Streamweasels_Kick_Integration/public
 * @author     StreamWeasels <admin@streamweasels.com>
 */
class Streamweasels_Kick_Integration_Public {
    private $plugin_name;

    private $version;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function streamWeasels_shortcode() {
        // Setup the streamweasels shortcode
        add_shortcode( 'streamweasels-kick', array($this, 'get_streamweasels_shortcode') );
        add_shortcode( 'sw-kick', array($this, 'get_streamweasels_shortcode') );
        add_shortcode( 'sw-kick-integration', array($this, 'get_streamweasels_shortcode') );
        add_shortcode( 'sw-kick-embed', array($this, 'get_streamweasels_shortcode_embed') );
    }

    public function get_streamweasels_shortcode_embed( $args ) {
        $autoplay = ( !empty( $args['autoplay'] ) && ($args['autoplay'] === 'true' || $args['autoplay'] === '1') ? 'true' : 'false' );
        $muted = ( !empty( $args['muted'] ) && ($args['muted'] === 'true' || $args['muted'] === '1') ? 'true' : 'false' );
        $width = ( !empty( $args['width'] ) ? esc_attr( sanitize_text_field( $args['width'] ) ) : '100%' );
        $height = ( !empty( $args['height'] ) ? esc_attr( sanitize_text_field( $args['height'] ) ) : '100%' );
        $channel = esc_attr( sanitize_text_field( $args['channel'] ?? '' ) );
        $output = '<iframe src="https://player.kick.com/' . $channel . '?autoplay=' . $autoplay . '&muted=' . $muted . '" style="width:' . $width . ';height:' . $height . ';aspect-ratio:16/9"></iframe>';
        return $output;
    }

    public function get_streamweasels_shortcode( $args ) {
        $uuid = rand( 1000, 9999 );
        // Set default arguments
        $options = get_option( 'swki_options' );
        $layout = sanitize_text_field( $args['layout'] ?? $options['swki_layout'] ?? '' );
        $channels = sanitize_text_field( isset( $args['channels'] ) ?? $args['channels'] ?? $args['channel'] ?? 'kick' );
        $channels = str_replace( ' ', '', $channels );
        $channels = str_replace( ',,', ',', $channels );
        if ( substr( $channels, -1 ) == ',' ) {
            $channels = substr( $channels, 0, -1 );
        }
        $this->streamweasels_content( $args, $uuid );
        ob_start();
        if ( $layout == 'wall' ) {
            include plugin_dir_path( __FILE__ ) . 'partials/streamweasels-kick-wall-public-display.php';
        } else {
            if ( $layout == 'status' ) {
                include plugin_dir_path( __FILE__ ) . 'partials/streamweasels-kick-status-public-display.php';
            } else {
                if ( $layout == 'vods' ) {
                    include plugin_dir_path( __FILE__ ) . 'partials/streamweasels-kick-vods-public-display.php';
                } else {
                    if ( $layout == 'feature' ) {
                        if ( ski_fs()->is_plan_or_trial( 'pro', true ) ) {
                            include 'partials/streamweasels-kick-feature-public-display.php';
                        } else {
                            echo '<p style="text-align:center">Kick Feature requires an active subscription to Kick Integration PRO!</p>';
                        }
                    } else {
                        include 'partials/streamweasels-kick-integration-public-display.php';
                    }
                }
            }
        }
        return ob_get_clean();
    }

    public function streamweasels_content( $args, $uuid ) {
        $options = get_option( 'swki_options' );
        $optionsFeature = get_option( 'swki_options_feature' );
        $optionsVods = get_option( 'swki_options_vods' );
        if ( empty( $args['channels'] ) && empty( $args['channel'] ) ) {
            $channels = sanitize_text_field( $options['swki_channels'] ?? '' );
        } else {
            $channels = sanitize_text_field( $args['channel'] ?? $args['channels'] ?? '' );
        }
        if ( substr( $channels, -1 ) == ',' ) {
            $channels = substr( $channels, 0, -1 );
        }
        $channels = str_replace( ' ', '', $channels );
        $channels = str_replace( ',,', ',', $channels );
        $layout = sanitize_text_field( $args['layout'] ?? $options['swki_layout'] ?? '' );
        $limit = sanitize_text_field( ( $args['limit'] ?? $options['swki_limit'] ?? '15' ?: '15' ) );
        $freeEmbed = 'page';
        $offlineAddonCheck = 0;
        if ( ski_fs()->can_use_premium_code() == false ) {
            if ( $limit > 15 ) {
                $limit = 15;
            }
        }
        if ( ski_fs()->is_plan_or_trial( 'essentials', true ) ) {
            if ( $limit > 50 ) {
                $limit = 50;
            }
        }
        if ( $optionsFeature && $layout == 'feature' ) {
            $embedPosition = ( isset( $optionsFeature['swki_feature_embed_position'] ) ? $optionsFeature['swki_feature_embed_position'] : 'inside' );
            $embedPosition = ( isset( $args['feature-embed-position'] ) ? $args['feature-embed-position'] : $embedPosition );
            if ( $embedPosition == 'inside' ) {
                $embed = 'inside';
            }
        }
        if ( $layout == 'status' ) {
            $freeEmbed = 'kick';
            $offlineAddonCheck = 1;
        }
        // For block themes, register a dummy script to allow inline scripts to be added
        if ( !wp_script_is( $this->plugin_name, 'registered' ) ) {
            wp_register_script( $this->plugin_name . '-blocks', '' );
            wp_enqueue_script( $this->plugin_name . '-blocks' );
            $inlineScriptHandle = $this->plugin_name . '-blocks';
        } else {
            $inlineScriptHandle = $this->plugin_name;
        }
        wp_add_inline_script( $inlineScriptHandle, 'const streamWeaselsKickVars' . $uuid . ' = ' . json_encode( array(
            'channels'           => $channels,
            'limit'              => $limit,
            'layout'             => $layout,
            'embed'              => $freeEmbed,
            'embedTitle'         => 0,
            'embedTitlePosition' => '',
            'embedMuted'         => 0,
            'showOffline'        => $offlineAddonCheck,
            'showOfflineText'    => 'No Streams Online!',
            'showOfflineImage'   => '',
            'autoplay'           => 0,
            'autoplaySelect'     => '',
            'featured'           => '',
            'title'              => '',
            'subtitle'           => '',
            'offlineImage'       => '',
            'logoImage'          => '',
            'profileImage'       => 0,
            'logoBgColour'       => '',
            'logoBorderColour'   => '',
            'maxWidth'           => '1440',
            'tileLayout'         => 'detailed',
            'tileSorting'        => 'most',
            'tileLive'           => 'viewer',
            'tileBgColour'       => '',
            'tileTitleColour'    => '',
            'tileSubtitleColour' => '',
            'tileRoundedCorners' => '',
            'hoverColour'        => '',
        ) ) . ';', 'before' );
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Streamweasels_Kick_Integration_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Streamweasels_Kick_Integration_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'dist/streamweasels-public.min.css',
            array(),
            $this->version,
            'all'
        );
        // The following options are used as CSS variables on the page
        $options = get_option( 'swki_options' );
        $colourTheme = sanitize_text_field( ( $options['swki_colour_theme'] ?? 'light' ?: 'light' ) );
        if ( $colourTheme == 'light' ) {
            $tileBgColourDefault = '#F7F7F8';
            $tileTitleColourDefault = '#1F1F23';
            $tileSubtitleColourDefault = '#53535F';
        } else {
            $tileBgColourDefault = '#0E0E10';
            $tileTitleColourDefault = '#DEDEE3';
            $tileSubtitleColourDefault = '#adb8a8';
        }
        $optionsWall = get_option( 'swki_options_wall' );
        $tileColumnCount = sanitize_text_field( ( $optionsWall['swki_wall_column_count'] ?? '4' ?: '4' ) );
        $tileColumnSpacing = sanitize_text_field( ( $optionsWall['swki_wall_column_spacing'] ?? '10' ?: '10' ) );
        $optionsVods = get_option( 'swki_options_vods' );
        $tileVodsColumnCount = sanitize_text_field( ( $optionsVods['swki_vods_column_count'] ?? '4' ?: '4' ) );
        $tileVodsColumnSpacing = sanitize_text_field( ( $optionsVods['swki_vods_column_spacing'] ?? '10' ?: '10' ) );
        $optionsFeature = get_option( 'swki_options_feature' );
        $controlsBgColour = sanitize_text_field( ( $optionsFeature['swki_feature_controls_bg_colour'] ?? '#000' ?: '#000' ) );
        $controlsArrowColour = sanitize_text_field( ( $optionsFeature['swki_feature_controls_arrow_colour'] ?? '#fff' ?: '#fff' ) );
        $optionsStatus = get_option( 'swki_options_status' );
        $statusVerticalDistance = sanitize_text_field( ( $optionsStatus['swki_status_vertical_distance'] ?? '25' ?: '25' ) );
        $statusHorizontalDistance = sanitize_text_field( ( $optionsStatus['swki_status_horizontal_distance'] ?? '25' ?: '25' ) );
        $statusLogoBackgroundColour = sanitize_text_field( ( $optionsStatus['swki_status_logo_background_colour'] ?? '#6441A4' ?: '#6441A4' ) );
        $statusLogoAccentColour = sanitize_text_field( ( $optionsStatus['swki_status_accent_colour'] ?? '#6441A4' ?: '#6441A4' ) );
        $statusCarouselBackgroundColour = sanitize_text_field( ( $optionsStatus['swki_status_carousel_background_colour'] ?? '#fff' ?: '#fff' ) );
        $statusCarouselArrowColour = sanitize_text_field( ( $optionsStatus['swki_status_carousel_arrow_colour'] ?? '#000' ?: '#000' ) );
        $logoBgColour = 'transparent';
        $logoBorderColour = 'transparent';
        $maxWidth = 'none';
        $tileBgColour = $tileBgColourDefault;
        $tileTitleColour = $tileTitleColourDefault;
        $tileSubtitleColour = $tileSubtitleColourDefault;
        $tileRoundedCorners = '0';
        $hoverColour = 'transparent';
        $streamWeaselsCssVars = '
			:root {
				--kick-logo-bg-colour: ' . $logoBgColour . ';
				--kick-logo-border-colour: ' . $logoBorderColour . ';
				--kick-max-width: ' . $maxWidth . ';
				--kick-hover-colour: ' . $hoverColour . ';    
				--kick-tile-bg-colour: ' . $tileBgColour . ';
				--kick-tile-title-colour: ' . $tileTitleColour . ';
				--kick-tile-subtitle-colour: ' . $tileSubtitleColour . ';
				--kick-tile-rounded-corners: ' . $tileRoundedCorners . ';
				--kick-tile-column-count: ' . $tileColumnCount . ';
				--kick-tile-column-spacing: ' . $tileColumnSpacing . ';
				--kick-tile-vods-column-count: ' . $tileVodsColumnCount . ';
				--kick-tile-vods-column-spacing: ' . $tileVodsColumnSpacing . ';				
				--kick-status-vertical-distance: ' . $statusVerticalDistance . ';
				--kick-status-horizontal-distance: ' . $statusHorizontalDistance . ';
				--kick-status-logo-accent-colour: ' . $statusLogoAccentColour . ';
				--kick-status-logo-background-colour: ' . $statusLogoBackgroundColour . ';
				--kick-status-carousel-background-colour: ' . $statusCarouselBackgroundColour . ';
				--kick-status-carousel-arrow-colour: ' . $statusCarouselArrowColour . ';				   			
			}
		';
        wp_add_inline_style( $this->plugin_name, $streamWeaselsCssVars );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Streamweasels_Kick_Integration_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Streamweasels_Kick_Integration_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'dist/streamweasels-public.min.js',
            array('jquery'),
            $this->version,
            true
        );
        $options = get_option( 'swki_options' );
        wp_add_inline_script( $this->plugin_name, 'const streamWeaselsKickVars = ' . json_encode( array(
            'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
            'kickOffline' => plugin_dir_url( __FILE__ ) . 'img/sw-blank.png',
            'kickIcon'    => plugin_dir_url( __FILE__ ) . 'img/kick-logo.png',
        ) ), 'before' );
    }

    public function swki_status_show_global() {
        $options = get_option( 'swki_options' );
        $optionsStatus = get_option( 'swki_options_status' );
        $channels = sanitize_text_field( ( isset( $options['swki_channels'] ) ? $options['swki_channels'] : '' ) );
        $showGlobal = sanitize_text_field( ( isset( $optionsStatus['swki_status_show_global'] ) && !empty( $optionsStatus['swki_status_show_global'] ) ? $optionsStatus['swki_status_show_global'] : '0' ) );
        if ( $showGlobal ) {
            echo do_shortcode( '[sw-kick layout="status" channels="' . $channels . '" status-placement="absolute"]' );
        }
    }

}
