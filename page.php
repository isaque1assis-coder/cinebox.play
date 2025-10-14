<?php
/**
 * O template para exibir todas as páginas estáticas. (VERSÃO FINAL CORRIGIDA)
 */

get_header();
?>

<main id="main" class="site-main">

    <?php
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();

            // Verifica se a página TEM uma imagem destacada
            if ( has_post_thumbnail() ) :
                // Pega a URL da imagem em tamanho completo
                $featured_img_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                ?>
                <div class="page-featured-image-full-width parallax-banner" style="background-image: url('<?php echo esc_url($featured_img_url); ?>');">
                </div>
            <?php
            endif; // Fim da verificação da imagem
            ?>

            <div class="page-container">
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="page-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                    </header>
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </article>
            </div><?php
        endwhile;
    endif;
    ?>

</main><?php
get_footer();
?>