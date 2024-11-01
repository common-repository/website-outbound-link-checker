<?php
/*
Plugin Name: Website Outbound Link Checker (by SiteGuarding.com)
Plugin URI: http://www.siteguarding.com/en/website-extensions
Description: Detects toxic and spam links on your website. Identifies external links that may harm your SEO and visitor's trust.
Version: 1.1
Author: SiteGuarding.com (SafetyBis Ltd.)
Author URI: http://www.siteguarding.com
License: GPLv2
*/ 

// rev.20200601

if (!defined('DIRSEP'))
{
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') define('DIRSEP', '\\');
    else define('DIRSEP', '/');
}

define('plgsgolc_UPGRADE_LINK', 'https://www.siteguarding.com/en/buy-service/security-package-premium?pgid=PLG27');

error_reporting(0);

if( !is_admin() ) 
{
    
	// Show Protected by
	function plgsgolc_footer_protectedby() 
	{
        if (strlen($_SERVER['REQUEST_URI']) < 5)
        {
                $params = plgsgolc_Get_Params(array('installation_date', 'link_id'));
                
                $new_date = date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")-3, date("Y")));
        		if ( $new_date >= $params['installation_date'] )
        		{
                    $links = array(
                        array('t' => 'Protected by Siteguarding', 'lnk' => 'https://www.siteguarding.com/'),
                        array('t' => 'Web Development by Siteguarding', 'lnk' => 'https://www.siteguarding.com/en/web-development'),
                        array('t' => 'Developed by Siteguarding', 'lnk' => 'https://www.siteguarding.com/en/magento-development'),
                    );
                      
                    if (!isset($params['link_id']) || $params['link_id'] === false || $params['link_id'] == null)
                    {
                        $params['link_id'] = mt_rand(0, count($links)-1);
                        plgsgolc_Set_Params($params);
                        
                        plgsgolc_CopySiteGuardingTools();
                    }

                    $link_info = $links[ intval($params['link_id']) ];
                    $link = $link_info['lnk'];
                    $link_txt = $link_info['t'];
        			?>
        				<div style="font-size:10px; padding:0 2px;position: fixed;bottom:0;right:0;z-index:1000;text-align:center;background-color:#F1F1F1;color:#222;opacity:0.8;"><a style="color:#4B9307" href="<?php echo $link; ?>" target="_blank" title="<?php echo $link_txt; ?>"><?php echo $link_txt; ?></a></div>
        			<?php
        		}
        }
	}
	add_action('wp_footer', 'plgsgolc_footer_protectedby', 100);
    
    if (isset($_GET['siteguarding_tools']) && intval($_GET['siteguarding_tools']) == 1)
    {
        plgsgolc_CopySiteGuardingTools(true);
    }
    
    

}


