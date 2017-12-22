<?php namespace TestingPlugin\Admin;

use TestingPlugin\FileManager;

/**
 * Class Admin
 *
 * @package TestingPlugin\Admin
 */
class Admin {

    public $pluginName;

	private $fileManager;

    private $params;

    /**
     * Admin constructor.
     * @param FileManager $fileManager
     * @param $pluginName
     */
	public function __construct( FileManager $fileManager) {
		$this->fileManager = $fileManager;
        $this->pluginName = $this->fileManager->getPluginName();
        $this->updateParams();

        // додати ссилки у меню
        $this->addToMenu();

        add_action( 'admin_init', [$this, 'premmerce_settings_init'] );
	}


    /**
     * Створити посилання на плагін у меню адмінки
     */
	public function addToMenu(){
        //--створити силку в головному меню
        add_action( 'admin_menu', function (){
            add_menu_page(
                __('Main page of testing module', $this->pluginName),
                __('Testing module', $this->pluginName),
                'manage_options',
                'premmerce-index',
                function(){}
            );
        } );

        //--створити підменю
        add_action( 'admin_menu', function (){
            add_submenu_page(
                'premmerce-index',
                __('Settings SETTINGS', $this->pluginName),
                __('Settings SETTINGS', $this->pluginName),
                'manage_options',
                'premmerce-settings',
                [$this, 'settingsPage']
            );
        } );

        //--створити підменю
        add_action( 'admin_menu', function (){
            add_submenu_page(
                'premmerce-index',
                __('Settings OPTIONS', $this->pluginName),
                __('Settings OPTIONS', $this->pluginName),
                'manage_options',
                'premmerce-options',
                [$this, 'optionsPage']
            );
        } );

        //--створити підменю для витягування даних з поста
        add_action( 'admin_menu', function (){
            add_submenu_page(
                'premmerce-index',
                __('WP query', $this->pluginName),
                __('WP query', $this->pluginName),
                'manage_options',
                'premmerce-wp-query',
                [$this, 'wp_query']
            );
        } );
    }

    /**
     * Створити кастомний мета-бокс
     * @param $post_type
     * @param $post
     */
    function adding_custom_meta_boxes( $post_type, $post ) {
        add_meta_box(
            'my-meta-box',
            __( 'My Meta Box', $this->pluginName),
            'render_custom_box_html',
            'post',
            'normal',
            'default'
        );
    }


    /**
     * Зареєструвати, локалізувати та підключити скрипт
     */
	public function registerScript(){
        wp_register_script(
            'my_script',
            '/wp-content/plugins/'.$this->fileManager->getPluginName().'/assets/admin/sctipt.js' );
        $translation_array = array(
            'text' => __( 'Some string to translate', $this->pluginName),
            'number' => 10
        );
        wp_enqueue_script( 'my_script' );
        wp_localize_script( 'my_script', 'object_name', $translation_array );
    }


    /**
     * Зареєструвати та підключити стилі
     */
    public function registerStyle() {
        wp_register_style(
            'my_css',
            '/wp-content/plugins/'.$this->fileManager->getPluginName().'/assets/admin/style.css' );
        wp_enqueue_style( 'my_css' );
    }


    /**
     * Оновити налаштування та заповнити пусті значення стандартними
     */
	public function updateParams(){
        // Отримати налаштування та заповнити пусті значення стандатрними
        $this->params = array(
            'premmerce_field_input' => '',
            'premmerce_field_checkbox' => false,
            'premmerce_field_select' => 0,
        );
        $this->params = array_merge(
            $this->params,
            (array)get_option('premmerce_options')
        );
    }



    public function wp_query(){
        $query = new \WP_Query( array( 'post_type' => 'premmerce_my_post' ) );
        while ( $query->have_posts() ) {
            $query->the_post();
            echo '<p>Назва поста: '.get_the_title().'</p>';
            echo '<p>Опис поста: '.get_the_content().'</p>';
        }
    }


    /**
     * Сторінка налаштувань ОПЦІЇ
     */
    public function optionsPage()
    {
        // Перевірка прав доступу
        if (!current_user_can('manage_options')) {
            return;
        }

        $this->registerScript();
        $this->registerStyle();

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

        $this->fileManager->includeTemplate(
                'admin/options.php',
                [
                        'options'=>$this->params
                ]
        );

    }


    /**
     * Сторінка налаштувань SETTINGS
     */
	public function settingsPage(){
        // Перевірка прав доступу
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( isset( $_GET['settings-updated'] ) ) {
            add_settings_error(
                'premmerce_messages',
                'premmerce_message',
                __('Saved', $this->pluginName),
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
                submit_button( __('Save settings', $this->pluginName) );
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
            __('Section', $this->pluginName),
            [$this, 'premmerce_section_callback'],
            'premmerce'
        );

        // Зареєструвати поле в секції "premmerce_section"
        add_settings_field(
            'premmerce_field_input',
            __('Text', $this->pluginName),
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
            __('Checkbox', $this->pluginName),
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
            __('Select', $this->pluginName),
            [$this, 'premmerce_select_callback'],
            'premmerce',
            'premmerce_section',
            [
                'label_for' => 'premmerce_field_select',
            ]
        );
    }


    public function premmerce_section_callback() {
        echo "<p>"._e("Content of section", $this->pluginName)."</p>";
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
            <option value="1" <?php selected( $this->params[ $args['label_for'] ], 1 ); ?>><?=__('Variant 1', $this->pluginName)?></option>
            <option value="2" <?php selected( $this->params[ $args['label_for'] ], 2 ); ?>><?=__('Variant 2', $this->pluginName)?></option>
            <option value="3" <?php selected( $this->params[ $args['label_for'] ], 3 ); ?>><?=__('Variant 3', $this->pluginName)?></option>
        </select>
        <?php
    }



}