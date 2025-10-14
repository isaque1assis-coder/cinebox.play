<?php
/**
 * O template para exibir o rodapé.
 */
?>

        </div><footer id="colophon" class="site-footer">
            <div id="search-overlay" class="search-overlay">
                <button id="close-search-btn" class="close-search-btn" aria-label="Fechar busca">&times;</button>
                <div class="search-overlay-content">
                    <form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                        <label>
                            <span class="screen-reader-text">Pesquisar por:</span>
                            <input type="search" id="ajax-search-input" class="search-field" placeholder="Títulos, gente, gêneros" value="" name="s" />
                        </label>
                    </form>
                    <div id="ajax-search-results" class="ajax-search-results">
                        </div>
                </div>
            </div>

            <div class="footer-container">
                <?php if ( is_active_sidebar( 'footer-widgets' ) ) : ?>
                    <div class="footer-widgets">
                        <?php dynamic_sidebar( 'footer-widgets' ); ?>
                    </div>
                <?php endif; ?>

                <div class="site-info">
                    &copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>. Todos os direitos reservados.
                </div></div></footer></div><?php wp_footer();  ?>
</body>
</html>