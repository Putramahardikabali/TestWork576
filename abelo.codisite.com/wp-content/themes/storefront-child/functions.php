<?php
function storefront_child_enqueue_styles() {
    wp_enqueue_style('storefront-child-style', get_stylesheet_directory_uri() . '/style.css', ['storefront-style'], wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'storefront_child_enqueue_styles');

// Custom Post Type for Cities
function register_cities_post_type() {
    $args = array(
        'labels' => array(
            'name' => 'Cities',
            'singular_name' => 'City',
            'menu_name' => 'Cities',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New City',
            'edit_item' => 'Edit City',
            'new_item' => 'New City',
            'view_item' => 'View City',
            'all_items' => 'All Cities',
        ),
        'public' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'cities'),
        'show_in_rest' => true,
        'supports' => array('title', 'editor'),
        'menu_icon' => 'dashicons-location-alt',
    );
    register_post_type('city', $args);
}
add_action('init', 'register_cities_post_type');


// Meta Boxes
function add_city_meta_boxes() {
    add_meta_box('city_location', 'City Location', 'city_location_meta_box', 'city', 'normal', 'default');
}
add_action('add_meta_boxes', 'add_city_meta_boxes');

function city_location_meta_box($post) {
    // Retrieve existing values if available
    $latitude = get_post_meta($post->ID, '_latitude', true);
    $longitude = get_post_meta($post->ID, '_longitude', true);
    
    ?>
    <label for="latitude">Latitude:</label>
    <input type="text" name="latitude" id="latitude" value="<?php echo esc_attr($latitude); ?>" />
    
    <label for="longitude">Longitude:</label>
    <input type="text" name="longitude" id="longitude" value="<?php echo esc_attr($longitude); ?>" />
    <?php
}

function save_city_location_meta($post_id) {
    if (isset($_POST['latitude'])) {
        update_post_meta($post_id, '_latitude', sanitize_text_field($_POST['latitude']));
    }
    if (isset($_POST['longitude'])) {
        update_post_meta($post_id, '_longitude', sanitize_text_field($_POST['longitude']));
    }
}
add_action('save_post', 'save_city_location_meta');

// Countries
function register_countries_taxonomy() {
    $args = array(
        'labels' => array(
            'name' => 'Countries',
            'singular_name' => 'Country',
            'menu_name' => 'Countries',
        ),
        'hierarchical' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
    );
    register_taxonomy('country', 'city', $args);
}
add_action('init', 'register_countries_taxonomy');

// Weather Widget
class City_Weather_Widget extends WP_Widget {
    function __construct() {
        parent::__construct(
            'city_weather_widget',
            'City Weather',
            array('description' => 'Displays city name and current temperature')
        );
    }

    public function widget($args, $instance) {
        $city_id = get_option('selected_city');
        $city = get_post($city_id);
        $latitude = get_post_meta($city_id, '_latitude', true);
        $longitude = get_post_meta($city_id, '_longitude', true);
        
        $api_key = '71fdd4a0d769a219462790af4e88d638';
        $weather_url = "http://api.openweathermap.org/data/2.5/weather?lat=$latitude&lon=$longitude&appid=$api_key&units=metric";
        $weather_data = wp_remote_get($weather_url);
        
        if (is_wp_error($weather_data)) {
            return;
        }
        
        $weather = json_decode(wp_remote_retrieve_body($weather_data));
        $temperature = $weather->main->temp;
        
        echo $args['before_widget'];
        echo '<h3>' . esc_html($city->post_title) . '</h3>';
        echo '<p>Temperature: ' . esc_html($temperature) . '¡ÆC</p>';
        echo $args['after_widget'];
    }
}

function register_city_weather_widget() {
    register_widget('City_Weather_Widget');
}
add_action('widgets_init', 'register_city_weather_widget');

