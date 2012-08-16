<?php
/**
 * Plugin settings for WG-splash
 *
 * @author Anders Lisspers, Webbgaraget (http://www.webbgaraget.se)
 * @version 1.0
 */

class WG_Splash_Settings
{
	const SLUG = 'wg-splash';
	const SECTION_SLUG = 'wg-splash-section';
	
	public function __construct()
	{
		// Set up the settings page
		add_action( 'admin_init', array( &$this, 'setup_settings' ) );

		// Give the plugin a settings link in the menu
		add_action( 'admin_menu', array( &$this, 'add_admin_page' ) );
	}

	public function add_admin_page()
	{
		add_submenu_page( 'options-general.php', 'Webbgaraget Splash', 'WG Splash', 'manage_options', self::SLUG, array( &$this, 'render_options_page' ) );
	}

	public function setup_settings()
	{
		add_settings_section( self::SECTION_SLUG, 'Settings', array( &$this, 'render_options_section' ), self::SLUG );
		
		// Splash content
		register_setting( self::SLUG . '-options', self::SLUG . '-content');
		add_settings_field( self::SLUG . '-content', 'HTML Markup<br>Use %URL% where you want the link to hide the splash.', array( &$this, 'render_input_content' ), self::SLUG, self::SECTION_SLUG );
		
		// Use JavaScript
		register_setting( self::SLUG . '-options', self::SLUG . '-use-javascript');
		add_settings_field( self::SLUG . '-use-javascript', 'Use JavaScript to hide the splash', array( &$this, 'render_input_use_javascript' ), self::SLUG, self::SECTION_SLUG );

	}

	public function render_options_page()
	{
	?>
		<h2>Webbgaraget Splash</h2>
		
		<p>
			This plugin ouputs markup for a splash page at wp_footer if the visitor has not seen the splash before.<br>
			Cookies are used in order to detect whether the visitor is new or returning.
		</p>
		<p>
			There are two ways of hiding the splash:
		</p>
		<ol>
			<li>By reloading the page</li>
			<li>With JavaScript (this requires that your markup contains an element with the class "wg-splash-hide")</li>
		</ol>
		<p>
			When the splash is hidden, a cookie is set so the visitor does not have to see the splash again. <br>Using alternative 1 above, the
			cookie is set at page reload with PHP. Using alternative 2, the cookie is set with JavaScript.
		</p>
		
		<form action="options.php" method="post">
			<?php settings_fields( self::SLUG . '-options' ); ?>
			<?php do_settings_sections( self::SLUG ); ?>
			<input name="Submit" type="submit" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
		</form>
	<?php
	}

	public function render_options_section()
	{
		?>
		<p>
			Enter your HTML markup for the splash page.
		</p>
		<p>The output in wp_footer will be as follows. Remember to style the mentioned classes with CSS.</p>
		<pre>
&lt;section class="wg-splash-wrapper"&gt;
	&lt;div class="wg-splash-overlay"&gt;&lt;/div&gt;
	&lt;div class="wg-splash-content"&gt;
		
		---- YOUR MARKUP HERE  ---

	&lt;/div&gt;
&lt;/section&gt;
		</pre>
		<?php
	}

	public function render_input_content()
	{
		$this->render_setting_input( self::SLUG . '-content', 'textarea' );
	}
	
	public function render_input_use_javascript()
	{
		$this->render_setting_input( self::SLUG . '-use-javascript', 'checkbox' );
	}
	
	protected function render_setting_input($id, $type = 'text')
	{
		$saved_value = get_option( $id );
		switch ( $type )
		{
			case 'checkbox':
				echo "<input type=\"{$type}\" id=\"{$id}\" name=\"{$id}\" value=\"1\"" . ($saved_value == '1' ? ' checked="checked"' : '') . ">";
				break;

			case 'textarea':
				echo "<textarea rows=\"10\" cols=\"70\" id=\"{$id}\" name=\"{$id}\">{$saved_value}</textarea>";
				break;
			
			default:
				echo "<input type=\"{$type}\" id=\"{$id}\" name=\"{$id}\" value=\"{$saved_value}\">";
				break;
		}
	}
}
new WG_Splash_Settings();