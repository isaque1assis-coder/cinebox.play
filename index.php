<?php
/**
 * O template principal do tema.
 */

get_header();
?>

<main id="main" class="site-main" style="padding: 150px 4% 50px 4%;">
    <div class="page-container" style="max-width: 800px; margin: 0 auto;">

        <?php if ( have_posts() ) : ?>

            <header class="page-header">
                <h1 class="page-title"><?php _e( 'Últimos Posts', 'temagemini' ); ?></h1>
            </header>

            <?php
            // Inicia o Loop
            while ( have_posts() ) :
                the_post();
                ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
                    </header>
                    <div class="entry-summary">
                        <?php the_excerpt(); ?>
                    </div>
                </article>
                <hr style="margin: 30px 0; border-color: #333;">
                <?php
            endwhile;

        else :
            // Se não houver posts
            echo '<p>Nenhum conteúdo encontrado.</p>';
        endif;
        ?>

    </div>
</main>

<?php
get_footer();
?>