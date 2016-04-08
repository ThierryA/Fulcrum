<?php
/**
 * Login Form
 *
 * @package     Fulcrum\Security
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */
namespace Fulcrum\Security;

use Fulcrum\Config\Config_Contract;

class Login_Form {

	/**
	 * Runtime configuration parameters
	 *
	 * @var Config_Contract
	 */
	protected $config;

	/**************************
	 * Instantiate & Initialize
	 *************************/

	/**
	 * Instantiate the object
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract $config
	 */
	public function __construct( Config_Contract $config ) {
		$this->config = $config;

		$this->init_events();
	}

	/**
	 * Initialize each of the hooks
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function init_events() {

		add_filter( 'login_errors', array( $this, 'login_errors' ) );

		add_action( 'login_enqueue_scripts', array( $this, 'enqueue_scripts' ), 9999 );

		add_filter( 'login_headerurl', array( $this, 'link_header_url_to_main_page' ) );

		add_filter( 'login_headertitle', array( $this, 'link_header_title_to_main_page' ) );
	}

	/**
	 * Render out the login form CSS
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		?>
		<style type="text/css">
			<?php include( $this->config['css'] ); ?>

			.login h1 a {
				background-image: url( <?php echo esc_url( $this->config['logo'] );?> );
			}
		</style>
		<?php
	}

	/**
	 * Change the standard WordPress login form's header title
	 *
	 * @since  1.0.0
	 *
	 * @return string site's name
	 */
	public function link_header_title_to_main_page() {
		return get_bloginfo( 'name' );
	}


	/**
	 * Change the standard WordPress login form's header title
	 *
	 * @since  1.0.0
	 *
	 * @return string home URL
	 */
	public function link_header_url_to_main_page() {
		return home_url();
	}

	/**
	 * Prevents brute-force hacking into our site by eliminating the
	 * error messages on the wp-login.php page.
	 *
	 * @since 1.0.0
	 *
	 * @param string $error
	 *
	 * @return string Returns the new error message
	 */
	public function login_errors( $error ) {
		$error_message = esc_html( $this->config->login_error_message );
		$error         = str_replace( 'Invalid username.', $error_message, $error );
		$error         = preg_replace( '{The password you entered for the username <strong>.*</strong> is incorrect\.}', $error_message, $error );

		return $error;
	}
}
