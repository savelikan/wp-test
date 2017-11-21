<?php namespace TestingPlugin;

use TestingPlugin\Admin\Admin;
use TestingPlugin\Frontend\Frontend;

/**
 * Class TestingPluginPlugin
 *
 * @package TestingPlugin
 */
class TestingPluginPlugin {

    private $fileManager;

    public $pluginName = 'premmerce-testing-plugin';


	public function __construct( FileManager $fileManager ) {

		$this->fileManager = $fileManager;

        add_action( 'init', [ $this, 'loadTextDomain' ]);
        add_action( 'init', [$this, 'register_new_post_type'] );
        add_action( 'init', [$this, 'register_new_texomony'] );

        add_action( 'add_meta_boxes', [$this, 'adding_post_type_metabox'] );
        add_action( 'save_post', [$this, 'adding_post_type_metabox_save']);
	}

	/**
	 * Run plugin part
	 */
	public function run() {
		if ( is_admin() ) {
			new Admin( $this->fileManager);
		} else {
			new Frontend( $this->fileManager);
		}

	}

    /**
     * Load plugin translations
     */
    public function loadTextDomain()
    {
        $name = $this->fileManager->getPluginName();
        load_plugin_textdomain($this->pluginName, false, $name . '/languages/');
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
    public function register_new_post_type() {
        register_post_type('premmerce_my_post',
            [
                'labels'              => [
                    'name'          => __('My post type', $this->pluginName),
                    'singular_name' => __('My post types', $this->pluginName),
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

    // додати метабокс до поста
    public function adding_post_type_metabox( $post ) {
        add_meta_box(
            'id-meta-box',
            __( 'My Personal Meta Box' ),
            [$this, 'render_post_type_metabox'],
            'premmerce_my_post',
            'side',
            'default'
        );
    }

    // вигляд метабоксу
    public function render_post_type_metabox ($post,$add)
    {
        $meta_values = get_post_meta( $post->ID, $add['id'], true );
        ?>
        <label for="id-meta-box">Description for this field</label>
        <select name="id-meta-box" id="id-meta-box" class="postbox">
            <option <?php selected($meta_values, '' ); ?> value="">Select something...</option>
            <option <?php selected($meta_values, 'something' ); ?> value="something">Something</option>
            <option <?php selected($meta_values, 'else' ); ?> value="else">Else</option>
        </select>
        <?php
    }

    public function adding_post_type_metabox_save( $post_id ){
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
            return $post_id;
        if ( ! current_user_can( 'edit_page', $post_id ) )
            return $post_id;
        if (array_key_exists('id-meta-box', $_POST)) {
            update_post_meta(
                $post_id,
                'id-meta-box',
                $_POST['id-meta-box']
            );
        }
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