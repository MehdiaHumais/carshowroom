<?php get_header(); ?>
<<<<<<< HEAD
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
=======
<div class="car-archive">
    <h1>All Cars</h1>
    <div class="car-grid">
        <?php
        if (have_posts()) :
            while (have_posts()) : the_post();
                $price = get_post_meta(get_the_ID(), 'car_price', true);
                ?>
                <div class="car-card">
                    <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail('medium'); ?>
                        <h2><?php the_title(); ?></h2>
                        <p>Price: <?php echo esc_html($price); ?></p>
                        <button>View Details</button>
                    </a>
                </div>
            <?php endwhile;
        else :
            echo '<p>No cars found.</p>';
        endif;
        ?>
>>>>>>> 8a47fa6 (push of car showroom error)
    </div>
</div>
<?php get_footer(); ?>
