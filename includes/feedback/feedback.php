<?php
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
    define('OPTIN', "bill-vote" );
    define('DINSTALL', "bill_installed" );
class Bill_Vote478 {
	function __construct() {
		add_action( 'load-plugins.php', array( __CLASS__, 'init' ) );
		add_action( 'wp_ajax_bill_vote',  array( __CLASS__, 'vote' ) );
	}
	public static function init() {
		$vote = get_option( 'bill_vote' );
        // $vote = '';
        // die('vote: '.$vote);
        // yes
  		$timeinstall =  get_option( DINSTALL, '');
        if($timeinstall == '')
        {
            $w = update_option(DINSTALL, time() );
            if (!$w)
              add_option(DINSTALL, time() );
            $timeinstall = time();    
        }  
   		$timeout = time() > ( $timeinstall + 60*60*24*3 );     
		if ( in_array( $vote, array( 'yes', 'no', 'tweet' ) ) || !$timeout ) return;
    	add_action( 'in_admin_footer', array( __CLASS__, 'message' ) );
		add_action( 'admin_head',      array( __CLASS__, 'register' ) );
		add_action( 'admin_footer',    array( __CLASS__, 'enqueue' ) );
	}
	public static function register() {
	    wp_enqueue_style( 'bill-vote' , REPORTATTACKSURL.'includes/feedback/feedback.css');
    	wp_register_script( 'bill-vote',REPORTATTACKSURL.'includes/feedback/feedback.js' , array( 'jquery' ), REPORTATTACKSVERSION, true );
	}
	public static function enqueue() {
		wp_enqueue_style( 'bill-vote' );
		wp_enqueue_script( 'bill-vote' );
	}
	public static function vote() {
		$vote = sanitize_key( $_GET['vote'] );
		if ( !is_user_logged_in() || !in_array( $vote, array( 'yes', 'no', 'later' ) ) ) die( 'error' );
		update_option( 'bill_vote', $vote );
		if ( $vote === 'later' ) update_option( 'bill_installed', time() );
		die( 'OK: ' . $vote );
	}
	public static function message() {
?>
		<div class="bill-vote" style="display:none">
			<div class="bill-vote-wrap">
				<div class="bill-vote-gravatar"><a href="http://profiles.wordpress.org/sminozzi" target="_blank"><img src="https://en.gravatar.com/userimage/94727241/31b8438335a13018a1f52661de469b60.jpg?size=100" alt="Bill Minozzi" width="70" height="70"></a></div>
				<div class="bill-vote-message">
					<p>
                    <?php _e( 'Hi, my name is Bill Minozzi, and I am developer of plugin Report Attacks.', 'reportattacks' );
                          echo '<br />'; 
                          _e( 'If you like this plugin, please write a few words about it. It will help other people find this useful plugin more quickly.' , "reportattacks");
                          echo '<br /><br /><b>';
                          _e('Thank you!', "reportattacks");
                          echo '</b>';
                          ?></p>
   					<p>
						<a href="<?php echo admin_url( 'admin-ajax.php' ); ?>?action=bill_vote&amp;vote=yes" class="bill-vote-action button button-medium button-primary" data-action="http://reportattacks.com/share/"><?php _e( 'Rate or Share', 'reportattacks' ); ?></a>
						<a href="<?php echo admin_url( 'admin-ajax.php' ); ?>?action=bill_vote&amp;vote=no" class="bill-vote-action button button-medium"><?php _e( 'No, dismiss', 'reportattacks' ); ?></a>
						<span><?php _e( 'or', 'reportattacks' ); ?></span>
						<a href="<?php echo admin_url( 'admin-ajax.php' ); ?>?action=bill_vote&amp;vote=later" class="bill-vote-action button button-medium"><?php _e( 'Remind me later', 'reportattacks' ); ?></a>
					</p>
				</div>
				<div class="bill-vote-clear"></div>
			</div>
		</div>
		<?php
	}
}
new Bill_Vote478;