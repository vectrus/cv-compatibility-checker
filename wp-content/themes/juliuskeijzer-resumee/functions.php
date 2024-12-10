<?php
/*
 *
Theme Name: Julius Developer Portfolio
Theme URI: https://juliuskeijzer.nl/julius-portfolio
Description: A professional developer portfolio theme
Version: 1.0
Author: OBDH

*/

// Enqueue styles and scripts
function julius_portfolio_scripts()
{
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css');
    wp_enqueue_style('julius-portfolio-style', get_stylesheet_uri());
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', array('jquery'), '', true);

}

add_action('wp_enqueue_scripts', 'julius_portfolio_scripts');




/**
 * Font Awesome Kit Setup
 *
 * This will add your Font Awesome Kit to the front-end, the admin back-end,
 * and the login screen area.
 */
/*if (!function_exists('fa_custom_setup_kit')) {
    function fa_custom_setup_kit($kit_url = '')
    {
        foreach (['wp_enqueue_scripts', 'admin_enqueue_scripts', 'login_enqueue_scripts'] as $action) {
            add_action(
                $action,
                function () use ($kit_url) {
                    wp_enqueue_script('font-awesome-kit', $kit_url, [], null);
                }
            );
        }
    }
}
fa_custom_setup_kit('https://kit.fontawesome.com/42deadbeef.js');*/


// Add theme support
function julius_portfolio_setup()
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');

    // Register navigation menus
    register_nav_menus(array(
        'primary' => 'Primary Menu',
        'footer' => 'Footer Menu'
    ));
}

add_action('after_setup_theme', 'julius_portfolio_setup');

