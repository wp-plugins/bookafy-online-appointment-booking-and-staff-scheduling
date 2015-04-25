<?php
/*
Plugin Name: Bookafy-online-appointment-booking-and-staff-scheduling
Plugin URI: www.bookafy.com
Description: Bookafy is an online appointment booking and staff scheduling software for small service based businesses. Bookafy enables customers to book appointments online 24/7 and gives staff members access to the calendar from mobile, tablet or computer.
Version: 1.0
Author: Bookafy 
Author URI: www.bookafy.com
License: GPL2
*/
class MyBookafy_Plugin {

	private static $add_script;
	/**
	 * Construct.
	 */
	function __construct() {

		add_action('admin_init', array( &$this, 'bookafy_init'), 0 );
		add_action('admin_menu', array( &$this, 'bookafy_add_page'), 0 );
			add_action('plugins_loaded', array( &$this, 'bookafy_widgets'), 0 );
	
		add_action( 'admin_enqueue_scripts', array(&$this,'load_styles'));
		
		// Admin Notices
		if ( ! empty( $_GET['hide_sched_check_bizkey'] ) ) {
			update_option( 'sched_hide_check_bizkey', 'hide' );
		}
		$sched_hide_check_bizkey = get_option( 'sched_hide_check_bizkey' );
		$sched_bizkey = array();
		$sched_bizkey = get_option( 'user_bizurl' );
	
		if (is_array($sched_bizkey)) {
			$sched_bizkey = array_filter($sched_bizkey);
		}

		
		if (($sched_hide_check_bizkey != 'hide') && (empty($sched_bizkey))){
			add_action( 'admin_notices', array( &$this, 'missing_your_bizurl' ), 0);
		}
		
	}
	
	// Init plugin options to white list our options
	function bookafy_init(){
	
		register_setting( 'bookify_options', 'user_bizurl');
	
	}
	
	function missing_your_bizurl() {
		$error = 'The Bookafy Plugin needs a valid business url to work. Please update your business url from the settings page';
		$message = sprintf('<div class="error"><p>%1$s. <a href="%2$s">%3$s</a></p></div>', $error, add_query_arg( 'hide_sched_check_bizkey', 'true' ), 'Hide this notice' );
		echo $message;
	}
	
	

	// Add menu page
	function bookafy_add_page() {
		add_options_page('Bookafy Plugin Setup', 'Bookafy Scheduling', 'manage_options', 'myBookafy_admin_page', array( &$this, 'my_options_do_page'));
	}
	
	

	/**
	 * Load CSS
	 */
	public function load_styles() {
		
		$css = file_exists( get_stylesheet_directory() . '/book-admin.css' )
			? get_stylesheet_directory_uri() . '/book-admin.css'
			: plugins_url( '/css/book-admin.css', __FILE__ );
			
		wp_register_style( 'book-admin', $css, array(), '', 'all' );
		wp_enqueue_style( 'book-admin' );
	}

