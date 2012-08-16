<?php
/**
 * WordPress plugin for generating markup for a splash page
 *
 * @author Anders Lisspers, Webbgaraget (http://www.webbgaraget.se)
 * @version 1.0
 */
class WG_Splash
{
	const COOKIE_NAME = 'wg-splash';
	
	const QUERY_STRING = 'splash-ok';
	
	
	public function __construct()
	{
		add_action( 'init', array( &$this, 'set_cookie' ) );
		
		if ( !$this->is_facebook_robot() && !$this->is_splash_cookie_set() )
		{
			// Add plugin CSS
			wp_enqueue_style( 'wg-splash', plugin_dir_url( __FILE__ ) . 'wg-splash.css' );
			
			if ( get_option( 'wg-splash-use-javascript' ) == '1' )
			{
				// Add plugin JavaScript for hiding the splash
				wp_enqueue_script( 'wg-splash', plugin_dir_url( __FILE__ ) . 'wg-splash.js', array( 'jquery' ), false, true );
			}
			
			// Add the splash markup too the bottom of the page
			add_action( 'wp_footer', array( &$this, 'the_splash' ) );
		}
	}
	
	/**
	 * Sets the cookie for the splash and then redirects to the same address (to remove the query string)
	 */
	public function set_cookie()
	{
		if ( !isset( $_GET[self::QUERY_STRING] ) )
		{
			return;
		}

		if ( $_GET[self::QUERY_STRING] == '0' )
		{
			// Ta bort cookie
			setcookie( self::COOKIE_NAME, 0, time() - 10, COOKIEPATH, COOKIE_DOMAIN );
		}
		elseif ( $_GET[self::QUERY_STRING] == '1' )
		{
			// Skapa cookie
			setcookie( self::COOKIE_NAME, 1, time() + 3600 * 24 * 365, COOKIEPATH, COOKIE_DOMAIN );
		}
			
		wp_redirect( $this->get_filtered_url() );
		exit();
		
	}
	
	/**
	 * Prints the HTML Markup for the splash
	 */
	public function the_splash()
	{
		$content = get_option( 'wg-splash-content' );
		$content = str_replace( '%URL%', $_SERVER['REQUEST_URI'] . ( strpos( $_SERVER['REQUEST_URI'], '?' ) === false ? '?' : '&') . self::QUERY_STRING . '=1', $content );
		?>
		<section class="wg-splash-wrapper">
			<div class="wg-splash-overlay"></div>
			<div class="wg-splash-content">
				<?php echo $content; ?>
			</div>
		</section>
		
		<?php
	}
	
	/**
	 * Detects whether the cookie has been set or not
	 * @return boolean
	 */
	protected function is_splash_cookie_set()
	{
		return ( isset( $_COOKIE[self::COOKIE_NAME] ) && $_COOKIE[self::COOKIE_NAME] == true);
	}
	
	/**
	 * Detects whether the current visitor is the Facebook scraping robot.
	 * The robot should be allowed in without seeing the splash
	 * @return boolean
	 */
	protected function is_facebook_robot()
	{
		return ( $_SERVER['HTTP_USER_AGENT'] == 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)' );
	}
	
	/**
	 * Gets the current URL without the splash query string
	 * @return string
	 */
	protected function get_filtered_url()
	{
		$url = $_SERVER['REQUEST_URI'];
		$url = preg_replace( '/[?&]' . self::QUERY_STRING . '=[0-1]/', '', $url );
		
		return $url;
	}
}
