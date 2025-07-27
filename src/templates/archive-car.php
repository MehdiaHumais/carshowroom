<?php get_header(); ?>
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
    </div>
</div>
<?php get_footer(); ?>
