<?php
/**
 * Class Test_Custom_Post_Type_Widgets_Basic
 *
 * @package Custom_Post_Type_Widgets
 */

/**
 * Basic test case.
 */
class Test_Custom_Post_Type_Widgets_Basic extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();
		$this->custom_post_type_widgets = new Custom_Post_Type_Widgets();
	}

	/**
	 * @test
	 * @group basic
	 */
	function constructor() {
		$this->assertEquals( 10, has_filter( 'init', array( $this->custom_post_type_widgets, 'load_textdomain' ) ) );
		$this->assertEquals( 10, has_filter( 'plugins_loaded', array( $this->custom_post_type_widgets, 'load' ) ) );
		$this->assertEquals( 10, has_filter( 'widgets_init', array( $this->custom_post_type_widgets, 'init' ) ) );
	}

	/**
	 * @test
	 * @group basic
	 */
	function load() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

	/**
	 * @test
	 * @group basic
	 */
	function init() {
		$this->custom_post_type_widgets->init();

		global $wp_widget_factory;

		$this->assertArrayHasKey( 'WP_Custom_Post_Type_Widgets_Recent_Posts', $wp_widget_factory->widgets );
		$this->assertArrayHasKey( 'WP_Custom_Post_Type_Widgets_Archives', $wp_widget_factory->widgets );
		$this->assertArrayHasKey( 'WP_Custom_Post_Type_Widgets_Categories', $wp_widget_factory->widgets );
		$this->assertArrayHasKey( 'WP_Custom_Post_Type_Widgets_Calendar', $wp_widget_factory->widgets );
		$this->assertArrayHasKey( 'WP_Custom_Post_Type_Widgets_Recent_Comments', $wp_widget_factory->widgets );
		$this->assertArrayHasKey( 'WP_Custom_Post_Type_Widgets_Tag_Cloud', $wp_widget_factory->widgets );
		$this->assertArrayHasKey( 'WP_Custom_Post_Type_Widgets_Search', $wp_widget_factory->widgets );
	}

	/**
	 * @test
	 * @group basic
	 */
	public function load_textdomain() {
		$result = $this->custom_post_type_widgets->load_textdomain();
		$this->assertNull( $result );
	}

	/**
	 * @test
	 * @group basic
	 */
	function uninstall() {
		$this->markTestIncomplete( 'This test has not been implemented yet.' );
	}

}
