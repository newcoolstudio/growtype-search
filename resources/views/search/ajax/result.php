<div class="growtype-search-result">
    <a href="<?php echo get_permalink($post) ?>" class="growtype-search-results-content-inner" onclick="typeof gtag !== 'undefined' ? gtag('event', 'click', {'event_category' : 'search_result','event_label' : '<?php echo get_the_title($post) ?>'}) : ''">
        <?php if (!empty(get_the_post_thumbnail_url($post))) { ?>
            <div class="growtype-search-result-img">
                <div class="img" style="background: url('<?php echo get_the_post_thumbnail_url($post, 'thumbnail') ?>');background-size:cover;background-position: center;"></div>
            </div>
        <?php } ?>
        <div class="growtype-search-result-content">
            <?php echo growtype_search_result_content($post) ?>
        </div>
    </a>
</div>
