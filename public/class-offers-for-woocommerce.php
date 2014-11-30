<?php
/**
 * Offers for WooCommerce - public
 *
 * @package   Angelleye_Offers_For_Woocommerce
 * @author    AngellEYE <andrew@angelleye.com>
 * @license   GPL-2.0+
 * @link      http://www.angelleye.com
 * @copyright 2014 AngellEYE
 */

/**
 * Plugin class - public
 *
 * @since	0.1.0
 * @package Angelleye_Offers_For_Woocommerce
 * @author  AngellEYE <andrew@angelleye.com>
 */
class Angelleye_Offers_For_Woocommerce {
	/**
	 * Plugin version
	 *
	 * @since   0.1.0
	 *
	 * @var     string
	 */
	const VERSION = '0.0.2';

	/**
	 *
	 * Unique pluginidentifier
	 *
	 * @since    0.1.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'offers-for-woocommerce';

	/**
	 * Instance of this class
	 *
	 * @since    0.1.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since    0.1.0
	 */
	private function __construct()
	{
		/**
		 * Load plugin text domain
		 */
		add_action('init', array($this, 'load_plugin_textdomain'));
		
		/**
		 * Activate plugin when new blog is added
		 */
		add_action('wpmu_new_blog', array($this, 'activate_new_site'));
		
		/**
		 * Load public-facing style sheet and javascript
		 */
		add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
		add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

		/*******************************
		 * Define custom functionality *
		 *******************************
		 */							
		 
		 
		/**
		 * Init - New Offer Form Submit
		 * @since	0.1.0
		 */
		add_action( 'init', array(&$this, 'new_offer_form_submit'));
		 
		/* Add "Make Offer" button code parts - Before add to cart */
		add_action( 'woocommerce_before_add_to_cart_button', array( &$this, 'angelleye_ofwc_before_add_to_cart_button' ) );
		
		/* Add "Make Offer" button code parts - After add to cart */
		add_action( 'woocommerce_after_add_to_cart_button', array( &$this, 'angelleye_ofwc_after_add_to_cart_button' ) );
		
		/* Add "Make Offer" button code parts - After shop loop item */
		add_action( 'woocommerce_after_shop_loop_item', array( &$this, 'angelleye_ofwc_after_show_loop_item' ), 99, 2 );
		
		/* Add "Make Offer" product tab on product single view */
		add_filter( 'woocommerce_product_tabs', array( &$this, 'angelleye_ofwc_add_custom_woocommerce_product_tab' ) );
		
	}
	
	
	/**
	 * Add extra div wrap before add to cart button
	 *
	 * @since	0.1.0
	 */
	function angelleye_ofwc_before_add_to_cart_button()
	{
		global $post;
		$custom_tab_options_offers = array(
			'enabled' => get_post_meta($post->ID, 'offers_for_woocommerce_enabled', true),
		);
	
		if ( $custom_tab_options_offers['enabled'] == 'yes' ){
			echo '<div class="offers-for-woocommerce-make-offer-button-cleared"></div>
			<div class="offers-for-woocommerce-add-to-cart-wrap"><div>';
		}
	}
	
