<?php

use Elementor\Widgets;

class My_Elementor_Widgets
{

    protected static $instance = null;

    public static function get_instance()
    {
        if (!isset(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    protected function __construct()
    {

        add_action('elementor/widgets/widgets_registered', [$this, 'register_widgets']);
    }

    public function register_widgets()
    {
        // \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new Elementor\MobilePricing());

    }
}

add_action('init', 'my_elementor_init');

function my_elementor_init()
{
    My_Elementor_Widgets::get_instance();
}

/**
 * register custom elementor cateogry
 *
 * @param $elements_manager
 */
function add_elementor_widget_categories($elements_manager)
{

    $elements_manager->add_category(
        'theme',
        [
            'title' => __('Fiber', 'fiber'),
        ]
    );
}
add_action('elementor/elements/categories_registered', 'add_elementor_widget_categories');