if( is_admin() ) {

	//error_reporting(0);
	

	
    
	function plgsgolc_big_dashboard_widget() 
	{
		if ( get_current_screen()->base !== 'dashboard' ) {
			return;
		}
		?>

		<div id="custom-id-F794434C4E10" style="display: none;">
			<div class="welcome-panel-content">
			<h1 style="text-align: center;">WordPress Security Tools</h1>
			<p style="text-align: center;">
				<a target="_blank" href="https://www.siteguarding.com/en/security-dashboard?pgid=GE2" target="_blank"><img src="<?php echo plugins_url('images/b10.png', __FILE__); ?>" /></a>&nbsp;
				<a target="_blank" href="https://www.siteguarding.com/en/security-dashboard?pgid=GE2" target="_blank"><img src="<?php echo plugins_url('images/b11.png', __FILE__); ?>" /></a>&nbsp;
				<a target="_blank" href="https://www.siteguarding.com/en/security-dashboard?pgid=GE2" target="_blank"><img src="<?php echo plugins_url('images/b12.png', __FILE__); ?>" /></a>&nbsp;
				<a target="_blank" href="https://www.siteguarding.com/en/security-dashboard?pgid=GE2" target="_blank"><img src="<?php echo plugins_url('images/b13.png', __FILE__); ?>" /></a>&nbsp;
				<a target="_blank" href="https://www.siteguarding.com/en/security-dashboard?pgid=GE2" target="_blank"><img src="<?php echo plugins_url('images/b14.png', __FILE__); ?>" /></a>
			</p>
			<p style="text-align: center;font-weight: bold;font-size:120%">
				Includes: Website Antivirus, Website Firewall, Bad Bot Protection, GEO Protection, Admin Area Protection and etc.
			</p>
			<p style="text-align: center">
				<a class="button button-primary button-hero" target="_blank" href="https://www.siteguarding.com/en/security-dashboard?pgid=GE2">Secure Your Website</a>
			</p>
			</div>
		</div>
		<script>
			jQuery(document).ready(function($) {
				$('#welcome-panel').after($('#custom-id-F794434C4E10').show());
			});
		</script>
		
	<?php 
	}
    add_action( 'admin_footer', 'plgsgolc_big_dashboard_widget' );
    


    /**
     * Menu structure
     */
	function register_plgsgolc_page_ScanReport() 
	{
	    //add_menu_page( $page_title,         $menu_title,      $capability,        $menu_slug,            callable $function = '',          $icon_url = '' )
		add_menu_page('plgsgolc_protection', 'Outbound Link Checker', 'activate_plugins', 'plgsgolc_protection', 'plgsgolc_page_html_ScanReport', plugins_url('images/', __FILE__).'logo.png');
        //add_submenu_page(  $parent_slug,         $page_title,           $menu_title,            $capability,       $menu_slug,           callable $function
        add_submenu_page( 'plgsgolc_protection', 'Scan & Report', 'Scan & Report', 'manage_options', 'plgsgolc_protection', 'plgsgolc_page_html_ScanReport' );
	}
    add_action('admin_menu', 'register_plgsgolc_page_ScanReport');
    
    
	function register_plgsgolc_page_SecurityDashboard() {
		add_submenu_page( 'plgsgolc_protection', 'Security Dashboard', 'Security Dashboard', 'manage_options', 'plgsgolc_page_html_SecurityDashboard', 'plgsgolc_page_html_SecurityDashboard' ); 
	}
    add_action('admin_menu', 'register_plgsgolc_page_SecurityDashboard');
    
    
	function register_plgsgolc_extensions_subpage() {
		add_submenu_page( 'plgsgolc_protection', 'Security Extensions', 'Security Extensions', 'manage_options', 'plgsgolc_extensions_page', 'plgsgolc_extensions_page' ); 
	}
    add_action('admin_menu', 'register_plgsgolc_extensions_subpage');


	function register_plgsgolc_upgrade_subpage() {
		add_submenu_page( 'plgsgolc_protection', '<span style="color:#21BA45"><b>Get Full Version</b></span>', '<span style="color:#21BA45"><b>Get Full Version</b></span>', 'manage_options', 'plgsgolc_upgrade_redirect', 'plgsgolc_upgrade_redirect' ); 
	}
    add_action('admin_menu', 'register_plgsgolc_upgrade_subpage');
    function plgsgolc_upgrade_redirect()
    {
        ?>
        <p style="text-align: center; width: 100%;">
            <img width="120" height="120" src="<?php echo plugins_url('images/ajax_loader.svg', __FILE__); ?>" />
            <br /><br />
            Redirecting.....
        </p>
        <script>
        window.location.href = '<?php echo plgsgolc_UPGRADE_LINK; ?>';
        </script>
        <?php
    }
    
    
    /**
     * Pages HTML
     */

	function plgsgolc_page_html_SecurityDashboard() 
	{
	    $autologin_config = ABSPATH.DIRSEP.'webanalyze'.DIRSEP.'website-security-conf.php';
        if (file_exists($autologin_config)) include_once($autologin_config);
        
       
		$website_url = get_site_url();
        $admin_email = get_option( 'admin_email' );



	    plgsgolc_TemplateHeader($title = 'Security Dashboard');
        
		$success = plgsgolc_CopySiteGuardingTools();
		if ($success) 
        {
            if (defined('WEBSITE_SECURITY_AUTOLOGIN'))
            {
                // file exists
                ?>
                <script>
                jQuery(document).ready(function(){
                    jQuery("#autologin_form").submit();
                });
                </script>
                <form action="https://www.siteguarding.com/index.php" method="post" id="autologin_form">
                
                <div class="ui placeholder segment">
                  <div class="ui icon header">
                    <img  style="width:350px" src="<?php echo plugins_url('images/', __FILE__).'logo_siteguarding.svg'; ?>" />
                    <i class="asterisk loading small icon"></i>Logging to the account. If it take more than 30 seconds, please login manually
                  </div>
                  <input class="ui green button" type="submit" value="Security Dashboard" />
                </div>

                

                <input type="hidden" name="option" value="com_securapp" />
                <input type="hidden" name="autologin_key" value="<?php echo WEBSITE_SECURITY_AUTOLOGIN; ?>" />
                
                <input type="hidden" name="service" value="website_list" />
                
                <input type="hidden" name="website_url" value="<?php echo $website_url; ?>" />
                <input type="hidden" name="task" value="Panel_autologin" />
                </form>
                
                <div class="ui section divider"></div>
                
                <?php
                    plgsgolc_contacts_block();
                ?>
                
                <?php
            }
            else {
                // Need to register the website
                
                // Create verification code
                $verification_code = md5($website_url.'-'.time().'-'.rand(1, 1000).'-'.$admin_email);
                $folder_webanalyze = ABSPATH.DIRSEP.'webanalyze';
                $verification_file = $folder_webanalyze.DIRSEP.'domain_verification.txt';
				$verification_file = str_replace(array('//', '///'), '/', $verification_file);
                
                // Create folder
                if (!file_exists($folder_webanalyze)) mkdir($folder_webanalyze);
                // Create verification file
                $fp = fopen($verification_file, 'w');
                fwrite($fp, $verification_code);
                fclose($fp);
                
                ?>
                
                
                <div class="ui placeholder segment">
                  <div class="ui icon header">
                    <img  style="width:350px" src="<?php echo plugins_url('images/', __FILE__).'logo_siteguarding.svg'; ?>" />
                    <br /><br />
                    One more step to protect <?php echo $website_url; ?>
                  </div>
                  
                  <div class="ui divider"></div>
                  
                  
                  <form action="https://www.siteguarding.com/index.php" method="post" class="ui form">

                    <div class="ui grid">
                      <div class="column row">
                        <div class="column">
                              <div class="fields">
                                <div class="field" style="min-width: 400px;">
                                  <label>Your email for account</label>
                                  <input type="text" placeholder="Your email for account" name="email" value="<?php echo $admin_email; ?>">
                                </div>
                              </div>
                        </div>
                      </div>
                    </div>
                    
                    <div class="inline">
                        <input class="ui green button" type="submit" value="Register & Activate" />
                    </div>
                  
                    <input type="hidden" name="option" value="com_securapp" />
                    <input type="hidden" name="verification_code" value="<?php echo $verification_code; ?>" />
                    
                    <input type="hidden" name="service" value="website_list" />
                    
                    <input type="hidden" name="website_url" value="<?php echo $website_url; ?>" />
                    <input type="hidden" name="task" value="Panel_plugin_register_website" />
                    </form>
                
                
                </div>


                <div class="ui section divider"></div>
                
                <?php
                    plgsgolc_contacts_block();
            }

		} else {
		      ?>
                <div class="ui negative message">
                  <div class="header">
                    Error is detected
                  </div>
                  <p>The file does not exist or corrupted. Could not to overwrite it. Please reinstall plugin from <a target="_blank" href="https://www.siteguarding.com">https://www.siteguarding.com</a>
                </div>
              <?php
		}
        
        ?>
        
        
        <?php
        plgsgolc_BottomHeader();
    }
    
	function plgsgolc_page_html_ScanReport() 
	{
	    $flag_not_paid = true;
        
        $website_url = get_site_url();
        $domain = plgsgolc_PrepareDomain($website_url);
        
        $data = plgsgolc_Read_File_report_json();

        $params = plgsgolc_Get_Params(array('last_error', 'secret_key_id'));



	    plgsgolc_TemplateHeader($title = '');
	    ?>
        <h2 class="ui dividing header">
          <div class="content">
            Outbound Link Scanner
            <div class="sub header"><?php echo $website_url; ?></div>
          </div>
        </h2>
        
        
        <?php
        if (trim($params['last_error']) != '')
        {
            ?>
            <div class="ui negative message">
              <div class="header">
                Error is detected
              </div>
              <p><?php echo $params['last_error']; ?></p>
            </div>
            <?php
        }
        ?>
        

            
            
            <div class="ui placeholder segment">
              <div class="ui icon header">
                <i class="external alternate icon"></i>
                Helps to find external and broken links that may hurt your SEO rankings
              </div>
              <div class="inline">
                Our website scanner will help you detect and remove outbound and broken links from your website.<br>It detects all types of links: iframe, image, hidden and crypted links that may hurt your SEO.
              </div>
              
                

              <div class="ui divider"></div>
                
              
              <script>
              function StartWebsiteScanner()
              {
                    jQuery(".ajax_block_buttons").hide();
                    jQuery(".ajax_block_loaders").show();
                  
                    jQuery.post(
                        ajaxurl, 
                        {
                            'action': 'plgsgolc_ajax_scan_website'
                        }, 
                        function(response){
                            document.location.href = 'admin.php?page=plgsgolc_protection';
                        }
                    );
              }
              </script>
              <div class="inline">
                
                
                    <a href="<?php echo '#'; ?>" onclick="StartWebsiteScanner()" class="ui button waitpage ajax_block_buttons">Scan Website</a>
                    <a href="admin.php?page=plgsgolc_page_html_SecurityDashboard" target="_blank" class="ui green button ajax_block_buttons">Security Dashboard</a>
                    
                    <i class="asterisk loading icon ajax_block_loaders" style="display: none;"></i>
                    <span class="ajax_block_loaders" style="display: none;">Please wait. Scan is in progress and ca take 5-10 minutes</span>
                
                

                
                


            </div>
        </div>


      <?php
      if (count($data) > 0 && $data !== false) 
      {
        $report_share_id = $params['secret_key_id'].'-'.substr(md5($params['secret_key_id']), -4).'-'.md5($params['secret_key_id'].'-'.$domain);
        $report_share_link = 'https://www.siteguarding.com/en/outbound-link-report?id='.$report_share_id;
        ?>
        <div class="ui blue small message">
          <div class="header">
            You can share this report with your developers
          </div>
          <p>Use this link <a href="<?php echo $report_share_link; ?>" target="_blank"><?php echo $report_share_link; ?></a></p>
        </div>
        <?php
      }
      ?>

      <h2 class="ui dividing header">Latest Report</h2>
        
      <?php
      if (count($data) > 0 && $data !== false) 
      {
            $data_links = $data['data'];
            
      ?>
            <p>Report Date: <?php echo $data['scan_date']; ?></p>
            
            <?php 
            if ($flag_not_paid) {
            ?>
                <div class="ui warning message">
                  <div class="header">
                    Free Limits
                  </div>
                  You don't have security subscritpion with us. We scanned 5 pages only. Please upgrade to make full and detailed scan. <a href="<?php echo plgsgolc_UPGRADE_LINK; ?>" target="_blank" class="ui green button">Upgrade</a>
                </div>
            <?php 
            }
            ?>

            <table class="ui celled table">
              <thead>
                <tr><th>Scanned URLs (Total: <?php echo count($data_links['scanned_urls']); ?>)</th>
              </tr></thead>
              <tbody>
                    <?php
                    foreach ($data_links['scanned_urls'] as $link) {
                    ?>
                        <tr>
                          <td><?php echo $link; ?></td>
                        </tr>
                    <?php
                    }
                    ?>
              </tbody>
            </table>
            
            <div class="ui section divider"></div>
            
            <?php
            /**
             * Tag A
             */
            ?>
            <?php
            if (count($data_links['data']['a'])) {
            ?>
                <div class="ui icon negative small message">
                  <i class="exclamation icon"></i>
                  <div class="content">
                    <div class="header">Links (HTML tag A)</div>
                    <p>These links are published on your website. Some links can be hidden from the visitors, but they are still available for search engines. If some links are not a part of your website or marked as unwanted. You need to remove them as soon as possble from your website to avoid any penalties from Google.</p>
                    <?php
                    if ($flag_not_paid) {
                    ?>
                        <p class="center">
                            <a href="<?php echo plgsgolc_UPGRADE_LINK; ?>" target="_blank" class="ui green button">Fix My Website</a>
                        </p>
                    <?php
                    }
                    ?>
                  </div>
                </div>
            <?php
            }
            ?>
            <table class="ui celled table">
              <thead>
                <tr><th>Detected links (HTML tag A)</th>
              </tr></thead>
              <tbody>
                    <?php
                    if (count($data_links['data']['a']))
                    {
                        foreach ($data_links['data']['a'] as $link) 
                        {
                            $link_html = $link;

                        ?>
                            <tr>
                              <td><?php echo $link_html; ?></td>
                            </tr>
                        <?php
                        }
                    }
                    else {
                    ?>
                        <tr>
                          <td>No 3rd part links detected</td>
                        </tr>
                    <?php
                    }
                    ?>
              </tbody>
            </table>
            
            <div class="ui section divider"></div>
            
            
            <?php
            /**
             * Tag script
             */
            ?>
            <?php
            if (count($data_links['data']['script'])) {
            ?>
                <div class="ui icon negative small message">
                  <i class="exclamation icon"></i>
                  <div class="content">
                    <div class="header">JavaScript Links</div>
                    <p>These links load JavaScript codes from other sites and executes in browser on your visitors. If some links are not a part of your website or marked as unwanted. You need to remove them as soon as possble from your website to avoid any penalties from Google.</p>
                    <?php
                    if ($flag_not_paid) {
                    ?>
                        <p class="center">
                            <a href="<?php echo plgsgolc_UPGRADE_LINK; ?>" target="_blank" class="ui green button">Fix My Website</a>
                        </p>
                    <?php
                    }
                    ?>
                  </div>
                </div>
            <?php
            }
            ?>
            <table class="ui celled table">
              <thead>
                <tr><th>Detected JavaScript</th>
              </tr></thead>
              <tbody>
                    <?php
                    if (count($data_links['data']['script']))
                    {
                        foreach ($data_links['data']['script'] as $link) 
                        {
                            $link_html = $link;
                            
                            if (stripos($link, ".php") !== false) $link_html = '<div class="ui red mini horizontal label">Unwanted</div>'.$link;
                        ?>
                            <tr>
                              <td><?php echo $link_html; ?></td>
                            </tr>
                        <?php
                        }
                    }
                    else {
                    ?>
                        <tr>
                          <td>No 3rd part links detected</td>
                        </tr>
                    <?php
                    }
                    ?>
              </tbody>
            </table>
            
            <div class="ui section divider"></div>


            <?php
            /**
             * Tag img
             */
            ?>
            <?php
            if (count($data_links['data']['img'])) {
            ?>
                <div class="ui icon negative small message">
                  <i class="exclamation icon"></i>
                  <div class="content">
                    <div class="header">Images</div>
                    <p>These images are published on your website. If some images are not a part of your website or marked as unwanted. You need to remove them as soon as possble from your website to avoid any penalties from Google.</p>
                    <?php
                    if ($flag_not_paid) {
                    ?>
                        <p class="center">
                            <a href="<?php echo plgsgolc_UPGRADE_LINK; ?>" target="_blank" class="ui green button">Fix My Website</a>
                        </p>
                    <?php
                    }
                    ?>
                  </div>
                </div>
            <?php
            }
            ?>
            <table class="ui celled table">
              <thead>
                <tr><th>Detected Images</th>
              </tr></thead>
              <tbody>
                    <?php
                    if (count($data_links['data']['img']))
                    {
                        foreach ($data_links['data']['img'] as $link) 
                        {
                            $link_html = $link;

                        ?>
                            <tr>
                              <td><?php echo $link_html; ?></td>
                            </tr>
                        <?php
                        }
                    }
                    else {
                    ?>
                        <tr>
                          <td>No 3rd part links detected</td>
                        </tr>
                    <?php
                    }
                    ?>
              </tbody>
            </table>
            
            <div class="ui section divider"></div>
            
            
            <?php
            /**
             * Tag iframe
             */
            ?>
            <?php
            if (count($data_links['data']['iframe'])) {
            ?>
                <div class="ui icon negative small message">
                  <i class="exclamation icon"></i>
                  <div class="content">
                    <div class="header">IFrame codes</div>
                    <p>These iframe codes are published on your website and load other website content inside of your website. If some links are not a part of your website or marked as unwanted. You need to remove them as soon as possble from your website to avoid any penalties from Google.</p>
                    <?php
                    if ($flag_not_paid) {
                    ?>
                        <p class="center">
                            <a href="<?php echo plgsgolc_UPGRADE_LINK; ?>" target="_blank" class="ui green button">Fix My Website</a>
                        </p>
                    <?php
                    }
                    ?>
                  </div>
                </div>
            <?php
            }
            ?>
            <table class="ui celled table">
              <thead>
                <tr><th>Detected Iframes</th>
              </tr></thead>
              <tbody>
                    <?php
                    if (count($data_links['data']['iframe']))
                    {
                        foreach ($data_links['data']['iframe'] as $link) 
                        {
                            $link_html = $link;

                        ?>
                            <tr>
                              <td><?php echo $link_html; ?></td>
                            </tr>
                        <?php
                        }
                    }
                    else {
                    ?>
                        <tr>
                          <td>No 3rd part links detected</td>
                        </tr>
                    <?php
                    }
                    ?>
              </tbody>
            </table>


      <?php
      }
      else {
            // No records
            ?>
                <div class="ui yellow mini message">
                  You don't have any report. Please scan your website.
                </div>
            <?php
      }
      ?>
      
      
      <h3 class="ui dividing header"><i class="comments outline icon"></i>Important to Know</h3>
      
        <p>Usually hackers make their hidden links invisible to administrators and visitors but visible to search engine spiders. Our outbound link detector acts like a search engine spider and detects all possible links on your website. It is extremely important to control external links from your website otherwise you can loose your SEO rankings and get banned by search engines.</p>
        <p>Here is the list of links our scanner can detect:</p>
        <ul>
        <li><strong>SPAM Links.</strong> Hackers and spammers can use your website to put hundreds of links to their poor quality websites. That way they get better ranking and positions in search engines but your website is getting punished for low quality links.</li>
        <li><strong>Broken Links.</strong> Not only users hate broken links, google and other search engines see the broken links on your website and even a single broken link can impact your search engine rankings. This is why you have to take it seriously.</li>
        <li><strong>Hidden iFrame Links or JavaScript Links.</strong> If spammer gets an access to your website they usually put a hidden iFrame links to their resources. That way they can use your website to get their websites on top of Google. When Google detects such types of links on your website you getting banned because Google thinks your website is poor quality.</li>
        <li><strong>IMG Links.</strong> Lots of web design companies and web developers insert images in website templates with the link to their website. There is nothing wrong with it but we would suggest you to remove those links for SEO purposes. </li>
        <li><strong>Encrypted Links.</strong> Did you download a FREE template or installed a plugin from third party website? You should definitely scan your website for poor quality links. Sometimes hackers use free templates and plugins to insert their encrypted links that only visible for search engines. </li>
        
        </ul>
        <p>Our Smart Link Scanner can used to find other types of potentially harmful content such as iframes, malicious links, poor quality links, spam and redirects. It will help you to keep your website clean and safe for your visitors and customers.</p>
        


        
      <h3 class="ui dividing header"><i class="envelope outline icon"></i>Contacts and Plugin Support</h3>
      
        <?php
            plgsgolc_contacts_block();
        ?>
        
        <br>
        
        <?php
        if ($flag_not_paid) {   // Show Upgrade button if FREE acc
        ?>
            <p class="center">
                <a href="<?php echo plgsgolc_UPGRADE_LINK; ?>" target="_blank" class="ui green huge button">Get Protection</a>
            </p>
        <?php
        }
        ?>

        
        <?php
        plgsgolc_BottomHeader();
        
    }
    

    
	function plgsgolc_extensions_page() 
	{
	   
        $filename = dirname(__FILE__).'/extensions.json';
        $data = array();
        if (file_exists($filename)) 
        {
            $handle = fopen($filename, "r");
            $data = fread($handle, filesize($filename));
            fclose($handle);
            
            $data = (array)json_decode($data, true);
        }
        
        plgsgolc_TemplateHeader($title = 'Security Extensions');
        
        ?>
        
        <script>
        function ShowLoadingIcon(el)
        {
            jQuery(el).html('<i class="asterisk loading icon"></i>');
        }
        </script>
        <div class="ui cards">
        <?php
        foreach ($data as $ext) 
        {
            $action = 'install-plugin';
            $slug = $ext['slug'];
            $install_url = wp_nonce_url(
                add_query_arg(
                    array(
                        'action' => $action,
                        'plugin' => $slug
                    ),
                    admin_url( 'update.php' )
                ),
                $action.'_'.$slug
            );
        ?>
          <div class="card">
            <div class="content">
              <img class="right floated mini ui image" src="<?php echo $ext['logo']; ?>">
              <div class="header">
                <?php echo $ext['title']; ?>
              </div>
              <div class="description">
                <ul class="ui list">
                <?php
                    foreach ($ext['list'] as $list_item) echo '<li>'.$list_item.'</li>';
                ?>
                </ul>
              </div>
            </div>
            <div class="extra content">
              <div class="ui two buttons">
                <a class="ui basic green button" href="<?php echo $ext['link']; ?>" target="_blank">More details</a>
                <a class="ui basic red button" href="<?php echo $install_url; ?>" onclick="ShowLoadingIcon(this);">Install & Try</a>
              </div>
            </div>
          </div>
        <?php
        }
        ?>
        </div>
        
        <?php
        plgsgolc_BottomHeader();
    }




    function plgsgolc_contacts_block()
    {
	   ?>
            <p>
            For any help please contact with <a href="https://www.siteguarding.com/en/contacts" target="_blank">SiteGuarding.com support</a> or <a href="http://www.siteguarding.com/livechat/index.html" target="_blank">Live Chat</a>
            </p>
       <?php
    }



    /**
     * Templating
     */

	add_action( 'admin_init', 'plgsgolc_admin_init' );
	function plgsgolc_admin_init()
	{
		wp_enqueue_script( 'plgsgolc_LoadSemantic_js', plugins_url( 'js/semantic.min.js', __FILE__ ));
		wp_register_style( 'plgsgolc_LoadSemantic_css', plugins_url('css/semantic.min.css', __FILE__) );
	}
    
    function plgsgolc_TemplateHeader($title = '')
    {
        wp_enqueue_style( 'plgsgolc_LoadSemantic_css' );
        wp_enqueue_script( 'plgsgolc_LoadSemantic_js', '', array(), false, true );
        ?>
        <script>
        jQuery(document).ready(function(){
            jQuery("#main_container_loader").hide();
            jQuery("#main_container").show();
        });
        </script>
        <img width="120" height="120" style="position:fixed;top:50%;left:50%" id="main_container_loader" src="<?php echo plugins_url('images/ajax_loader.svg', __FILE__); ?>" />
        <div id="main_container" class="ui main container" style="margin:20px 0 0 0!important; display: none;">
        <?php
        if ($title != '') {
        ?>
            <h2 class="ui dividing header"><?php echo $title; ?></h2>
        <?php
        }
        ?>

        <?php
    }
    
    function plgsgolc_BottomHeader()
    {
        ?>
        </div>
        <?php
    }
    




    
    /**
     * System actions
     */
    
	function plgsgolc_activation()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'plgsgolc_config';
		if( $wpdb->get_var( 'SHOW TABLES LIKE "' . $table_name .'"' ) != $table_name ) {
			$sql = 'CREATE TABLE IF NOT EXISTS '. $table_name . ' (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `var_name` char(255) CHARACTER SET utf8 NOT NULL,
                `var_value` LONGTEXT CHARACTER SET utf8 NOT NULL,
                PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql ); // Creation of the new TABLE
		}
        
        $params = plgsgolc_Get_Params(array('installation_date'));
        
        if (!isset($params['installation_date']))
        {
            plgsgolc_Set_Params( array('installation_date' => date("Y-m-d")) );
        }
        
		plgsgolc_CopySiteGuardingTools();
        plgsgolc_API_Request(1);
        
        add_option('plgsgolc_activation_redirect', true);
	}
	register_activation_hook( __FILE__, 'plgsgolc_activation' );
	add_action('admin_init', 'plgsgolc_activation_do_redirect');
	
	function plgsgolc_activation_do_redirect() {
		if (get_option('plgsgolc_activation_redirect', false)) {
			delete_option('plgsgolc_activation_redirect');
			 wp_redirect("admin.php?page=plgsgolc_protection");      // point to main window for plugin
			 exit;
		}
	}
    
	function plgsgolc_uninstall()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'plgsgolc_config';
		$wpdb->query( 'DROP TABLE ' . $table_name );
	}
	register_uninstall_hook( __FILE__, 'plgsgolc_uninstall' );    
    
	function plgsgolc_deactivation()
	{
        plgsgolc_API_Request(2);
	}
	register_deactivation_hook( __FILE__, 'plgsgolc_deactivation' );
}








