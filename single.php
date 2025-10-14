

<?php
/**
 * O template para exibir posts individuais (versão com Layout A - CORRIGIDO).
 */

get_header();
?>

<main id="main" class="site-main">

    <?php
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();

            // --- BLOCO DO CABEÇALHO COM TRAILER/IMAGEM (Hero Section) ---
            $trailer_url = get_field('trailer_url');
            $is_youtube_video = ( strpos($trailer_url, 'youtube.com') !== false || strpos($trailer_url, 'youtu.be') !== false );
            $background_element = '';

            if ( $trailer_url && $is_youtube_video ) :
                $video_id = '';
                if (strpos($trailer_url, 'youtube.com/watch?v=') !== false) {
                    parse_str( parse_url( $trailer_url, PHP_URL_QUERY ), $params );
                    $video_id = $params['v'];
                } elseif (strpos($trailer_url, 'youtu.be/') !== false) {
                    $video_id = substr( parse_url( $trailer_url, PHP_URL_PATH ), 1 );
                }
                $embed_url = 'https://www.youtube.com/embed/' . esc_attr($video_id) . '?autoplay=1&mute=1&loop=1&playlist=' . esc_attr($video_id) . '&controls=0&modestbranding=1&rel=0&showinfo=0';
                
                $background_element = '<div class="post-trailer-container video-youtube">';
                $background_element .= '<iframe class="post-trailer-video" src="' . esc_url($embed_url) . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen loading="lazy" title="' . the_title_attribute('echo=0') . ' Trailer"></iframe>'; // CORREÇÃO APLICADA
                $background_element .= '</div>';
            
            elseif ( $trailer_url ) :
                $background_element = '<div class="post-trailer-container">';
                $background_element .= '<video class="post-trailer-video" autoplay loop muted playsinline controlslist="nodownload" poster="' . esc_url( get_the_post_thumbnail_url(get_the_ID(), 'full') ) . '">';
                $background_element .= '<source src="' . esc_url($trailer_url) . '" type="video/mp4">';
                $background_element .= '</video>';
                $background_element .= '</div>';

            elseif ( has_post_thumbnail() ) :
                $featured_img_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                $background_element = '<div class="page-featured-image-full-width parallax-banner" style="background-image: url(\'' . esc_url($featured_img_url) . '\');"></div>';
            endif;
            
            // --- NOVA ESTRUTURA DO CONTEÚDO NO HERO ---
            echo '<div class="single-hero-container">';
            echo $background_element; // Imprime o elemento de fundo (vídeo ou imagem)
            echo '<div class="single-hero-overlay"></div>'; // Overlay escuro por cima
            
            echo '<div class="single-hero-content">';
                echo '<h1 class="entry-title">' . get_the_title() . '</h1>';
                echo '<div class="post-meta-info">';
                    echo '<div class="meta-item post-categories"><strong>Gênero:</strong> ' . get_the_category_list(', ') . '</div>';
                    if( get_field('idiomas_disponiveis') ) { echo '<div class="meta-item"><strong>Idiomas:</strong> ' . get_field('idiomas_disponiveis') . '</div>'; }
                echo '</div>';
                if( get_field('sinopse_curta') ) { echo '<div class="post-synopsis"><p>' . get_field('sinopse_curta') . '</p></div>'; }
                
                $video_premium_url = get_field('video_principal_url');
                $video_free_url = get_field('video_free_url');
                $url_para_usar = $video_free_url;
                $button_class = 'hero-button free';
                if ( function_exists('pmpro_hasMembershipLevel') && pmpro_hasMembershipLevel('1') && !empty($video_premium_url) ) {
                    $url_para_usar = $video_premium_url;
                    $button_class = 'hero-button premium';
                }
                if ( ! empty($url_para_usar) ) {
                    echo '<div class="play-button-container">';
                    echo '<button id="play-main-video-btn" class="' . esc_attr($button_class) . '" data-video-url="' . esc_url($url_para_usar) . '" data-post-id="' . get_the_ID() . '">► Reproduzir</button>';
                    echo '</div>';
                }
            echo '</div>';
            echo '</div>';
            
            
            // --- SEÇÃO DE POSTS RELACIONADOS (MOVIDA PARA CÁ) ---
                    $categories = get_the_category($post->ID);
                    if ($categories) {
                        $category_ids = array();
                        foreach($categories as $individual_category) $category_ids[] = $individual_category->term_id;

                        $args = array(
                            'category__in'        => $category_ids,
                            'post__not_in'        => array($post->ID),
                            'posts_per_page'      => 8,
                            'ignore_sticky_posts' => 1
                        );

                        $related_query = new WP_Query($args);

                        if ($related_query->have_posts()) {
                            ?>
                            <div class="related-posts-section">
                                <h2 class="related-title">Títulos Semelhantes</h2>
                                <div class="carousel">
                                    <?php while ($related_query->have_posts()) : $related_query->the_post();
                                        $trailer_url_related = get_field('trailer_url', get_the_ID());
                                        ?>
                                        <a href="<?php the_permalink(); ?>" class="carousel-item-link" <?php if ($trailer_url_related) { echo 'data-trailer-url="' . esc_url($trailer_url_related) . '"'; } ?>>
                                            <div class="carousel-item" style="background-image: url('<?php echo get_the_post_thumbnail_url(null, 'medium_large'); ?>')">
                                            </div>
                                        </a>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                            <?php
                        }
                        wp_reset_postdata();
                    }
                    // --- FIM DA SEÇÃO DE POSTS RELACIONADOS ---
            
        endwhile;
    endif;
    ?>
</main>

<?php 
$video_principal_url_check = get_field('video_principal_url', get_the_ID());
if ( $video_principal_url_check ) : ?>
<div id="fullscreen-player-container" class="fullscreen-player-container">
    <div class="player-top-controls">
        <button id="fullscreen-toggle-btn" class="player-top-button" aria-label="Tela Cheia">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3H5a2 2 0 0 0-2 2v3m13 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"/></svg>
        </button>
        <button id="close-player-btn" class="player-top-button close-player-btn">&times;</button>
    </div>
    <div id="player-placeholder"></div>
</div>
<?php endif; ?>

<?php get_footer(); ?>