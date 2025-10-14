<?php
/**
 * O template para exibir páginas 404 (não encontrado).
 */

get_header();
?>

<main id="main" class="site-main">
    <div class="error-404-container">
        <div class="error-content text-center">
            <h1 class="error-title">404</h1>
            <h2 class="page-title"><?php _e( 'Página Não Encontrada', 'temagemini' ); ?></h2>
            <p><?php _e( 'Parece que o conteúdo que você está procurando se perdeu no espaço. Que tal tentar uma busca?', 'temagemini' ); ?></p>
            
            <?php
            // Exibe o formulário de busca padrão do WordPress
            get_search_form();
            ?>
        </div>
    </div>
</main>

<?php
get_footer();
?>