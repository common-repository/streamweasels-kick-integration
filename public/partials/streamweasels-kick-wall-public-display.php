<?php
$options            = get_option('swki_options');
$layout             = ( isset( $args['layout'] ) ? $args['layout'] : $options['swki_layout'] ); 
$optionsWall        = get_option('swki_options_wall');
$tileColumnCount    = ( isset( $optionsWall['swki_wall_column_count'] ) ? $optionsWall['swki_wall_column_count'] : '4' );
$tileColumnCount    = ( isset( $args['wall-column-count'] ) ? $args['wall-column-count'] : $tileColumnCount );
$tileColumnSpacing  = ( isset( $optionsWall['swki_wall_column_spacing'] ) ? $optionsWall['swki_wall_column_spacing'] : '10' );
$tileColumnSpacing  = ( isset( $args['wall-column-spacing'] ) ? $args['wall-column-spacing'] : $tileColumnSpacing );
$titleMarkup        = '';
$subtitleMarkup     = '';
if (ski_fs()->can_use_premium_code()) {
    $tileLayout         = ( isset( $args['tile-layout'] ) ? $args['tile-layout'] : $options['swki_tile_layout'] );
    $hoverEffect        = ( isset( $args['hover-effect'] ) ? $args['hover-effect'] : $options['swki_hover_effect'] );
    $title 				= ( isset( $args['title'] ) ? $args['title'] : $options['swki_title'] );
    $subtitle 			= ( isset( $args['subtitle'] ) ? $args['subtitle'] : $options['swki_subtitle'] );
    $embedTitlePosition = ( isset( $args['title-position'] ) ? $args['title-position'] : $options['swki_embed_title_position'] );
    $showTitleTop       = ($embedTitlePosition == 'top' ? '<div class="cp-streamweasels-kick__title"></div>' : '');
    $showTitleBottom    = ($embedTitlePosition == 'bottom' ? '<div class="cp-streamweasels-kick__title"></div>' : '');
    $maxWidth           = ( $args['max-width'] ?? $options['swki_max_width'] ?? '1440');
} else {
    $tileLayout         = 'detailed';
    $hoverEffect        = 'play';
    $title 				= '';
    $subtitle 			= '';
    $embedTitlePosition = '';
    $showTitleTop       = '';
    $showTitleBottom    = '';
    $maxWidth           = '1440';
}
if ($title !== '') {
    $titleMarkup = '<h2 class="cp-streamweasels-kick__heading">'.$title.'</h2>';
}
if ($subtitle !== '') {
    $subtitleMarkup = '<h3 class="cp-streamweasels-kick__subheading">'.$subtitle.'</h3>';
}
?>
<div class="cp-streamweasels-kick cp-streamweasels-kick--wall cp-streamweasels-kick--<?php echo $uuid; ?> cp-streamweasels-kick--<?php echo $layout; ?>" data-uuid="<?php echo $uuid; ?>" data-channel-count="0" data-online="0" data-offline="0">
    <div class="cp-streamweasels-kick__inner" style="<?php echo (($maxWidth !== 'none') ? 'max-width:'.$maxWidth.'px' : ''); ?>">
    <?php echo $titleMarkup; ?>
    <?php echo $subtitleMarkup; ?>
        <div class="cp-streamweasels-kick__loader">
            <div class="spinner-item"></div>
            <div class="spinner-item"></div>
            <div class="spinner-item"></div>
            <div class="spinner-item"></div>
            <div class="spinner-item"></div>
        </div>
        <?php echo $showTitleTop; ?>
        <div class="cp-streamweasels-kick__player"></div>
        <?php echo $showTitleBottom; ?>
        <div class="cp-streamweasels-kick__offline-wrapper"></div>
        <div class="cp-streamweasels-kick__streams cp-streamweasels-kick__streams--<?php echo $tileLayout ?> cp-streamweasels-kick__streams--hover-<?php echo $hoverEffect ?>" style="<?php echo (($tileColumnSpacing) ? 'grid-gap:'.$tileColumnSpacing.'px;' : '').(($tileColumnCount) ? 'grid-template-columns: repeat('.$tileColumnCount.',minmax(100px,1fr))' : '') ?>"></div>
    </div>
</div>