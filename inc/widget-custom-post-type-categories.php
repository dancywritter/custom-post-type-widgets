<?php
/**
 * Custom Post Type Categories widget class
 *
 * @since 1.0.0
 * @package Custom Post Type Widgets
 */

/**
 * Core class WP_Custom_Post_Type_Widgets_Categories
 *
 * @since 1.0.0
 */
class WP_Custom_Post_Type_Widgets_Categories extends WP_Widget {

	/**
	 * Sets up a new widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'                   => 'widget_categories',
			'description'                 => __( 'A list or dropdown of categories.', 'custom-post-type-widgets' ),
			'customize_selective_refresh' => true,
		);
		parent::__construct( 'custom-post-type-categories', __( 'Categories (Custom Post Type)', 'custom-post-type-widgets' ), $widget_ops );
	}

	/**
	 * Outputs the content for the widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current widget instance.
	 */
	public function widget( $args, $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( 'Categories', 'custom-post-type-widgets' );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$taxonomy = ! empty( $instance['taxonomy'] ) ? $instance['taxonomy'] : 'category';
		$c        = ! empty( $instance['count'] ) ? (bool) $instance['count'] : false;
		$h        = ! empty( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
		$d        = ! empty( $instance['dropdown'] ) ? (bool) $instance['dropdown'] : false;

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$cat_args = array(
			'orderby'      => 'name',
			'taxonomy'     => $taxonomy,
			'show_count'   => $c,
			'hierarchical' => $h,
		);

		if ( $d ) {
			$dropdown_id = "{$this->id_base}-dropdown-{$this->number}";

			echo '<label class="screen-reader-text" for="' . esc_attr( $dropdown_id ) . '">' . $title . '</label>';

			$cat_args['show_option_none'] = __( 'Select Category', 'custom-post-type-widgets' );
			$cat_args['name']             = 'category' === $taxonomy ? 'category_name' : $taxonomy;
			$cat_args['id']               = $dropdown_id;
			$cat_args['value_field']      = 'slug';
?>

<form action="<?php echo esc_url( home_url() ); ?>" method="get">
			<?php
			/**
			 * Filters the arguments for the Categories widget drop-down.
			 *
			 * @since 2.8.0
			 * @since 4.9.0 Added the `$instance` parameter.
			 *
			 * @see wp_dropdown_categories()
			 *
			 * @param array $cat_args An array of Categories widget drop-down arguments.
			 * @param array $instance Array of settings for the current widget.
			 */
			wp_dropdown_categories( apply_filters( 'widget_categories_dropdown_args', $cat_args ) );
			?>
</form>
<script>
/* <![CDATA[ */
(function() {
	var dropdown = document.getElementById( "<?php echo esc_js( $dropdown_id ); ?>" );
	function onCatChange() {
		if ( dropdown.options[dropdown.selectedIndex].value ) {
			return dropdown.form.submit();
		}
	}
	dropdown.onchange = onCatChange;
})();
/* ]]> */
</script>
<?php
		}
		else {
?>
		<ul>
<?php
		$cat_args['title_li'] = '';
		wp_list_categories( apply_filters( 'widget_categories_args', $cat_args, $instance ) );
?>
		</ul>
<?php
		}

		echo $args['after_widget'];
	}

	/**
	 * Handles updating settings for the current Archives widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @param array $new_instance New settings for this instance as input by the user via form() method.
	 * @param array $old_instance Old settings for this instance.
	 *
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                 = $old_instance;
		$instance['title']        = sanitize_text_field( $new_instance['title'] );
		$instance['taxonomy']     = stripslashes( $new_instance['taxonomy'] );
		$instance['count']        = ! empty( $new_instance['count'] ) ? (bool) $new_instance['count'] : false;
		$instance['hierarchical'] = ! empty( $new_instance['hierarchical'] ) ? (bool) $new_instance['hierarchical'] : false;
		$instance['dropdown']     = ! empty( $new_instance['dropdown'] ) ? (bool) $new_instance['dropdown'] : false;

		return $instance;
	}

	/**
	 * Outputs the settings form for the widget.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$title        = isset( $instance['title'] ) ? $instance['title'] : '';
		$taxonomy     = isset( $instance['taxonomy'] ) ? $instance['taxonomy'] : '';
		$count        = isset( $instance['count'] ) ? (bool) $instance['count'] : false;
		$hierarchical = isset( $instance['hierarchical'] ) ? (bool) $instance['hierarchical'] : false;
		$dropdown     = isset( $instance['dropdown'] ) ? (bool) $instance['dropdown'] : false;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'custom-post-type-widgets' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>

		<?php
		$taxonomies = get_taxonomies( '', 'objects' );

		if ( $taxonomies ) {
			printf(
				'<p><label for="%1$s">%2$s</label>' .
				'<select class="widefat" id="%1$s" name="%3$s">',
				$this->get_field_id( 'taxonomy' ),
				__( 'Taxonomy:', 'custom-post-type-widgets' ),
				$this->get_field_name( 'taxonomy' )
			);

			foreach ( $taxonomies as $taxobjects => $value ) {
				if ( ! $value->hierarchical ) {
					continue;
				}
				if ( 'nav_menu' === $taxobjects || 'link_category' === $taxobjects || 'post_format' === $taxobjects ) {
					continue;
				}

				printf(
					'<option value="%s"%s>%s</option>',
					esc_attr( $taxobjects ),
					selected( $taxobjects, $taxonomy, false ),
					__( $value->label, 'custom-post-type-widgets' ) . ' ' . $taxobjects
				);
			}
			echo '</select></p>';
		}
		?>

		<p><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'dropdown' ); ?>" name="<?php echo $this->get_field_name( 'dropdown' ); ?>"<?php checked( $dropdown ); ?> />
		<label for="<?php echo $this->get_field_id( 'dropdown' ); ?>"><?php esc_html_e( 'Display as dropdown', 'custom-post-type-widgets' ); ?></label><br />

		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>"<?php checked( $count ); ?> />
		<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php esc_html_e( 'Show post counts', 'custom-post-type-widgets' ); ?></label><br />

		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id( 'hierarchical' ); ?>" name="<?php echo $this->get_field_name( 'hierarchical' ); ?>"<?php checked( $hierarchical ); ?> />
		<label for="<?php echo $this->get_field_id( 'hierarchical' ); ?>"><?php esc_html_e( 'Show hierarchy', 'custom-post-type-widgets' ); ?></label></p>
<?php
	}
}
