<?php namespace TestingPlugin;

use WP_Widget;

class TestingWidget extends WP_Widget {
	
	private $TextContent = [
		'select' => [
			'one' 	=> 'Один',
			'two' 	=> 'Два',
			'three' => 'Три',
		]
	];

    public function __construct() {

        parent::__construct(
            'testing_widget',  // Base ID
            'My first widget'   // Name
        );

    }


    // вивід на фронт
    public function widget( $args, $instance ) {

        echo $args['before_widget'];

        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }

        echo '<div class="textwidget">';

        echo '<p>INPUT: '.$instance['title'].'</p>';
        echo '<p>TEXT: '.esc_html__( do_shortcode($instance['text']), 'text_domain' ).'</p>';
        echo '<p> SELECT: '.$this->TextContent['select'][$instance['select']].'</p>';

        echo '</div>';

        echo $args['after_widget'];

    }

    // редагування в адмінці
    public function form( $instance ) {

        $title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'text_domain' );
        $text = ! empty( $instance['text'] ) ? $instance['text'] : esc_html__( '', 'text_domain' );
        $select = ! empty( $instance['select'] ) ? $instance['select'] : false;
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" type="text" cols="30" rows="10"><?php echo esc_attr( $text ); ?></textarea>
        </p>
		<p>
            <select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'select' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'select' ) ); ?>">
			<?php foreach($this->TextContent['select'] as $value => $text): ?>
                <option value="<?=$value?>" <?=selected($select,$value)?>><?=$text?></option>
			<?php endforeach; ?>
			</select>
		</p>
        <?php
    }

    // оновлення
    public function update( $new_instance, $old_instance ) {

        $instance = array();

        $instance['title'] = ( !empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['text'] = ( !empty( $new_instance['text'] ) ) ? $new_instance['text'] : '';
        $instance['select'] = ( !empty( $new_instance['select'] ) ) ? $new_instance['select'] : 1;

        return $instance;
    }

}