	/**
	 * Add Make Offer button after add to cart button
	 *
	 * @since	0.1.0
	 */
	function angelleye_ofwc_after_add_to_cart_button()
	{
		global $post;
		$custom_tab_options_offers = array(
			'enabled' => get_post_meta($post->ID, 'offers_for_woocommerce_enabled', true),
		);
		
		if ( $custom_tab_options_offers['enabled'] == 'yes' ){
			
			// get options for button display
			$button_display_options = get_option('offers_for_woocommerce_options_display');
			
			$button_title = (isset($button_display_options['display_setting_custom_make_offer_btn_text']) && $button_display_options['display_setting_custom_make_offer_btn_text'] != '') ? $button_display_options['display_setting_custom_make_offer_btn_text'] : __( 'Make Offer', 'angelleye_offers_for_woocommerce' );
			
			$custom_styles_override = 'style="';
			if(isset($button_display_options['display_setting_custom_make_offer_btn_text_color']) && $button_display_options['display_setting_custom_make_offer_btn_text_color'] != '')
			{
				$custom_styles_override.= 'color:'.$button_display_options['display_setting_custom_make_offer_btn_text_color'].'!important;';
			}
			if(isset($button_display_options['display_setting_custom_make_offer_btn_color']) && $button_display_options['display_setting_custom_make_offer_btn_color'] != '')
			{
				$custom_styles_override.= ' background:'.$button_display_options['display_setting_custom_make_offer_btn_color'].'!important; border-color:'.$button_display_options['display_setting_custom_make_offer_btn_color'].'!important;';
			}
			$custom_styles_override.= '"';
			
			echo '<div class="angelleye-offers-clearfix"></div></div><div class="single_variation_wrap ofwc_offer_tab_form_wrap"><button type="button" id="offers-for-woocommerce-make-offer-button-id-'.$post->ID.'" class="offers-for-woocommerce-make-offer-button-single-product button alt" '.$custom_styles_override.'>'.$button_title.'</button></div>';
			echo '</div>';
		}
	}
	
	/**
	 * Callback - Add Make Offer button after add to cart button on Catalog view
	 *
	 * @since	0.1.0
	 */
	function angelleye_ofwc_after_show_loop_item($post)
	{
		global $post;
		$custom_tab_options_offers = array(
			'enabled' => get_post_meta($post->ID, 'offers_for_woocommerce_enabled', true),
		);
	
		if ( $custom_tab_options_offers['enabled'] == 'yes' )
		{
			// get options for button display
			$button_display_options = get_option('offers_for_woocommerce_options_display');
			
			$button_title = (isset($button_display_options['display_setting_custom_make_offer_btn_text']) && $button_display_options['display_setting_custom_make_offer_btn_text'] != '') ? $button_display_options['display_setting_custom_make_offer_btn_text'] : __( 'Make Offer', 'angelleye_offers_for_woocommerce' );
			
			$custom_styles_override = 'style="';
			if(isset($button_display_options['display_setting_custom_make_offer_btn_text_color']) && $button_display_options['display_setting_custom_make_offer_btn_text_color'] != '')
			{
	
				$custom_styles_override.= 'color:'.$button_display_options['display_setting_custom_make_offer_btn_text_color'].'!important;';
			}
			if(isset($button_display_options['display_setting_custom_make_offer_btn_color']) && $button_display_options['display_setting_custom_make_offer_btn_color'] != '')
			{
				$custom_styles_override.= ' background:'.$button_display_options['display_setting_custom_make_offer_btn_color'].'!important; border-color:'.$button_display_options['display_setting_custom_make_offer_btn_color'].'!important;';
			}
			$custom_styles_override.= '"';
				
			echo '<a href="'.get_permalink($post->ID).'" id="offers-for-woocommerce-make-offer-button-id-'.$post->ID.'" class="offers-for-woocommerce-make-offer-button-catalog button alt" '.$custom_styles_override.'>'.$button_title.'</a>';
		}
	}
	
	/**
	 * Filter - Add new tab on woocommerce product single view
	 *
	 * @since	0.1.0
	 */
	function angelleye_ofwc_add_custom_woocommerce_product_tab($tabs)
	{
		global $post;
		$custom_tab_options_offers = array(
			'enabled' => get_post_meta($post->ID, 'offers_for_woocommerce_enabled', true),
		);
		
		if ( $custom_tab_options_offers['enabled'] == 'yes' )
		{
			// get options for button display
			$button_display_options = get_option('offers_for_woocommerce_options_display');
			
			$tab_title = (isset($button_display_options['display_setting_custom_make_offer_btn_text']) && $button_display_options['display_setting_custom_make_offer_btn_text'] != '') ? $button_display_options['display_setting_custom_make_offer_btn_text'] : __( 'Make Offer', 'angelleye_offers_for_woocommerce' ); 
			// Add new tab "Make Offer"
			$tabs['tab_custom_ofwc_offer'] = array(
				'title' => $tab_title,
				'priority' => 50,
				'callback' => array( &$this, 'angelleye_ofwc_display_custom_woocommerce_product_tab_content' ) );
	
			// Set priority of the new tab to 20 -- second place
			$tabs['tab_custom_ofwc_offer']['priority'] = 20;
		}
		return $tabs;
	}
	
