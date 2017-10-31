<?php namespace TestingPlugin\Admin;

use TestingPlugin\FileManager;

/**
 * Class Admin
 *
 * @package TestingPlugin\Admin
 */
class Admin {

	/**
	 * @var FileManager
	 */
	private $fileManager;

    /**
     * Тут збережемо всі налаштування
     */
    private $params;

	/**
	 * Admin constructor.
	 *
	 * Register menu items and handlers
	 *
	 * @param FileManager $fileManager
	 */
	public function __construct( FileManager $fileManager ) {
		$this->fileManager = $fileManager;
        $this->updateParams();

        //--створити силку в головному меню
        add_action( 'admin_menu', function (){
            add_menu_page(
                __('Main page of testing module', 'premmerce-testing-plugin'),
                __('Testing module', 'premmerce-testing-plugin'),
                'manage_options',
                'premmerce-index',
                function(){}
            );
        } );

        //--створити підменю
        add_action( 'admin_menu', function (){
            add_submenu_page(
                'premmerce-index',
                __('Settings SETTINGS', 'premmerce-testing-plugin'),
                __('Settings SETTINGS', 'premmerce-testing-plugin'),
                'manage_options',
                'premmerce-settings',
                [$this, 'settingsPage']
            );
        } );

        //--створити підменю
        add_action( 'admin_menu', function (){
            add_submenu_page(
                'premmerce-index',
                __('Settings OPTIONS', 'premmerce-testing-plugin'),
                __('Settings OPTIONS', 'premmerce-testing-plugin'),
                'manage_options',
                'premmerce-options',
                [$this, 'optionsPage']
            );
        } );

        /**
         * Реєстрація premmerce_settings_init по хуку admin_init
         */
        add_action( 'init', [$this, 'register_new_post_type'] );
        add_action( 'admin_init', [$this, 'premmerce_settings_init'] );
	}



	public function updateParams(){
        // Отримати налаштування та заповнити пусті значення стандатрними
        $this->params = array(
            'premmerce_field_input' => '',
            'premmerce_field_checkbox' => false,
            'premmerce_field_select' => 0,
        );
        $this->params = array_merge(
            $this->params,
            get_option('premmerce_options')
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




    //--показати сторінку налаштування OPTIONS
    function optionsPage()
    {
        // Перевірка прав доступу
        if (!current_user_can('manage_options')) {
            return;
        }

        // Зберігаємо або оновлюємо дані
        if(isset($_POST) and isset($_POST['submit'])) {
            if (get_option('premmerce_options') !== false) {
                update_option('premmerce_options', $_POST['premmerce_options']);
            } else {
                add_option('premmerce_options', $_POST['premmerce_options'], null, 'no');
            }

            // Оновити параметри в класі, щоб після збереження показати нові дані
            $this->updateParams();
        }

        $this->fileManager->includeTemplate('admin/options.php', ['options'=>$this->params]);

    }






	public function settingsPage(){
        // Перевірка прав доступу
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( isset( $_GET['settings-updated'] ) ) {
            add_settings_error(
                'premmerce_messages',
                'premmerce_message',
                __('Saved', 'premmerce-testing-plugin'),
                'updated' );
        }

        // Показати повідомлення error/update
        settings_errors( 'premmerce_messages' );

        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                // Поля для безпеки для зареєстрованих налаштувань "premmerce"
                settings_fields( 'premmerce' );

                // Вивід секцій налаштувань та їх полів
                do_settings_sections( 'premmerce' );

                // Кнопка збереження
                submit_button( __('Save settings', 'premmerce-testing-plugin') );
                ?>
            </form>
        </div>
        <?php
    }



    public function premmerce_settings_init() {
        // Зареєструвати налаштування "premmerce" page
        register_setting( 'premmerce', 'premmerce_options' );

        // Зареєструвати секцію на сторінці "premmerce"
        add_settings_section(
            'premmerce_section',
            __('Section', 'premmerce-testing-plugin'),
            [$this, 'premmerce_section_callback'],
            'premmerce'
        );

        // Зареєструвати поле в секції "premmerce_section"
        add_settings_field(
            'premmerce_field_input',
            __('Text', 'premmerce-testing-plugin'),
            [$this, 'premmerce_input_callback'],
            'premmerce',
            'premmerce_section',
            [
                'label_for' => 'premmerce_field_input',
            ]
        );

        // Зареєструвати поле в секції "premmerce_section"
        add_settings_field(
            'premmerce_field_checkbox',
            __('Checkbox', 'premmerce-testing-plugin'),
            [$this, 'premmerce_checkbox_callback'],
            'premmerce',
            'premmerce_section',
            [
                'label_for' => 'premmerce_field_checkbox',
            ]
        );

        // Зареєструвати поле в секції "premmerce_section"
        add_settings_field(
            'premmerce_field_select',
            __('Select', 'premmerce-testing-plugin'),
            [$this, 'premmerce_select_callback'],
            'premmerce',
            'premmerce_section',
            [
                'label_for' => 'premmerce_field_select',
            ]
        );
    }


    public function premmerce_section_callback() {
        echo "<p>"._e("Content of section", 'premmerce-testing-plugin')."</p>";
    }

    public function premmerce_input_callback( $args ) {
        ?>
        <input type="text"
               name="premmerce_options[<?= esc_attr( $args['label_for'] ); ?>]"
               value="<?= $this->params[ $args['label_for'] ] ?>">
        <?php
    }


    public function premmerce_checkbox_callback( $args ) {
        ?>
        <input type="checkbox"
               name="premmerce_options[<?= esc_attr( $args['label_for'] ); ?>]"
               value="1" <?php checked( $this->params[ $args['label_for'] ] ); ?> >
        <?php
    }


    public function premmerce_select_callback( $args ) {
        ?>
        <select name="premmerce_options[<?= esc_attr( $args['label_for'] ); ?>]">
            <option value="1" <?php selected( $this->params[ $args['label_for'] ], 1 ); ?>><?=__('Variant 1', 'premmerce-testing-plugin')?></option>
            <option value="2" <?php selected( $this->params[ $args['label_for'] ], 2 ); ?>><?=__('Variant 2', 'premmerce-testing-plugin')?></option>
            <option value="3" <?php selected( $this->params[ $args['label_for'] ], 3 ); ?>><?=__('Variant 3', 'premmerce-testing-plugin')?></option>
        </select>
        <?php
    }



}