	function bookafy_widgets() {
		
		
		function responsive_widget($atts) {
		
				$user_bizkey = get_option('user_bizurl');
				 
				$bfy_bizkey = $user_bizkey['bizurl'];
				if(empty($bfy_bizkey)){
					$bfy_bizkey="http://bookafy.com";
				}
				if(isset($atts['width'])){
					$width=$atts['width'];
				} else {
					$width=1000;	
				}
				if(isset($atts['height'])){
					$height=$atts['height'];
				} else {
					$height=700;
				}
				$bookify_button = '<iframe src="'.$bfy_bizkey.'" height="'.$height.'" width="'.$width.'"/></iframe>';
				return $bookify_button;
				
		}
		
		function my_button($atts) {
		
				$user_bizkey = get_option('user_bizurl');
				 
				$bfy_bizkey = $user_bizkey['bizurl'];
				if(empty($bfy_bizkey)){
					$bfy_bizkey="http://bookafy.com";
				}
				$buttons=array('button1'=>1,'button2'=>2,'button3'=>3,'button4'=>4,'button5'=>5,'button6'=>6,'button7'=>7,'button8'=>8,'button9'=>9,'button10'=>10,'button11'=>11,'button12'=>12,'button13'=>13,'button14'=>14,'button15'=>15,'button16'=>16,'button17'=>17,'button18'=>18,'button19'=>19,'button20'=>20,'button21'=>21,'button22'=>22,'button23'=>23,'button24'=>24,'button25'=>25,'button26'=>26,'button27'=>27,'button28'=>28,'button29'=>29,'button30'=>30,'button31'=>31,'button32'=>32,'button33'=>33,'button34'=>34,'button35'=>35,'button36'=>36,'button37'=>37,'button38'=>38);
				if(isset($atts['type'])){
					if(in_array($atts['type'],array_keys($buttons))){
						
						$imgUrl=plugins_url( "images/".$buttons[$atts['type']].".jpg", __FILE__ );
						$bookify_button = '<div style="text-align:center;"><a href="'.$bfy_bizkey.'" title="Book Now"  id="schednowlink"><img style="height:50px;width:150px;" src="'.$imgUrl.'" alt="Book Now" border="0" /></a></div>';
						return $bookify_button;
					} else {
						return "Button type is invalid";
					}
				} else {
					$imgUrl=plugins_url( "images/1.jpg", __FILE__ );
					$bookify_button = '<div style="text-align:center;"><a href="'.$bfy_bizkey.'" title="Book Now"  id="schednowlink"><img style="height:50px;width:150px;" src="'.$imgUrl.'" alt="Book Now" border="0" /></a></div>';
					return $bookify_button;
				}
				
		}
		add_shortcode('bookafy_button' , 'my_button');
		add_shortcode('bookafy_now', 'responsive_widget');
	}
	

