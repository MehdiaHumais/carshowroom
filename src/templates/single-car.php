<?php

use Carbon\Carbon;

get_header();

// Get car fields
$mileage = get_post_meta(get_the_ID(), 'mileage', true);
$condition = get_post_meta(get_the_ID(), 'condition', true);
$price = get_post_meta(get_the_ID(), 'price', true);
$demand_price = get_post_meta(get_the_ID(), 'demand_price', true);
$owner_name = get_post_meta(get_the_ID(), 'owner_name', true);
$owner_phone = get_post_meta(get_the_ID(), 'owner_phone', true);

// Get how long ago the car was added
$posted_time = get_the_date('Y-m-d H:i:s');
$time_ago = Carbon::parse($posted_time)->diffForHumans();
?>

<div class="container mx-auto px-4 py-10">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-lg overflow-hidden">
            <?php if (has_post_thumbnail()) : ?>
                <img class="w-full h-64 object-cover" src="<?php the_post_thumbnail_url('large'); ?>" alt="<?php the_title(); ?>">
            <?php endif; ?>

            <div class="p-6 space-y-4">
                <h1 class="text-3xl font-bold"><?php the_title(); ?></h1>

                <div class="text-gray-600 text-sm">Posted: <?= esc_html($time_ago); ?></div>

                <div class="space-y-1 text-lg">
                    <p><strong>Mileage:</strong> <?= esc_html($mileage); ?></p>
                    <p><strong>Condition:</strong> <?= esc_html($condition); ?></p>
                    <p><strong>Price:</strong> Rs. <?= esc_html($price); ?></p>
                    <p><strong>Demand Price:</strong> Rs. <?= esc_html($demand_price); ?></p>
                    <p><strong>Owner Name:</strong> <?= esc_html($owner_name); ?></p>
                    <p><strong>Owner Phone:</strong> <?= esc_html($owner_phone); ?></p>
                </div>
            </div>
        </div>
    <?php endwhile; endif; ?>
</div>

<?php get_footer(); ?>