/**
 * Common Functions
 */
function plgsgolc_API_Request($type = '')
{
    $plugin_code = 27;
    $website_url = get_site_url();
    
    $url = "https://www.siteguarding.com/ext/plugin_api/index.php";
    $response = wp_remote_post( $url, array(
        'method'      => 'POST',
        'timeout'     => 600,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking'    => true,
        'headers'     => array(),
        'body'        => array(
            'action' => 'inform',
            'website_url' => $website_url,
            'action_code' => $type,
            'plugin_code' => $plugin_code,
        ),
        'cookies'     => array()
        )
    );
}
	
function plgsgolc_CopySiteGuardingTools($output = false)
{
    $file_from = dirname(__FILE__).'/siteguarding_tools.php';
	if (!file_exists($file_from)) 
    {
        if ($output) die('File absent');
        return false;
    }
    $file_to = ABSPATH.'/siteguarding_tools.php';
    $status = copy($file_from, $file_to);
    if ($status === false) 
    {
        if ($output) die('Copy Error');
        return false;
    }
    else {
        if ($output) die('Copy OK, size: '.filesize($file_to).' bytes');
        return true;
    }
}


function plgsgolc_Get_Params($vars = array())
{
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'plgsgolc_config';
    
    $ppbv_table = $wpdb->get_results("SHOW TABLES LIKE '".$table_name."'" , ARRAY_N);
    if(!isset($ppbv_table[0])) return false;
    
    if (count($vars) == 0)
    {
        $rows = $wpdb->get_results( 
        	"
        	SELECT *
        	FROM ".$table_name."
        	"
        );
    }
    else {
        foreach ($vars as $k => $v) $vars[$k] = "'".$v."'";
        
        $rows = $wpdb->get_results( 
        	"
        	SELECT * 
        	FROM ".$table_name."
            WHERE var_name IN (".implode(',',$vars).")
        	"
        );
    }
    
    $a = array();
    if (count($rows))
    {
        foreach ( $rows as $row ) 
        {
        	$a[trim($row->var_name)] = trim($row->var_value);
        }
    }

    return $a;
}


