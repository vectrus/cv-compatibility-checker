<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 */

get_header(); ?>

    <main class="site-main">
        <div class="container py-5">
            <div class="row">
                <div class="col-lg-8">
                    <?php if (have_posts()) : ?>
                        <?php if (is_home() && !is_front_page()) : ?>
                            <header class="page-header mb-5">
                                <h1 class="page-title"><?php single_post_title(); ?></h1>
                            </header>
                        <?php endif; ?>

                        <?php while (have_posts()) : the_post(); ?>
                            <article id="post-<?php the_ID(); ?>" <?php post_class('mb-5'); ?>>
                                <header class="entry-header mb-4">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="entry-thumbnail mb-4">
                                            <?php the_post_thumbnail('large', array('class' => 'img-fluid')); ?>
                                        </div>
                                    <?php endif; ?>

                                    <h2 class="entry-title">
                                        <a href="<?php the_permalink(); ?>" class="text-decoration-none">
                                            <?php the_title(); ?>
                                        </a>
                                    </h2>

                                    <div class="entry-meta text-muted">
                                    <span class="posted-on">
                                        Posted on <?php echo get_the_date(); ?>
                                    </span>
                                        <span class="byline ms-3">
                                        by <?php the_author(); ?>
                                    </span>
                                        <?php if (has_category()) : ?>
                                            <span class="categories-links ms-3">
                                            in <?php the_category(', '); ?>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </header>

                                <div class="entry-content">
                                    <?php
                                    if (is_single()) {
                                        the_content();
                                    } else {
                                        the_excerpt();
                                        ?>
                                        <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                                            Read More
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </div>

                                <?php if (has_tag()) : ?>
                                    <footer class="entry-footer mt-4">
                                        <div class="tags-links">
                                            <?php the_tags('Tags: ', ', '); ?>
                                        </div>
                                    </footer>
                                <?php endif; ?>
                            </article>
                        <?php endwhile; ?>

                        <!-- Pagination -->
                        <nav class="navigation pagination" role="navigation">
                            <div class="nav-links">
                                <?php
                                echo paginate_links(array(
                                    'prev_text' => '<span class="btn btn-outline-primary">&laquo; Previous</span>',
                                    'next_text' => '<span class="btn btn-outline-primary">Next &raquo;</span>',
                                    'type' => 'list'
                                ));
                                ?>
                            </div>
                        </nav>

                    <?php else : ?>
                        <div class="no-results">
                            <header class="page-header">
                                <h1 class="page-title">Nothing Found</h1>
                            </header>
                            <div class="page-content">
                                <?php if (is_search()) : ?>
                                    <p>Sorry, but nothing matched your search terms. Please try again with some
                                        different keywords.</p>
                                    <?php get_search_form(); ?>
                                <?php else : ?>
                                    <p>It seems we can't find what you're looking for. Perhaps searching can help.</p>
                                    <?php get_search_form(); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-4">
                    <aside class="sidebar">
                        <?php get_sidebar(); ?>
                    </aside>
                </div>
            </div>
        </div>
    </main>

<?php get_footer(); ?>