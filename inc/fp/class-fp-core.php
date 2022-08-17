<?php

class FP_Core {

	private static $instanse;


	public function __construct() {

		$this->hooks();

		$this->includes();
	}


	public function hooks() {

		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );

		add_action( 'init', [ $this, 'register_cpt_event' ] );
		add_action( 'init', [ $this, 'register_tax_topics' ] );
		add_action( 'init', [ $this, 'register_tax_hashtags' ] );

	}


	public function includes() {

		require_once get_template_directory() .'inc/fp/class-fp-shortcode.php';
		new fp_Shortcode();

		//require_once get_template_directory() .'inc/fp/class-fp-ajax.php';
		//new fp_Ajax();

		require_once get_template_directory() .'inc/fp/class-fp-rest.php';
		new FP_Rest();

	}


	public function enqueue() {

		

		wp_register_script(
			'fp-script',
			get_template_directory() . 'js/fp-script.js',
			[ 'jquery' ],
			filemtime( get_template_directory() .'js/fp-script.js' ),
			true
		);

		

		/*wp_register_script(
			'fp-script-ajax',
			fp_URI . 'js/fp-ajax.js',
			[ 'jquery' ],
			filemtime( get_template_directory() .'js/fp-ajax.js' ),
			true
		);
		wp_localize_script(
			'fp-script-ajax',
			'fp_ajax',
			[
				'url'   => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'fp-ajax-nonce' ),
			]
		);*/

		wp_register_script(
			'fp-script-rest',
			fp_URI . 'js/fp-rest.js',
			[ 'jquery' ],
			filemtime( get_template_directory() .'js/fp-rest.js' ),
			true
		);

		wp_localize_script(
			'fp-script-rest',
			'fp_rest',
			[
				'root'  => esc_url_raw( rest_url() ),
				'nonce' => wp_create_nonce( 'wp_rest' ),
			]
		);

	}

	


	public static function instance() {

		if ( is_null( self::$instanse ) ) {
			self::$instanse = new self();

		}

		return self::$instanse;
	}
}