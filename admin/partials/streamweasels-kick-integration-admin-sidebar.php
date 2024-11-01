<div class="meta-box-sortables">
    <div class="postbox">
        <h3>StreamWeasels Links</h3>
        <div class="inside">
            <p>WordPress Themes and Plugins for Kick Streamers.</p>
            <ul>
                <li>
                    <a href="https://support.streamweasels.com/article/79-getting-started-with-kick-integration" target="_blank">Getting Started with Kick Integration!</a>
                </li>
                <li>
                    <a href="https://support.streamweasels.com/article/81-kick-integration-shortcode-guides" target="_blank">StreamWeasels Shortcodes Guide</a>
                </li>                
                <li>
                    <a href="https://support.streamweasels.com/article/80-kick-integration-blocks-guide" target="_blank">StreamWeasels Blocks Guide</a>
                </li>                              
                <li>
                    <a href="https://www.youtube.com/channel/UCo885jUiOeyhtHDFUbdx8rQ" target="_blank">Check out our YouTube Guides</a>
                </li>       
                <li>
                    <a href="https://twitter.com/StreamWeasels" target="_blank">Follow us on Twitter</a>
                </li>
                <li>
                    <a href="https://www.streamweasels.com/contact/" target="_blank">Need Help? Get in touch!</a>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="meta-box-sortables">
    <div class="postbox">
        <h3><span class="dashicons dashicons-discord"></span>Join us on Discord</h3>
        <div class="inside">
            <h4>Got a question? Ask us in Discord.</h4>
            <p>Get support from StreamWeasels developers and connect with like-minded Kick & WordPress users.</p>
            <div style="text-align:center">
                <?php 
?>
                    <a class="button button-primary" href="https://discord.gg/HSwfPbm" target="_blank" style="background-color: #6E85D2;">JOIN DISCORD</a>
                <?php 
?>
            </div>
        </div>
    </div>
</div>

<div class="meta-box-sortables">
    <div class="postbox">
        <h3><span class="dashicons dashicons-info" style="color: #022E4C;"></span>New Status Bar Plugin!</h3>
        <div class="inside">
            <p>Check out our new Twitch / YouTube / Kick Online Status Bar plugin for WordPress!</p>
            <h4>StreamWeasels Status Bar</h4>
            <img src="<?php 
echo esc_url( SWKI_PLUGIN_DIR . '/admin/img/status-thumbnail.png' );
?>" style="max-width:128px;margin:0 auto;display:block">
            <p>Display Twitch / YouTube / Kick Online Status!</p>
            <div style="text-align:center">
                <a class="button button-primary" style="background-color: #026E95;border-color: #022E4C;" target="_blank" href="/wp-admin/plugin-install.php?s=streamweasels status bar&tab=search&type=term">Install Online Status Bar</a>    
            </div>
        </div>
    </div>
</div>

<div class="meta-box-sortables">
    <div class="postbox">
        <h3><span class="dashicons dashicons-youtube" style="color: red;"></span>New YouTube Plugin</h3>
        <div class="inside">
            <p>Check out our new YouTube integration plugin for WordPress!</p>
            <h4>StreamWeasels YouTube Integration</h4>
            <img src="<?php 
echo SWKI_PLUGIN_DIR . '/admin/img/youtube-thumbnail.png';
?>" style="max-width:128px;margin:0 auto;display:block">       
            <p>Display YouTube Channels, Playlists and Livestreams!</p>
            <div style="text-align:center">
                <a class="button button-primary" style="background-color: #FF0000;border-color: #BE0404;" target="_blank" href="/wp-admin/plugin-install.php?s=streamweasels youtube integration&tab=search&type=term">Install YouTube Integration</a>    
            </div>
        </div>
    </div>
</div>

<div class="meta-box-sortables">
    <div class="postbox">
        <h3>Kick Integration Layouts</h3>
        <div class="inside">
            <p>Upgrade your Kick Integration with one of our PRO layouts.</p>
            <?php 
