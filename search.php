<?php
/**
 * O template para exibir os resultados da busca.
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="search-page-container">

        <header class="archive-header text-center">
            <h1 class="page-title">
                <?php echo '<span>' . get_search_query() . '</span>'; ?>
            </h1>
        </header>

        <?php if ( have_posts() ) : ?>

            <div class="post-grid">
                <?php
                while ( have_posts() ) :
                    the_post();
                    get_template_part( 'template-parts/content', 'grid-item' );
                endwhile;
                ?>
            </div>

            <?php
            the_posts_pagination();
            ?>

        <?php
        else :
            ?>
            <section class="no-results not-found text-center">
                <h2 class="page-title"><?php _e( 'Nada Encontrado', 'temagemini' ); ?></h2>
                <p><?php _e( 'Mas talvez você se interesse por um destes títulos recentes:', 'temagemini' ); ?></p>
            </section>
            
            <div class="post-grid">
            <?php
                $fallback_args = array(
                    'post_type' => 'post',
                    'posts_per_page' => 3,
                    'ignore_sticky_posts' => 1,
                    'orderby'             => 'rand' 
                );
                $fallback_query = new WP_Query($fallback_args);

                if ($fallback_query->have_posts()) {
                    while ($fallback_query->have_posts()) {
                        $fallback_query->the_post();
                        get_template_part( 'template-parts/content', 'grid-item' );
                    }
                }
                wp_reset_postdata();
            ?>
            </div>
            <?php
        endif;
        ?>

    </div>
</main>

<?php get_footer(); ?>