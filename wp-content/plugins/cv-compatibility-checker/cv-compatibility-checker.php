<?php

/**
 * Plugin Name: CV Compatibility Checker
 * Description: Analyzes job descriptions against your CV using Claude.ai
 * Version: 1.0
 * Author: julius keijzer / claude
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class CVCompatibilityChecker
{

    private $api_key = ANTROPIC_KEY; // Your Claude.ai Set API key in wp_config.php


    private $debug = false; // Enable debugging

    public function __construct()
    {
        //add_action('init', array($this, 'register_custom_post_types'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_shortcode('cv_compatibility_checker', array($this, 'render_form'));
        add_action('wp_ajax_analyze_compatibility', array($this, 'analyze_compatibility'));
        add_action('wp_ajax_nopriv_analyze_compatibility', array($this, 'analyze_compatibility'));
    }


    public function enqueue_scripts()
    {
        wp_enqueue_style('cv-compatibility-checker', plugins_url('css/style.css', __FILE__));
        wp_enqueue_script('cv-compatibility-checker', plugins_url('js/script.js', __FILE__), array('jquery'), '1.0', true);
        wp_localize_script('cv-compatibility-checker', 'ajaxurl', admin_url('admin-ajax.php'));
    }

    public function render_form()
    {
        ob_start();
        ?>
        <div class="cv-compatibility-checker no-print">
            <form id="job-description-form">
                <div class="form-group">
                    <label for="job-description">Vacature tekst:</label>
                    <textarea id="job-description" name="job-description" rows="10" required></textarea>
                </div>
                <button type="submit" class="submit-button">Check jouw vacaturetekst tegen mijn C.V.!</button>
            </form>
            <div id="analysis-results" class="hidden">
                <h3>Compatibiliteits analyse</h3>
                <div class="results-content"></div>
            </div>
            <?php if ($this->debug): ?>
                <div id="debug-output" class="debug-section hidden">
                    <h4>Debug Informatie:</h4>
                    <pre class="debug-content"></pre>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    public function analyze_compatibility()
    {
        try {
            if (!isset($_POST['job_description'])) {
                throw new Exception('Geen vacature tekst ingevuld');
            }

            if (empty($this->api_key)) {
                throw new Exception('Geen API Key');
            }

            $job_description = sanitize_textarea_field($_POST['job_description']);
            $cv_data = $this->get_cv_data();

            // Log CV data if debugging is enabled
            if ($this->debug) {
                error_log('C.V. Data: ' . print_r($cv_data, true));
            }

            // Prepare data for Claude.ai analysis
            $analysis_prompt = $this->prepare_claude_prompt($job_description, $cv_data);

            // Call Claude.ai API
            $analysis_result = $this->call_claude_api($analysis_prompt);

            if ($analysis_result) {
                $response = array(
                    'success' => true,
                    'data' => $analysis_result,
                    'debug' => $this->debug ? array(
                        'cv_data' => $cv_data,
                        'prompt' => $analysis_prompt,
                        'api_response' => $analysis_result
                    ) : null
                );
            } else {
                throw new Exception('Analyse heeft gefaald - geen antwoord van API');
            }

        } catch (Exception $e) {
            $response = array(
                'success' => false,
                'error' => $e->getMessage(),
                'debug' => $this->debug ? array(
                    'error_details' => $e->getTraceAsString(),
                    'cv_data' => isset($cv_data) ? $cv_data : 'Not collected',
                    'prompt' => isset($analysis_prompt) ? $analysis_prompt : 'Not generated'
                ) : null
            );
        }

        wp_send_json($response);
        exit;
    }

    private function call_claude_api($prompt)
    {
        $url = 'https://api.anthropic.com/v1/messages';

        $headers = array(
            'Content-Type' => 'application/json',
            'x-api-key' => $this->api_key,
            'anthropic-version' => '2023-06-01'
        );

        $body = array(
            'model' => 'claude-3-opus-20240229',
            'max_tokens' => 1024,
            'messages' => array(
                array(
                    'role' => 'user',
                    'content' => $prompt
                )
            ),
            'system' => "You are a CV analysis expert. Analyze the compatibility between job descriptions and CV data, providing detailed feedback and scores."
        );

        if ($this->debug) {
            error_log('Claude API Request: ' . print_r($body, true));
        }

        $response = wp_remote_post($url, array(
            'headers' => $headers,
            'body' => json_encode($body),
            'timeout' => 30,
            'data_format' => 'body'
        ));

        if (is_wp_error($response)) {
            if ($this->debug) {
                error_log('Claude API Error: ' . $response->get_error_message());
            }
            throw new Exception('API request failed: ' . $response->get_error_message());
        }

        $response_code = wp_remote_retrieve_response_code($response);
        $response_body = wp_remote_retrieve_body($response);

        if ($this->debug) {
            error_log('Claude API Response Code: ' . $response_code);
            error_log('Claude API Response Body: ' . $response_body);
        }

        if ($response_code !== 200) {
            $error_body = json_decode($response_body, true);
            $error_message = isset($error_body['error']['message']) ? $error_body['error']['message'] : 'Unknown error';
            throw new Exception('API returned error code: ' . $response_code . ' - ' . $error_message);
        }

        $body = json_decode($response_body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Failed to parse API response');
        }

        // Extract the content from the response
        if (isset($body['content'][0]['text'])) {
            return $body['content'][0]['text'];
        } else {
            throw new Exception('Unexpected API response format');
        }
    }

    private function get_cv_data()
    {
        $cv_data = array(
            'soft_skills' => $this->get_skills('soft_skill'),
            'technical_skills' => $this->get_skills('technical_skill'),
            'education' => $this->get_education(),
            'portfolio' => $this->get_portfolio(),
            'work_experience' => $this->get_work_experience()
        );
        return $cv_data;
    }

    private function get_skills($type)
    {
        $args = array(
            'post_type' => $type,
            'posts_per_page' => -1
        );

        $skills = array();
        $posts = get_posts($args);

        foreach ($posts as $post) {
            $categories = wp_get_post_terms($post->ID, 'skill_category');
            $skills[] = array(
                'name' => $post->post_title,
                'description' => $post->post_content,
                'category' => !empty($categories) ? $categories[0]->name : ''
            );
        }

        return $skills;
    }

    private function get_education()
    {
        $args = array(
            'post_type' => 'education',
            'posts_per_page' => -1
        );

        $education = array();
        $posts = get_posts($args);

        foreach ($posts as $post) {
            $education[] = array(
                'title' => $post->post_title,
                'details' => $post->post_content
            );
        }

        return $education;
    }

    private function get_portfolio()
    {
        $args = array(
            'post_type' => 'portfolio',
            'posts_per_page' => -1
        );

        $education = array();
        $posts = get_posts($args);

        foreach ($posts as $post) {
            $education[] = array(
                'title' => $post->post_title,
                'details' => $post->post_content
            );
        }

        return $education;
    }

    private function get_work_experience()
    {
        $args = array(
            'post_type' => 'work_experience',
            'posts_per_page' => -1
        );

        $experience = array();
        $posts = get_posts($args);

        foreach ($posts as $post) {
            $experience[] = array(
                'title' => $post->post_title,
                'description' => $post->post_content
            );
        }

        return $experience;
    }

    private function prepare_claude_prompt($job_description, $cv_data)
    {
        return "Analyseer als een HR expert de compatibiliteit tussen deze vacature en CV gegevens. Geef de analyse in het Nederlands.

VACATURE:
{$job_description}

CV GEGEVENS:
Soft Skills: " . json_encode($cv_data['soft_skills']) . "
Technische Skills: " . json_encode($cv_data['technical_skills']) . "
Opleiding: " . json_encode($cv_data['education']) . "
Werkervaring: " . json_encode($cv_data['work_experience']) . "
Portfolio: " . json_encode($cv_data['portfolio']) . "

Geef een gestructureerde analyse met de volgende onderdelen:
1. Een algemene compatibiliteitsscore (0-100%)
2. Sterke punten: welke vereisten matchen goed met het CV
3. Ontwikkelpunten: welke vereisten ontbreken of kunnen worden verbeterd
4. Concrete aanbevelingen voor de kandidaat
5. Samenvatting: een korte conclusie over de algemene geschiktheid

Format het antwoord in duidelijk leesbare HTML met de volgende structuur:
<div class='analysis-section score'>Compatibiliteitsscore: [score]%</div>
<div class='analysis-section strengths'><h3>Sterke Punten</h3>[lijst met sterke punten]</div>
<div class='analysis-section gaps'><h3>Ontwikkelpunten</h3>[lijst met ontwikkelpunten]</div>
<div class='analysis-section recommendations'><h3>Aanbevelingen</h3>[lijst met aanbevelingen]</div>
<div class='analysis-section summary'><h3>Conclusie</h3>[samenvattende conclusie]</div>";
    }

    private function prepare_claude_prompt_en($job_description, $cv_data)
    {
        return "Please analyze the compatibility between the following job description and CV data:

Job Description:
{$job_description}

CV Data:
Soft Skills: " . json_encode($cv_data['soft_skills']) . "
Technical Skills: " . json_encode($cv_data['technical_skills']) . "
Education: " . json_encode($cv_data['education']) . "
Work Experience: " . json_encode($cv_data['work_experience']) . "

Please provide:
1. Overall compatibility score (0-100%)
2. Matching skills and qualifications
3. Missing or gaps in requirements
4. Recommendations for improvement";
    }

    /*private function call_claude_api($prompt)
    {
        $url = 'https://api.anthropic.com/v1/messages';

        $headers = array(
            'Content-Type' => 'application/json',
            'x-api-key' => $this->api_key,
            'anthropic-version' => '2023-06-01'
        );

        $body = array(
            'model' => 'claude-3-opus-20240229',
            'max_tokens' => 1024,
            'messages' => array(
                array(
                    'role' => 'user',
                    'content' => $prompt
                )
            )
        );

        $response = wp_remote_post($url, array(
            'headers' => $headers,
            'body' => json_encode($body),
            'timeout' => 30
        ));

        if (is_wp_error($response)) {
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);
        return isset($body['content']) ? $body['content'] : false;
    }*/
}

// Initialize the plugin
new CVCompatibilityChecker();