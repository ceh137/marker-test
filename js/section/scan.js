var scan = {

    // COMMON

    paginator: function(offset) {
        // vars
        var query = gv('scans_search');
        // vars (call)
        var data = {offset: offset, query: query};
        var location = {dpt: 'scan', sub: 'common', act: 'paginator'};
        // call
        request({location: location, data: data}, function(result) {
            // common
            html('scans_table', result.info);
            html('scans_paginator', result.paginator);
            // url
            var url = '/scans';
            var p = [];
            if (query) p.push('q=' + query);
            if (offset) p.push('offset=' + offset);
            if (p.length > 0) url += '?' + p.join('&');
            window.history.pushState('', '', url);
        });
    },

    show_product: function(product_id) {
        document.location.href = '/products/' + product_id;
    },

}
