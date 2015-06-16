<?php

/**
 * ECWD_Admin
 */
class ECWD_Admin {

	protected static $instance = null;
	protected $version = '1.0.5';
	protected $ecwd_page = null;

	private function __construct() {
		$plugin        = ECWD::get_instance();
		$this->prefix  = $plugin->get_prefix();
		$this->version = $plugin->get_version();
		add_filter( 'plugin_action_links_' . plugin_basename( plugin_dir_path( __FILE__ ) . $this->prefix . '.php' ), array(
			$this,
			'add_action_links'
		) );

		// Setup admin stants
		add_action( 'init', array( $this, 'define_admin_constants' ) );
		add_action( 'init', array( $this, ECWD_PLUGIN_PREFIX . '_shortcode_button' ) );

		// Add admin styles and scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ), 2 );
		foreach ( array( 'post.php', 'post-new.php' ) as $hook ) {
			add_action( "admin_head-$hook", array( $this, 'admin_head' ) );
		}
	}


	/**
	 * Check user is on plugin page
	 * @return  bool
	 */
	private function ecwd_page() {
		if ( ! isset( $this->ecwd_page ) ) {
			return false;
		}
		$screen = get_current_screen();
		if ( $screen->id == 'edit-ecwd_event' || $screen->id == ECWD_PLUGIN_PREFIX . '_event' || in_array( $screen->id, $this->ecwd_page ) || $screen->post_type == ECWD_PLUGIN_PREFIX . '_event' || $screen->post_type == ECWD_PLUGIN_PREFIX . '_theme' || $screen->post_type == ECWD_PLUGIN_PREFIX . '_venue' || $screen->id == 'edit-ecwd_calendar' || $screen->id == ECWD_PLUGIN_PREFIX . '_calendar' || $screen->id == ECWD_PLUGIN_PREFIX . '_countdown_theme' ) {
			return true;
		} else {
			return false;
		}
	}

	public function save_events() {
		wp_die();
	}

	public static function activate() {
		//setup default theme if there is no one

		if (!defined('ECWD_PLUGIN_PREFIX')) {
			define('ECWD_PLUGIN_PREFIX', 'ecwd');
		}

	}


	public function add_plugin_admin_menu() {
		$this->ecwd_page[] = add_submenu_page(
			'edit.php?post_type=ecwd_calendar', __( 'Settings', 'ecwd' ), __( 'Settings', 'ecwd' ), 'manage_options', $this->prefix . '_general_settings', array(
				$this,
				'display_admin_page'
			)
		);
		$this->ecwd_page[] = add_submenu_page(
			'edit.php?post_type=ecwd_calendar', __( 'Themes', 'ecwd' ), __( 'Themes', 'ecwd' ), 'manage_options', $this->prefix . '_themes', array(
				$this,
				'display_themes_page'
			)
		);
		$this->ecwd_page[] = add_submenu_page(
			'edit.php?post_type=ecwd_calendar', __( 'Licensing', 'ecwd' ), __( 'Licensing', 'ecwd' ), 'manage_options', $this->prefix . '_licensing', array(
				$this,
				'display_license_page'
			)
		);
		$this->ecwd_page[] = add_submenu_page(
			'edit.php?post_type=ecwd_calendar', __( 'Featured plugins', 'ecwd' ), __( 'Featured plugins', 'ecwd' ), 'manage_options', $this->prefix . '_featured_plugins', array(
				$this,
				'display_featured_plugins'
			)
		);
		$this->ecwd_page[] = add_submenu_page(
			'edit.php?post_type=ecwd_calendar', __( 'Featured themes', 'ecwd' ), __( 'Featured themes', 'ecwd' ), 'manage_options', $this->prefix . '_featured_themes', array(
				$this,
				'display_featured_themes'
			)
		);


	}

	public function display_featured_themes() {
		include_once( ECWD_DIR . '/views/admin/ecwd-featured-themes.php' );
	}