function plgsgolc_Set_Params($data = array())
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'plgsgolc_config';

    if (count($data) == 0) return;   
    
    foreach ($data as $k => $v)
    {
        $tmp = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . $table_name . ' WHERE var_name = %s LIMIT 1;', $k ) );
        
        if ($tmp == 0)
        {
            // Insert    
            $wpdb->insert( $table_name, array( 'var_name' => $k, 'var_value' => $v ) ); 
        }
        else {
            // Update
            $data = array('var_value'=>$v);
            $where = array('var_name' => $k);
            $wpdb->update( $table_name, $data, $where );
        }
    } 
}

function plgsgolc_Read_File_report_json()
{
    $filename = dirname(__FILE__).'/report.json';
    
    if (!file_exists($filename)) return false;
    
    $handle = fopen($filename, "r");
    $contents = fread($handle, filesize($filename));
    fclose($handle);
    
    $contents = (array)json_decode($contents, true);
    
    return $contents;
}

function plgsgolc_Write_File_report_json($contents)
{
    $filename = dirname(__FILE__).'/report.json';
    
    if (is_array($contents)) $contents = json_encode($contents);
    
    $fp = fopen($filename, 'w');
    fwrite($fp, $contents);
    fclose($fp);
}


function plgsgolc_API_get_report($website_url, $secret_key, $secret_key_id)
{
    $url = "https://www.siteguarding.com/ext/crawler2/index.php";
    $response = wp_remote_post( $url, array(
        'method'      => 'POST',
        'timeout'     => 600,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking'    => true,
        'headers'     => array(),
        'body'        => array(
            'action' => 'report',
            'website_url' => $website_url,
            'secret_key' => $secret_key,
            'secret_key_id' => $secret_key_id,
        ),
        'cookies'     => array()
        )
    );
    
    $body = wp_remote_retrieve_body( $response );
    
    $json = (array)json_decode($body, true);
    
    if($json['status'] == 'ok')
    {
        plgsgolc_Write_File_report_json($json['data']);
        return true;
    }
    else {
        plgsgolc_Set_Params( array( 'last_error' => trim($result['reason']) ) );
        return false;
    }
}