	// Draw the menu page itself
	function my_options_do_page() {
	?>
		<div class="wrap" style="font-size: 18px">	
			<?php
			if (isset($_GET['tab'])) {
				$active_tab = $_GET['tab'];
			} else {
				$active_tab = 'standard_setup';
			}
			?>
			<h2 class="nav-tab-wrapper">
				<a href="?page=myBookafy_admin_page&tab=standard_setup" class="nav-tab <?php echo $active_tab == 'standard_setup' ? 'nav-tab-active' : ''; ?>">Settings</a>  
				<a href="?page=myBookafy_admin_page&tab=advanced_setup" class="nav-tab <?php echo $active_tab == 'advanced_setup' ? 'nav-tab-active' : ''; ?>">Advanced</a> 
			</h2>
			<?php
			if ($active_tab == 'standard_setup') {
			?>
			<div style="background: #FFF;-moz-border-radius: 3px;border-radius: 3px;margin:5%;margin-top: 30px;padding: 20px;-moz-box-shadow: 0 0 5px #888;-webkit-box-shadow: 0 0 5px#888;box-shadow: 0 0 5px #888;">
				
			<div style="margin-bottom: 20px">
				<h2>Bookafy Plugin Settings</h2>
			</div>
			<form method="post" action="options.php">
				<?php settings_fields('bookify_options'); ?>
				
				<ol>
                	<div style="float:left;width:60%;">
					<li style="font-size: 18px; font-weight: bold; margin-top: 10px;margin-bottom:10px">Step One - Insert Your Biz URL</li>
						<?php $options = get_option('user_bizurl'); ?>			
						<ul style="font-size: 16px">
						Business URL: <input type="text" name="user_bizurl[bizurl]" id="bizkey_field" value="<?php echo $options['bizurl']; ?>" /><input type="submit" class="button-primary" value="<?php _e('Save Business URL') ?>" style="margin-left: 20px" />
						 
                        <p> 
							 <span style="margin-left: 20px;font-size: 14px"><a href="?page=myBookafy_admin_page&tab=advanced_setup#bizkey">What's my Business URL?</a></span>
						</p>
                        <p> 
							If you don't have a Bookafy account <a href="http://bookafy.com/" target="_blank">click here</a> to signup fast.
						</p>
						</ul>
					<li style="font-size: 18px; font-weight: bold; margin-top: 10px;margin-bottom:10px">Step Two - Create Your Shortcode</li>
							<ul style="font-size: 16px">
							<p>
								Choose from 21 button types and 2 widgets. Just click the button size you want and the style and watch the button shortcode change to the right.
								If you want to add a Scheduling widget click either the embedded or overlay widget below.
							</p>
                            </div>
                            <div style="float:left;width:40%;margin-bottom:20px;"><iframe width="360" height="250" src="https://www.youtube.com/embed/Gx_rJ0EpelU" frameborder="0" allowfullscreen></iframe>
                            </div>
							<div style="margin-left: -4%;margin-right: -1%">	
							<div class="buttons" style="float: left;width: 60%;">
								<?php for($i=1;$i<=38;$i++){ ?>
								<div class="block" style="float: left;width: 30%;cursor:pointer;" data-btn="button<?php echo $i; ?>" rel="<?php echo plugins_url( "images/$i.jpg", __FILE__ ); ?>" id="button<?php echo $i; ?>">
									<img style="height:50px;width:150px;" src="<?php echo plugins_url( "images/$i.jpg", __FILE__ ); ?>">
								</div>
							
								<?php } ?></div>	
				                <div class="shortcode-updater" id="button-shortcode-updater">
				                	<h2>Button Shortcode</h2>
				                	<p>Copy the below shortcode and paste it into any page or post.</p>
				                	<img class="buttonImg" src="<?php echo plugins_url( "images/1.jpg", __FILE__ ); ?>" />
				                	<input type="text" name="mybookafy_shortcode" id="mybookafy_shortcode" value="[bookafy_button type='button1']" />
									<h2>Widget Shortcode</h2>
				                	<p>Copy the below shortcode and paste it into any page or post.</p>
				                	<input type="text" name="mybookafy_widgetshortcode" id="mybookafy_widgetshortcode" value="[bookafy_now width='1000' height='700']" />
				                </div>
													            
				                <div class="schedclear"></div>
				            </div>
				            </ul>
					
				</ol>
                <script type="text/javascript">
                	jQuery( document ).ready(function( $ ) {
						$('.block').click(function(){
							rel=$(this).attr('rel');
							$(".buttonImg").attr("src",rel);
							btn=$(this).data('btn');
							$("#mybookafy_shortcode").attr("value","[bookafy_button type='"+btn+"']");
						});
						
					});
                </script>
			</form>
			</div>
			<?php
			}
			else {
			?>
			<div style="background: #FFF;-moz-border-radius: 3px;border-radius: 3px;margin:5%;margin-top: 30px;padding: 20px;-moz-box-shadow: 0 0 5px #888;-webkit-box-shadow: 0 0 5px#888;box-shadow: 0 0 5px #888;">
				<h2>Bookafy Advanced Setup</h2>
				<div id="business_url">
					<h4>1. How to get business URL?</h4>
					<p style="margin-left: 20px; font-size: 14px">
						<p style="margin-left: 20px; font-size: 14px">Make sure you have an Bookafy account. If you don't have one you can <a href="http://bookafy.com/" target="_blank">create here</a></p>
					</p>
				</div>
				<div id="use_schortcode">
					<h4>2. Use shortcode in the post or page for using Bookafy</h4>
					<p style="margin-left: 20px; font-size: 14px">Using the bookafy plugin shortcode <b>[bookafy_button]</b> and <b>[bookafy_now]</b> connect the bookafy with your website.
					</p>
				</div>
				<div id="manage_appointments">
					<h4>3. Here you can manage your appointments</h4>
					<p style="margin-left: 20px; font-size: 14px"><a href="http://bookafy.com/" target="_blank">Click here</a> to manage your appointments on the Bookafy.com
					</p>
				</div>
			</div>	
			<?php
			}
			?>
			
		</div>
	<?php	
	}

	

}
$MyBookafy_Plugin = new MyBookafy_Plugin();
?>