function ajax() {
    let searchOnEmptyInput = false;

    Object.entries(window.growtype_search).map(function (element) {
        let searchId = element[0];
        let searchForm = jQuery('#' + searchId + ' .growtype-search-form');
        let searchOnLoad = element[1]['static']['search_on_load'] === 'true' ? true : false;
        let searchOnType = element[1]['static']['search_on_type'] === 'true' ? true : false;

        let is_clicked = true;
        searchForm.find('.btn-growtype-search-submit').on('click', function (event) {
            let ajaxEnabled = jQuery(this).closest('.growtype-search-wrapper[data-ajax="true"]');
            ajaxEnabled = ajaxEnabled && ajaxEnabled.length > 0;

            let searchOnEmpty = element[1]['static']['search_on_empty'] === 'true' ? true : false;
            let searchInput = searchForm.find('.growtype-search-input');

            if (!searchOnEmpty && searchInput.val().length === 0) {
                event.preventDefault();
                searchInput.addClass('is-error');
            }

            if (ajaxEnabled) {
                event.preventDefault();
                if (is_clicked == true) {
                    is_clicked = false;
                    setTimeout(function () {
                        is_clicked = true;
                    }, 2500);

                    if (searchOnEmpty) {
                        searchOnEmptyInput = true;
                    }

                    ajax_search(searchForm, element[1]);
                }
            }
        });

        if (searchOnLoad) {
            searchOnEmptyInput = true;
            ajax_search(searchForm, element[1]);
        }

        if (searchOnType) {
            let inputDelayTimer;
            $('.growtype-search-form .growtype-search-input').on('input', function () {
                clearTimeout(inputDelayTimer);
                inputDelayTimer = setTimeout(function () {
                    ajax_search(searchForm, element[1]);
                }, 500); // Set the delay time in milliseconds (e.g., 500ms)
            });
        }

        showMoreResults(searchForm);
    });

    function ajax_search(form, settings = null) {
        let searchInput = form.find('.growtype-search-value');

        let searchValues = {};
        let isValid = true;
        searchInput.map(function (index, element) {
            let searchInputVal = $(element).val();

            let hasError = false;
            if (searchOnEmptyInput) {
                hasError = false;
            } else if (searchInputVal.length === 0) {
                hasError = true;
            }

            if (hasError) {
                $(element).addClass('is-error');
                isValid = false;
                return;
            }

            $(element).removeClass('is-error');

            searchValues[$(element).attr('name')] = searchInputVal;
        });

        if (!isValid) {
            return;
        }

        form.closest('.growtype-search-wrapper').removeClass('is-loading');
        form.closest('.growtype-search-wrapper').find('.growtype-search-results').fadeOut();
        form.closest('.growtype-search-wrapper').find('.growtype-search-results-actions').fadeOut();

        /**
         * Ga send search value
         */
        if (typeof gtag !== 'undefined') {
            gtag('event', 'search', {'event_category': 'search', 'event_label': searchValues});
        }

        $.ajax({
            type: "post", context: this, dataType: "json", url: growtype_search_ajax.url, data: {
                action: growtype_search_ajax.action,
                nonce: growtype_search_ajax.nonce,
                search: searchValues,
                settings_static: settings !== null ? settings['static'] : null,
            }, beforeSend() {
                jQuery('.growtype-search-wrapper').addClass('is-loading');
            }, success: function (response) {
                searchOnEmptyInput = false;

                form.closest('.growtype-search-wrapper').removeClass('is-loading');
                form.closest('.growtype-search-wrapper').removeClass('is-loading');
                form.closest('.growtype-search-wrapper').find('.growtype-search-results').fadeIn();

                let html = jQuery(response['html'])

                let showActions = false;
                if (settings['static']['visible_results_amount']) {
                    let counter = 0;
                    html.map(function (index, element) {
                        if (element.innerHTML !== undefined) {
                            if (counter >= settings['static']['visible_results_amount']) {
                                jQuery(element).addClass('initialy-is-hidden').hide();
                                showActions = true;
                            }

                            counter++;
                        }
                    });
                }

                if (showActions) {
                    form.closest('.growtype-search-wrapper').find('.growtype-search-results-actions').fadeIn();
                }

                form.closest('.growtype-search-wrapper').find('.growtype-search-results .growtype-search-results-inner').html(html);

                closeSearchResults();
            }
        });
    }

    function closeSearchResults() {
        $('.growtype-search-results .btn-close').click(function () {
            $(this).closest('.growtype-search-results').fadeOut();
        })
    }

    /**
     *
     */
    function showMoreResults(form) {
        form.closest('.growtype-search-wrapper').find('.growtype-search-results-actions .growtype-search-results-btn').click(function () {
            if (jQuery(this).hasClass('is-active')) {
                jQuery(this).removeClass('is-active')
                jQuery(this).text(jQuery(this).attr('data-show-more'));
                jQuery(this).closest('.growtype-search-wrapper').find('.growtype-search-results .initialy-is-hidden').fadeOut();
            } else {
                jQuery(this).addClass('is-active')
                jQuery(this).text(jQuery(this).attr('data-show-less'));
                jQuery(this).closest('.growtype-search-wrapper').find('.growtype-search-results .initialy-is-hidden').fadeIn();
            }
        });
    }
}

export {ajax};