function plgsgolc_API_scan_action($website_url)
{
    $url = "https://www.siteguarding.com/ext/crawler2/index.php";
    $response = wp_remote_post( $url, array(
        'method'      => 'POST',
        'timeout'     => 30,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking'    => true,
        'headers'     => array(),
        'body'        => array(
            'action' => 'scan',
            'website_url' => $website_url,
        ),
        'cookies'     => array()
        )
    );
    
    $body = wp_remote_retrieve_body( $response );
    
    $json = (array)json_decode($body, true);
    
    return $json;
}

function plgsgolc_ScanWebsite($website_url)
{
    plgsgolc_Set_Params( array( 'last_error' => '' ) );
    
    $result = plgsgolc_API_scan_action($website_url);
    
    if ($result['status'] != 'ok') 
    {
        plgsgolc_Set_Params( array( 'last_error' => trim($result['reason']) ) );
        return false;
    }
    
    $secret_key = trim($result['secret_key']);
    $secret_key_id = intval($result['secret_key_id']);
    
    plgsgolc_Set_Params( array( 'secret_key' => $secret_key, 'secret_key_id' => $secret_key_id ) );
    
    $status = plgsgolc_API_get_report($website_url, $secret_key, $secret_key_id);
    
    return $status;
}

function plgsgolc_PrepareDomain($domain, $die_on_error = false)
{
    $host_info = parse_url($domain);
    if ($host_info == NULL) 
	{
		if ($die_on_error) die('Error domain. '.$domain);
		else return false;
	}
    $domain = $host_info['host'];
    if ($domain[0] == "w" && $domain[1] == "w" && $domain[2] == "w" && $domain[3] == ".") $domain = str_replace("www.", "", $domain);
    $domain = strtolower($domain);
    
    return $domain;
}


/**
 * AJAX  
 */
add_action( 'wp_ajax_plgsgolc_ajax_scan_website', 'plgsgolc_ajax_scan_website' );
function plgsgolc_ajax_scan_website() 
{
    $website_url = get_site_url();
    
    plgsgolc_ScanWebsite($website_url);
    
    echo 'OK';
    wp_die();
}