	public function display_featured_plugins() {
		include_once( ECWD_DIR . '/views/admin/ecwd-featured-plugins.php' );
	}
	public function display_themes_page() {
		include_once( ECWD_DIR . '/views/admin/ecwd-theme-meta.php' );
	}
	public function display_license_page() {
		include_once( ECWD_DIR . '/views/admin/licensing.php' );
	}

	public function display_admin_page() {
		include_once( 'views/admin/admin.php' );
	}

	/**
	 * Enqueue styles for the admin area
	 */
	public function enqueue_admin_styles() {
		wp_enqueue_style( $this->prefix . '-calendar-buttons-style', plugins_url( 'css/admin/mse-buttons.css', __FILE__ ), '', $this->version, 'all' );
		if ( $this->ecwd_page() ) {
			wp_enqueue_style( $this->prefix . '-main', plugins_url( 'css/calendar.css', __FILE__ ), '', $this->version );
			wp_enqueue_style( 'ecwd-admin-css', plugins_url( 'css/admin/admin.css', __FILE__ ), array(), $this->version, 'all' );
			wp_enqueue_style( 'ecwd-admin-datetimepicker-css', plugins_url( 'css/admin/jquery.datetimepicker.css', __FILE__ ), array(), $this->version, 'all' );
			wp_enqueue_style( 'ecwd-admin-colorpicker-css', plugins_url( 'css/admin/evol.colorpicker.css', __FILE__ ), array(), $this->version, 'all' );
			wp_enqueue_style( $this->prefix . '-calendar-style', plugins_url( 'css/style.css', __FILE__ ), '', $this->version, 'all' );
			wp_enqueue_style($this->prefix . '_font-awesome', plugins_url('/css/font-awesome/font-awesome.css', __FILE__), '', $this->version, 'all');
			wp_enqueue_style($this->prefix . '-featured_plugins', plugins_url('/css/admin/featured_plugins.css', __FILE__), '', $this->version, 'all');
			wp_enqueue_style($this->prefix . '-featured_themes', plugins_url('/css/admin/featured_themes.css', __FILE__), '', $this->version, 'all');
			wp_enqueue_style($this->prefix . '-licensing', plugins_url('/css/admin/licensing.css', __FILE__), '', $this->version, 'all');
		}
	}

	/**
	 * Register scripts for the admin area
	 */
	public function enqueue_admin_scripts() {
		if ( $this->ecwd_page() ) {
			wp_enqueue_script( $this->prefix . '-admin-datetimepicker', plugins_url( 'js/admin/jquery.datetimepicker.js', __FILE__ ), array(
				'jquery',
				'jquery-ui-widget'
			), $this->version, true );
			wp_enqueue_script( $this->prefix . '-admin-colorpicker', plugins_url( 'js/admin/evol.colorpicker.js', __FILE__ ), array( 'jquery' ), $this->version, true );
			//wp_enqueue_script('sp-admin-google', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCIUXhK7om6EZ9Ca5xFcEbXMKEQZz7U2kM', array('jquery'), $this->version, true);
			wp_enqueue_script( $this->prefix . '-public', plugins_url( 'js/scripts.js', __FILE__ ), array(
				'jquery',
				'masonry'
			), $this->version, true );
			wp_register_script($this->prefix . '-admin-scripts', plugins_url( 'js/admin/admin.js', __FILE__ ), array(
				'jquery',
				'jquery-ui-datepicker',
				'jquery-ui-tabs',
				'jquery-ui-selectable',
				$this->prefix . '-public'
			), $this->version, true );
			wp_enqueue_script( $this->prefix . '-admin-datetimepicker-scripts', plugins_url( 'js/admin/datepicker.js', __FILE__ ), array( 'jquery' ), $this->version, true );

			$params['ajaxurl'] = admin_url( 'admin-ajax.php' );
			$params['version'] = get_bloginfo( 'version' );
			if ( $params['version'] >= 3.5 ) {
				wp_enqueue_media();
			} else {
				wp_enqueue_style( 'thickbox' );
				wp_enqueue_script( 'thickbox' );
			}

			wp_localize_script( $this->prefix . '-admin-scripts', 'params', $params );
			wp_enqueue_script( $this->prefix . '-admin-scripts' );

		}
	}


