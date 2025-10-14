<?php
/**
 * Template Name: Homepage Estilo Gemini
 *
 * Este é o template para criar uma homepage dinâmica no estilo Netflix/Gemini.
 */

get_header(); // Inclui o header do seu tema
?>

<main class="gemini-home">

    <?php
    // Query para buscar o post em destaque
    $hero_args = array(
        'post_type'      => 'post',
        'posts_per_page' => 1,
        'tag'            => 'destaque',
    );

    $hero_query = new WP_Query($hero_args);

    if ($hero_query->have_posts()) :
        while ($hero_query->have_posts()) : $hero_query->the_post();
            $hero_bg_url = get_the_post_thumbnail_url(null, 'full');
    ?>
    <header class="hero" style="background-image: linear-gradient(to top, rgba(20, 20, 20, 1) 0%, rgba(20, 20, 20, 0) 50%, rgba(20, 20, 20, 1) 100%), url('<?php echo esc_url($hero_bg_url); ?>');">
        <div class="hero-content">
            <h1 class="hero-title"><?php the_title(); ?></h1>
            <div class="hero-description">
                <?php the_excerpt(); ?>
            </div>
            <a href="<?php the_permalink(); ?>" class="hero-button">► Assistir Agora</a>
        </div>
    </header>
    <?php
        endwhile;
        wp_reset_postdata();
    else :
    ?>
    <header class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Bem-vindo ao seu site</h1>
            <p class="hero-description">Crie um post, adicione uma imagem destacada e a tag "destaque" para que ele apareça aqui.</p>
        </div>
    </header>
    <?php endif; ?>

    <div class="carousel-container">
    <?php
    // Pega a string de categorias salvas no Personalizador.
    $category_slugs_string = get_theme_mod( 'temagemini_home_categories', 'acao,comedia' );

    // Converte a string em um array PHP.
    $categories_to_show = array_map( 'trim', explode( ',', $category_slugs_string ) );

    // Loop para exibir um carrossel para cada categoria.
    foreach ($categories_to_show as $category_slug) :
        $category = get_category_by_slug($category_slug);

        if ($category) :
            $category_link = get_category_link($category->term_id);

            $category_args = array(
                'post_type'      => 'post',
                'posts_per_page' => 10,
                'category_name'  => $category_slug,
            );
            $category_query = new WP_Query($category_args);

            if ($category_query->have_posts()) :
    ?>
    <section class="content-section">
        <h2 class="category-title">
            <a href="<?php echo esc_url($category_link); ?>">
                <?php echo esc_html($category->name); ?>
            </a>
        </h2>
        <button class="carousel-arrow arrow-left" aria-label="Rolar para esquerda">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
        </button>
        <div class="carousel">
            <?php while ($category_query->have_posts()) : $category_query->the_post(); ?>
                <?php
                        // Busca a URL do trailer e adiciona classes condicionais
                        $trailer_url = get_field('trailer_url', get_the_ID());
                        $card_classes = 'carousel-item-link'; // Classe padrão
                        if ( has_category( array( 'no-cinema', 'lancamentos' ) ) ) {
                            $card_classes .= ' card-portrait'; // Adiciona a classe especial
                        }
                        ?>
                        <a href="<?php the_permalink(); ?>" class="<?php echo $card_classes; ?>" <?php if ($trailer_url) { echo 'data-trailer-url="' . esc_url($trailer_url) . '"'; } ?>>     
                    <div class="carousel-item" style="background-image: url('<?php echo get_the_post_thumbnail_url(null, 'medium_large'); ?>')">
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
        <button class="carousel-arrow arrow-right" aria-label="Rolar para direita">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </button>
    </section>
<?php
            endif;
            wp_reset_postdata();
        endif;
    endforeach;
    ?>

    <?php
    // Query para exibir carrossel baseado em tags
    $tag_slugs_string = get_theme_mod( 'temagemini_home_tags', 'trending,populares' );

    $tags_to_show = array_map( 'trim', explode( ',', $tag_slugs_string ) );

    foreach ($tags_to_show as $tag_slug) :
        $tag = get_term_by('slug', $tag_slug, 'post_tag');

        if ($tag) :
            $tag_link = get_term_link($tag->term_id);
            $tag_args = array(
                'post_type'      => 'post',
                'posts_per_page' => 10,
                'tag_slug__in'   => array($tag_slug),
            );
            $tag_query = new WP_Query($tag_args);

            if ($tag_query->have_posts()) :
    ?>
    <section class="content-section">
        <h2 class="category-title">
            <a href="<?php echo esc_url($tag_link); ?>">
                <?php echo esc_html($tag->name); ?>
            </a>
        </h2>
        <button class="carousel-arrow arrow-left" aria-label="Rolar para esquerda">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
        </button>
        <div class="carousel">
            <?php while ($tag_query->have_posts()) : $tag_query->the_post(); ?>
                <a href="<?php the_permalink(); ?>" class="carousel-item-link">
                    <div class="carousel-item" style="background-image: url('<?php echo get_the_post_thumbnail_url(null, 'medium_large'); ?>')">
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
        <button class="carousel-arrow arrow-right" aria-label="Rolar para direita">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </button>
    </section>
<?php
            endif;
            wp_reset_postdata();
        endif;
    endforeach;
    ?>
    </div>
</main>

<?php
get_footer(); // Inclui o footer do seu tema
?>