// Ajax
function enqueue_city_search_script() {
    wp_enqueue_script('city-search', get_template_directory_uri() . '/js/city-search.js', array('jquery'), null, true);
    wp_localize_script('city-search', 'citySearch', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_city_search_script');

// Handle Ajax Request
function city_search_ajax() {
    $search_term = sanitize_text_field($_POST['city_name']);
    global $wpdb;
    
    $query = "SELECT p.ID, p.post_title FROM {$wpdb->posts} p WHERE p.post_title LIKE '%$search_term%' AND p.post_type = 'city'";
    $results = $wpdb->get_results($query);
    
    foreach ($results as $city) {
        echo '<p>' . esc_html($city->post_title) . '</p>';
    }

    wp_die();
}

function register_custom_page_templates($templates) {
    $templates['templates/page-countries-cities.php'] = 'Countries and Cities';
    return $templates;
}
add_filter('theme_page_templates', 'register_custom_page_templates');

add_action('wp_ajax_search_cities', 'handle_city_search');
add_action('wp_ajax_nopriv_search_cities', 'handle_city_search');

function handle_city_search() {
    global $wpdb;

    $query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';

    if (empty($query)) {
        echo '<p>Please enter a city name to search.</p>';
        wp_die();
    }

    $results = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT ID, post_title FROM {$wpdb->prefix}posts 
             WHERE post_type = 'city' 
             AND post_status = 'publish' 
             AND post_title LIKE %s",
            '%' . $wpdb->esc_like($query) . '%'
        )
    );

    if (empty($results)) {
        echo '<p>No cities found matching your search.</p>';
        wp_die();
    }

    echo '<table>';
    echo '<thead><tr><th>City</th><th>Country</th><th>Temperature</th></tr></thead><tbody>';

    foreach ($results as $result) {
        $city_id = $result->ID;
        $city_name = esc_html($result->post_title);

        $countries = wp_get_post_terms($city_id, 'country', ['fields' => 'names']);
        $country_name = !empty($countries) ? esc_html($countries[0]) : 'No country assigned';

        $latitude = get_post_meta($city_id, '_latitude', true);
        $longitude = get_post_meta($city_id, '_longitude', true);

        if (empty($latitude) || empty($longitude)) {
            $temperature = 'No temperature available (lat/lon missing)';
        } else {
   
            $api_key = '71fdd4a0d769a219462790af4e88d638';
            $weather_url = "https://api.openweathermap.org/data/2.5/weather?lat=$latitude&lon=$longitude&units=metric&appid=$api_key";
            
            error_log('Weather API URL: ' . $weather_url);

            $response = wp_remote_get($weather_url);

            if (is_wp_error($response)) {
                $error_message = $response->get_error_message();

                error_log('Weather API Error: ' . $error_message);
                $temperature = 'Error fetching temperature';
            } else {

                $data = json_decode(wp_remote_retrieve_body($response), true);

                error_log('API Response: ' . print_r($data, true));

                if (isset($data['main']['temp'])) {
                    $temperature = $data['main']['temp'] . '&deg;C';
                } else {
                    $temperature = 'No temperature data';
                }
            }
        }

        // Output table row
        echo '<tr>';
        echo '<td>' . $city_name . '</td>';
        echo '<td>' . $country_name . '</td>';
        echo '<td>' . $temperature . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';

    wp_die();
}

function enqueue_ajax_script() {
    wp_enqueue_script(
        'ajax-script',
        get_stylesheet_directory_uri() . '/js/ajax-script.js',
        ['jquery'],
        null,
        true
    );

    wp_localize_script('ajax-script', 'ajaxurl', admin_url('admin-ajax.php'));
}
add_action('wp_enqueue_scripts', 'enqueue_ajax_script');

function enqueue_search_script() {
    wp_enqueue_script('city-search', get_stylesheet_directory_uri() . '/js/city-search.js', ['jquery'], null, true);
    wp_localize_script('city-search', 'ajaxurl', admin_url('admin-ajax.php'));
}
add_action('wp_enqueue_scripts', 'enqueue_search_script');

$countries = wp_get_post_terms($city_id, 'country');

if (!empty($countries) && !is_wp_error($countries)) {
    $country_name = esc_html($countries[0]->name);
} else {
    $country_name = 'N/A';
}


