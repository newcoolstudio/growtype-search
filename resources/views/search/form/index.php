<div id="<?php echo $parent_id ?>" class="growtype-search-wrapper growtype-search-<?php echo $search_type ?>">
    <div class="growtype-search-inner">
        <?php if (!empty(get_theme_mod('growtype_search_intro_text'))) { ?>
            <p class="e-label"><?php echo get_theme_mod('growtype_search_intro_text') ?></p>
        <?php } ?>

        <form class="growtype-search-form" role="search" method="get" action="<?php echo growtype_search_permalink() ?>" data-post-types-included="<?php echo $post_types_included ?>">
            <input type="text" value="" name="s" class="growtype-search-input" placeholder="<?php echo $search_input_placeholder ?>"/>
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

        <?php echo growtype_search_include_view('search.form.actions.index') ?>
    </div>
</div>
