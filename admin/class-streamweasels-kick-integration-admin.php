<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.streamweasels.com
 * @since      1.0.0
 *
 * @package    Streamweasels_Kick_Integration
 * @subpackage Streamweasels_Kick_Integration/admin
 */
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Streamweasels_Kick_Integration
 * @subpackage Streamweasels_Kick_Integration/admin
 * @author     StreamWeasels <admin@streamweasels.com>
 */
class Streamweasels_Kick_Integration_Admin {
    private $plugin_name;

    private $version;

    private $options;

    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->options = get_option( 'swki_options' );
    }

    public function add_rest_endpoints() {
        register_rest_route( 'streamweasels-kick/v1', '/data/', array(
            'methods'             => 'GET',
            'callback'            => array($this, 'sw_rest_endpoints'),
            'permission_callback' => '__return_true',
        ) );
    }

    public function sw_rest_endpoints( $data ) {
        $weaselsData = array();
        $weaselsData['proStatus'] = ski_fs()->can_use_premium_code();
        if ( empty( $weaselsData ) ) {
            return new WP_Error('no_streamweasels_data', 'StreamWeasels Data Missing', array(
                'status' => 404,
            ));
        }
        return new WP_REST_Response($weaselsData, 200);
    }

    public function swki_custom_block_category( $categories, $post ) {
        $existingCategorySlugs = wp_list_pluck( $categories, 'slug' );
        $desiredCategorySlug = 'streamweasels';
        if ( !in_array( $desiredCategorySlug, $existingCategorySlugs ) ) {
            array_unshift( $categories, array(
                'slug'  => $desiredCategorySlug,
                'title' => 'StreamWeasels',
            ) );
        }
        return $categories;
    }

    public function enqueue_blocks() {
        $blocks_json_path = plugin_dir_path( dirname( __FILE__ ) ) . 'build/';
        register_block_type( $blocks_json_path . 'kick-integration/block.json', array(
            'render_callback' => array($this, 'swki_streamweasels_kick_integration_render_cb'),
        ) );
        register_block_type( $blocks_json_path . 'kick-embed/block.json', array(
            'render_callback' => array($this, 'swki_streamweasels_kick_embed_render_cb'),
        ) );
    }

    public function swki_streamweasels_kick_integration_render_cb( $attr ) {
        $output = do_shortcode( '[sw-kick-integration layout="' . $attr['layout'] . '" channels="' . $attr['channels'] . '" limit="' . $attr['limit'] . '" colour="' . $attr['colour'] . '"]' );
        return $output;
    }

    public function swki_streamweasels_kick_embed_render_cb( $attr ) {
        $attr['autoplay'] = ( isset( $attr['autoplay'] ) && !empty( $attr['autoplay'] ) ? $attr['autoplay'] : 'false' );
        $attr['muted'] = ( isset( $attr['muted'] ) && !empty( $attr['muted'] ) ? $attr['muted'] : 'false' );
        $attr['width'] = ( isset( $attr['width'] ) && !empty( $attr['width'] ) ? $attr['width'] : '100%' );
        $attr['height'] = ( isset( $attr['height'] ) && !empty( $attr['height'] ) ? $attr['height'] : '100%' );
        if ( substr( $attr['width'], -2 ) !== 'px' && substr( $attr['width'], -1 ) !== '%' ) {
            $attr['width'] .= 'px';
        }
        if ( substr( $attr['height'], -2 ) !== 'px' && substr( $attr['height'], -1 ) !== '%' ) {
            $attr['height'] .= 'px';
        }
        $output = '<div ' . get_block_wrapper_attributes() . '>';
        $output .= do_shortcode( '[sw-kick-embed channel="' . $attr['channel'] . '" autoplay="' . $attr['autoplay'] . '" muted="' . $attr['muted'] . '" width="' . $attr['width'] . '" height="' . $attr['height'] . '"]' );
        $output .= '</div>';
        return $output;
    }

    /**
     * Register the stylesheets for the admin area.
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
            plugin_dir_url( __FILE__ ) . 'dist/streamweasels-admin.min.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style(
            $this->plugin_name . '-powerange',
            plugin_dir_url( __FILE__ ) . 'dist/powerange.min.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style(
            $this->plugin_name . '-addons',
            plugin_dir_url( __FILE__ ) . '../freemius/assets/css/admin/add-ons.css',
            array(),
            $this->version,
            'all'
        );
        wp_enqueue_style( 'wp-color-picker' );
    }

    /**
     * Register the JavaScript for the admin area.
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
            plugin_dir_url( __FILE__ ) . 'dist/streamweasels-admin.min.js',
            array('jquery', 'wp-color-picker'),
            $this->version,
            false
        );
        wp_enqueue_script(
            $this->plugin_name . '-powerange',
            plugin_dir_url( __FILE__ ) . 'dist/powerange.min.js',
            array('jquery', 'wp-color-picker'),
            $this->version,
            false
        );
        wp_enqueue_media();
    }

    public function display_admin_upsell() {
        $display_status = get_transient( 'swki_notice_closed5' );
        $display_status2 = ( isset( $this->options['swki_dismiss_for_good5'] ) ? $this->options['swki_dismiss_for_good5'] : '' );
        if ( !$display_status ) {
            if ( !$display_status2 ) {
                echo '<div class="notice is-dismissible swki-notice">';
                echo '<div class="swki-notice__content">';
                echo '<h2>Introducing StreamWeasels Status Bar!</h2>';
                echo '<img src="' . plugin_dir_url( __FILE__ ) . '../admin/img/status-bar-example.png">';
                echo '<p>Add a sticky, customisable Status Bar to the top of your website and let your users know when you\'re live on Twitch, Kikc or YouTube!</p>';
                echo '<p>Check out <strong>StreamWeasels Status Bar</strong> for WordPress - <a href="/wp-admin/plugin-install.php?s=streamweasels status bar&tab=search&type=term" target="_blank"><strong>Download for free now</strong></a>.</p>';
                echo '<p><a class="dismiss-for-good" href="#">Don\'t show this again!</a></p>';
                echo '</div>';
                echo '</div>';
            }
        }
    }

    public function swki_admin_notice_dismiss() {
        set_transient( 'swki_notice_closed5', true, 604800 );
        wp_die();
    }

    public function swki_admin_notice_dismiss_for_good() {
        $swki_options = get_option( 'swki_options' );
        $swki_options['swki_dismiss_for_good5'] = true;
        update_option( 'swki_options', $swki_options );
        wp_die();
    }

    public function display_admin_page() {
        add_menu_page(
            'StreamWeasels',
            'Kick Integration',
            'manage_options',
            'streamweasels-kick',
            array($this, 'swki_showAdmin'),
            "data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyINCiB3aWR0aD0iMTUwLjAwMDAwMHB0IiBoZWlnaHQ9IjE1MC4wMDAwMDBwdCIgdmlld0JveD0iMCAwIDE1MC4wMDAwMDAgMTUwLjAwMDAwMCINCiBwcmVzZXJ2ZUFzcGVjdFJhdGlvPSJ4TWlkWU1pZCBtZWV0Ij4NCg0KPGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMC4wMDAwMDAsMTUwLjAwMDAwMCkgc2NhbGUoMC4xMDAwMDAsLTAuMTAwMDAwKSINCmZpbGw9IiNhN2FhYWQiIHN0cm9rZT0ibm9uZSI+DQo8cGF0aCBkPSJNMjcwIDc1MCBsMCAtNTQwIDE4MCAwIDE4MCAwIDAgMTIwIDAgMTIwIDYwIDAgNjAgMCAwIC02MCAwIC02MCA2MA0KMCA2MCAwIDAgLTYwIDAgLTYwIDE4MCAwIDE4MCAwIDAgMTgwIDAgMTgwIC02MCAwIC02MCAwIDAgNjAgMCA2MCAtNjAgMCAtNjANCjAgMCA2MCAwIDYwIDYwIDAgNjAgMCAwIDYwIDAgNjAgNjAgMCA2MCAwIDAgMTgwIDAgMTgwIC0xODAgMCAtMTgwIDAgMCAtNjAgMA0KLTYwIC02MCAwIC02MCAwIDAgLTYwIDAgLTYwIC02MCAwIC02MCAwIDAgMTIwIDAgMTIwIC0xODAgMCAtMTgwIDAgMCAtNTQweiIvPg0KPC9nPg0KPC9zdmc+"
        );
        $tooltipArray = array(
            'Game'                   => 'Game <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="game=\'\'"></span>',
            'Language'               => 'Language <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="language=\'\'"></span>',
            'Channels'               => 'Channels <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="channels=\'\'"></span>',
            'Team'                   => 'Team <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="team=\'\'"></span>',
            'Title Filter'           => 'Title Filter <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="title-filter=\'\'"></span>',
            'Limit'                  => 'Limit <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="limit=\'\'"></span>',
            'Colour Theme'           => 'Colour Theme <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="colour-theme=\'\'"></span>',
            'Layout'                 => 'Layout <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="layout=\'\'"></span>',
            'Embed'                  => 'Embed <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="embed=\'\'"></span>',
            'Embed Colour Scheme'    => 'Embed Colour Scheme <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="embed-theme=\'\'"></span>',
            'Display Chat'           => 'Display Chat <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="embed-chat=\'\'"></span>',
            'Display Title'          => 'Display Title <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="embed-title=\'\'"></span>',
            'Title Position'         => 'Title Position <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="title-position=\'\'"></span>',
            'Start Muted'            => 'Start Muted <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="embed-muted=\'\'"></span>',
            'Show Offline Streams'   => 'Show Offline Streams <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="show-offline=\'\'"></span>',
            'Offline Message'        => 'Offline Message <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="show-offline-text=\'\'"></span>',
            'Show Offline Image'     => 'Offline Image <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="show-offline-image=\'\'"></span>',
            'Autoplay Stream'        => 'Autoplay Stream <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="autoplay=\'\'"></span>',
            'Autoplay Select'        => 'Autoplay Select <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="autoplay-select=\'\'"></span>',
            'Featured Streamer'      => 'Featured Streamer <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="featured-stream=\'\'"></span>',
            'Title'                  => 'Title <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="title=\'\'"></span>',
            'Subtitle'               => 'Subtitle <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="subtitle=\'\'"></span>',
            'Offline Image'          => 'Offline Image <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="offline-image=\'\'"></span>',
            'Logo'                   => 'Custom Logo <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="logo-image=\'\'"></span>',
            'Profile'                => 'Hide Profile Image <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="profile-image=\'\'"></span>',
            'Logo Background Colour' => 'Logo Background Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="logo-bg-colour=\'\'"></span>',
            'Logo Border Colour'     => 'Logo Border Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="logo-border-colour=\'\'"></span>',
            'Max Width'              => 'Max Width <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="max-width=\'\'"></span>',
            'Tile Layout'            => 'Tile Layout <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="tile-layout=\'\'"></span>',
            'Tile Sorting'           => 'Tile Sorting <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="tile-sorting=\'\'"></span>',
            'Tile Live'              => 'Live Info <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="live-info=\'\'"></span>',
            'Background Colour'      => 'Background Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="tile-bg-colour=\'\'"></span>',
            'Title Colour'           => 'Title Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="tile-title-colour=\'\'"></span>',
            'Subtitle Colour'        => 'Subtitle Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="tile-subtitle-colour=\'\'"></span>',
            'Rounded Corners'        => 'Rounded Corners <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="tile-rounded-corners=\'\'"></span>',
            'Hover Effect'           => 'Hover Effect <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="hover-effect=\'\'"></span>',
            'Hover Colour'           => 'Hover Colour <span class="sw-shortcode-help tooltipped tooltipped-n" aria-label="hover-colour=\'\'"></span>',
        );
        // register settings
        register_setting( 'swki_options', 'swki_options', array($this, 'swki_options_validate') );
        // translation settings
        register_setting( 'swki_translations', 'swki_translations', array($this, 'swki_translations_validate') );
        // License Settings section
        add_settings_section(
            'swki_license_settings',
            'License Key',
            false,
            'swki_license_fields'
        );
        // API Settings section
        add_settings_section(
            'swki_api_settings',
            'Kick API Settings',
            false,
            'swki_api_fields'
        );
        // Shortcode Settings section
        add_settings_section(
            'swki_shortcode_settings',
            'Shortcode',
            false,
            'swki_shortcode_fields'
        );
        // Shortcode Settings section
        add_settings_section(
            'swki_translations_settings',
            'Translations',
            false,
            'swki_translations_fields'
        );
        // Main Settings section
        add_settings_section(
            'swki_main_settings',
            'Main Settings',
            false,
            'swki_main_fields'
        );
        // Main Settings section
        add_settings_section(
            'swki_layout_settings',
            'Layout Settings',
            false,
            'swki_layout_fields'
        );
        // Embed Settings section
        add_settings_section(
            'swki_embed_settings',
            'Embed Settings',
            false,
            'swki_embed_fields'
        );
        // Offline Settings section
        add_settings_section(
            'swki_offline_settings',
            'Offline Settings',
            false,
            'swki_offline_fields'
        );
        // Autoplay Settings section
        add_settings_section(
            'swki_autoplay_settings',
            'Autoplay Settings',
            false,
            'swki_autoplay_fields'
        );
        // Appearance Settings section
        add_settings_section(
            'swki_appearance_settings',
            'Appearance Settings',
            false,
            'swki_appearance_fields'
        );
        // Tile Settings section
        add_settings_section(
            'swki_tile_settings',
            'Tile Settings',
            false,
            'swki_tile_fields'
        );
        // Hover Settings section
        add_settings_section(
            'swki_hover_settings',
            'Hover Settings',
            false,
            'swki_hover_fields'
        );
        // Debug Settings section
        add_settings_section(
            'swki_debug_settings',
            'Debug Settings',
            false,
            'swki_debug_fields'
        );
        // License Key Fields
        // Kick API Fields
        add_settings_field(
            'swki_api_connection_status',
            'Connection Status',
            array($this, 'swki_api_connection_status_cb'),
            'swki_api_fields',
            'swki_api_settings'
        );
        add_settings_field(
            'swki_client_token',
            'Auth Token',
            array($this, 'swki_client_token_cb'),
            'swki_api_fields',
            'swki_api_settings'
        );
        add_settings_field(
            'swki_client_id',
            'Client ID',
            array($this, 'swki_client_id_cb'),
            'swki_api_fields',
            'swki_api_settings'
        );
        add_settings_field(
            'swki_client_secret',
            'Client Secret',
            array($this, 'swki_client_secret_cb'),
            'swki_api_fields',
            'swki_api_settings'
        );
        // Shortcode Fields
        add_settings_field(
            'swki_shortcode',
            'Shortcode',
            array($this, 'swki_shortcode_cb'),
            'swki_shortcode_fields',
            'swki_shortcode_settings'
        );
        // Translation Fields
        add_settings_field(
            'swki_translations_live',
            'Live',
            array($this, 'swki_translations_live_cb'),
            'swki_translations_fields',
            'swki_translations_settings'
        );
        add_settings_field(
            'swki_translations_offline',
            'Offline',
            array($this, 'swki_translations_offline_cb'),
            'swki_translations_fields',
            'swki_translations_settings'
        );
        add_settings_field(
            'swki_translations_streaming',
            'Streaming',
            array($this, 'swki_translations_streaming_cb'),
            'swki_translations_fields',
            'swki_translations_settings'
        );
        add_settings_field(
            'swki_translations_for',
            'For',
            array($this, 'swki_translations_for_cb'),
            'swki_translations_fields',
            'swki_translations_settings'
        );
        add_settings_field(
            'swki_translations_viewers',
            'Viewers',
            array($this, 'swki_translations_viewers_cb'),
            'swki_translations_fields',
            'swki_translations_settings'
        );
        // Main Settings
        // add_settings_field('swki_game', $tooltipArray['Game'], array($this, 'swki_game_cb'), 'swki_main_fields', 'swki_main_settings');
        // add_settings_field('swki_langauge', $tooltipArray['Language'], array($this, 'swki_language_cb'), 'swki_main_fields', 'swki_main_settings');
        add_settings_field(
            'swki_channels',
            $tooltipArray['Channels'],
            array($this, 'swki_channels_cb'),
            'swki_main_fields',
            'swki_main_settings'
        );
        // add_settings_field('swki_title_filter', $tooltipArray['Title Filter'], array($this, 'swki_title_filter_cb'), 'swki_main_fields', 'swki_main_settings');
        add_settings_field(
            'swki_limit',
            $tooltipArray['Limit'],
            array($this, 'swki_limit_cb'),
            'swki_main_fields',
            'swki_main_settings'
        );
        if ( !ski_fs()->is__premium_only() || ski_fs()->is_free_plan() ) {
            add_settings_field(
                'swki_colour_theme',
                $tooltipArray['Colour Theme'],
                array($this, 'swki_colour_theme_cb'),
                'swki_main_fields',
                'swki_main_settings'
            );
        }
        // Layout Settings
        add_settings_field(
            'swki_layout',
            $tooltipArray['Layout'],
            array($this, 'swki_layout_cb'),
            'swki_layout_fields',
            'swki_layout_settings'
        );
        // Embed Settings
        add_settings_field(
            'swki_embed',
            $tooltipArray['Embed'],
            array($this, 'swki_embed_cb'),
            'swki_embed_fields',
            'swki_embed_settings'
        );
        // add_settings_field('swki_embed_theme', $tooltipArray['Embed Colour Scheme'], array($this, 'swki_embed_theme_cb'), 'swki_embed_fields', 'swki_embed_settings');
        // add_settings_field('swki_embed_chat', $tooltipArray['Display Chat'], array($this, 'swki_embed_chat_cb'), 'swki_embed_fields', 'swki_embed_settings');
        add_settings_field(
            'swki_embed_title',
            $tooltipArray['Display Title'],
            array($this, 'swki_embed_title_cb'),
            'swki_embed_fields',
            'swki_embed_settings'
        );
        add_settings_field(
            'swki_embed_title_position',
            $tooltipArray['Title Position'],
            array($this, 'swki_embed_title_position_cb'),
            'swki_embed_fields',
            'swki_embed_settings'
        );
        add_settings_field(
            'swki_embed_muted',
            $tooltipArray['Start Muted'],
            array($this, 'swki_embed_muted_cb'),
            'swki_embed_fields',
            'swki_embed_settings'
        );
        // Extra Settings
        add_settings_field(
            'swki_show_offline',
            $tooltipArray['Show Offline Streams'],
            array($this, 'swki_show_offline_cb'),
            'swki_offline_fields',
            'swki_offline_settings'
        );
        add_settings_field(
            'swki_show_offline_text',
            $tooltipArray['Offline Message'],
            array($this, 'swki_show_offline_text_cb'),
            'swki_offline_fields',
            'swki_offline_settings'
        );
        add_settings_field(
            'swki_show_offline_image',
            $tooltipArray['Show Offline Image'],
            array($this, 'swki_show_offline_image_cb'),
            'swki_offline_fields',
            'swki_offline_settings'
        );
        // Offline Settings
        add_settings_field(
            'swki_autoplay',
            $tooltipArray['Autoplay Stream'],
            array($this, 'swki_autoplay_cb'),
            'swki_autoplay_fields',
            'swki_autoplay_settings'
        );
        add_settings_field(
            'swki_autoplay_select',
            $tooltipArray['Autoplay Select'],
            array($this, 'swki_autoplay_select_cb'),
            'swki_autoplay_fields',
            'swki_autoplay_settings'
        );
        add_settings_field(
            'swki_featured_stream',
            $tooltipArray['Featured Streamer'],
            array($this, 'swki_featured_stream_cb'),
            'swki_autoplay_fields',
            'swki_autoplay_settings'
        );
        // Appearance Settings
        add_settings_field(
            'swki_title',
            $tooltipArray['Title'],
            array($this, 'swki_title_cb'),
            'swki_appearance_fields',
            'swki_appearance_settings'
        );
        add_settings_field(
            'swki_subtitle',
            $tooltipArray['Subtitle'],
            array($this, 'swki_subtitle_cb'),
            'swki_appearance_fields',
            'swki_appearance_settings'
        );
        add_settings_field(
            'swki_offline_image',
            $tooltipArray['Offline Image'],
            array($this, 'swki_offline_image_cb'),
            'swki_appearance_fields',
            'swki_appearance_settings'
        );
        add_settings_field(
            'swki_logo_image',
            $tooltipArray['Logo'],
            array($this, 'swki_logo_image_cb'),
            'swki_appearance_fields',
            'swki_appearance_settings'
        );
        add_settings_field(
            'swki_profile_image',
            $tooltipArray['Profile'],
            array($this, 'swki_profile_image_cb'),
            'swki_appearance_fields',
            'swki_appearance_settings'
        );
        add_settings_field(
            'swki_logo_bg_colour',
            $tooltipArray['Logo Background Colour'],
            array($this, 'swki_logo_bg_colour_cb'),
            'swki_appearance_fields',
            'swki_appearance_settings'
        );
        add_settings_field(
            'swki_logo_border_colour',
            $tooltipArray['Logo Border Colour'],
            array($this, 'swki_logo_border_colour_cb'),
            'swki_appearance_fields',
            'swki_appearance_settings'
        );
        add_settings_field(
            'swki_max_width',
            $tooltipArray['Max Width'],
            array($this, 'swki_max_width_cb'),
            'swki_appearance_fields',
            'swki_appearance_settings'
        );
        // Tile Settings
        add_settings_field(
            'swki_tile_layout',
            $tooltipArray['Tile Layout'],
            array($this, 'swki_tile_layout_cb'),
            'swki_tile_fields',
            'swki_tile_settings'
        );
        add_settings_field(
            'swki_tile_sorting',
            $tooltipArray['Tile Sorting'],
            array($this, 'swki_tile_sorting_cb'),
            'swki_tile_fields',
            'swki_tile_settings'
        );
        add_settings_field(
            'swki_tile_live_select',
            $tooltipArray['Tile Live'],
            array($this, 'swki_tile_live_select_cb'),
            'swki_tile_fields',
            'swki_tile_settings'
        );
        add_settings_field(
            'swki_tile_bg_colour',
            $tooltipArray['Background Colour'],
            array($this, 'swki_tile_bg_colour_cb'),
            'swki_tile_fields',
            'swki_tile_settings'
        );
        add_settings_field(
            'swki_tile_title_colour',
            $tooltipArray['Title Colour'],
            array($this, 'swki_tile_title_colour_cb'),
            'swki_tile_fields',
            'swki_tile_settings'
        );
        add_settings_field(
            'swki_tile_subtitle_colour',
            $tooltipArray['Subtitle Colour'],
            array($this, 'swki_tile_subtitle_colour_cb'),
            'swki_tile_fields',
            'swki_tile_settings'
        );
        add_settings_field(
            'swki_tile_rounded_corners',
            $tooltipArray['Rounded Corners'],
            array($this, 'swki_tile_rounded_corners_cb'),
            'swki_tile_fields',
            'swki_tile_settings'
        );
        // Hover  Settings
        add_settings_field(
            'swki_hover_effect',
            $tooltipArray['Hover Effect'],
            array($this, 'swki_hover_effect_cb'),
            'swki_hover_fields',
            'swki_hover_settings'
        );
        add_settings_field(
            'swki_hover_colour',
            $tooltipArray['Hover Colour'],
            array($this, 'swki_hover_colour_cb'),
            'swki_hover_fields',
            'swki_hover_settings'
        );
        // Error  Settings
        add_settings_field(
            'swki_debug',
            'Error Log',
            array($this, 'swki_debug_cb'),
            'swki_debug_fields',
            'swki_debug_settings'
        );
    }

    public function swki_api_connection_status_cb() {
        $connection_status = ( isset( $this->options['swki_api_connection_status'] ) ? $this->options['swki_api_connection_status'] : '' );
        $connection_token = ( isset( $this->options['swki_api_access_token'] ) ? $this->options['swki_api_access_token'] : '' );
        $connection_expires = ( isset( $this->options['swki_api_access_token_expires'] ) ? $this->options['swki_api_access_token_expires'] : '' );
        $connection_error_code = ( isset( $this->options['swki_api_access_token_error_code'] ) ? $this->options['swki_api_access_token_error_code'] : '' );
        $connection_error_message = ( isset( $this->options['swki_api_access_token_error_message'] ) ? $this->options['swki_api_access_token_error_message'] : '' );
        $connection_expires_meta = '';
        $dateTimestamp1 = '';
        $dateTimestamp2 = '';
        // swki_twitch_debug_log($connection_token);
        // swki_twitch_debug_log($connection_expires);
        if ( $connection_token !== '' ) {
            $license_status_colour = 'green';
            $license_status_label = 'Kick API Connected!';
        } else {
            $license_status_colour = 'gray';
            $license_status_label = 'Not Connected';
        }
        if ( $connection_expires !== '' ) {
            $connection_expires_meta = '(expires on ' . $connection_expires . ')';
            $dateTimestamp1 = strtotime( $connection_expires );
            $dateTimestamp2 = strtotime( date( 'Y-m-d' ) );
        }
        if ( $connection_expires !== '' && $dateTimestamp2 > $dateTimestamp1 ) {
            $license_status_colour = 'red';
            $license_status_label = 'Kick API Connection Expired!';
            $connection_expires_meta = '(expired on ' . $connection_expires . ')';
        }
        if ( $connection_error_code !== '' ) {
            $license_status_colour = 'red';
            $license_status_label = 'Kick API Connection Error!';
            $connection_expires_meta = '(' . $connection_error_message . ')';
        }
        ?>
		<span style="color: <?php 
        echo esc_html( $license_status_colour );
        ?>; font-weight: bold;"><?php 
        echo esc_html( $license_status_label ) . ' ' . esc_html( $connection_expires_meta );
        ?></span>
		<div class="sw-debug-fields">
			<br>		
			<input type="hidden"  id="sw-access-token" name="swki_options[swki_api_access_token]" value="<?php 
        echo esc_html( $connection_token );
        ?>" />
			<input type="hidden"  id="sw-access-token-expires" name="swki_options[swki_api_access_token_expires]" value="<?php 
        echo esc_html( $connection_expires );
        ?>" />
			<input type="hidden"  id="sw-access-token-error-code" name="swki_options[swki_api_access_token_error_code]" value="<?php 
        echo esc_html( $connection_error_code );
        ?>" />
			<input type="hidden"  id="sw-access-token-error-message" name="swki_options[swki_api_access_token_error_message]" value="<?php 
        echo esc_html( $connection_error_message );
        ?>" />
		</div>
		<?php 
    }

    public function swki_client_id_cb() {
        $connection_token = ( isset( $this->options['swki_api_access_token'] ) ? $this->options['swki_api_access_token'] : '' );
        $client_id = ( isset( $this->options['swki_client_id'] ) ? $this->options['swki_client_id'] : '' );
        ?>

		<?php 
        if ( !empty( $connection_token ) && empty( $client_id ) ) {
            ?>
			<div class="sw-notice notice-error"><p><strong>Error. Client ID cannot be empty!</strong></p></div>
		<?php 
        }
        ?>		

		<input type="" id="sw-client-id" name="swki_options[swki_client_id]" size='40' value="<?php 
        echo esc_html( $client_id );
        ?>" />

		<?php 
    }

    public function swki_client_secret_cb() {
        $client_secret = ( isset( $this->options['swki_client_secret'] ) ? $this->options['swki_client_secret'] : '' );
        ?>

		<input type="" id="sw-client-secret" name="swki_options[swki_client_secret]" size='40' value="<?php 
        echo esc_html( $client_secret );
        ?>" />

		<?php 
    }

    public function swki_client_token_cb() {
        $token = ( isset( $this->options['swki_api_access_token'] ) ? $this->options['swki_api_access_token'] : '' );
        ?>
		
		<input type="text" disabled id="sw-client-token" name="" size='40' value="<?php 
        echo esc_html( $token );
        ?>" />

		<input type="hidden" id="sw-refresh-token" name="swki_options[swki_refresh_token]" value="0" />
		<?php 
        submit_button(
            'Refresh Token',
            'delete button-secondary',
            'sw-refresh-token-submit',
            false,
            array(
                'style' => '',
            )
        );
        ?>

		<?php 
    }

    /**
     * Shortcode Settings
     *
     */
    public function swki_shortcode_cb() {
        ?>
		<div class="postbox-half-wrapper">
			<div class="postbox-half">
				<h3>Simple Shortcode (for one Kick Integration)</h3>
				<p>If you are simply using one Kick Integration on your site, you can fill in the settings on this page and use this simple shortcode:</p>
				<span class="swti-shortcode simple-shortcode">[sw-kick]</span>
				<br>
				<br>
				<a class="button-secondary tooltipped-n" id="sw-copy-shortcode" data-done="section copied" data-clipboard-target=".simple-shortcode" aria-label="Copied!" >Copy Simple Shortcode</a>
			</div>
			<div class="postbox-half">
				<h3>Advanced Shortcode (for many Kick Integrations)</h3>
				<p>If you are using more than one Kick Integration on your site, and you need to change the settings on each, use our advanced shortcode:</p>
				<span class="swti-shortcode advanced-shortcode">[sw-kick]</span>
				<br>
				<br>
				<a class="button-secondary tooltipped-n" id="sw-copy-shortcode" data-done="section copied" data-clipboard-target=".advanced-shortcode" aria-label="Copied!" >Copy Advanced Shortcode</a>
			</div>	
		</div>
		<?php 
    }

    /**
     * Shortcode Settings
     *
     */
    public function swki_translations_live_cb() {
        $live = ( isset( $this->translations['swki_translations_live'] ) ? $this->translations['swki_translations_live'] : '' );
        ?>
		
		<input type="text" id="sw-translations-live" name="swki_translations[swki_translations_live]" size='40' placeholder="live" value="<?php 
        echo esc_html( $live );
        ?>" />
		<?php 
    }

    public function swki_translations_offline_cb() {
        $offline = ( isset( $this->translations['swki_translations_offline'] ) ? $this->translations['swki_translations_offline'] : '' );
        ?>
		
		<input type="text" id="sw-translations-offline" name="swki_translations[swki_translations_offline]" size='40' placeholder="offline" value="<?php 
        echo esc_html( $offline );
        ?>" />
		<?php 
    }

    public function swki_translations_for_cb() {
        $for = ( isset( $this->translations['swki_translations_for'] ) ? $this->translations['swki_translations_for'] : '' );
        ?>
		
		<input type="text" id="sw-translations-for" name="swki_translations[swki_translations_for]" size='40' placeholder="for" value="<?php 
        echo esc_html( $for );
        ?>" />
		<?php 
    }

    public function swki_translations_viewers_cb() {
        $viewers = ( isset( $this->translations['swki_translations_viewers'] ) ? $this->translations['swki_translations_viewers'] : '' );
        ?>
		
		<input type="text" id="sw-translations-viewers" name="swki_translations[swki_translations_viewers]" size='40' placeholder="viewers" value="<?php 
        echo esc_html( $viewers );
        ?>" />
		<?php 
    }

    public function swki_translations_streaming_cb() {
        $streaming = ( isset( $this->translations['swki_translations_streaming'] ) ? $this->translations['swki_translations_streaming'] : '' );
        ?>
		
		<input type="text" id="sw-translations-streaming" name="swki_translations[swki_translations_streaming]" size='40' placeholder="streaming" value="<?php 
        echo esc_html( $streaming );
        ?>" />
		<?php 
    }

    /**
     * Main Settings
     *
     */
    public function swki_game_cb() {
        $game = ( isset( $this->options['swki_game'] ) ? $this->options['swki_game'] : '' );
        $gameId = ( isset( $this->options['swki_game_id'] ) ? $this->options['swki_game_id'] : '' );
        ?>
		
		<?php 
        if ( !empty( $game ) && empty( $gameId ) ) {
            ?>
			<div class="sw-notice notice-error"><p><strong>Error. Game not found in the <a href="https://www.twitch.tv/directory/">Kick Directory</a>. Are you sure it's spelt correctly? <a href="#">Get help!</a></strong></p></div>
		<?php 
        }
        ?>

		<div>
			<input type="text" id="sw-game" name="swki_options[swki_game]" size='40' placeholder="example: Hearthstone" value="<?php 
        echo esc_html( $game );
        ?>" />
			<?php 
        if ( !empty( $game ) && !empty( $gameId ) ) {
            ?>
				<p class="sw-success"><span class="dashicons dashicons-yes-alt"></span>Game ID: <?php 
            echo esc_html( $gameId );
            ?></p>
			<?php 
        }
        ?>
		</div>

		<input type="hidden" id="sw-game-id" name="swki_options[swki_game_id]" size='40' value="<?php 
        echo esc_html( $gameId );
        ?>" />
		<p>Enter the game name exactly as it appears on <a href="https://www.twitch.tv/directory/gaming">Kick</a>.</p>

		<?php 
    }

    public function swki_language_cb() {
        $language = ( isset( $this->options['swki_language'] ) ? $this->options['swki_language'] : '' );
        ?>
		
		<input type="text" id="sw-language" name="swki_options[swki_language]" size='40' placeholder="example: en" value="<?php 
        echo esc_html( $language );
        ?>" />
		<p>If you would like to limit your streams to a certain language, enter the ISO 639-1 two-letter <a href="https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes">language code</a>.</p>

		<?php 
    }

    public function swki_channels_cb() {
        $channels = ( isset( $this->options['swki_channels'] ) ? $this->options['swki_channels'] : '' );
        ?>
		
		<div>
			<input type="text" id="sw-channels" name="swki_options[swki_channels]" size='40' placeholder="example: xqc,adin,amouranth," value="<?php 
        echo esc_html( $channels );
        ?>" />
			<?php 
        if ( !empty( $channels ) ) {
            ?>
				<p class="sw-success"><span class="dashicons dashicons-yes-alt"></span>Channels: <?php 
            echo count( explode( ',', $channels ) );
            ?></p>
			<?php 
        }
        ?>			
		</div>
		<p>Enter a list of channel names, with each channel name seperated by a comma.</p>

		<?php 
    }

    public function swki_title_filter_cb() {
        $titleFilter = ( isset( $this->options['swki_title_filter'] ) ? $this->options['swki_title_filter'] : '' );
        $channels = ( isset( $this->options['swki_channels'] ) ? $this->options['swki_channels'] : '' );
        $team = ( isset( $this->options['swki_team'] ) ? $this->options['swki_team'] : '' );
        $game = ( isset( $this->options['swki_game'] ) ? $this->options['swki_game'] : '' );
        ?>
		
		<?php 
        if ( !empty( $titleFilter ) && empty( $channels ) && empty( $team ) && empty( $game ) ) {
            ?>
			<div class="sw-notice notice-error"><p><strong>Error. Title Filter field must be combined with either Game, Channels or Team.</strong></p></div>
		<?php 
        }
        ?>

		<div>
			<input type="text" id="sw-title-filter" name="swki_options[swki_title_filter]" size='40' placeholder="example: NoPixel" value="<?php 
        echo esc_html( $titleFilter );
        ?>" />
		</div>
		<p>Enter a specific tag and we will only only show streams which include that tag in the stream title.</p>

		<?php 
    }

    public function swki_limit_cb() {
        $limit = ( isset( $this->options['swki_limit'] ) ? $this->options['swki_limit'] : '' );
        ?>
		
		<input type="text" id="sw-limit" name="swki_options[swki_limit]" size='40' placeholder="example: 15" value="<?php 
        echo esc_html( $limit );
        ?>" />
		<p>Limit the maximum number of streams to display.</p>
		<?php 
        if ( ski_fs()->can_use_premium_code() == false ) {
            ?>
				<p>Current Plan: Free</p>
				<p>Stream Limit: 15</p>
				<p><a href="admin.php?page=streamweasels-kick-pricing">Unlock more streams</a></p>
		<?php 
        }
        ?>		
		<?php 
        if ( ski_fs()->is_plan_or_trial( 'essentials', true ) ) {
            ?>
				<p>Current Plan: Essentials</p>
				<p>Stream Limit: 50</p>
				<p><a href="admin.php?page=streamweasels-kick-pricing">Unlock more streams</a></p>
		<?php 
        }
        ?>
		<?php 
        if ( ski_fs()->is_plan_or_trial( 'pro', true ) ) {
            ?>
				<p>Current Plan: Pro</p>
				<p>Stream Limit: Unlimited</p>	
		<?php 
        }
    }

    public function swki_colour_theme_cb() {
        $colourTheme = ( isset( $this->options['swki_colour_theme'] ) ? $this->options['swki_colour_theme'] : '' );
        ?>
		
		<select id="sw-colour-theme" name="swki_options[swki_colour_theme]">
			<option value="light" <?php 
        echo selected( $colourTheme, 'light', false );
        ?>>Light Theme</option>	
            <option value="dark" <?php 
        echo selected( $colourTheme, 'dark', false );
        ?>>Dark Theme</option>
        </select>
		<p>Select the colour theme for your Kick content. These colours match Kick's own Light and Dark mode.</p>

		<?php 
    }

    /**
     * Layout Settings
     *
     */
    public function swki_layout_cb() {
        $swki_layout_options = $this->swki_twitch_get_layout_options();
        $layout = ( isset( $this->options['swki_layout'] ) ? $this->options['swki_layout'] : '' );
        ?>

		<select id="sw-layout" name="swki_options[swki_layout]">
			<?php 
        foreach ( $swki_layout_options as $key => $label ) {
            ?>
				<option value="<?php 
            echo esc_html( $key );
            ?>" <?php 
            selected( $layout, $key );
            ?>><?php 
            echo esc_html( $label );
            ?></option>
			<?php 
        }
        ?>
		</select>		
		
		<div id="fs_addons" class="wrap fs-section">
			<h3 id="free-layouts">Free Layouts</h3>
			<p>StreamWeasels Kick Integration comes with the <strong>Kick Wall layout</strong> with more layouts coming soon!</p>		
			<br>	
			<ul class="fs-cards-list">
				<li class="fs-card fs-addon" data-slug="ttv-easy-embed-wall">
					<div class="fs-inner">
						<ul>
							<li class="fs-card-banner" style="background-image: url('<?php 
        echo plugin_dir_url( __FILE__ );
        ?>../admin/img/wall.png');">
								<?php 
        echo ( in_array( "Wall", $swki_layout_options ) ? '<span class="fs-badge fs-installed-addon-badge">Active</span>' : '' );
        ?>           
							</li>
							<li class="fs-title">Kick Wall</li>
							<li class="fs-offer">
							<span class="fs-price">Free Layout</span>
							</li>
							<li class="fs-description">Classic Kick layout for displaying many streams at once.</li>
							<li class="fs-cta"><a class="button">View Details</a></li>
						</ul>
					</div>
				</li>
				<li class="fs-card fs-addon" data-slug="stream-status-for-twitch">
					<div class="fs-inner">
						<ul>
							<li class="fs-card-banner" style="background-image: url('<?php 
        echo plugin_dir_url( __FILE__ );
        ?>../admin/img/status.png');">
								<?php 
        echo ( in_array( "Status", $swki_layout_options ) ? '<span class="fs-badge fs-installed-addon-badge">Active</span>' : '' );
        ?>
							</li>
							<!-- <li class="fs-tag"></li> -->
							<li class="fs-title">Kick Status</li>
							<li class="fs-offer">
							<span class="fs-price">Free Layout</span>
							</li>
							<li class="fs-description">Simply display Kick live status on every page of your website.</li>
							<li class="fs-cta"><a class="button">View Details</a></li>
						</ul>
					</div>
				</li>
				<li class="fs-card fs-addon" data-slug="stream-status-for-twitch">
					<div class="fs-inner">
						<ul>
							<li class="fs-card-banner" style="background-image: url('<?php 
        echo plugin_dir_url( __FILE__ );
        ?>../admin/img/vods.png');">
								<?php 
        echo ( in_array( "Vods", $swki_layout_options ) ? '<span class="fs-badge fs-installed-addon-badge">Active</span>' : '' );
        ?>
							</li>
							<!-- <li class="fs-tag"></li> -->
							<li class="fs-title">Kick Vods</li>
							<li class="fs-offer">
							<span class="fs-price">Free Layout</span>
							</li>
							<li class="fs-description">Display clips from Kick.</li>
							<li class="fs-cta"><a class="button">View Details</a></li>
						</ul>
					</div>
				</li>								
			</ul>
			<h3 id="paid-layouts">PRO Layouts</h3>
			<p>Looking for more options? Try <strong>Kick Feature</strong>, with more PRO layouts on the way.</p>		
			<br>			
			<ul class="fs-cards-list">
				<li class="fs-card fs-addon" data-slug="streamweasels-feature-pro">
					<a href="admin.php?page=streamweasels-kick-pricing" aria-label="More information about Kick Feature" data-title="Kick Feature" class="fs-overlay"></a>
					<div class="fs-inner">
						<ul>
							<li class="fs-card-banner" style="background-image: url('<?php 
        echo plugin_dir_url( __FILE__ );
        ?>../admin/img/feature.png');">
								<?php 
        echo ( in_array( "Feature", $swki_layout_options ) ? '<span class="fs-badge fs-installed-addon-badge">Active</span>' : '' );
        ?>
							</li>
							<!-- <li class="fs-tag"></li> -->
							<li class="fs-title">Kick Feature</li>
							<li class="fs-offer">
							<span class="fs-price">PRO Layout</span>
							</li>
							<li class="fs-description">Slick, professional layout inspired by the Kick homepage.</li>
							<li class="fs-cta"><a class="button" href="admin.php?page=streamweasels-pricing">View Demo</a></li>
						</ul>
					</div>
					<div class="fs-extras">
						<a href="admin.php?page=streamweasels-kick-pricing">Unlock Layout</a> | 
						<a href="https://www.streamweasels.com/kick-wordpress-plugins/kick-feature/?utm_source=wordpress&utm_medium=kick-integration&utm_campaign=view-demo" target="_blank">View Demo</a>
					</div>
				</li>																	
			</ul>			
		</div>
		<?php 
    }

    public function swki_embed_cb() {
        $embed = ( isset( $this->options['swki_embed'] ) ? $this->options['swki_embed'] : '' );
        ?>
		
		<select id="sw-embed" name="swki_options[swki_embed]">
            <option value="page" <?php 
        echo selected( $embed, 'page', false );
        ?>>Embed on page</option>
            <option value="popup" <?php 
        echo selected( $embed, 'popup', false );
        ?>>Embed in a popup</option>
			<option value="twitch" <?php 
        echo selected( $embed, 'twitch', false );
        ?>>Link to Kick</option>
        </select>
		<p>When users interact with your Kick integration, you can choose how to display the embedded content.</p>

		<?php 
    }

    public function swki_embed_theme_cb() {
        $embedColour = ( isset( $this->options['swki_embed_theme'] ) ? $this->options['swki_embed_theme'] : '' );
        ?>
		
		<select id="sw-embed-theme" name="swki_options[swki_embed_theme]">
            <option value="dark" <?php 
        echo selected( $embedColour, 'dark', false );
        ?>>Dark Theme</option>
            <option value="light" <?php 
        echo selected( $embedColour, 'light', false );
        ?>>Light Theme</option>
        </select>
		<p>Select the colour scheme for your embedded Kick content.</p>

		<?php 
    }

    public function swki_embed_chat_cb() {
        $chat = ( isset( $this->options['swki_embed_chat'] ) ? $this->options['swki_embed_chat'] : '' );
        ?>
		
		<input type="hidden" name="swki_options[swki_embed_chat]" value="0"/>
		<input type="checkbox" id="sw-embed-chat" name="swki_options[swki_embed_chat]" value="1" <?php 
        checked( 1, $chat, true );
        ?>/>
		<p>Choose to display chat for your embedded Kick content.</p>

		<?php 
    }

    public function swki_embed_muted_cb() {
        $muted = ( isset( $this->options['swki_embed_muted'] ) ? $this->options['swki_embed_muted'] : '' );
        ?>
		
		<input type="hidden" name="swki_options[swki_embed_muted]" value="0"/>
		<input type="checkbox" id="sw-embed-muted" name="swki_options[swki_embed_muted]" value="1" <?php 
        checked( 1, $muted, true );
        ?>/>
		<p>Choose to start your embedded Kick content muted.</p>

		<?php 
    }

    public function swki_embed_title_cb() {
        $title = ( isset( $this->options['swki_embed_title'] ) ? $this->options['swki_embed_title'] : '' );
        ?>
		
		<input type="hidden" name="swki_options[swki_embed_title]" value="0"/>
		<input type="checkbox" id="sw-embed-title" name="swki_options[swki_embed_title]" value="1" <?php 
        checked( 1, $title, true );
        ?>/>
		<p>Choose to display the title for your embedded Kick content.</p>

		<?php 
    }

    public function swki_embed_title_position_cb() {
        $titlePosition = ( isset( $this->options['swki_embed_title_position'] ) ? $this->options['swki_embed_title_position'] : '' );
        ?>
		
		<select id="sw-embed-title-position" name="swki_options[swki_embed_title_position]">
            <option value="top" <?php 
        echo selected( $titlePosition, 'top', false );
        ?>>Top</option>
            <option value="bottom" <?php 
        echo selected( $titlePosition, 'bottom', false );
        ?>>Bottom</option>
        </select>
		<p>Change the position of the title for your embedded Kick content.</p>

		<?php 
    }

    /**
     * Extra Settings
     *
     */
    public function swki_show_offline_cb() {
        $offline = ( isset( $this->options['swki_show_offline'] ) ? $this->options['swki_show_offline'] : '' );
        ?>
		
		<input type="hidden" name="swki_options[swki_show_offline]" value="0"/>
		<input type="checkbox" id="sw-show-offline" name="swki_options[swki_show_offline]" value="1" <?php 
        checked( 1, $offline, true );
        ?>/>
		<p>Choose to show all streams, even if they're offline.</p>

		<?php 
    }

    public function swki_show_offline_text_cb() {
        $offlineText = ( isset( $this->options['swki_show_offline_text'] ) ? $this->options['swki_show_offline_text'] : '' );
        ?>
		
		<input type="text" id="sw-show-offline-text" name="swki_options[swki_show_offline_text]" size='40' value="<?php 
        echo esc_html( $offlineText );
        ?>" />
		<p>Choose to display a custom message at the top when ALL streams are offline.</p>

		<?php 
    }

    public function swki_show_offline_image_cb() {
        $showOfflineImage = ( isset( $this->options['swki_show_offline_image'] ) ? $this->options['swki_show_offline_image'] : '' );
        ?>
		
		<input type="text" id="sw-show-offline-image" name="swki_options[swki_show_offline_image]" size='40' value="<?php 
        echo esc_html( $showOfflineImage );
        ?>" />
        <input type="button" name="upload-btn" class="upload-btn button-secondary" value="Upload Image">
		<p>Choose to display a custom image at the top when ALL streams are offline.</p>

		<?php 
    }

    public function swki_autoplay_cb() {
        $autoplay = ( isset( $this->options['swki_autoplay'] ) ? $this->options['swki_autoplay'] : 0 );
        ?>
		
		<input type="hidden" name="swki_options[swki_autoplay]" value="0"/>
		<input type="checkbox" id="sw-autoplay" name="swki_options[swki_autoplay]" value="1" <?php 
        checked( 1, $autoplay, true );
        ?> />
		<p>Choose to autoplay the top stream.</p>


		<?php 
    }

    public function swki_autoplay_select_cb() {
        $select = ( isset( $this->options['swki_autoplay_select'] ) ? $this->options['swki_autoplay_select'] : '' );
        ?>
		
		<select id="sw-autoplay-select" name="swki_options[swki_autoplay_select]">
			<option value="most" <?php 
        echo selected( $select, 'most', false );
        ?>>Most Viewers</option>
			<option value="least" <?php 
        echo selected( $select, 'least', false );
        ?>>Least Viewers</option>
			<option value="random" <?php 
        echo selected( $select, 'random', false );
        ?>>Random</option>
        </select>
		<p>Choose which stream to autoplay.</p>


		<?php 
    }

    public function swki_featured_stream_cb() {
        $featured = ( isset( $this->options['swki_featured_stream'] ) ? $this->options['swki_featured_stream'] : '' );
        ?>
		
		<input type="text" id="sw-featured-stream" name="swki_options[swki_featured_stream]" size='40' value="<?php 
        echo esc_html( $featured );
        ?>" />
		<p>Choose to autoplay a featured streamer, only if that streamer is online.</p>

		<?php 
    }

    /**
     * Appearance Settings
     *
     */
    public function swki_title_cb() {
        $title = ( isset( $this->options['swki_title'] ) ? $this->options['swki_title'] : '' );
        ?>
		
		<input type="text" id="sw-title" name="swki_options[swki_title]" size='40' value="<?php 
        echo esc_html( $title );
        ?>" />
		<p>Add your own title.</p>

		<?php 
    }

    public function swki_subtitle_cb() {
        $subtitle = ( isset( $this->options['swki_subtitle'] ) ? $this->options['swki_subtitle'] : '' );
        ?>
		
		<input type="text" id="sw-subtitle" name="swki_options[swki_subtitle]" size='40' value="<?php 
        echo esc_html( $subtitle );
        ?>" />
		<p>Add your own subtitle.</p>

		<?php 
    }

    public function swki_offline_image_cb() {
        $offline_image = ( isset( $this->options['swki_offline_image'] ) ? $this->options['swki_offline_image'] : '' );
        ?>
		
		<input type="text" id="sw-offline-image" name="swki_options[swki_offline_image]" size='40' value="<?php 
        echo esc_html( $offline_image );
        ?>" />
        <input type="button" name="upload-btn" class="upload-btn button-secondary" value="Upload Image">
		<p>Choose to display a custom image when there are no streams online. Ideal image dimensions are 440 x 248 or 880 x 496.</p>

		<?php 
    }

    public function swki_logo_image_cb() {
        $logo = ( isset( $this->options['swki_logo_image'] ) ? $this->options['swki_logo_image'] : '' );
        ?>
		
		<input type="text" id="sw-logo-image" name="swki_options[swki_logo_image]" size='40' value="<?php 
        echo esc_html( $logo );
        ?>" />
        <input type="button" name="upload-btn" class="upload-btn button-secondary" value="Upload Image">
		<p>Add your own logo. This should be a small square image, Ideal image dimensions are 80 x 80.</p>

		<?php 
    }

    public function swki_profile_image_cb() {
        $profileImage = ( isset( $this->options['swki_profile_image'] ) ? $this->options['swki_profile_image'] : '' );
        ?>
		
		<input type="hidden" name="swki_options[swki_profile_image]" value="0"/>
		<input type="checkbox" id="sw-profile-image" name="swki_options[swki_profile_image]" value="1" <?php 
        checked( 1, $profileImage, true );
        ?> />
		<p>Choose to hide the users profile image from Kick.</p>

		<?php 
    }

    public function swki_logo_bg_colour_cb() {
        $logoBg = ( isset( $this->options['swki_logo_bg_colour'] ) ? $this->options['swki_logo_bg_colour'] : '' );
        ?>
		
		<input type="text" id="sw-logo-bg-colour" name="swki_options[swki_logo_bg_colour]" size='40' value="<?php 
        echo esc_html( $logoBg );
        ?>" />
		<p>Add a background colour for your logo.</p>

		<?php 
    }

    public function swki_logo_border_colour_cb() {
        $logoBorder = ( isset( $this->options['swki_logo_border_colour'] ) ? $this->options['swki_logo_border_colour'] : '' );
        ?>
		
		<input type="text" id="sw-logo-border-colour" name="swki_options[swki_logo_border_colour]" size='40' value="<?php 
        echo esc_html( $logoBorder );
        ?>" />
		<p>Add a border colour for your logo.</p>


		<?php 
    }

    public function swki_max_width_cb() {
        $width = ( isset( $this->options['swki_max_width'] ) ? $this->options['swki_max_width'] : '' );
        ?>
		
		<select id="sw-max-width" name="swki_options[swki_max_width]">
            <option value="none" <?php 
        echo selected( $width, 'none', false );
        ?>>None</option>
            <option value="1920" <?php 
        echo selected( $width, '1920', false );
        ?>>1920px</option>
            <option value="1680" <?php 
        echo selected( $width, '1680', false );
        ?>>1680px</option>
            <option value="1440" <?php 
        echo selected( $width, '1440', false );
        ?>>1440px</option>
            <option value="1280" <?php 
        echo selected( $width, '1280', false );
        ?>>1280px</option>
            <option value="1024" <?php 
        echo selected( $width, '1024', false );
        ?>>1024px</option>
            <option value="768" <?php 
        echo selected( $width, '768', false );
        ?>>768px</option>
        </select>
		<p>Add a max width to your Kick integration.</p>


		<?php 
    }

    /**
     * Tile Settings
     *
     */
    public function swki_tile_layout_cb() {
        $layout = ( isset( $this->options['swki_tile_layout'] ) ? $this->options['swki_tile_layout'] : '' );
        ?>
		
		<select id="sw-tile-layout" name="swki_options[swki_tile_layout]">
            <option value="detailed" <?php 
        echo selected( $layout, 'detailed', false );
        ?>>Detailed</option>
            <option value="compact" <?php 
        echo selected( $layout, 'compact', false );
        ?>>Compact</option>
        </select>
		<p>Choose the layout mode for your Kick stream tiles.</p>

		<?php 
    }

    public function swki_tile_sorting_cb() {
        $sorting = ( isset( $this->options['swki_tile_sorting'] ) ? $this->options['swki_tile_sorting'] : '' );
        ?>
		
		<select id="sw-tile-sorting" name="swki_options[swki_tile_sorting]">
			<option value="most" <?php 
        echo selected( $sorting, 'most', false );
        ?>>Most Viewers</option>
			<option value="least" <?php 
        echo selected( $sorting, 'least', false );
        ?>>Least Viewers</option>
			<option value="alpha" <?php 
        echo selected( $sorting, 'alpha', false );
        ?>>Alphabetical</option>
			<option value="latest" <?php 
        echo selected( $sorting, 'latest', false );
        ?>>Latest</option>
			<option value="random" <?php 
        echo selected( $sorting, 'random', false );
        ?>>Random</option>
        </select>
		<p>Choose the sorting of the Kick stream tiles.</p>

		<?php 
    }

    public function swki_tile_live_select_cb() {
        $live = ( isset( $this->options['swki_tile_live_select'] ) ? $this->options['swki_tile_live_select'] : '' );
        ?>
		
		<select id="sw-live-info" name="swki_options[swki_tile_live_select]">
			<option value="viewer" <?php 
        echo selected( $live, 'viewer', false );
        ?>>Viewer Count</option>
			<option value="online" <?php 
        echo selected( $live, 'online', false );
        ?>>Online / Offline dot</option>
			<option value="live" <?php 
        echo selected( $live, 'live', false );
        ?>>LIVE</option>
			<option value="none" <?php 
        echo selected( $live, 'none', false );
        ?>>None</option>
        </select>
		<p>Choose the live information to display in the top-left of each live stream.</p>

		<?php 
    }

    public function swki_tile_bg_colour_cb() {
        $bgColour = ( isset( $this->options['swki_tile_bg_colour'] ) ? $this->options['swki_tile_bg_colour'] : '' );
        ?>
		
		<input type="text" id="sw-tile-bg-colour" name="swki_options[swki_tile_bg_colour]" size='40' value="<?php 
        echo esc_html( $bgColour );
        ?>" />
		<p>Change the background colour for your Kick stream tiles.</p>


		<?php 
    }

    public function swki_tile_title_colour_cb() {
        $titleColour = ( isset( $this->options['swki_tile_title_colour'] ) ? $this->options['swki_tile_title_colour'] : '' );
        ?>
		
		<input type="text" id="sw-tile-title-colour" name="swki_options[swki_tile_title_colour]" size='40' value="<?php 
        echo esc_html( $titleColour );
        ?>" />
		<p>Change the title colour for your Kick stream tiles.</p>

		<?php 
    }

    public function swki_tile_subtitle_colour_cb() {
        $subtitleColour = ( isset( $this->options['swki_tile_subtitle_colour'] ) ? $this->options['swki_tile_subtitle_colour'] : '' );
        ?>
		
		<input type="text" id="sw-tile-subtitle-colour" name="swki_options[swki_tile_subtitle_colour]" size='40' value="<?php 
        echo esc_html( $subtitleColour );
        ?>" />
		<p>Change the subtitle colour for your Kick stream tiles.</p>


		<?php 
    }

    public function swki_tile_rounded_corners_cb() {
        $roundedCorners = ( isset( $this->options['swki_tile_rounded_corners'] ) ? $this->options['swki_tile_rounded_corners'] : '5' );
        ?>

		<input id="sw-tile-rounded-corners" type="text" name="swki_options[swki_tile_rounded_corners]" value="<?php 
        echo esc_html( $roundedCorners );
        ?>">
		<span class="range-bar-value"></span>
		<p>Add rounded corners to your Kick stream tiles.</p>


		<?php 
    }

    public function swki_hover_effect_cb() {
        $hoverEffect = ( isset( $this->options['swki_hover_effect'] ) ? $this->options['swki_hover_effect'] : '' );
        ?>
		
		<select id="sw-hover-effect" name="swki_options[swki_hover_effect]">
            <option value="none" <?php 
        echo selected( $hoverEffect, 'none', false );
        ?>>none</option>
            <option value="twitch" <?php 
        echo selected( $hoverEffect, 'twitch', false );
        ?>>Kick Style</option>
			<option value="play" <?php 
        echo selected( $hoverEffect, 'play', false );
        ?>>Play Button</option>
        </select>
		<p>Change the hover effect for your Kick stream tiles.</p>


		<?php 
    }

    public function swki_hover_colour_cb() {
        $hoverColour = ( isset( $this->options['swki_hover_colour'] ) ? $this->options['swki_hover_colour'] : '' );
        ?>
		
		<input type="text" id="sw-hover-colour" name="swki_options[swki_hover_colour]" size='40' value="<?php 
        echo esc_html( $hoverColour );
        ?>" />
		<p>Change the hover colour for your Kick stream tiles.</p>

		<?php 
    }

    /**
     * Debug Settings
     *
     */
    public function swki_debug_cb() {
        $dismissForGood5 = ( isset( $this->options['swki_dismiss_for_good5'] ) ? $this->options['swki_dismiss_for_good5'] : 0 );
        ?>
		
		<p>
			<textarea rows="6" style="width: 100%;"><?php 
        echo get_option( 'swki_debug_log', '' );
        ?></textarea>
		</p>
		<p>
			<input type="hidden" id="sw-delete-log" name="swki_options[swki_delete_log]" value="0" />
			<input type="hidden" id="sw-dismiss-for-good5" name="swki_options[swki_dismiss_for_good5]" value="<?php 
        echo esc_html( $dismissForGood5 );
        ?>" />
			<?php 
        submit_button(
            'Clear logs',
            'delete button-secondary',
            'sw-delete-log-submit',
            false
        );
        ?>
		</p>

		<?php 
    }

    /**
     * Field Validation
     *
     */
    public function swki_options_validate( $input ) {
        $new_input = [];
        $options = get_option( 'swki_options' );
        // Main Settings
        if ( isset( $input['swki_game'] ) ) {
            $new_input['swki_game'] = wp_kses( $input['swki_game'], 'post' );
        }
        if ( isset( $input['swki_language'] ) ) {
            $new_input['swki_language'] = sanitize_text_field( $input['swki_language'] );
        }
        if ( isset( $input['swki_channels'] ) ) {
            if ( substr( $input['swki_channels'], -1 ) == ',' ) {
                $input['swki_channels'] = substr( $input['swki_channels'], 0, -1 );
            }
            $input['swki_channels'] = str_replace( ' ', '', $input['swki_channels'] );
            $input['swki_channels'] = strtolower( $input['swki_channels'] );
            $new_input['swki_channels'] = sanitize_text_field( $input['swki_channels'] );
        }
        if ( isset( $input['swki_title_filter'] ) ) {
            $new_input['swki_title_filter'] = sanitize_text_field( $input['swki_title_filter'] );
        }
        if ( !empty( $input['swki_limit'] ) ) {
            $new_input['swki_limit'] = $input['swki_limit'];
            if ( ski_fs()->can_use_premium_code() == false && $input['swki_limit'] > 15 ) {
                $new_input['swki_limit'] = 15;
            }
            if ( ski_fs()->is_plan( 'essentials', true ) && $input['swki_limit'] > 50 ) {
                $new_input['swki_limit'] = 50;
            }
            if ( ski_fs()->is_plan( 'premium', true ) && $input['swki_limit'] > 100 ) {
                $new_input['swki_limit'] = 100;
            }
        } else {
            $new_input['swki_limit'] = '15';
        }
        if ( isset( $input['swki_colour_theme'] ) ) {
            $new_input['swki_colour_theme'] = $input['swki_colour_theme'];
        }
        // Layout Settings
        if ( isset( $input['swki_layout'] ) ) {
            $new_input['swki_layout'] = $input['swki_layout'];
        }
        if ( isset( $input['swki_dismiss_for_good5'] ) ) {
            $new_input['swki_dismiss_for_good5'] = (int) $input['swki_dismiss_for_good5'];
        }
        if ( isset( $input['swki_delete_log'] ) && $input['swki_delete_log'] == 1 ) {
            $new_input['swki_dismiss_for_good5'] = 0;
            delete_option( 'swki_debug_log' );
        }
        return $new_input;
    }

    /**
     * Field Validation
     *
     */
    public function swki_translations_validate( $input ) {
        // Translation Settings
        if ( isset( $input['swki_translations_live'] ) && !empty( $input['swki_translations_live'] ) ) {
            $new_input['swki_translations_live'] = $input['swki_translations_live'];
        } else {
            $new_input['swki_translations_live'] = 'live';
        }
        if ( isset( $input['swki_translations_offline'] ) && !empty( $input['swki_translations_offline'] ) ) {
            $new_input['swki_translations_offline'] = $input['swki_translations_offline'];
        } else {
            $new_input['swki_translations_offline'] = 'offline';
        }
        if ( isset( $input['swki_translations_viewers'] ) && !empty( $input['swki_translations_viewers'] ) ) {
            $new_input['swki_translations_viewers'] = $input['swki_translations_viewers'];
        } else {
            $new_input['swki_translations_viewers'] = 'viewers';
        }
        if ( isset( $input['swki_translations_streaming'] ) && !empty( $input['swki_translations_streaming'] ) ) {
            $new_input['swki_translations_streaming'] = $input['swki_translations_streaming'];
        } else {
            $new_input['swki_translations_streaming'] = 'streaming';
        }
        if ( isset( $input['swki_translations_for'] ) && !empty( $input['swki_translations_for'] ) ) {
            $new_input['swki_translations_for'] = $input['swki_translations_for'];
        } else {
            $new_input['swki_translations_for'] = 'for';
        }
        return $new_input;
    }

    public function swki_showAdmin() {
        include 'partials/streamweasels-kick-integration-admin-display.php';
    }

    public function swki_twitch_get_layout_options() {
        $options['none'] = 'None';
        $options['wall'] = 'Wall';
        $options['vods'] = 'Vods';
        $options['status'] = 'Status';
        $options = apply_filters( 'swki_twitch_layout_options', $options );
        return $options;
    }

    public function swki_do_settings_sections(
        $page,
        $icon,
        $desc,
        $status
    ) {
        global $wp_settings_sections, $wp_settings_fields;
        if ( !isset( $wp_settings_sections[$page] ) ) {
            return;
        }
        foreach ( (array) $wp_settings_sections[$page] as $section ) {
            $title = '';
            $description = '';
            if ( $section['title'] ) {
                $title = "<h3 class='hndle'><span class='dashicons {$icon}'></span>{$section['title']}</h3>";
            }
            if ( $desc ) {
                $description = "<p>" . $desc . "</p>";
            }
            echo '<div class="postbox postbox-' . sanitize_title( $title ) . ' postbox-' . $status . '">';
            echo wp_kses( $title, 'post' );
            echo '<div class="inside">';
            echo wp_kses( $description, 'post' );
            if ( $section['callback'] ) {
                call_user_func( $section['callback'], $section );
            }
            echo '<table class="form-table">';
            do_settings_fields( $page, $section['id'] );
            echo '</table>';
            if ( $section['title'] == 'Shortcode' ) {
                echo '';
            } else {
                submit_button();
            }
            echo '</div>';
            if ( !ski_fs()->is__premium_only() || ski_fs()->is_free_plan() ) {
                if ( $status == 'pro' ) {
                    echo '<div class="postbox-trial-wrapper"><a href="admin.php?page=streamweasels-kick-pricing" target="_blank" type="button" class="button button-primary">Buy Now</a></div>';
                }
            }
            echo '</div>';
        }
    }

    public function swki_twitch_debug_log( $message ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG === true ) {
            if ( is_array( $message ) || is_object( $message ) ) {
                error_log( print_r( $message, true ) );
            } else {
                error_log( $message );
            }
        }
    }

    public function swki_twitch_debug_field( $message ) {
        if ( is_array( $message ) ) {
            $message = print_r( $message, true );
        }
        $log = get_option( 'swki_debug_log', '' );
        $string = date( 'd.m.Y H:i:s' ) . " : " . $message . "\n";
        $log .= $string;
        update_option( 'swki_debug_log', $log );
    }

}
