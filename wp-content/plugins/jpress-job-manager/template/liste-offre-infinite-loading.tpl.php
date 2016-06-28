<?php
/**
 * Template Name: Gabarit Liste des offres
 *
 * @package jpress-job-manager
 *  @since jpress-job-manager 1.0
 */

get_header(); ?>

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

        <section>
            <?php
            $filters = jpress_jm_is_in_options( JM_META_LIST_OFFRE_FILTRE, 'template-list' );
            $sortings = jpress_jm_is_in_options( JM_META_LIST_OFFRE_SORT, 'template-list' );
            $item_per_page = jpress_jm_is_in_options( JM_META_LIST_ITEM_PER_PAGE, 'template-list' );
            ?>
            <div class="offre-filter">
                <?php if ( !empty($filters)  ) :?>
                    <?php foreach ( $filters  as $tax):
                        $the_taxonomie = get_taxonomy( $tax );
                        $terms = get_terms( $tax, array( 'hide_empty' => false  ) );
                        ?>
                        <select class="filter-<?php echo $tax;?>">
                            <option data-filter="filter-<?php echo $tax;?>" data-filterby="" value=""><?php echo __('Sélectionner ') . $the_taxonomie->labels->name ;?></option>
                            <?php foreach($terms as $t):?>
                                <option data-filter="filter-<?php echo $tax;?>" data-filterby="<?php echo $t->term_id;?>" value="<?php echo $t->term_id;?>"><?php echo $t->name;?></option>
                            <?php endforeach;?>
                        </select>
                    <?php endforeach;?>
                <?php endif;?>
            </div>
            <div class="offre-sort">
                <?php if ( !empty($sortings)  ) :?>
                    <?php foreach ( $sortings  as $meta):?>
                            Ordonner par <?php echo $meta;?>
                            <a href="javascript:;" data-order="ASC" data-orderby="<?php echo $meta;?>" class="sort-<?php echo $meta;?>">v</a>
                            <a href="javascript:;" data-order="DESC" data-orderby="<?php echo $meta;?>" class="sort-<?php echo $meta;?>">^</a>
                    <?php endforeach;?>
                <?php endif;?>
            </div>
            <div class="offreListe">
                <?php if ( class_exists( 'WP_Infinite_Loading' ) ):?>

                    <?php
                    $infinite_loading = new WP_Infinite_Loading('offres-box');

                    // Configuration
                    $infinite_loading->setListView('list');
                    $infinite_loading->removeFirstMask();
                    $infinite_loading->setItemNumberOnLoad( $item_per_page );
                    $infinite_loading->setItemNumberToLoad( $item_per_page );
                    $infinite_loading->setContainerClasses('liste-offre clearfix');
                    $infinite_loading->setRenderItemCallback( array(JM_Offre, 'renderItemCallback'));
                    $infinite_loading->setGetItemsCallback( array(JM_Offre, 'getItemsCallback'));
                    $infinite_loading->setInfiniteLoadButton(
                        array(
                            'id'=>'load-more-offre',
                            'tpl'=>'<a id="load-more-offre" href="javascript:;" title="' .  __("En voir plus") . '" class="plus load-more-offre"><span>' . __("En voir plus") . '</span></a>',
                        ),
                        array(
                            'id'=>'no-load-more',
                            'tpl'=>'<div id="no-load-more" class="footer-hide-button"></div>'
                        )
                    );


                    if ( !empty($filters) ){
                        foreach ( $filters as $filter) {
                            $infinite_loading->addFilter( 'filter-' . $filter );
                        }
                    }


                    if ( !empty($sortings) ){
                        foreach ( $sortings as $sort) {
                            $infinite_loading->addSorting( 'sort-' . $sort );
                        }
                    }

                    //message not found
                    $infinite_loading->setMessageNotFound('<p class="no-results">Aucun résultat</p>');

                    // Display infinite loading box
                    $infinite_loading->displayItems();
                    ?>

                    <!-- Bouton voir plus -->
                    <?php $infinite_loading->displayInfiniteLoadButton(); ?>
                    <!-- /Bouton voir plus -->

                <?php else :  ?>
                    <p>WP_Infinite_Loading n'existe pas. Merci d'activer cet extension.</p>
                <?php endif; ?>
            </div>

        </section>

    </div><!-- .content-area -->

<?php get_footer(); ?>