	/**
	 * Localize Script
	 */
	public function admin_head() {

		$args           = array(
			'post_type'           => ECWD_PLUGIN_PREFIX . '_calendar',
			'post_status'         => 'publish',
			'posts_per_page'      => - 1,
			'ignore_sticky_posts' => 1
		);
		$calendar_posts = get_posts( $args );
		$args           = array(
			'post_type'           => $this->prefix . '_event',
			'post_status'         => 'publish',
			'posts_per_page'      => - 1,
			'ignore_sticky_posts' => 1
		);
		$event_posts    = get_posts( $args );
		$plugin_url = plugins_url( '/', __FILE__ );
		?>
		<!-- TinyMCE Shortcode Plugin -->
		<script type='text/javascript'>
			var ecwd_plugin = {
				'url': '<?php echo $plugin_url; ?>',
				'ecwd_calendars': [
					<?php foreach($calendar_posts as $calendar){?>
					{
						text: '<?php echo str_replace("'", "\'", $calendar->post_title);?>',
						value: '<?php echo $calendar->ID;?>'
					},
					<?php }?>
				],
				'ecwd_events': [
					{text: 'None', value: 'none'},
					<?php foreach($event_posts as $event){?>
					{
						text: '<?php echo str_replace("'", "\'", $event->post_title);?>',
						value: '<?php echo $event->ID;?>'
					},
					<?php }?>
				],

				'ecwd_views': [
					{text: 'None', value: 'none'},
					{text: 'Month', value: 'full'},
					{text: 'List', value: 'list'},
					{text: 'Week', value: 'week'},
					{text: 'Day', value: 'day'}
				]
			};
		</script>
		<!-- TinyMCE Shortcode Plugin -->
	<?php
	}


	public function ecwd_shortcode_button() {
		// Don't bother doing this stuff if the current user lacks permissions
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		// Add only in Rich Editor mode
		if ( get_user_option( 'rich_editing' ) == 'true' ) {
			// filter the tinyMCE buttons and add our own
			add_filter( "mce_external_plugins", array( $this, 'add_tinymce_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'register_buttons' ) );
		}
	}

// registers the buttons for use
	function register_buttons( $buttons ) {
		// inserts a separator between existing buttons and our new one
		// "friendly_button" is the ID of our button
		if(!$this->ecwd_page()) {
			array_push( $buttons, "|", ECWD_PLUGIN_PREFIX );
		}

		return $buttons;
	}

// add the button to the tinyMCE bar
	function add_tinymce_plugin( $plugin_array ) {
		if(!$this->ecwd_page()) {
			$plugin_array[ ECWD_PLUGIN_PREFIX ] = plugins_url( 'js/admin/editor-buttons.js', __FILE__ );
		}
		return $plugin_array;
	}

	public function define_admin_constants() {
		if ( ! defined( 'ECWD_DIR' ) ) {
			define( 'ECWD_DIR', dirname( __FILE__ ) );
		}
	}

	/**
	 * Return an instance of this class.
	 */
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Return the page
	 */
	public function get_page() {
		return $this->ecwd_page();
	}

	/**
	 * Return plugin name
	 */
	public function get_plugin_title() {
		return __( 'Event Calendar WD', 'ecwd' );
	}

	public function add_action_links( $links ) {
		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'edit.php?post_type=ecwd_calendar&page=ecwd_general_settings' ) . '">' . __( 'Settings', 'ecwd' ) . '</a>',
				'events'   => '<a href="' . admin_url( 'edit.php?post_type=ecwd_event' ) . '">' . __( 'Events', 'ecwd' ) . '</a>'
			), $links
		);
	}

}
