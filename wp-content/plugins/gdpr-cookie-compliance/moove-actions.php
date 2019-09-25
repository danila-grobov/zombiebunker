<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Moove_GDPR_Actions File Doc Comment
 *
 * @category  Moove_GDPR_Actions
 * @package   gdpr-cookie-compliance
 * @author    Gaspar Nemes
 */

/**
 * Moove_GDPR_Actions Class Doc Comment
 *
 * @category Class
 * @package  Moove_GDPR_Actions
 * @author   Gaspar Nemes
 */
class Moove_GDPR_Actions {
	/**
	 * Global variable used in localization
	 *
	 * @var array
	 */
	var $gdpr_loc_data;
	/** 
	 * Construct
	 */
	function __construct() {
		$this->moove_register_scripts();
		$this->moove_register_ajax_actions();
		add_action( 'gdpr_cookie_filter_settings', array( &$this, 'gdpr_remove_cached_scripts' ) );
		add_action( 'gdpr_settings_tab_nav_extensions', array( &$this,'gdpr_settings_tab_nav_extensions' ), 999, 1 );
		add_action( 'gdpr_check_extensions', array( &$this, 'gdpr_check_extensions' ), 10, 2 );
		add_action( 'gdpr_premium_section_ads', array( &$this, 'gdpr_premium_section_ads' ) );

		add_action( 'gdpr_tab_cbm_ph', array( &$this, 'gdpr_premium_section_ads' ) );
		add_action( 'gdpr_tab_cbm_ps', array( &$this, 'gdpr_premium_section_ads' ) );

		add_action( 'gdpr_cdn_url', array( &$this, 'gdpr_cdn_base_url' ), 10, 1 );
		add_action( 'gdpr_info_bar_button_extensions', array( &$this, 'gdpr_info_add_reject_button_extensions' ) );
    add_action( 'gdpr_support_sidebar_class', array( &$this, 'gdpr_support_sidebar_class'  ), 10, 1 );
	}

  public function gdpr_support_sidebar_class( $class ) {
    if ( class_exists( 'Moove_GDPR_Addon_View' ) ) :
      $class = 'm-plugin-box-highlighted';
    endif;
    return $class;
  }

	/**
	 * Reject button extension, will be listed next to the Accept button if it's enabled in the CMS
	 */
	public function gdpr_info_add_reject_button_extensions() {
    $gdpr_default_content 		= new Moove_GDPR_Content();
    $option_name    					= $gdpr_default_content->moove_gdpr_get_option_name();
    $modal_options  					= get_option( $option_name );
    $wpml_lang      					= $gdpr_default_content->moove_gdpr_get_wpml_lang();
    if ( isset( $modal_options['moove_gdpr_reject_button_enable'] ) && intval( $modal_options['moove_gdpr_reject_button_enable'] ) === 1 ) :
      $button_label = isset( $modal_options['moove_gdpr_infobar_reject_button_label'.$wpml_lang] ) && $modal_options['moove_gdpr_infobar_reject_button_label'.$wpml_lang] ? $modal_options['moove_gdpr_infobar_reject_button_label'.$wpml_lang] : __('Reject','gdpr-cookie-compliance-addon');
      ?>
      <button class="mgbutton moove-gdpr-infobar-reject-btn"><?php echo $button_label; ?></button>
      <?php
    endif;
  }

  /**
   * CDN base URL for lity lightbox
   */
  public function gdpr_cdn_base_url( $plugin_url ) {
    $gdpr_default_content 	= new Moove_GDPR_Content();
    $option_name    		    = $gdpr_default_content->moove_gdpr_get_option_name();
    $modal_options  		    = get_option( $option_name );

    if ( isset( $modal_options['moove_gdpr_cdn_url'] )  && $modal_options['moove_gdpr_cdn_url'] ) :
      $cdn_url = esc_url_raw( $modal_options['moove_gdpr_cdn_url'] );
      $plugin_url = str_replace( trailingslashit( site_url() ) , trailingslashit( $cdn_url ), $plugin_url );
    endif;

    return $plugin_url;

 }

	/**
	 * Lock screen of premium tabs, visible in the free version
	 */
	public function gdpr_premium_section_ads() {

    if ( class_exists( 'Moove_GDPR_Addon_View' ) ) :
      $add_on_view = new Moove_GDPR_Addon_View();
      $view_content = $add_on_view->load( 'moove.admin.settings.'.$slug, array() );
      if ( ! $view_content && $slug ) :
        ?>
        <div class="gdpr-locked-section">
          <span>
            <i class="dashicons dashicons-lock"></i>
            <h4>This feature is not supported by your Premium Add-on</h4>
            <a href="https://www.mooveagency.com/wordpress-plugins/gdpr-cookie-compliance/" target="_blank" class="plugin-buy-now-btn">Buy Now for £49</a>
          </span>

        </div>
        <!--  .gdpr-locked-section -->
        <?php
      endif;
    else :
      ?>
      <div class="gdpr-locked-section">
        <span>
          <i class="dashicons dashicons-lock"></i>
          <h4>This feature is part of our Premium Add-on</h4>
          <a href="https://www.mooveagency.com/wordpress-plugins/gdpr-cookie-compliance/" target="_blank" class="plugin-buy-now-btn">Buy Now for £49</a>
        </span>

      </div>
      <!--  .gdpr-locked-section -->
      <?php
    endif;
	}

