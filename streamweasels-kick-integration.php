<?php

/**
 * Plugin Name:       SW Kick Integration - Blocks and Shortcodes for Embedding Kick Streams
 * Description:       StreamWeasels Kick Integration for embedding live streams from Kick
 * Requires at least: 5.0
 * Requires PHP:      7.0
 * Version:           1.1.2
 * Author:            StreamWeasels
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       streamweasels-kick-integration
 *
 * @package           streamweasels-kick-integration
 */
define( 'STREAMWEASELS_KICK_VERSION', '1.1.2' );
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( function_exists( 'ski_fs' ) ) {
    ski_fs()->set_basename( false, __FILE__ );
} else {
    // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
    if ( !function_exists( 'ski_fs' ) ) {
        // Create a helper function for easy SDK access.
        function ski_fs() {
            global $ski_fs;
            if ( !isset( $ski_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $ski_fs = fs_dynamic_init( array(
                    'id'             => '12987',
                    'slug'           => 'streamweasels-kick-integration',
                    'premium_slug'   => 'streamweasels-kick-integration-paid',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_cede312fd581aeed630c57e260a68',
                    'is_premium'     => false,
                    'premium_suffix' => '(Paid)',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'trial'          => array(
                        'days'               => 10,
                        'is_require_payment' => true,
                    ),
                    'menu'           => array(
                        'slug'    => 'streamweasels-kick',
                        'support' => false,
                    ),
                    'is_live'        => true,
                ) );
            }
            return $ski_fs;
        }

        // Init Freemius.
        ski_fs();
        // Signal that SDK was initiated.
        do_action( 'ski_fs_loaded' );
    }
}
// Plugin Folder Path
if ( !defined( 'SWKI_PLUGIN_DIR' ) ) {
    define( 'SWKI_PLUGIN_DIR', plugin_dir_url( __FILE__ ) );
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-streamweasels-kick-integration-activator.php
 */
function activate_streamweasels_kick_integration() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-streamweasels-kick-integration-activator.php';
    Streamweasels_Kick_Integration_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-streamweasels-kick-integration-deactivator.php
 */
function deactivate_streamweasels_kick_integration() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-streamweasels-kick-integration-deactivator.php';
    Streamweasels_Kick_Integration_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_streamweasels_kick_integration' );
register_deactivation_hook( __FILE__, 'deactivate_streamweasels_kick_integration' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-streamweasels-kick-integration.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_streamweasels_kick_integration() {
    $plugin = new Streamweasels_Kick_Integration();
    $plugin->run();
}

run_streamweasels_kick_integration();