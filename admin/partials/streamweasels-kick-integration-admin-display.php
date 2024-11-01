<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.streamweasels.com
 * @since      1.0.0
 *
 * @package    Streamweasels_Kick_Integration
 * @subpackage Streamweasels_Kick_Integration/admin/partials
 */
?>

<?php 
switch ( get_admin_page_title() ) {
    case '[Layout] Wall':
        $activePage = 'wall';
        break;
    case '[Layout] Player':
        $activePage = 'player';
        break;
    case '[Layout] Rail':
        $activePage = 'rail';
        break;
    case '[Layout] Feature':
        $activePage = 'feature';
        break;
    case '[Layout] Status':
        $activePage = 'status';
        break;
    case '[Layout] Nav':
        $activePage = 'nav';
        break;
    case '[Layout] Vods':
        $activePage = 'vods';
        break;
    case '[Layout] Showcase':
        $activePage = 'showcase';
        break;
    default:
        $activePage = 'wall';
}
?>
<div class="cp-streamweasels-kick wrap">
    <div class="cp-streamweasels-kick__header">
        <div class="cp-streamweasels-kick__header-logo">
            <img src="<?php 
echo plugin_dir_url( __FILE__ ) . '../img/sw-full-logo.png';
?>">
        </div>
        <div class="cp-streamweasels-kick__header-title">
            <h3>StreamWeasels</h3>
            <p>Kick Integration <?php 
?>for WordPress</p>
        </div>        
    </div>
    <div class="cp-streamweasels-kick__wrapper">
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <div class="inside">
                            <div class="setup-instructions">
                                <div class="setup-instructions--left">
                                    <h3>Setup Guide</h3>
                                    <p>StreamWeasels plugins now use the <strong>new Kick API</strong>, this unlocks new possibilities, better performance and more reliability!</p>
                                    <h4>StreamWeasels Blocks</h4>
                                    <p>If your site uses the Block Editor (Gutenberg) you can add our Kick Blocks directly to your page. Look out for the Kick Integration Block and the Kick Embed Block, and learn more in our <a href="https://support.streamweasels.com/article/80-kick-integration-blocks-guide" target="_blank">Kick Integration Blocks Guide</a>.</p>
                                    <h4>StreamWeasels Shortcodes</h4>
                                    <p>You can simply use the shortcode <code>[sw-kick]</code> to display your Kick Integration on your page, using all the settings set here on this page. Learn more in our <a href="https://support.streamweasels.com/article/81-kick-integration-shortcode-guides" target="_blank">Kick Integration Shortcodes Guide</a></p> 
                                    <h4>Advanced Shortcodes</h4>
                                    <p>For more complicated integrations, for example if you have more than one Kick Integration on your site, you can use shortcode attributes to change the settings directly on your shortcode.<br><br><strong>For example</strong>:<br><br>
                                    <?php 
