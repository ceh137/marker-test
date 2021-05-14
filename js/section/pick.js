var pick = {

    expand: function(pick_id) {
        if (has_class('pick_details_' + pick_id, 'active')) {
            remove_class('pick_preview_' + pick_id, 'active');
            remove_class('pick_details_' + pick_id, 'active');
            set_style('pick_details_' + pick_id, 'height', 0);
        } else {
            add_class('pick_preview_' + pick_id, 'active');
            add_class('pick_details_' + pick_id, 'active');
            var height = ge('pick_details_wrap_' + pick_id).offsetHeight + 12;
            set_style('pick_details_' + pick_id, 'height', height);
        }
    },

}
