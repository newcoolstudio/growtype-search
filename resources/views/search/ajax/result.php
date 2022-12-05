<div class="growtype-search-result">
    <a href="<?php echo get_permalink($post) ?>" class="growtype-search-results-content-inner">
        <?php if (!empty(get_the_post_thumbnail_url($post))) { ?>
            <div class="growtype-search-result-img">
                <div class="img" style="background: url('<?php echo get_the_post_thumbnail_url($post) ?>');background-size:cover;background-position: center;"></div>
            </div>
        <?php } ?>
        <div class="growtype-search-result-content">
            <div class="title"><?php echo $post->post_title ?></div>
            <?php if (!empty($post->post_content)) { ?>
                <div class="content"><?php echo growtype_search_get_limited_content($post->post_content) ?></div>
            <?php } ?>
        </div>
    </a>
</div>
