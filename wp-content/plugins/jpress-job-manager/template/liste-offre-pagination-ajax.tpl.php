<?php
/**
 * Template Name: Gabarit Liste des offres
 *
 * @package jpress-job-manager
 *  @since jpress-job-manager 1.0
 */

get_header(); ?>
pagin ajax
    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

            <?php
            // Start the loop.
            while ( have_posts() ) : the_post();?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                    <header class="entry-header">
                        <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
                    </header><!-- .entry-header -->

                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div><!-- .entry-content -->

                </article><!-- #post-## -->

            <?php endwhile; ?>

        </main><!-- .site-main -->
    </div><!-- .content-area -->

<?php get_footer(); ?>