	/**
	 * Checking for Premium Add-on installed and activated
	 */
	public function gdpr_check_extensions( $content, $slug ) {
    $return = $content;
    if ( class_exists( 'Moove_GDPR_Addon_View' ) ) :
      $add_on_view = new Moove_GDPR_Addon_View();
      // $view_content = $add_on_view->load( 'moove.admin.settings.'.$slug, array() );
      $view_content = true;
      if ( $view_content ) :
       $return = '';
      endif;
    endif;
		return $return;
	}

	/**
	 * Clearing AJAX transient cache 
	 */
	public function gdpr_remove_cached_scripts() {
		$transient_key = 'gdpr_cookie_cache';
    delete_transient( $transient_key );
  }

	/**
	 * Register Front-end / Back-end scripts
	 *
	 * @return void
	 */
	public function moove_register_scripts() {
		if ( is_admin() ) :
			add_action( 'admin_enqueue_scripts', array( &$this, 'moove_gdpr_admin_scripts' ) );
		else :
			add_action( 'wp_enqueue_scripts', array( &$this, 'moove_frontend_gdpr_scripts' ), 999 );
		endif;
	}

	/**
	 * Register global variables to head, AJAX, Form validation messages
	 *
	 * @param  string $ascript The registered script handle you are attaching the data for.
	 * @return void
	 */
	public function moove_localize_script( $ascript ) {
		$gdpr_default_content 	= new Moove_GDPR_Content();
    $option_name    		    = $gdpr_default_content->moove_gdpr_get_option_name();
    $modal_options  		    = get_option( $option_name );
    $force_reload 			    = apply_filters( 'gdpr_force_reload', false );
    $force_reload 			    = $force_reload ? 'true' : 'false';
    $loc_data 				      = array(
      'ajaxurl'								=> admin_url( 'admin-ajax.php' ),
      'post_id'               => get_the_ID(),
      'plugin_dir'            => apply_filters( 'gdpr_cdn_url', plugins_url( basename( dirname( __FILE__ ) ) ) ),
      'is_page'								=> is_page(),
      'strict_init'						=> isset( $modal_options['moove_gdpr_strictly_necessary_cookies_functionality'] ) && intval( $modal_options['moove_gdpr_strictly_necessary_cookies_functionality'] ) ? intval( $modal_options['moove_gdpr_strictly_necessary_cookies_functionality'] ) : 1,
      'enabled_default'				=> array(
        'third_party'         => isset( $modal_options['moove_gdpr_third_party_cookies_enable_first_visit'] ) && intval( $modal_options['moove_gdpr_third_party_cookies_enable_first_visit'] ) ? intval( $modal_options['moove_gdpr_third_party_cookies_enable_first_visit'] ) : 0,
        'advanced'            => isset( $modal_options['moove_gdpr_advanced_cookies_enable_first_visit'] ) && intval( $modal_options['moove_gdpr_advanced_cookies_enable_first_visit'] ) ? intval( $modal_options['moove_gdpr_advanced_cookies_enable_first_visit'] ) : 0,
      ),
      'force_reload'          => $force_reload,
      'is_single'             => is_single(),
      'current_user'          => get_current_user_id(),
    );
    $this->gdpr_loc_data = apply_filters( 'gdpr_extend_loc_data', $loc_data );
    wp_localize_script( $ascript, 'moove_frontend_gdpr_scripts', $this->gdpr_loc_data );

  }

	/**
	 * Registe FRONT-END Javascripts and Styles
	 *
	 * @return void
	 */
	public function moove_frontend_gdpr_scripts() {

		wp_enqueue_script( 'moove_gdpr_frontend', plugins_url( basename( dirname( __FILE__ ) ) ) . '/dist/scripts/main.js', array( 'jquery' ), MOOVE_GDPR_VERSION, true );

		$gdpr_default_content 	= new Moove_GDPR_Content();
    $option_name    		    = $gdpr_default_content->moove_gdpr_get_option_name();
    $modal_options  		    = get_option( $option_name );
    $wpml_lang      		    = $gdpr_default_content->moove_gdpr_get_wpml_lang();
    $css_file 				      = 'gdpr-main.css';
    if ( isset( $modal_options['moove_gdpr_plugin_font_type'] ) ) :
      if (  $modal_options['moove_gdpr_plugin_font_type'] === '1' ) :
        $css_file = 'gdpr-main.css';
      elseif ( $modal_options['moove_gdpr_plugin_font_type'] === '2' ) :
        $css_file = 'gdpr-main-nf.css';
      else :
        $css_file = isset( $modal_options['moove_gdpr_plugin_font_family'] ) && $modal_options['moove_gdpr_plugin_font_family'] && strpos( strtolower( $modal_options['moove_gdpr_plugin_font_family'] ), 'nunito') === false ?'gdpr-main-nf.css' : 'gdpr-main.css';
      endif;
    endif;
    wp_enqueue_style( 'moove_gdpr_frontend', plugins_url( basename( dirname( __FILE__ ) ) ) . '/dist/styles/' . $css_file, '', MOOVE_GDPR_VERSION );
    $this->moove_localize_script( 'moove_gdpr_frontend' );
  }

