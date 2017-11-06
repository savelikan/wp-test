<?php namespace TestingPlugin;

use TestingPlugin\Admin\Admin;
use TestingPlugin\Frontend\Frontend;

/**
 * Class TestingPluginPlugin
 *
 * @package TestingPlugin
 */
class TestingPluginPlugin {

	/**
	 * @var FileManager
	 */
	private $fileManager;

	/**
	 * PluginManager constructor.
	 *
	 * @param FileManager $fileManager
	 */
	public function __construct( FileManager $fileManager ) {

		$this->fileManager = $fileManager;

        add_action('init', [ $this, 'loadTextDomain' ]);
        add_action( 'init', [$this, 'register_new_post_type'] );
        add_action( 'init', [$this, 'register_new_texomony'] );

	}

	/**
	 * Run plugin part
	 */
	public function run() {
		if ( is_admin() ) {
			new Admin( $this->fileManager );
		} else {
			new Frontend( $this->fileManager );
		}

	}

    /**
     * Load plugin translations
     */
    public function loadTextDomain()
    {
        $name = $this->fileManager->getPluginName();
        load_plugin_textdomain('premmerce-testing-plugin', false, $name . '/languages/');
    }


    // Створити нову таксономію
    public function register_new_texomony() {
        // створити одну таксономію
        register_taxonomy(
            'premmerce_my_taxonomy1',
            'premmerce_my_post',
            [
                'label'        => __( 'Моя таксономія 1' ),
                'query_var'     => true,
                'rewrite'     => true,
            ]
        );
        // створити ще одну таксономію
        register_taxonomy(
            'premmerce_my_taxonomy2',
            'premmerce_my_post',
            [
                'label'        => __( 'Моя таксономія 2' ),
                'query_var'     => true,
                'rewrite'     => true,
            ]
        );
    }


    // Реєстрація нового типу
    function register_new_post_type() {
        register_post_type('premmerce_my_post',
            [
                'labels'              => [
                    'name'          => __('My post type', 'premmerce-testing-plugin'),
                    'singular_name' => __('My post types', 'premmerce-testing-plugin'),
                ],
                'public'              => true,
                'has_archive'         => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'menu_position'       => 5,
                'menu_icon'           => 'dashicons-admin-page',
                'show_in_admin_bar'   => true,
                'show_in_nav_menus'   => true,
                'can_export'          => true,
                'exclude_from_search' => false,
                'publicly_queryable'  => true,
            ]
        );
    }



	/**
	 * Fired when the plugin is activated
	 */
	public function activate() {
		// TODO: Implement activate() method.
	}

	/**
	 * Fired when the plugin is deactivated
	 */
	public function deactivate() {
		// TODO: Implement deactivate() method.
	}

	/**
	 * Fired during plugin uninstall
	 */
	public static function uninstall() {
		// TODO: Implement uninstall() method.
	}
}