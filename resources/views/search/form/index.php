<div id="<?php echo $parent_id ?>" class="growtype-search-wrapper growtype-search-<?php echo $search_type ?>">
    <div class="growtype-search-inner">
        <?php if (!empty(get_theme_mod('growtype_search_intro_text'))) { ?>
            <p class="e-label"><?php echo get_theme_mod('growtype_search_intro_text') ?></p>
        <?php } ?>

        <form class="growtype-search-form" role="search" method="get" action="<?php echo growtype_search_permalink() ?>" data-post-types-included="<?php echo $post_types_included ?>">
            <input type="text" value="" name="s" class="growtype-search-input" placeholder="Search..."/>
            <button class="btn-growtype-search-submit" type="submit" value="Search">
                <?php echo growtype_search_render_svg('images/search.svg') ?>
                <?php echo growtype_search_render_svg('images/loader.svg') ?>
            </button>
        </form>

        <?php if ($search_type === 'fixed') { ?>
            <div class="btn-growtype-search-close">
                <?php echo growtype_search_render_svg('images/close.svg') ?>
            </div>
        <?php } ?>

        <div class="growtype-search-results" style="display: none;"></div>

        <div class="growtype-search-results-actions">
            <button class="btn-more" data-show-more="<?php echo __('Show more', 'growtype-search') ?>" data-show-less="<?php echo __('Show less', 'growtype-search') ?>"><?php echo __('Show more', 'growtype-search') ?></button>
        </div>
    </div>
</div>