	/**
	 * Registe BACK-END Javascripts and Styles
	 *
	 * @return void
	 */
	public static function moove_gdpr_admin_scripts() {
		wp_enqueue_script( 'moove_gdpr_backend', plugins_url( basename( dirname( __FILE__ ) ) ) . '/dist/scripts/admin.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-slider', 'jquery-ui-draggable' ), MOOVE_GDPR_VERSION, false );
		wp_enqueue_style( 'moove_gdpr_backend', plugins_url( basename( dirname( __FILE__ ) ) ) . '/dist/styles/admin.css', '', MOOVE_GDPR_VERSION );
	}

	/**
	 * Register AJAX actions for the plugin
	 */
	public function moove_register_ajax_actions() {
		add_action( 'wp_ajax_moove_gdpr_get_scripts', array( 'Moove_GDPR_Controller', 'moove_gdpr_get_scripts' ) );
		add_action( 'wp_ajax_nopriv_moove_gdpr_get_scripts', array( 'Moove_GDPR_Controller', 'moove_gdpr_get_scripts' ) );

		add_action( 'wp_ajax_moove_gdpr_remove_php_cookies', array( 'Moove_GDPR_Controller', 'moove_gdpr_remove_php_cookies' ) );
		add_action( 'wp_ajax_nopriv_moove_gdpr_remove_php_cookies', array( 'Moove_GDPR_Controller', 'moove_gdpr_remove_php_cookies' ) );

    add_action( 'wp_ajax_moove_hide_language_notice', array( 'Moove_GDPR_Controller', 'moove_hide_language_notice' ) );
  }

	/**
	 * GDPR Modal Footer Branding
	 */
	public function moove_gdpr_footer_branding_content() {
		$gdpr_default_content = new Moove_GDPR_Content();
    $option_name          = $gdpr_default_content->moove_gdpr_get_option_name();
    $modal_options        = get_option( $option_name );
    $wpml_lang            = $gdpr_default_content->moove_gdpr_get_wpml_lang();
    $powered_label 	      = ( isset( $modal_options[ 'moove_gdpr_modal_powered_by_label'.$wpml_lang ] ) && $modal_options[ 'moove_gdpr_modal_powered_by_label'.$wpml_lang ] ) ? $modal_options[ 'moove_gdpr_modal_powered_by_label'.$wpml_lang ] : 'Powered by';
    ob_start();
    ?>

    <a href="https://wordpress.org/plugins/gdpr-cookie-compliance" target="_blank" rel="noopener" class='moove-gdpr-branding'><?php echo $powered_label; ?> GDPR <?php _e( 'plugin','gdpr-cookie-compliance' ); ?></a>
    <?php
    return ob_get_clean();
  }

	/**
	 * GDPR Cookie Compliance - Admin Tabs 
	 * Routing & views
	 */
	public function gdpr_settings_tab_nav_extensions( $active_tab ) {
    $tab_data = array(
      array(
        'name'  => __('Export/Import Settings','gdpr-cookie-compliance-addon'),
        'slug'  => 'export-import',
      ),
      array(
        'name'  => __('Multisite Settings','gdpr-cookie-compliance-addon'),
        'slug'  => 'multisite-settings',
      ),
      array(
        'name'  => __('Accept Cookies on Scroll','gdpr-cookie-compliance-addon'),
        'slug'  => 'accept-on-scroll',
      ),
      array(
        'name'  => __('Full-screen / Cookiewall','gdpr-cookie-compliance-addon'),
        'slug'  => 'full-screen-mode',
      ),
      array(
        'name'  => __('Analytics','gdpr-cookie-compliance-addon'),
        'slug'  => 'stats',
      ),
      array(
        'name'  => __('Geo Location','gdpr-cookie-compliance-addon'),
        'slug'  => 'geo-location',
      ),
      array(
        'name'  => __('Hide Cookie Banner','gdpr-cookie-compliance-addon'),
        'slug'  => 'cookie-banner-manager',
      ),
      array(
        'name'  => __('Iframe Blocker','gdpr-cookie-compliance-addon'),
        'slug'  => 'iframe-blocker',
      ),
    );
    foreach ( $tab_data as $tab ) :
     ob_start();
     ?>
     <a href="?page=moove-gdpr&amp;tab=<?php echo $tab['slug']; ?>" class="gdpr-cc-addon nav-tab gdpr-cc-disabled <?php echo $active_tab == $tab['slug'] ? 'nav-tab-active' : ''; ?>">
        <?php echo $tab['name']; ?>
      </a>
     <?php
     echo apply_filters( 'gdpr_check_extensions', ob_get_clean(), $tab['slug'] );
    endforeach;
  }

}
$moove_gdpr_actions_provider = new Moove_GDPR_Actions();

