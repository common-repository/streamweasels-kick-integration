<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.streamweasels.com
 * @since      1.0.0
 *
 * @package    Streamweasels_Feature_Pro
 * @subpackage Streamweasels_Feature_Pro/public/partials
 */
?>

<?php
$options            = get_option('swki_options');
$layout             = sanitize_text_field( $args['layout'] ?? $options['swki_layout'] );
$optionsFeature     = get_option('swki_options_feature');
$embedMarkupTop = $embedMarkupBottom = $embedPosition = '';
$tileLayout         = sanitize_text_field( $args['tile-layout'] ?? $options['swki_tile_layout'] );
$hoverEffect        = sanitize_text_field( $args['hover-effect'] ?? $options['swki_hover_effect'] );
$title              = sanitize_text_field( $args['title'] ?? $options['swki_title'] );
$subtitle           = sanitize_text_field( $args['subtitle'] ?? $options['swki_subtitle'] );
$embedTitlePosition = sanitize_text_field( $args['title-position'] ?? $options['swki_embed_title_position'] );     
$maxWidth           = sanitize_text_field( $args['max-width'] ?? $options['swki_max_width'] );   
$showTitleTop       = ($embedTitlePosition == 'top' ? '<div class="cp-streamweasels-kick__title"></div>' : '');
$showTitleBottom    = ($embedTitlePosition == 'bottom' ? '<div class="cp-streamweasels-kick__title"></div>' : '');
$embedPosition      = sanitize_text_field( $args['feature-embed-position'] ?? $optionsFeature['swki_feature_embed_position'] ?? 'inside' );
$enableSkew         = sanitize_text_field( $args['feature-skew'] ?? $optionsFeature['swki_feature_skew'] ?? '0' );

if ($embedPosition == 'above') {
    $embedMarkupTop = 
        $showTitleTop.'
        <div class="cp-streamweasels-kick__player"></div>
        '.$showTitleBottom.'
        <div class="cp-streamweasels-kick__offline-wrapper"></div>';
}
if ($embedPosition == 'below') {
    $embedMarkupBottom = 
        $showTitleTop.'
        <div class="cp-streamweasels-kick__player"></div>
        '.$showTitleBottom.'
        <div class="cp-streamweasels-kick__offline-wrapper"></div>';
}

echo    '<div class="cp-streamweasels-kick cp-streamweasels-kick--'.$uuid.' cp-streamweasels-kick--'.$layout.' cp-streamweasels-kick--'.$tileLayout.' cp-streamweasels-kick--'.$embedPosition.'" data-uuid="'.$uuid.'" data-channel-count="0" data-online="0" data-offline="0" style="'.(($maxWidth !== 'none') ? 'max-width:'.$maxWidth.'px; margin: 0 auto;' : '').'"  data-skew="'.esc_attr($enableSkew).'">
            <div class="cp-streamweasels-kick__inner" style="'.(($maxWidth !== 'none') ? 'max-width:'.$maxWidth.'px' : '').'">
                <div class="cp-streamweasels-kick__loader">
                    <div class="spinner-item"></div>
                    <div class="spinner-item"></div>
                    <div class="spinner-item"></div>
                    <div class="spinner-item"></div>
                    <div class="spinner-item"></div>
                </div>
                '.$embedMarkupTop.'
                <div class="cp-streamweasels-kick__streams cp-streamweasels-kick__streams--'.$tileLayout.' cp-streamweasels-kick__streams--hover-'.$hoverEffect.'"></div>
                '.$embedMarkupBottom.'
            </div>
        </div>';