if ( $activePage == 'vods' ) {
    ?>
                                        <code>[sw-kick layout="<?php 
    echo $activePage;
    ?>" channel="xqc"]</code></p>
                                    <?php 
} else {
    ?>
                                        <code>[sw-kick layout="<?php 
    echo $activePage;
    ?>" channels="xqc,adin,amouranth" autoplay="1"]</code></p>
                                    <?php 
}
?>
                                    <p>The complete list of shortcode attributes can be viewed in our <a href="https://support.streamweasels.com/article/81-kick-integration-shortcode-guides" target="_blank">Kick Integration Shortcode Guide</a>.</p>
                                </div>
                                <div class="setup-instructions--right">
                                    <!-- <h3>Video Guide</h3> -->
                                    <!-- <iframe width="560" height="315" src="https://www.youtube.com/embed/JK06TumS6ho" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe> -->
                                </div>
                            </div>
                        </div>
                    </div>     
                    <form id="sw-form" method="post" action="options.php">
                        <?php 
if ( get_admin_page_title() == 'StreamWeasels' ) {
    ?>
                            <?php 
    settings_fields( 'swki_options' );
    ?>
                            <?php 
    // $this->swki_do_settings_sections('swki_api_fields', 'dashicons-format-video', 'This plugin requires an active Kick Auth Token to work. <a href="https://support.streamweasels.com/article/12-how-to-setup-a-client-id-and-client-secret" target="_blank">Click here</a> to learn more about Kick Auth Tokens.', 'free');
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_shortcode_fields',
        'dashicons-shortcode',
        'You can add Kick Integration to your page with either Kick Integration <a href="https://support.streamweasels.com/article/80-kick-integration-blocks-guide" target="_blank">Blocks</a> or <a href="https://support.streamweasels.com/article/81-kick-integration-shortcode-guides" target="_blank">Shortcodes</a>. For shortcodes, simply use the shortcode <code>[sw-kick]</code> for your Kick Integration. For more complicated integrations you can change the attributes directly on your shortcode. <a href="https://support.streamweasels.com/article/81-kick-integration-shortcode-guides" target="_blank">Click here</a> to view our full list of StreamWeasels shortcode attributes.',
        'free'
    );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_main_fields',
        'dashicons-slides',
        'Here you can define the channels to display in your Kick integration.',
        'free'
    );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_layout_fields',
        'dashicons-slides',
        'Here you can select the layout of your Kick integration.',
        'free'
    );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_embed_fields',
        'dashicons-format-video',
        'Here you can change the settings for the Kick embed in your Kick integration.',
        'pro'
    );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_offline_fields',
        'dashicons-slides',
        'Here you can change the settings for the offline channels in your Kick integration.',
        'pro'
    );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_autoplay_fields',
        'dashicons-format-video',
        'Here you can change the settings for the autoplay in your Kick Integration.',
        'pro'
    );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_appearance_fields',
        'dashicons-admin-appearance',
        'Here you can change the overall appearance of your Kick integration.',
        'pro'
    );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_tile_fields',
        'dashicons-grid-view',
        'Here you can change the finer appearance details of your Kick integration. ',
        'pro'
    );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_hover_fields',
        'dashicons-search',
        'Here you can change what happens when you hover over channels in your Kick integration.',
        'pro'
    );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_debug_fields',
        'dashicons-admin-tools',
        'If your StreamWeasels plugin is encountering errors with the Kick API, those errors will be output below. You can get in touch with us <a href="https://www.streamweasels.com/contact/" target="_blank">here</a>, please include a copy of any errors that might be relevant from below.',
        'free'
    );
    ?>
                        <?php 
}
?>
                        <?php 
if ( get_admin_page_title() == 'Translations' ) {
    ?>
                            <?php 
    settings_fields( 'swki_translations' );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_translations_fields',
        'dashicons-translation',
        'This page allows you to translate strings found within the StreamWeasels plugins.',
        'free'
    );
    ?>
                        <?php 
}
?>                         
                        <?php 
if ( get_admin_page_title() == '[Layout] Wall' ) {
    ?>
                            <?php 
    settings_fields( 'swki_options_wall' );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_wall_shortcode_fields',
        'dashicons-format-video',
        'You can simply use the shortcode <span class="advanced-shortcode">[sw-kick layout="wall"]</span> for your Kick Integration. For more complicated integrations you can change the settings directly on your shortcode. <a href="https://support.streamweasels.com/article/81-kick-integration-shortcode-guides" target="_blank">Click here</a> to learn more about StreamWeasels shortcodes.',
        'free'
    );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_wall_fields',
        'dashicons-format-video',
        'Custom fields for your Kick Integration [Layout] Wall.',
        'free'
    );
    ?>
                        <?php 
}
?>                        
                        <?php 
if ( get_admin_page_title() == '[Layout] Player' ) {
    ?>
                            <?php 
    settings_fields( 'swki_options_player' );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_player_shortcode_fields',
        'dashicons-format-video',
        'You can simply use the shortcode <span class="advanced-shortcode">[sw-kick layout="player"]</span> for your Kick Integration. For more complicated integrations you can change the settings directly on your shortcode. <a href="https://support.streamweasels.com/article/81-kick-integration-shortcode-guides" target="_blank">Click here</a> to learn more about StreamWeasels shortcodes.',
        'free'
    );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_player_fields',
        'dashicons-format-video',
        'Custom fields for your Kick Integration [Layout] Player.',
        'free'
    );
    ?>
                        <?php 
}
?>
                        <?php 
if ( get_admin_page_title() == '[Layout] Rail' ) {
    ?>
                            <?php 
    settings_fields( 'swki_options_rail' );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_rail_shortcode_fields',
        'dashicons-format-video',
        'You can simply use the shortcode <span class="advanced-shortcode">[sw-kick layout="rail"]</span> for your Kick Integration. For more complicated integrations you can change the settings directly on your shortcode. <a href="https://support.streamweasels.com/article/81-kick-integration-shortcode-guides" target="_blank">Click here</a> to learn more about StreamWeasels shortcodes.',
        'free'
    );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_rail_fields',
        'dashicons-format-video',
        'Custom fields for your Kick Integration [Layout] Rail.',
        'free'
    );
    ?>
                        <?php 
}
?>  
                        <?php 
