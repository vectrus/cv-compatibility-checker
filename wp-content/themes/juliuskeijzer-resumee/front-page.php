<?php
/**
 * Template Name: Front Page
 */

get_header(); ?>

    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2 <!--text-center-->">
                    <h1 class="home-page-title"><?php the_title(); ?></h1>
                    <p class="lead"><?php the_content(); ?></p>
                </div>
            </div>
        </div>
    </section>

    <section class="vacature-section py-5 no-print">
        <div class="container">
            <h2 class="section-title">Vacature checker</h2>
            <p style="width: 60%">Heb je een passende vacature voor mij? Kijk of hij aansluit op mijn C.V.. Plak je vacaturetekst in het
                onderstaande veld en kijk of er een match is. De analyse wordt gedaan door Antropic.ai.</p>

                <?php echo do_shortcode('[cv_compatibility_checker]') ?>
        </div>
    </section>

    <section class="experience-section py-2">
    <div class="container">
            <h2 class="section-title">Werkervaring</h2>
            <div class="timeline">
                <?php
                $args = array(
                    'post_type' => 'work_experience',
                    'posts_per_page' => -1,
                    'orderby' => 'meta_value',
                    'meta_key' => '_start_date',
                    'order' => 'DESC'
                );

                $experience_query = new WP_Query($args);

                while ($experience_query->have_posts()) : $experience_query->the_post();
                    $start_date = get_post_meta(get_the_ID(), '_start_date', true);
                    $end_date = get_post_meta(get_the_ID(), '_end_date', true);
                    $company = get_post_meta(get_the_ID(), '_company', true);
                    $position = get_post_meta(get_the_ID(), '_position', true);
                    $location = get_post_meta(get_the_ID(), '_location', true);
                    $key_achievements = get_post_meta(get_the_ID(), '_key_achievements', true);

                    ?>
                    <div class="timeline-item">
                        <h3><?php echo esc_html($position); ?></h3>
                        <h4><?php echo esc_html($company); ?></h4>
                        <p class="text-muted">
                            <?php
                            echo nlDate(date('M Y', strtotime($start_date)));
                            echo ' - ';
                            echo nlDate($end_date ? date('M Y', strtotime($end_date)) : 'nu');
                            ?> | <?php echo esc_html($location); ?>
                        </p>

                        <div class="content">
                            <?php the_content(); ?>
                        </div>

                        <?php if ($key_achievements) : ?>
                            <div class="key-achievements">
                                <h5>Key Achievements:</h5>
                                <div class="achievement-content">
                                    <?php echo nl2br(esc_html($key_achievements)); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </section>

    <section class="skills-section py-5 bg-light">
        <div class="container">
            <div class="row">
                <!-- Technical Skills -->
                <div class="col-md-6 skill-row">
                    <h2 class="section-title">Technical Skills</h2>
                    <?php
                    $tech_categories = get_terms(array(
                        'taxonomy' => 'skill_category',
                        'object_type' => array('technical_skill'),
                    ));

                    foreach ($tech_categories as $category) :
                        $args = array(
                            'post_type' => 'technical_skill',
                            'posts_per_page' => -1,
                            'orderby' => 'title',
                            'order' => 'ASC',
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'skill_category',
                                    'field' => 'term_id',
                                    'terms' => $category->term_id,
                                ),
                            ),
                        );
                        $technical_skills_query = new WP_Query($args);

                        if ($technical_skills_query->have_posts()) :
                            ?>
                            <div class="skill-category mb-4">
                                <h3 class="h4 mb-3"><?php echo esc_html($category->name); ?></h3>
                                <?php
                                while ($technical_skills_query->have_posts()) : $technical_skills_query->the_post();
                                    $proficiency = get_post_meta(get_the_ID(), '_proficiency', true);
                                    $years = get_post_meta(get_the_ID(), '_years_experience', true);
                                    ?>
                                    <div class="skill-item">
                                        <h4 class="h5"><?php the_title(); ?></h4>
                                        <div class="skill-meta">
                                            <!--<span class="badge bg-primary"><?php /*echo esc_html($proficiency); */?></span>-->
                                            <?php if ($years) : ?>
                                                <span class="text-muted ms-2"><?php echo esc_html($years); ?> years</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="skill-description">
                                            <?php the_content(); ?>
                                        </div>
                                    </div>
                                <?php
                                endwhile;
                                ?>
                            </div>
                        <?php
                        endif;
                        wp_reset_postdata();
                    endforeach;
                    ?>
                </div>

                <!-- Soft Skills -->
                <div class="col-md-6">
                    <h2 class="section-title">Soft Skills</h2>
                    <?php
                    $soft_categories = get_terms(array(
                        'taxonomy' => 'skill_category',
                        'object_type' => array('soft_skill'),
                    ));

                    foreach ($soft_categories as $category) :
                        $args = array(
                            'post_type' => 'soft_skill',
                            'posts_per_page' => -1,
                            'orderby' => 'title',
                            'order' => 'ASC',
                            'tax_query' => array(
                                array(
                                    'taxonomy' => 'skill_category',
                                    'field' => 'term_id',
                                    'terms' => $category->term_id,
                                ),
                            ),
                        );
                        $soft_skills_query = new WP_Query($args);

                        if ($soft_skills_query->have_posts()) :
                            ?>
                            <div class="skill-category mb-4">
                                <h3 class="h4 mb-3"><?php echo esc_html($category->name); ?></h3>
                                <?php
                                while ($soft_skills_query->have_posts()) : $soft_skills_query->the_post();
                                    $proficiency = get_post_meta(get_the_ID(), '_proficiency', true);
                                    $years = get_post_meta(get_the_ID(), '_years_experience', true);
                                    ?>
                                    <div class="skill-item">
                                        <?php the_title(); ?>
                                        <div class="skill-meta">
                                            <span class="badge bg-primary"><?php echo esc_html($proficiency); ?></span>
                                          <!--  <?php /*if ($years) : */?>
                                                <span class="text-muted ms-2"><?php /*echo esc_html($years); */?> years</span>
                                            --><?php /*endif; */?>
                                        </div>

                                        <!--<div class="skill-description">
                                            <?php /*the_content(); */?>
                                        </div>-->
                                    </div>
                                <?php
                                endwhile;
                                ?>
                            </div>
                        <?php
                        endif;
                        wp_reset_postdata();
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
    </section>

    <section class="education-section py-5">
        <div class="container">
            <h2 class="section-title">Opleidingen</h2>
            <div class="education-timeline">
                <?php
                $args = array(
                    'post_type' => 'education',
                    'posts_per_page' => -1,
                    'orderby' => 'meta_value_num',
                    'meta_key' => '_end_year',
                    'order' => 'DESC'
                );

                $education_query = new WP_Query($args);

                while ($education_query->have_posts()) : $education_query->the_post();
                    $start_year = get_post_meta(get_the_ID(), '_start_year', true);
                    $end_year = get_post_meta(get_the_ID(), '_end_year', true);
                    $institution = get_post_meta(get_the_ID(), '_institution', true);
                    $degree_type = get_post_meta(get_the_ID(), '_degree_type', true);
                    $field_of_study = get_post_meta(get_the_ID(), '_field_of_study', true);
                    $gpa = get_post_meta(get_the_ID(), '_gpa', true);
                    $location = get_post_meta(get_the_ID(), '_location', true);
                    $key_subjects = get_post_meta(get_the_ID(), '_key_subjects', true);
                    ?>
                    <div class="education-item">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="education-header">
                                    <h3 class="h4 mb-1">
                                        <?php echo esc_html(get_the_title(get_the_ID())); ?>
                                        <?php if ($field_of_study) : ?>
                                            in <?php echo esc_html($field_of_study); ?>
                                        <?php endif; ?>
                                    </h3>
                                    <h4 class="h5 text-primary mb-2"><?php echo esc_html($institution); ?></h4>
                                    <p class="text-muted">
                                        <?php echo esc_html($start_year); ?>
                                        <?php if ($location) : ?>
                                            | <?php echo esc_html($location); ?>
                                        <?php endif; ?>
                                        <?php if ($gpa) : ?>
                                            | GPA: <?php echo esc_html($gpa); ?>
                                        <?php endif; ?>
                                    </p>
                                </div>

                                <?php if (get_the_content()) : ?>
                                    <div class="education-description mb-3">
                                        <?php the_content(); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($key_subjects) : ?>
                                    <div class="key-subjects">
                                        <h5 class="h6 mb-2">Key Subjects:</h5>
                                        <div class="subjects-list">
                                            <?php
                                            $subjects = explode("\n", $key_subjects);
                                            foreach ($subjects as $subject) {
                                                $subject = trim($subject);
                                                if (!empty($subject)) {
                                                    echo '<span class="badge bg-light text-dark me-2 mb-2">' . esc_html($subject) . '</span>';
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </section>



    <section class="portfolio-section py-5">
        <div class="container">
            <h2 class="section-title">Portfolio</h2>
            <div class="row">
                <?php
                $args = array(
                    'post_type' => 'portfolio',
                    'posts_per_page' => 6
                );
                $portfolio_query = new WP_Query($args);
                while ($portfolio_query->have_posts()) : $portfolio_query->the_post();
                    $technical_skills = get_post_meta(get_the_ID(), '_technical_skills', true);
                    $soft_skills = get_post_meta(get_the_ID(), '_soft_skills', true);
                    $project_url = get_post_meta(get_the_ID(), '_project_url', true);
                    $github_url = get_post_meta(get_the_ID(), '_github_url', true);
                    $completion_date = get_post_meta(get_the_ID(), '_completion_date', true);
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="portfolio-item card h-100">
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="portfolio-image">
                                    <?php the_post_thumbnail('large', array('class' => 'card-img-top')); ?>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h3 class="card-title h5"><?php the_title(); ?></h3>
                                <div class="portfolio-excerpt no-print">
                                    <?php the_excerpt(); ?>
                                </div>

                                <?php if ($completion_date) : ?>
                                    <p class="text-muted small no-print">
                                         <?php echo nlDate(date('F Y', strtotime($completion_date))); ?>
                                    </p>
                                <?php endif; ?>

                                <?php if ($technical_skills) : ?>
                                    <div class="technical-skills no-print mb-2">
                                        <h4 class="h6">Gebruikte techniek(en):</h4>
                                        <div class="skills-tags">
                                            <?php
                                            $tech_ids = explode(',', $technical_skills);
                                            foreach ($tech_ids as $skill_id) {
                                                $skill = get_post($skill_id);
                                                if ($skill) {
                                                    echo '<span class="badge bg-primary me-1 mb-1">' . esc_html($skill->post_title) . '</span>';
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if ($soft_skills) : ?>
                                    <div class="soft-skills no-print mb-2">
                                        <h4 class="h6">Soft skills:</h4>
                                        <div class="skills-tags">
                                            <?php
                                            $soft_ids = explode(',', $soft_skills);
                                            foreach ($soft_ids as $skill_id) {
                                                $skill = get_post($skill_id);
                                                if ($skill) {
                                                    echo '<span class="badge bg-secondary me-1 mb-1">' . esc_html($skill->post_title) . '</span>';
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="portfolio-links mt-3">
                                    <?php if ($project_url) : ?>
                                        <a href="<?php echo esc_url($project_url); ?>"
                                           class="btn btn-primary btn-sm me-2" target="_blank">
                                            Bekijk
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($github_url) : ?>
                                        <a href="<?php echo esc_url($github_url); ?>"
                                           class="btn btn-outline-secondary btn-sm" target="_blank">
                                            Bekijk code
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                endwhile;
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </section>

<?php get_footer(); ?>