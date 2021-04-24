<?php
header('Access-Control-Allow-Origin: *'); 
class Bill_Feedback3 {
	function __construct() {
		add_action( 'load-plugins.php', array( __CLASS__, 'init' ) );
		add_action( 'wp_ajax_bill_feedback',  array( __CLASS__, 'feedback' ) );
	}
	public static function init() {
		add_action( 'in_admin_footer', array( __CLASS__, 'message' ) );
		add_action( 'admin_head',      array( __CLASS__, 'register' ) );
		add_action( 'admin_footer',    array( __CLASS__, 'enqueue' ) );
	}
	public static function register() {
	    wp_enqueue_style( 'bill-feedback3' , REPORTATTACKSURL.'includes/feedback/feedback.css');
      	wp_register_script( 'bill-feedback3',REPORTATTACKSURL.'includes/feedback/feedback-last.js' , array( 'jquery' ), REPORTATTACKSVERSION, true );
	}
	public static function enqueue() {
		wp_enqueue_style( 'bill-feedback3' );
		wp_enqueue_script( 'bill-feedback3' );
	}
   	public static function message() {
    $wpversion = get_bloginfo('version');
    $current_user = wp_get_current_user();
    $plugin = plugin_basename(__FILE__); 
    $email = $current_user->user_email;
    $username =  trim($current_user->user_firstname);
    $user = $current_user->user_login;
    $user_display = trim($current_user->display_name);
    if(empty($username))
       $username = $user;
    if(empty($username))
       $username = $user_display;
       
?>        
		   <div class="bill-vote-desactivate-wrap-reportattacks" style="display:none">
              <div class="bill-vote-gravatar"><a href="http://profiles.wordpress.org/sminozzi" target="_blank"><img src="https://en.gravatar.com/userimage/94727241/31b8438335a13018a1f52661de469b60.jpg?size=100" alt="Bill Minozzi" width="70" height="70"></a></div>
		    	<div class="bill-vote-message">
                 <h4><?php _e("If you have a moment, Please, let us know you and why you are deactivating.","reportattacks");?></h4>
                 <?php _e("Hi, my name is Bill Minozzi, and I am developer of plugin ReportAttacks.","reportattacks");?>
                 <br />
                 <?php _e("If you Kindly tell us the reason so we can improve it and maybe give some support by email to you.","reportattacks");?>
                 <br /><br />             
                 <strong><?php _e("Thank You!","reportattacks");?></strong>
                 <br /><br /> 
                 <textarea rows="4" cols="50" id="explanation" name="explanation" placeholder="<?php _e("type here yours sugestions ...","reportattacks");?>" ></textarea>
                 <br /><br /> 
                 <input type="checkbox" class="anonymous" value="anonymous" /><small>Participate anonymous <?php _e("(In this case, we are unable to email you)","reportattacks");?></small>
                 <br /><br /> 			
		    			<a href="#" class="button button-primary button-close-submit"><?php _e("Yes, Submit and Deactivate","reportattacks");?></a>
                        <img src="/wp-admin/images/wpspin_light-2x.gif" id="imagewait" style="display:none"/ >
		    			<a href="#" class="button button-Secondary button-close-dialog"><?php _e("Cancel Deactivation","reportattacks");?></a>
		    			<a href="#" class="button button-secondary button-deactivate"><?php _e("No, Just Deactivate it","reportattacks");?></a>
                        <input type="hidden" id="version" name="version" value="<?php echo BDVERSION;?>" />
		                <input type="hidden" id="email" name="email" value="<?php echo $email;?>" />
		                <input type="hidden" id="username" name="username" value="<?php echo $username;?>" />
		                <input type="hidden" id="produto" name="produto" value="<?php echo $plugin;?>" />
		                <input type="hidden" id="wpversion" name="wpversion" value="<?php echo $wpversion;?>" />

                 <br /><br />
               </div>
		    </div>        
         </div> 
		<?php
	}
}
new Bill_Feedback3;

 if( ! update_option('bill_last_feedback', '1' ))
     add_option('bill_last_feedback', '1' );
?>