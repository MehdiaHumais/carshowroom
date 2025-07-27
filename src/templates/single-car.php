<?php get_header(); ?>
<div class="single-car">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <h1><?php the_title(); ?></h1>
        <?php the_post_thumbnail('large'); ?>
        <ul>
            <li>Mileage: <?php echo get_post_meta(get_the_ID(), 'car_mileage', true); ?></li>
            <li>Condition: <?php echo get_post_meta(get_the_ID(), 'car_condition', true); ?></li>
            <li>Price: <?php echo get_post_meta(get_the_ID(), 'car_price', true); ?></li>
            <li>Demand Price: <?php echo get_post_meta(get_the_ID(), 'car_demand_price', true); ?></li>
            <li>Owner: <?php echo get_post_meta(get_the_ID(), 'car_owner_name', true); ?></li>
            <li>Phone: <?php echo get_post_meta(get_the_ID(), 'car_owner_phone', true); ?></li>
        </ul>

        <!-- Booking Button -->
        <button class="book-this-car-btn" data-car-id="<?php echo get_the_ID(); ?>">
            Book This Car
        </button>

        <?php the_content(); ?>
    <?php endwhile; endif; ?>
</div>
<?php get_footer(); ?>
