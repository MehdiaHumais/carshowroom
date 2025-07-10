<?php get_header(); ?>
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">All Cars</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <?php if (have_posts()): while (have_posts()): the_post(); ?>
            <div class="bg-white shadow rounded p-4">
                <?php if (has_post_thumbnail()): ?>
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium'); ?></a>
                <?php endif; ?>
                <h2 class="text-xl font-semibold mt-2"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            </div>
        <?php endwhile; else: ?>
            <p>No cars found.</p>
        <?php endif; ?>
    </div>
</div>
<?php get_footer(); ?>