$swki_layout_options = $this->swki_twitch_get_layout_options();
$wallCheck = ( in_array( "Wall", $swki_layout_options ) ? '<span style="color:green;"><strong><span class="dashicons dashicons-yes"></span>Active</strong>' : '<span style="color:red;"><span class="dashicons dashicons-no-alt"></span>Not Active' );
$playerCheck = ( in_array( "Player", $swki_layout_options ) ? '<span style="color:green;"><strong><span class="dashicons dashicons-yes"></span>Active</strong>' : '<span style="color:red;"><span class="dashicons dashicons-no-alt"></span>Coming Soon' );
$railCheck = ( in_array( "Rail", $swki_layout_options ) ? '<span style="color:green;"><strong><span class="dashicons dashicons-yes"></span>Active</strong>' : '<span style="color:red;"><span class="dashicons dashicons-no-alt"></span>Coming Soon' );
$featureCheck = ( in_array( "Feature", $swki_layout_options ) ? '<span style="color:green;"><strong><span class="dashicons dashicons-yes"></span>Active</strong>' : '<span style="color:red;"><span class="dashicons dashicons-no-alt"></span>Not Active' );
$statusCheck = ( in_array( "Status", $swki_layout_options ) ? '<span style="color:green;"><strong><span class="dashicons dashicons-yes"></span>Active</strong>' : '<span style="color:red;"><span class="dashicons dashicons-no-alt"></span>Coming Soon' );
$vodsCheck = ( in_array( "Vods", $swki_layout_options ) ? '<span style="color:green;"><strong><span class="dashicons dashicons-yes"></span>Active</strong>' : '<span style="color:red;"><span class="dashicons dashicons-no-alt"></span>Not Active' );
?>
            <ul>
                <li>
                    <strong>FREE Layouts</strong>
                </li>    
                <hr>
                <br>                               
                <li>
                    <a href="#free-layouts" style="display:inline-block;width:40%">Kick Wall</a><?php 
echo $wallCheck;
?>
                </li>
                <li>
                    <a href="#free-layouts" style="display:inline-block;width:40%">Kick Vods</a><?php 
echo $vodsCheck;
?>
                </li>                                              
                <li>
                    <a href="#free-layouts" style="display:inline-block;width:40%">Kick Status</a><?php 
echo $statusCheck;
?>
                </li>                                                                                               
            </ul>     
            <ul>
                <li>
                    <strong>PRO Layouts</strong>
                </li>    
                <hr>
                <br>                               
                <li>
                    <a href="#paid-layouts" style="display:inline-block;width:40%">Kick Feature</a><?php 
echo $featureCheck;
?>
                </li>                                                                                                            
            </ul>
        </div>
    </div>
</div>

<div class="meta-box-sortables">
    <div class="postbox">
        <h3>Health Check</h3>
        <div class="inside">
            <p>There are a few things we need to check before your plugin will work.</p>
            <?php 
$connection_status = ( isset( $this->baseOptions['swki_api_connection_status'] ) ? $this->baseOptions['swki_api_connection_status'] : '' );
$connection_token = ( isset( $this->baseOptions['swki_api_access_token'] ) ? $this->baseOptions['swki_api_access_token'] : '' );
$connection_expires = ( isset( $this->baseOptions['swki_api_access_token_expires'] ) ? $this->baseOptions['swki_api_access_token_expires'] : '' );
$connection_expires_meta = '';
$dateTimestamp1 = '';
$dateTimestamp2 = '';
if ( $connection_token !== '' ) {
    $twitchCheck = '<span style="color:green;"><strong><span class="dashicons dashicons-yes"></span>Connected</strong></span>';
} else {
    $twitchCheck = '<span style="color:red;"><strong><span class="dashicons dashicons-no"></span>Not Connected</strong></span>';
}
if ( $connection_expires !== '' ) {
    $dateTimestamp1 = strtotime( $connection_expires );
    $dateTimestamp2 = strtotime( date( 'Y-m-d' ) );
    if ( $dateTimestamp2 > $dateTimestamp1 ) {
        $twitchCheck = '<span style="color:red;"><strong><span class="dashicons dashicons-no"></span>Not Connected (expired!)</strong></span>';
    }
}
if ( function_exists( 'curl_version' ) ) {
    $curlVersion = curl_version();
    $curlCheck = '<span style="color:green;"><strong><span class="dashicons dashicons-yes"></span>Enabled (' . $curlVersion['version'] . ')</strong></span>';
}
?>
            <ul>    
                <li>
                    <strong style="display:inline-block;width:40%">PHP cURL</strong> <?php 
echo $curlCheck;
?>
                </li>                                                                                    
            </ul>
        </div>
    </div>
</div>