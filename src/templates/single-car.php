<?php get_header(); ?>

<div class="car-single">
    <?php while (have_posts()) : the_post(); ?>
        <h1><?php the_title(); ?></h1>
        <div><?php the_post_thumbnail('large'); ?></div>
        <p><strong>Price:</strong> <?php echo get_post_meta(get_the_ID(), 'price', true); ?></p>
        <p><strong>Mileage:</strong> <?php echo get_post_meta(get_the_ID(), 'mileage', true); ?></p>
        <p><strong>Condition:</strong> <?php echo get_post_meta(get_the_ID(), 'condition', true); ?></p>
        <p><strong>Owner:</strong> <?php echo get_post_meta(get_the_ID(), 'owner_name', true); ?> (<?php echo get_post_meta(get_the_ID(), 'owner_phone', true); ?>)</p>
        <p><strong>Posted:</strong> <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?></p>

        <a href="?book_car_id=<?php echo get_the_ID(); ?>" class="button">Book This Car</a>
    <?php endwhile; ?>
</div>

<?php get_footer(); ?>