if ( get_admin_page_title() == '[Layout] Feature' ) {
    ?>
                            <?php 
    settings_fields( 'swki_options_feature' );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_feature_shortcode_fields',
        'dashicons-format-video',
        'You can simply use the shortcode <span class="advanced-shortcode">[sw-kick layout="feature"]</span> for your Kick Integration. For more complicated integrations you can change the settings directly on your shortcode. <a href="https://support.streamweasels.com/article/81-kick-integration-shortcode-guides" target="_blank">Click here</a> to learn more about StreamWeasels shortcodes.',
        'free'
    );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_feature_fields',
        'dashicons-format-video',
        'Custom fields for your Kick Integration [Layout] Feature.',
        'free'
    );
    ?>
                        <?php 
}
?>    
                        <?php 
if ( get_admin_page_title() == '[Layout] Status' ) {
    ?>
                            <?php 
    settings_fields( 'swki_options_status' );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_status_shortcode_fields',
        'dashicons-format-video',
        'You can simply use the shortcode <span class="advanced-shortcode">[sw-kick layout="status"]</span> for your Kick Integration. For more complicated integrations you can change the settings directly on your shortcode. <a href="https://support.streamweasels.com/article/81-kick-integration-shortcode-guides" target="_blank">Click here</a> to learn more about StreamWeasels shortcodes.',
        'free'
    );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_status_placement_fields',
        'dashicons-format-video',
        'Custom fields for your Kick Integration [Layout] Status.',
        'free'
    );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_status_appearance_fields',
        'dashicons-format-video',
        'Custom fields for your Kick Integration [Layout] Status.',
        'free'
    );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_status_classic_fields',
        'dashicons-format-video',
        'Custom fields for your Kick Integration [Layout] Status.',
        'free'
    );
    ?>
                        <?php 
}
?> 
                        <?php 
if ( get_admin_page_title() == '[Layout] Nav' ) {
    ?>
                            <?php 
    settings_fields( 'swki_options_nav' );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_nav_shortcode_fields',
        'dashicons-format-video',
        'There is no need to use a shortcode for this plugin. To get Kick Nav to show on your page, navigate to <code>Appearance -> Menus</code>, look for Kick Nav and add either a Channel Status or Team Status (or both!) to your navigation.',
        'free'
    );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_nav_fields',
        'dashicons-format-video',
        'Custom fields for your Kick Integration [Layout] Nav.',
        'free'
    );
    ?>
                        <?php 
}
?>
                        <?php 
if ( get_admin_page_title() == '[Layout] Vods' ) {
    ?>
                            <?php 
    settings_fields( 'swki_options_vods' );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_vods_shortcode_fields',
        'dashicons-format-video',
        'You can simply use the shortcode <span class="advanced-shortcode">[sw-kick layout="vods"]</span> for your Kick Integration. For more complicated integrations you can change the settings directly on your shortcode. <a href="https://support.streamweasels.com/article/81-kick-integration-shortcode-guides" target="_blank">Click here</a> to learn more about StreamWeasels shortcodes.',
        'free'
    );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_vods_fields',
        'dashicons-format-video',
        'Custom fields for your Kick Integration [Layout] Vods.',
        'free'
    );
    ?>
                        <?php 
}
?> 
                        <?php 
if ( get_admin_page_title() == '[Layout] Showcase' ) {
    ?>
                            <?php 
    settings_fields( 'swki_options_showcase' );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_showcase_shortcode_fields',
        'dashicons-format-video',
        'You can simply use the shortcode <span class="advanced-shortcode">[sw-kick layout="showcase"]</span> for your Kick Integration. For more complicated integrations you can change the settings directly on your shortcode. <a href="https://support.streamweasels.com/article/81-kick-integration-shortcode-guides" target="_blank">Click here</a> to learn more about StreamWeasels shortcodes.',
        'free'
    );
    ?>
                            <?php 
    $this->swki_do_settings_sections(
        'swki_showcase_fields',
        'dashicons-format-video',
        'Custom fields for your Kick Integration [Layout] Showcase.',
        'free'
    );
    ?>
                        <?php 
}
?>                                                                                                                                             
                    </form>
                </div>
            </div>
            <div id="postbox-container-1" class="postbox-container">
                <?php 
include 'streamweasels-kick-integration-admin-sidebar.php';
?>
            </div>
        </div>
    </div>
</div>