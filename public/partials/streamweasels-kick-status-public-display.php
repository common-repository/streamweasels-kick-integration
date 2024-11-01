<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.streamweasels.com
 * @since      1.0.0
 *
 * @package    Streamweasels_Status_Pro
 * @subpackage Streamweasels_Status_Pro/public/partials
 */
?>

<?php

$options              = get_option('swki_options');
$layout               = ( isset( $args['layout'] ) ? $args['layout'] : $options['swki_layout'] ); 
$optionsStatus        = get_option('swki_options_status');
$hideOffline          = ( isset( $optionsStatus['swki_status_hide_when_offline'] ) ? $optionsStatus['swki_status_hide_when_offline'] : '0' ); 
$hideOffline          = ( isset( $args['status-hide-offline'] ) ? $args['status-hide-offline'] : $hideOffline);
$placement            = ( isset( $optionsStatus['swki_status_placement'] ) ? $optionsStatus['swki_status_placement'] : 'absolute' ); 
$placement            = ( isset( $args['status-placement'] ) ? $args['status-placement'] : $placement); 
$verticalPlacement    = ( isset( $optionsStatus['swki_status_vertical_placement'] ) ? $optionsStatus['swki_status_vertical_placement'] : 'top' ); 
$verticalPlacement    = ( isset( $args['status-vertical-placement'] ) ? $args['status-vertical-placement'] : $verticalPlacement); 
$horizontalPlacement  = ( isset( $optionsStatus['swki_status_horizontal_placement'] ) ? $optionsStatus['swki_status_horizontal_placement'] : 'left' ); 
$horizontalPlacement  = ( isset( $args['status-horizontal-placement'] ) ? $args['status-horizontal-placement'] : $horizontalPlacement ); 
$customLogo           = ( isset( $optionsStatus['swki_status_custom_logo'] ) ? $optionsStatus['swki_status_custom_logo'] : '' ); 
$customLogo           = ( isset( $args['status-custom-logo'] ) ? $args['status-custom-logo'] : $customLogo ); 
$logoBackgroundColour = ( isset( $optionsStatus['swki_status_logo_background_colour']  ) ? $optionsStatus['swki_status_logo_background_colour']  : '' ); 
$logoBackgroundColour = ( isset( $args['status-logo-background-colour'] ) ? $args['status-logo-background-colour'] : $logoBackgroundColour ); 
$disableCarousel      = ( isset( $optionsStatus['swki_status_disable_carousel'] ) ? $optionsStatus['swki_status_disable_carousel'] : '0' ); 
$disableCarousel      = ( isset( $args['status-disable-carousel'] ) ? $args['status-disable-carousel'] : $disableCarousel ); 
$enableClassic        = ( isset( $optionsStatus['swki_status_enable_classic'] ) ? $optionsStatus['swki_status_enable_classic'] : '0' ); 
$enableClassic        = ( isset( $args['status-enable-classic'] ) ? $args['status-enable-classic'] : $enableClassic );
$classicOnlineText    = ( isset( $optionsStatus['swki_status_classic_online_text']  ) && !empty( $optionsStatus['swki_status_classic_online_text'] ) ? $optionsStatus['swki_status_classic_online_text']  : 'Live Now! Click to View.' ); 
$classicOnlineText    = ( isset( $args['status-classic-online-text'] ) ? $args['status-classic-online-text'] : $classicOnlineText );
$classicOfflineText   = ( isset( $optionsStatus['swki_status_classic_offline_text'] ) && !empty( $optionsStatus['swki_status_classic_offline_text'] ) ? $optionsStatus['swki_status_classic_offline_text'] : 'Currently Offline.' );
$classicOfflineText   = ( isset( $args['status-classic-offline-text'] ) ? $args['status-classic-offline-text'] : $classicOfflineText );

if (ski_fs()->can_use_premium_code()) {
    $tileLayout         = ( isset( $args['tile-layout'] ) ? $args['tile-layout'] : $options['swki_tile_layout'] );
    $hoverEffect        = ( isset( $args['hover-effect'] ) ? $args['hover-effect'] : $options['swki_hover_effect'] );
} else {
    $tileLayout         = 'compact';
    $hoverEffect        = 'none';
}

echo    '<div class="cp-streamweasels-kick cp-streamweasels-kick--'.$uuid.' cp-streamweasels-kick--'.$layout.' cp-streamweasels-kick--hover-'.$hoverEffect.' cp-streamweasels-kick--placement-'.$placement.' cp-streamweasels-kick--position-'.$verticalPlacement.' cp-streamweasels-kick--position-'.$horizontalPlacement.' cp-streamweasels-kick--hide-'.$hideOffline.' " data-uuid="'.$uuid.'" data-enable-classic="'.$enableClassic.'" data-classic-online-text="'.$classicOnlineText.'" data-classic-offline-text="'.$classicOfflineText.'" data-channel-count="0" data-online="0" data-offline="0">
            <div class="cp-streamweasels-kick__inner">
                <div class="cp-streamweasels-kick__loader">
                    <div class="spinner-item"></div>
                    <div class="spinner-item"></div>
                    <div class="spinner-item"></div>
                    <div class="spinner-item"></div>
                    <div class="spinner-item"></div>
                </div>
                <div class="cp-streamweasels-kick__player"></div>
                <div class="cp-streamweasels-kick__offline-wrapper"></div>
                <div class="cp-streamweasels-kick__twitch-logo cp-streamweasels-kick__twitch-logo--'.(!$customLogo ? 'twitch' : 'custom').'" style="background-color:'.$logoBackgroundColour.'">
                    '.($customLogo ? '<img src="'.$customLogo.'">' : '').'
                </div>
                <div class="cp-streamweasels-kick__streams cp-streamweasels-kick__streams--'.$tileLayout.' cp-streamweasels-kick__streams--hover-'.$hoverEffect.' cp-streamweasels-kick__streams--carousel-'.$disableCarousel.'"></div>
            </div>
        </div>';