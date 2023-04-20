<div id="<?php echo $parent_id ?>" class="growtype-search-wrapper growtype-search-<?php echo $search_type ?>">
    <div class="growtype-search-inner">
        <?php if (!empty(get_theme_mod('growtype_search_intro_text'))) { ?>
            <p class="e-title-intro"><?php echo get_theme_mod('growtype_search_intro_text') ?></p>
        <?php } ?>

        <form class="growtype-search-form" role="search" method="get" action="<?php echo growtype_search_permalink() ?>">
            <input type="text" value="" name="s" class="growtype-search-input" placeholder="<?php echo $search_input_placeholder ?>"/>
            <button class="btn-growtype-search-submit" type="submit" value="Search">
                <span class="search-icon"><svg fill="#000000" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 72 72" width="64px" height="64px">
    <path d="M 31 11 C 19.973 11 11 19.973 11 31 C 11 42.027 19.973 51 31 51 C 34.974166 51 38.672385 49.821569 41.789062 47.814453 L 54.726562 60.751953 C 56.390563 62.415953 59.088953 62.415953 60.751953 60.751953 C 62.415953 59.087953 62.415953 56.390563 60.751953 54.726562 L 47.814453 41.789062 C 49.821569 38.672385 51 34.974166 51 31 C 51 19.973 42.027 11 31 11 z M 31 19 C 37.616 19 43 24.384 43 31 C 43 37.616 37.616 43 31 43 C 24.384 43 19 37.616 19 31 C 19 24.384 24.384 19 31 19 z"/>
</svg>
</span>
                <span class="icon-loading"><!-- By Sam Herbert (@sherb), for everyone. More @ http://goo.gl/7AJzbL -->
<svg width="45" height="45" viewBox="0 0 45 45" xmlns="http://www.w3.org/2000/svg" stroke="#fff">
    <g fill="none" fill-rule="evenodd" transform="translate(1 1)" stroke-width="2">
        <circle cx="22" cy="22" r="6" stroke-opacity="0">
            <animate attributeName="r"
                     begin="1.5s" dur="3s"
                     values="6;22"
                     calcMode="linear"
                     repeatCount="indefinite"/>
            <animate attributeName="stroke-opacity"
                     begin="1.5s" dur="3s"
                     values="1;0" calcMode="linear"
                     repeatCount="indefinite"/>
            <animate attributeName="stroke-width"
                     begin="1.5s" dur="3s"
                     values="2;0" calcMode="linear"
                     repeatCount="indefinite"/>
        </circle>
        <circle cx="22" cy="22" r="6" stroke-opacity="0">
            <animate attributeName="r"
                     begin="3s" dur="3s"
                     values="6;22"
                     calcMode="linear"
                     repeatCount="indefinite"/>
            <animate attributeName="stroke-opacity"
                     begin="3s" dur="3s"
                     values="1;0" calcMode="linear"
                     repeatCount="indefinite"/>
            <animate attributeName="stroke-width"
                     begin="3s" dur="3s"
                     values="2;0" calcMode="linear"
                     repeatCount="indefinite"/>
        </circle>
        <circle cx="22" cy="22" r="8">
            <animate attributeName="r"
                     begin="0s" dur="1.5s"
                     values="6;1;2;3;4;5;6"
                     calcMode="linear"
                     repeatCount="indefinite"/>
        </circle>
    </g>
</svg>
</span>
                <span class="e-label"><?php echo __('Search', 'growtype-search') ?></span>
            </button>
        </form>

        <?php if ($search_type === 'fixed') { ?>
            <div class="btn-growtype-search-close">
                <svg fill="#000000" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" width="30px" height="30px">
                    <path d="M 7 4 C 6.744125 4 6.4879687 4.0974687 6.2929688 4.2929688 L 4.2929688 6.2929688 C 3.9019687 6.6839688 3.9019687 7.3170313 4.2929688 7.7070312 L 11.585938 15 L 4.2929688 22.292969 C 3.9019687 22.683969 3.9019687 23.317031 4.2929688 23.707031 L 6.2929688 25.707031 C 6.6839688 26.098031 7.3170313 26.098031 7.7070312 25.707031 L 15 18.414062 L 22.292969 25.707031 C 22.682969 26.098031 23.317031 26.098031 23.707031 25.707031 L 25.707031 23.707031 C 26.098031 23.316031 26.098031 22.682969 25.707031 22.292969 L 18.414062 15 L 25.707031 7.7070312 C 26.098031 7.3170312 26.098031 6.6829688 25.707031 6.2929688 L 23.707031 4.2929688 C 23.316031 3.9019687 22.682969 3.9019687 22.292969 4.2929688 L 15 11.585938 L 7.7070312 4.2929688 C 7.5115312 4.0974687 7.255875 4 7 4 z"/>
                </svg>
            </div>
        <?php } ?>

        <div class="growtype-search-results" style="display: none;"></div>

        <?php echo growtype_search_include_view('search.form.actions.index') ?>
    </div>
</div>
