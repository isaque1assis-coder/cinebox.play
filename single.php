<?php
/**
 * O template para exibir posts individuais.
 */

get_header();
?>

<main id="main" class="site-main">

    <?php
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();

            // Pega a URL do vídeo principal que vamos usar no player
            $video_principal_url = get_field('video_principal_url');
            
            // --- BLOCO DO CABEÇALHO COM TRAILER/IMAGEM ---
            $trailer_url = get_field('trailer_url');

            // Verifica se a URL é do YouTube
            $is_youtube_video = ( strpos($trailer_url, 'youtube.com') !== false || strpos($trailer_url, 'youtu.be') !== false );
                
            if ( $trailer_url && $is_youtube_video ) :
                $video_id = '';
                if (strpos($trailer_url, 'youtube.com/watch?v=') !== false) {
                    parse_str( parse_url( $trailer_url, PHP_URL_QUERY ), $params );
                    $video_id = $params['v'];
                } elseif (strpos($trailer_url, 'youtu.be/') !== false) {
                    $video_id = substr( parse_url( $trailer_url, PHP_URL_PATH ), 1 );
                }
                
                $embed_url = 'https://www.youtube.com/embed/' . esc_attr($video_id) . '?autoplay=1&mute=1&loop=1&playlist=' . esc_attr($video_id) . '&controls=0&modestbranding=1&rel=0&showinfo=0';
                
                ?>
                <div class="post-trailer-container video-youtube">
                    <iframe 
                        class="post-trailer-video" 
                        src="<?php echo esc_url($embed_url); ?>" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                        allowfullscreen
                        loading="lazy"
                        title="<?php the_title_attribute(); ?> Trailer">
                    </iframe>
                    <div class="video-overlay"></div>
                </div>
                
            <?php
            elseif ( $trailer_url ) :
                // Se não for YouTube, mas ainda houver uma URL (assumimos que é um MP4 direto)
                ?>
                <div class="post-trailer-container">
                    <video class="post-trailer-video" autoplay loop muted playsinline controlslist="nodownload" poster="<?php echo esc_url( get_the_post_thumbnail_url(get_the_ID(), 'full') ); ?>">
                        <source src="<?php echo esc_url($trailer_url); ?>" type="video/mp4">
                        Seu navegador não suporta a tag de vídeo.
                    </video>
                    <div class="video-overlay"></div>
                </div>
            <?php
            elseif ( has_post_thumbnail() ) :
                // Se não tiver trailer nenhum, mas tiver imagem destacada, exibe a imagem
                $featured_img_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                ?>
                <div class="page-featured-image-full-width parallax-banner" style="background-image: url('<?php echo esc_url($featured_img_url); ?>');">
                </div>
            <?php
            endif;
            // --- FIM DO BLOCO DO CABEÇALHO ---
            ?>

            <div class="single-post-container">
                
                <?php // Botão "Assistir" com o data-post-id adicionado
                if ( $video_principal_url ) : ?>
                    <div class="play-button-container">
                        <button id="play-main-video-btn" class="hero-button" data-video-url="<?php echo esc_url($video_principal_url); ?>" data-post-id="<?php echo get_the_ID(); ?>">
                            ► Assistir
                        </button>
                    </div>
                <?php endif; ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                        <div class="post-meta-info">
                            <div class="meta-item post-categories"><strong>Gênero:</strong> <?php the_category(', '); ?></div>
                            <?php if( get_field('idiomas_disponiveis') ): ?><div class="meta-item"><strong>Idiomas:</strong> <?php the_field('idiomas_disponiveis'); ?></div><?php endif; ?>
                        </div>
                        <?php if( get_field('sinopse_curta') ): ?><div class="post-synopsis"><p><?php the_field('sinopse_curta'); ?></p></div><?php endif; ?>
                    </header>
                    <div class="entry-content"><?php the_content(); ?></div>
                    <footer class="entry-footer">
                        <div class="post-tags"><?php the_tags('<strong>Tags:</strong> ', ', ', ''); ?></div>
                    </footer>
                </article>
            </div>
            
            <?php
            // Seção de Posts Relacionados
            $categories = get_the_category($post->ID);
            if ($categories) {
                $category_ids = array();
                foreach($categories as $individual_category) $category_ids[] = $individual_category->term_id;
                $args = array(
                    'category__in' => $category_ids,
                    'post__not_in' => array($post->ID),
                    'posts_per_page' => 8,
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
                                    <div class="carousel-item" style="background-image: url('<?php echo get_the_post_thumbnail_url(null, 'medium_large'); ?>')"></div>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
            <?php
                }
                wp_reset_postdata();
            }
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