// Register custom post type for portfolio items
function create_portfolio_post_type()
{
    register_post_type('portfolio',
        array(
            'labels' => array(
                'name' => __('Portfolio'),
                'singular_name' => __('Portfolio Item')
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail'),
            'menu_icon' => 'dashicons-portfolio'
        )
    );
}

add_action('init', 'create_portfolio_post_type');

// Register Custom Post Types and Taxonomies
function register_custom_post_types_and_taxonomies()
{
    // Skill Categories Taxonomy
    register_taxonomy(
        'skill_category',
        array('technical_skill', 'soft_skill'),
        array(
            'labels' => array(
                'name' => 'Skill Categories',
                'singular_name' => 'Skill Category',
                'add_new_item' => 'Add New Skill Category',
            ),
            'hierarchical' => true,
            'show_admin_column' => true,
            'show_in_rest' => true,
        )
    );

    // Work Experience Post Type
    register_post_type('work_experience',
        array(
            'labels' => array(
                'name' => __('Work Experience'),
                'singular_name' => __('Work Experience'),
                'add_new' => __('Add New Experience'),
                'add_new_item' => __('Add New Work Experience'),
                'edit_item' => __('Edit Work Experience'),
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail'),
            'menu_icon' => 'dashicons-businessman',
            'show_in_rest' => true,
        )
    );

    // Technical Skills Post Type
    register_post_type('technical_skill',
        array(
            'labels' => array(
                'name' => __('Technical Skills'),
                'singular_name' => __('Technical Skill'),
                'add_new' => __('Add New Skill'),
                'add_new_item' => __('Add New Technical Skill'),
                'edit_item' => __('Edit Technical Skill'),
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor'),
            'menu_icon' => 'dashicons-desktop',
            'show_in_rest' => true,
            'taxonomies' => array('skill_category'),
        )
    );

    // Soft Skills Post Type
    register_post_type('soft_skill',
        array(
            'labels' => array(
                'name' => __('Soft Skills'),
                'singular_name' => __('Soft Skill'),
                'add_new' => __('Add New Skill'),
                'add_new_item' => __('Add New Soft Skill'),
                'edit_item' => __('Edit Soft Skill'),
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor'),
            'menu_icon' => 'dashicons-groups',
            'show_in_rest' => true,
            'taxonomies' => array('skill_category'),
        )
    );
}

add_action('init', 'register_custom_post_types_and_taxonomies');

// Add custom meta boxes for work experience
function add_work_experience_meta_boxes()
{
    add_meta_box(
        'work_experience_details',
        'Experience Details',
        'work_experience_details_callback',
        'work_experience',
        'normal',
        'high'
    );
}

add_action('add_meta_boxes', 'add_work_experience_meta_boxes');

function work_experience_details_callback($post)
{
    $start_date = get_post_meta($post->ID, '_start_date', true);
    $end_date = get_post_meta($post->ID, '_end_date', true);
    $company = get_post_meta($post->ID, '_company', true);
    $position = get_post_meta($post->ID, '_position', true);
    $location = get_post_meta($post->ID, '_location', true);
    $key_achievements = get_post_meta($post->ID, '_key_achievements', true);

    wp_nonce_field('work_experience_meta_box', 'work_experience_meta_box_nonce');
    ?>
    <p>
        <label for="company">Company:</label><br>
        <input type="text" id="company" name="company" value="<?php echo esc_attr($company); ?>" size="50">
    </p>
    <p>
        <label for="position">Position:</label><br>
        <input type="text" id="position" name="position" value="<?php echo esc_attr($position); ?>" size="50">
    </p>
    <p>
        <label for="location">Location:</label><br>
        <input type="text" id="location" name="location" value="<?php echo esc_attr($location); ?>" size="50">
    </p>
    <p>
        <label for="start_date">Start Date:</label><br>
        <input type="date" id="start_date" name="start_date" value="<?php echo esc_attr($start_date); ?>">
    </p>
    <p>
        <label for="end_date">End Date (leave empty if current):</label><br>
        <input type="date" id="end_date" name="end_date" value="<?php echo esc_attr($end_date); ?>">
    </p>
    <p>
        <label for="key_achievements">Key Achievements:</label><br>
        <textarea id="key_achievements" name="key_achievements" rows="5"
                  cols="50"><?php echo esc_textarea($key_achievements); ?></textarea>
    </p>
    <?php
}

// Save work experience meta box data
function save_work_experience_meta_box_data($post_id)
{
    if (!isset($_POST['work_experience_meta_box_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['work_experience_meta_box_nonce'], 'work_experience_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    $fields = array('start_date', 'end_date', 'company', 'position', 'location', 'key_achievements');

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta(
                $post_id,
                '_' . $field,
                sanitize_text_field($_POST[$field])
            );
        }
    }
}

add_action('save_post', 'save_work_experience_meta_box_data');

// Add custom meta boxes for skills
function add_skill_meta_boxes()
{
    add_meta_box(
        'skill_details',
        'Skill Details',
        'skill_details_callback',
        array('technical_skill', 'soft_skill'),
        'normal',
        'high'
    );
}

add_action('add_meta_boxes', 'add_skill_meta_boxes');

function skill_details_callback($post)
{
    $proficiency = get_post_meta($post->ID, '_proficiency', true);
    $years_experience = get_post_meta($post->ID, '_years_experience', true);

    wp_nonce_field('skill_meta_box', 'skill_meta_box_nonce');
    ?>
    <p>
        <label for="proficiency">Proficiency Level:</label><br>
        <select id="proficiency" name="proficiency">
            <option value="Beginner" <?php selected($proficiency, 'Beginner'); ?>>Beginner</option>
            <option value="Intermediate" <?php selected($proficiency, 'Intermediate'); ?>>Intermediate</option>
            <option value="Advanced" <?php selected($proficiency, 'Advanced'); ?>>Advanced</option>
            <option value="Expert" <?php selected($proficiency, 'Expert'); ?>>Expert</option>
        </select>
    </p>
    <p>
        <label for="years_experience">Years of Experience:</label><br>
        <input type="number" id="years_experience" name="years_experience"
               value="<?php echo esc_attr($years_experience); ?>" min="0" step="1">
    </p>
    <?php
}

// Save skill meta box data
function save_skill_meta_box_data($post_id)
{
    if (!isset($_POST['skill_meta_box_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['skill_meta_box_nonce'], 'skill_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    $fields = array('proficiency', 'years_experience');

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta(
                $post_id,
                '_' . $field,
                sanitize_text_field($_POST[$field])
            );
        }
    }
}

add_action('save_post', 'save_skill_meta_box_data');

// Add custom meta box for portfolio skills
function add_portfolio_skills_meta_box()
{
    add_meta_box(
        'portfolio_skills',
        'Project Skills',
        'portfolio_skills_callback',
        'portfolio',
        'normal',
        'high'
    );
}

add_action('add_meta_boxes', 'add_portfolio_skills_meta_box');

function portfolio_skills_callback($post)
{
    wp_nonce_field('portfolio_skills_meta_box', 'portfolio_skills_meta_box_nonce');

    // Get current values
    $technical_skills = get_post_meta($post->ID, '_technical_skills', true);
    $soft_skills = get_post_meta($post->ID, '_soft_skills', true);
    $project_url = get_post_meta($post->ID, '_project_url', true);
    $github_url = get_post_meta($post->ID, '_github_url', true);
    $completion_date = get_post_meta($post->ID, '_completion_date', true);

    // Query all technical skills
    $tech_args = array(
        'post_type' => 'technical_skill',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    );
    $technical_skills_query = new WP_Query($tech_args);

    // Query all soft skills
    $soft_args = array(
        'post_type' => 'soft_skill',
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    );
    $soft_skills_query = new WP_Query($soft_args);

    // Convert stored values to array if they exist
    $selected_tech_skills = $technical_skills ? explode(',', $technical_skills) : array();
    $selected_soft_skills = $soft_skills ? explode(',', $soft_skills) : array();
    ?>

    <div class="portfolio-meta-fields">
        <p>
            <label for="project_url"><strong>Project URL:</strong></label><br>
            <input type="url" id="project_url" name="project_url" value="<?php echo esc_attr($project_url); ?>"
                   class="widefat">
        </p>

        <p>
            <label for="github_url"><strong>GitHub URL:</strong></label><br>
            <input type="url" id="github_url" name="github_url" value="<?php echo esc_attr($github_url); ?>"
                   class="widefat">
        </p>

        <p>
            <label for="completion_date"><strong>Project Completion Date:</strong></label><br>
            <input type="date" id="completion_date" name="completion_date"
                   value="<?php echo esc_attr($completion_date); ?>">
        </p>

        <div class="skills-selection">
            <h4>Technical Skills Used</h4>
            <div class="skills-checkboxes"
                 style="max-height: 200px; overflow-y: auto; padding: 10px; border: 1px solid #ddd;">
                <?php
                if ($technical_skills_query->have_posts()) :
                    while ($technical_skills_query->have_posts()) : $technical_skills_query->the_post();
                        $skill_id = get_the_ID();
                        $checked = in_array($skill_id, $selected_tech_skills) ? 'checked' : '';
                        ?>
                        <label style="display: block; margin-bottom: 5px;">
                            <input type="checkbox" name="technical_skills[]"
                                   value="<?php echo $skill_id; ?>" <?php echo $checked; ?>>
                            <?php the_title(); ?>
                        </label>
                    <?php
                    endwhile;
                endif;
                wp_reset_postdata();
                ?>
            </div>

            <h4>Soft Skills Applied</h4>
            <div class="skills-checkboxes"
                 style="max-height: 200px; overflow-y: auto; padding: 10px; border: 1px solid #ddd;">
                <?php
                if ($soft_skills_query->have_posts()) :
                    while ($soft_skills_query->have_posts()) : $soft_skills_query->the_post();
                        $skill_id = get_the_ID();
                        $checked = in_array($skill_id, $selected_soft_skills) ? 'checked' : '';
                        ?>
                        <label style="display: block; margin-bottom: 5px;">
                            <input type="checkbox" name="soft_skills[]"
                                   value="<?php echo $skill_id; ?>" <?php echo $checked; ?>>
                            <?php the_title(); ?>
                        </label>
                    <?php
                    endwhile;
                endif;
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </div>
    <?php
}

// Save portfolio skills meta box data
function save_portfolio_skills_meta_box_data($post_id)
{
    if (!isset($_POST['portfolio_skills_meta_box_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['portfolio_skills_meta_box_nonce'], 'portfolio_skills_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Save technical skills
    if (isset($_POST['technical_skills'])) {
        $technical_skills = array_map('sanitize_text_field', $_POST['technical_skills']);
        update_post_meta($post_id, '_technical_skills', implode(',', $technical_skills));
    } else {
        delete_post_meta($post_id, '_technical_skills');
    }

    // Save soft skills
    if (isset($_POST['soft_skills'])) {
        $soft_skills = array_map('sanitize_text_field', $_POST['soft_skills']);
        update_post_meta($post_id, '_soft_skills', implode(',', $soft_skills));
    } else {
        delete_post_meta($post_id, '_soft_skills');
    }

    // Save URLs and completion date
    $meta_fields = array(
        'project_url',
        'github_url',
        'completion_date'
    );

    foreach ($meta_fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta(
                $post_id,
                '_' . $field,
                sanitize_text_field($_POST[$field])
            );
        }
    }
}

add_action('save_post', 'save_portfolio_skills_meta_box_data');

// Register Education Post Type
function register_education_post_type()
{
    $labels = array(
        'name' => 'Education',
        'singular_name' => 'Education',
        'menu_name' => 'Education',
        'add_new' => 'Add New Education',
        'add_new_item' => 'Add New Education Entry',
        'edit_item' => 'Edit Education Entry',
        'new_item' => 'New Education Entry',
        'view_item' => 'View Education Entry',
        'search_items' => 'Search Education',
        'not_found' => 'No education entries found',
        'not_found_in_trash' => 'No education entries found in trash'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-welcome-learn-more',
        'supports' => array('title', 'editor', 'thumbnail'),
        'show_in_rest' => true,
    );

    register_post_type('education', $args);
}

add_action('init', 'register_education_post_type');

// Add custom meta box for education details
function add_education_meta_boxes()
{
    add_meta_box(
        'education_details',
        'Education Details',
        'education_details_callback',
        'education',
        'normal',
        'high'
    );
}

add_action('add_meta_boxes', 'add_education_meta_boxes');

function education_details_callback($post)
{
    wp_nonce_field('education_meta_box', 'education_meta_box_nonce');

    // Get existing values
    $start_year = get_post_meta($post->ID, '_start_year', true);
    $end_year = get_post_meta($post->ID, '_end_year', true);
    $institution = get_post_meta($post->ID, '_institution', true);
    $degree_type = get_post_meta($post->ID, '_degree_type', true);
    $field_of_study = get_post_meta($post->ID, '_field_of_study', true);
    $gpa = get_post_meta($post->ID, '_gpa', true);
    $location = get_post_meta($post->ID, '_location', true);
    $key_subjects = get_post_meta($post->ID, '_key_subjects', true);
    ?>

    <div class="education-meta-fields">
        <p>
            <label for="institution"><strong>Institution:</strong></label><br>
            <input type="text" id="institution" name="institution" value="<?php echo esc_attr($institution); ?>"
                   class="widefat">
        </p>

        <p>
            <label for="degree_type"><strong>Degree Type:</strong></label><br>
            <select id="degree_type" name="degree_type" class="widefat">
                <option value="">Select Degree Type</option>
                <option value="High School" <?php selected($degree_type, 'High School'); ?>>High School</option>
                <option value="Associate" <?php selected($degree_type, 'Associate'); ?>>Associate Degree</option>
                <option value="Bachelor" <?php selected($degree_type, 'Bachelor'); ?>>Bachelor's Degree</option>
                <option value="Master" <?php selected($degree_type, 'Master'); ?>>Master's Degree</option>
                <option value="PhD" <?php selected($degree_type, 'PhD'); ?>>PhD</option>
                <option value="Certificate" <?php selected($degree_type, 'Certificate'); ?>>Certificate</option>
                <option value="Diploma" <?php selected($degree_type, 'Diploma'); ?>>Diploma</option>
            </select>
        </p>

        <p>
            <label for="field_of_study"><strong>Field of Study:</strong></label><br>
            <input type="text" id="field_of_study" name="field_of_study"
                   value="<?php echo esc_attr($field_of_study); ?>" class="widefat">
        </p>

        <div class="date-fields" style="display: flex; gap: 20px;">
            <p style="flex: 1;">
                <label for="start_year"><strong>Start Year:</strong></label><br>
                <input type="number" id="start_year" name="start_year" value="<?php echo esc_attr($start_year); ?>"
                       min="1900" max="2099" class="widefat">
            </p>

            <p style="flex: 1;">
                <label for="end_year"><strong>End Year:</strong></label><br>
                <input type="number" id="end_year" name="end_year" value="<?php echo esc_attr($end_year); ?>" min="1900"
                       max="2099" class="widefat">
            </p>
        </div>

        <p>
            <label for="gpa"><strong>GPA/Grade (optional):</strong></label><br>
            <input type="text" id="gpa" name="gpa" value="<?php echo esc_attr($gpa); ?>" class="widefat">
        </p>

        <p>
            <label for="location"><strong>Location:</strong></label><br>
            <input type="text" id="location" name="location" value="<?php echo esc_attr($location); ?>" class="widefat">
        </p>

        <p>
            <label for="key_subjects"><strong>Key Subjects/Focus Areas:</strong></label><br>
            <textarea id="key_subjects" name="key_subjects" rows="4"
                      class="widefat"><?php echo esc_textarea($key_subjects); ?></textarea>
            <span class="description">Enter key subjects or focus areas, one per line</span>
        </p>
    </div>
    <?php
}

// Save education meta box data
function save_education_meta_box_data($post_id)
{
    if (!isset($_POST['education_meta_box_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['education_meta_box_nonce'], 'education_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    $fields = array(
        'start_year',
        'end_year',
        'institution',
        'degree_type',
        'field_of_study',
        'gpa',
        'location',
        'key_subjects'
    );

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta(
                $post_id,
                '_' . $field,
                sanitize_text_field($_POST[$field])
            );
        }
    }
}

add_action('save_post', 'save_education_meta_box_data');


/* Dutchdate function replace met strings */
function nlDate($datum)
{
    /*
     // AM of PM doen we niet aan
     $parameters = str_replace("A", "", $parameters);
     $parameters = str_replace("a", "", $parameters);

    $datum = date($parameters);
   */
    // Vervang de maand, klein
    $datum = str_replace("january", "januari", $datum);
    $datum = str_replace("february", "februari", $datum);
    $datum = str_replace("march", "maart", $datum);
    $datum = str_replace("april", "april", $datum);
    $datum = str_replace("may", "mei", $datum);
    $datum = str_replace("june", "juni", $datum);
    $datum = str_replace("july", "juli", $datum);
    $datum = str_replace("august", "augustus", $datum);
    $datum = str_replace("september", "september", $datum);
    $datum = str_replace("october", "oktober", $datum);
    $datum = str_replace("november", "november", $datum);
    $datum = str_replace("december", "december", $datum);

    // Vervang de maand, hoofdletters
    $datum = str_replace("January", "Januari", $datum);
    $datum = str_replace("February", "Februari", $datum);
    $datum = str_replace("March", "Maart", $datum);
    $datum = str_replace("April", "April", $datum);
    $datum = str_replace("May", "Mei", $datum);
    $datum = str_replace("June", "Juni", $datum);
    $datum = str_replace("July", "Juli", $datum);
    $datum = str_replace("August", "Augustus", $datum);
    $datum = str_replace("September", "September", $datum);
    $datum = str_replace("October", "Oktober", $datum);
    $datum = str_replace("November", "November", $datum);
    $datum = str_replace("December", "December", $datum);

    // Vervang de maand, kort
    $datum = str_replace("Jan", "Jan", $datum);
    $datum = str_replace("Feb", "Feb", $datum);
    $datum = str_replace("Mar", "Maa", $datum);
    $datum = str_replace("Apr", "Apr", $datum);
    $datum = str_replace("May", "Mei", $datum);
    $datum = str_replace("Jun", "Jun", $datum);
    $datum = str_replace("Jul", "Jul", $datum);
    $datum = str_replace("Aug", "Aug", $datum);
    $datum = str_replace("Sep", "Sep", $datum);
    $datum = str_replace("Oct", "Ok", $datum);
    $datum = str_replace("Nov", "Nov", $datum);
    $datum = str_replace("Dec", "Dec", $datum);

    // Vervang de dag, klein
    $datum = str_replace("monday", "maandag", $datum);
    $datum = str_replace("tuesday", "dinsdag", $datum);
    $datum = str_replace("wednesday", "woensdag", $datum);
    $datum = str_replace("thursday", "donderdag", $datum);
    $datum = str_replace("friday", "vrijdag", $datum);
    $datum = str_replace("saturday", "zaterdag", $datum);
    $datum = str_replace("sunday", "zondag", $datum);

    // Vervang de dag, hoofdletters
    $datum = str_replace("Monday", "Maandag", $datum);
    $datum = str_replace("Tuesday", "Dinsdag", $datum);
    $datum = str_replace("Wednesday", "Woensdag", $datum);
    $datum = str_replace("Thursday", "Donderdag", $datum);
    $datum = str_replace("Friday", "Vrijdag", $datum);
    $datum = str_replace("Saturday", "Zaterdag", $datum);
    $datum = str_replace("Sunday", "Zondag", $datum);

    // Vervang de verkorting van de dag, hoofdletters
    $datum = str_replace("Mon", "Maa", $datum);
    $datum = str_replace("Tue", "Din", $datum);
    $datum = str_replace("Wed", "Woe", $datum);
    $datum = str_replace("Thu", "Don", $datum);
    $datum = str_replace("Fri", "Vri", $datum);
    $datum = str_replace("Sat", "Zat", $datum);
    $datum = str_replace("Sun", "Zon", $datum);

    return $datum;
}