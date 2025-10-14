<?php
/**
 * O template para exibir páginas de arquivo de categoria (VERSÃO FINAL COM BOTÃO).
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="category-container">

        <header class="archive-header text-center">
            <h1 class="page-title"><?php single_cat_title(); ?></h1>
            <?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
        </header>

        <?php if ( have_posts() ) : ?>

            <div id="post-grid-category" class="post-grid">
                <?php
                while ( have_posts() ) : the_post();
                    get_template_part( 'template-parts/content', 'grid-item' ); // <-- MUDANÇA IMPORTANTE
                endwhile;
                ?>
            </div>

            <?php
            // --- INÍCIO DO NOVO CÓDIGO DO BOTÃO ---
            global $wp_query;
            if ( $wp_query->max_num_pages > 1 ) : // Só mostra o botão se houver mais de 1 página
            ?>
               
                <div class="load-more-container">  <button id="load-more-btn" class="load-more-btn" 
                            data-page="2" 
                            data-max-pages="<?php echo $wp_query->max_num_pages; ?>"
                            data-category="<?php echo get_queried_object_id(); ?>"
                            aria-label="Carregar mais posts">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                    </button>
                </div>
            <?php endif; 
            // --- FIM DO NOVO CÓDIGO DO BOTÃO ---
            ?>

        <?php
        else :
            // Se não houver posts na categoria...
        endif;
        ?>

    </div>
</main>

<?php get_footer(); ?>