	/**
	 * Callback - Display "Make Offer" front-end form parts
	 *
	 * @since	0.1.0
	 */
	function angelleye_ofwc_display_custom_woocommerce_product_tab_content()
	{
		// get options for button display
		$button_display_options = get_option('offers_for_woocommerce_options_display');

        // set parent offer id if found in get var
        $parent_offer_id = (isset($_GET['pid']) && $_GET['pid'] != '') ? $_GET['pid'] : '';
        $offer_name = (isset($_GET['name']) && $_GET['name'] != '') ? $_GET['name'] : '';
        $offer_email = (isset($_GET['email']) && $_GET['email'] != '') ? $_GET['email'] : '';
		
		// Set html content for output
		include_once( 'views/public.php' );	
	}
	
	/**
	 * Return the plugin slug.
	 *
	 * @since    0.1.0
	 *
	 * @return    Plugin slug variable
	 */
	public function get_plugin_slug() 
	{
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class
	 *
	 * @since    0.1.0
	 *
	 * @return    object    A single instance of this class
	 */
	public static function get_instance() 
	{
		// If the single instance hasn't been set, set it now
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated
	 *
	 * @since    0.1.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog
	 */
	public static function activate( $network_wide ) 
	{
		if ( function_exists( 'is_multisite' ) && is_multisite()) 
		{
			if ( $network_wide ) 
			{
				// Get all blog ids
				$blog_ids = self::get_blog_ids();
				
				foreach ($blog_ids as $blog_id)
				{
					switch_to_blog($blog_id);
					self::single_activate();
				}
				
				restore_current_blog();
			} 
			else
			{
				self::single_activate();
			}
		}
		else
		{
			self::single_activate();
		}
		flush_rewrite_rules();
	}

	/**
	 * Fired when the plugin is deactivated
	 *
	 * @since    0.1.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog
	 */
	public static function deactivate($network_wide)
	{
		if ( function_exists( 'is_multisite' ) && is_multisite())
		{
			if ($network_wide)
			{
				// Get all blog ids
				$blog_ids = self::get_blog_ids();
				
				foreach ($blog_ids as $blog_id) 
				{
					switch_to_blog($blog_id);
					self::single_deactivate();
				}
				
				restore_current_blog();
			}
			else
			{
				self::single_deactivate();
			}
		}
		else
		{
			self::single_deactivate();
		}
		flush_rewrite_rules();
	}
	
	/**
	 * Fired when a new site is activated with a WPMU environment
	 *
	 * @since    0.1.0
	 *
	 * @param    int    $blog_id    ID of the new blog
	 */
	public function activate_new_site($blog_id)
	{
		if (1 !== did_action('wpmu_new_blog'))
		{
			return;
		}
		
		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();
	}
	
	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    0.1.0
	 *
	 * @return   array|false    The blog ids, false if no matches
	 */
	private static function get_blog_ids()
	{
		global $wpdb;
		
		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";
		return $wpdb->get_col( $sql );
	}

	/**
	 * Fired for each blog when the plugin is activated
	 *
	 * @since    0.1.0
	 */
	private static function single_activate()
	{
		// @TODO: Define activation functionality here
	}

	/**
	 * Fired for each blog when the plugin is deactivated
	 *
	 * @since    0.1.0
	 */
	private static function single_deactivate()
	{
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation
	 *
	 * @since    0.1.0
	 */
	public function load_plugin_textdomain()
	{
		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );
	}

	/**
	 * Register and enqueue public-facing style sheet
	 *
	 * @since    0.1.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}
	
	/**
	 * Register and enqueues public-facing JavaScript files
	 *
	 * @since    0.1.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
		wp_enqueue_script( $this->plugin_slug . '-plugin-script-jquery-auto-numeric-1-9-24', plugins_url( 'assets/js/autoNumeric-1-9-24.js', __FILE__ ), self::VERSION);		
	}
	
	public function new_offer_form_submit()
	{
		if(!is_admin())
		{
            global $wpdb; // this is how you get access to the database

			// Check if form was posted and select task accordingly
            if(isset($_REQUEST['woocommerceoffer_post']) && isset($_POST["offer_product_id"]) && $_POST["offer_product_id"] != '')
            {
				// set postmeta original vars
                $formData['orig_offer_name'] = (isset($_POST['offer_name'])) ? $_POST['offer_name'] : '';
                $formData['orig_offer_email'] = (isset($_POST['offer_email'])) ? $_POST['offer_email'] : '';
                $formData['orig_offer_product_id'] = (isset($_POST['offer_product_id'])) ? $_POST['offer_product_id'] : '';
                $formData['orig_offer_variation_id'] = (isset($_POST['offer_variation_id'])) ? $_POST['offer_variation_id'] : '';
				$formData['orig_offer_quantity'] = (isset($_POST['offer_quantity'])) ? $_POST['offer_quantity'] : '0';
                $formData['orig_offer_price_per'] = (isset($_POST['offer_price_each'])) ? $_POST['offer_price_each'] : '0';
				$formData['orig_offer_amount'] = ($formData['orig_offer_quantity'] * $formData['orig_offer_price_per']);
				
				// set postmeta vars
                $formData['offer_name'] = $formData['orig_offer_name'];
                $formData['offer_email'] = $formData['orig_offer_email'];
                $formData['offer_product_id'] = $formData['orig_offer_product_id'];
                $formData['offer_variation_id'] = $formData['orig_offer_variation_id'];
				$formData['offer_quantity'] = $formData['orig_offer_quantity'];
                $formData['offer_price_per'] = $formData['orig_offer_price_per'];
				$formData['offer_amount'] = $formData['orig_offer_amount'];
				
				// set post vars
                $newPostData['post_date'] = date("Y-m-d H:i:s", current_time('timestamp', 0 ) );
				$newPostData['post_date_gmt'] = gmdate("Y-m-d H:i:s", time());
                $newPostData['post_type'] = 'woocommerce_offer';
                $newPostData['post_status'] = 'publish';
                $newPostData['post_title'] = 'Offer - '. $formData['offer_email'];

                // check for parent post id
                $parent_post_id = (isset($_POST['parent_offer_id'])) ? $_POST['parent_offer_id'] : '';
                $parent_post_status = get_post_status($parent_post_id);
                $post_parent_type = get_post_type($parent_post_id);

                // set offer comments
                $comments = (isset($_POST['offer_notes']) && $_POST['offer_notes'] != '') ? strip_tags(nl2br($_POST['offer_notes']), '<br><p>') : '';

                // If has parent offer id - valid post id, post_type woocommerce_offer, post_status of pending offer or accepted offer
                if(isset($parent_post_id) && $parent_post_id != '' && $parent_post_status && ($parent_post_status == 'publish' || $parent_post_status == 'accepted-offer' ) && $post_parent_type == 'woocommerce_offer')
                {
                    $parent_post = array(
                        'ID'           => $parent_post_id,
                        'post_modified' => date("Y-m-d H:i:s", current_time('timestamp', 0 )),
                        'post_modified_gmt' => gmdate("Y-m-d H:i:s", current_time('timestamp', 0 )),
                        'post_status' => 'publish'
                    );

                    // Update the parent post into the database
                    wp_update_post( $parent_post);

                    $formDataUpdated = array();

                    $formDataUpdated['offer_quantity'] = $formData['offer_quantity'];
                    $formDataUpdated['offer_price_per'] = $formData['offer_price_per'];
                    $formDataUpdated['offer_amount'] = $formData['offer_amount'];

                    // Insert new Post Meta Values
                    foreach($formDataUpdated as $k => $v)
                    {
                        $newPostMetaData = array();
                        $newPostMetaData['post_id'] = $parent_post_id;
                        $newPostMetaData['meta_key'] = $k;
                        $newPostMetaData['meta_value'] = $v;

                        update_post_meta( $parent_post_id, $newPostMetaData['meta_key'], $newPostMetaData['meta_value']);
                    }

                    // Insert WP comment
                    $comment_text = "<span>Buyer Submitted Counter Offer</span>";

                    $data = array(
                        'comment_post_ID' => $parent_post_id,
                        'comment_author' => $formData['offer_name'],
                        'comment_author_email' => $formData['offer_email'],
                        'comment_author_url' => '',
                        'comment_content' => $comment_text,
                        'comment_type' => '',
                        'comment_parent' => 0,
                        'user_id' => '',
                        'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
                        'comment_agent' => '',
                        'comment_date' => date("Y-m-d H:i:s", current_time('timestamp', 0 )),
                        'comment_approved' => 1,
                    );
                    wp_insert_comment($data);
                }
                else
                {
                    // Insert new Post
                    if( wp_insert_post( $newPostData ) )
                    {
                        // Set Parent ID for use later
                        $parent_post_id = $wpdb->insert_id;

                        // Insert new Post Meta Values
                        foreach($formData as $k => $v)
                        {
                            $newPostMetaData = array();
                            $newPostMetaData['post_id'] = $parent_post_id;
                            $newPostMetaData['meta_key'] = $k;
                            $newPostMetaData['meta_value'] = $v;

                            if(!$wpdb->query( $wpdb->prepare(
                                "INSERT INTO $wpdb->postmeta
                                    ( post_id, meta_key, meta_value )
                                    VALUES ( %d, %s, %s )
                                ",
                                $parent_post_id,
                                $newPostMetaData['meta_key'],
                                $newPostMetaData['meta_value']
                                ) ) )
                            {
                                ////echo json_encode($wpdb->last_query);
                                // return error msg
                                echo json_encode(array("statusmsg" => 'failed', "statusmsgDetail" => 'database error'));
                                exit;
                            }
                        }

                        // Insert WP comment
                        $comment_text = "Created New Offer";

                        $data = array(
                            'comment_post_ID' => $parent_post_id,
                            'comment_author' => 'admin',
                            'comment_author_email' => '',
                            'comment_author_url' => '',
                            'comment_content' => $comment_text,
                            'comment_type' => '',
                            'comment_parent' => 0,
                            'user_id' => 1,
                            'comment_author_IP' => '127.0.0.1',
                            'comment_agent' => '',
                            'comment_date' => date("Y-m-d H:i:s", current_time('timestamp', 0 )),
                            'comment_approved' => 1,
                        );
                        wp_insert_comment($data);
                    }
                    else
                    {
                        // return error msg
                        echo json_encode(array("statusmsg" => 'failed', "statusmsgDetail" => 'database error'));
                        exit;
                    }
                }

                // Insert buyer comment
                if($comments != '')
                {
                    // Insert WP comment
                    $comment_text = "<span>Buyer Note: </span>".$comments;

                    $data = array(
                        'comment_post_ID' => $parent_post_id,
                        'comment_author' => '',
                        'comment_author_email' => '',
                        'comment_author_url' => '',
                        'comment_content' => $comment_text,
                        'comment_type' => '',
                        'comment_parent' => 0,
                        'user_id' => '',
                        'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
                        'comment_agent' => '',
                        'comment_date' => date("Y-m-d H:i:s", current_time('timestamp', 0 )),
                        'comment_approved' => 1,
                    );
                    wp_insert_comment($data);
                }

                // Email Out - admin email notification of new offer


                // Success
                sleep(1);
                echo json_encode(array("statusmsg" => 'success'));
                exit;
            }
		}
	}	
}