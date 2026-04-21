<?php
// Redirect to front page if accessed directly.
// WordPress uses front-page.php for the static front page automatically.
if ( is_front_page() ) {
    get_template_part( 'front-page' );
    return;
}

get_header();
?>

<main style="min-height:100vh; padding:8rem 2rem 4rem; max-width:1200px; margin:0 auto; overflow-y:auto;" class="overflow-y-auto">
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        <article>
            <h2 class="title-text" style="font-size:2rem; margin-bottom:1rem;">
                <a href="<?php the_permalink(); ?>" style="text-decoration:none; color:#2c221a;"><?php the_title(); ?></a>
            </h2>
            <div style="color:#68635f; font-size:13px; margin-bottom:2rem;"><?php the_excerpt(); ?></div>
        </article>
    <?php endwhile; endif; ?>
</main>

<?php get_footer(); ?>
