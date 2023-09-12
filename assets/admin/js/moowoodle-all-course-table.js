(function($) {
    'use strict';
    $(document).ready(function() {

       var columnsData = [];

        // Iterate through the 'from_heading' array
        for (var i = 0; i < table_args.from_heading.length; i++) {
            var element = table_args.from_heading[i];
            
            // Extract text content from the HTML element using a regex pattern
            var textContent = element.replace(/<[^>]*>/g, '').trim();

            // Check if the text content is present in 'non_sortable_column'
            if (table_args.non_sortable_column.includes(textContent)) {
                columnsData.push({ 'sortable': false });
            } else {
                columnsData.push({ 'sortable': true });
            }
        }
        var myTable = $("#moowoodle_table").DataTable({
            paging: false,
            searching: true,
            info: false,
            "columns":columnsData
        });
        //remove all default filteration
        $('.dataTables_filter').remove();
        //filter with seclect
        myTable.columns().flatten().each(function(colID) {
            //get the title of the column header
            var title = myTable.column(colID).header();
            //Manage column is not filterable
            if (table_args.non_filterable_column.some(v => ($(title).html().trim()).includes(v))) return;
            // Create the select list in the
            // header column header
            // On change of the list values,
            // perform search operation
            var mySelectList = $("<select />").appendTo($(".moowoodle-table-fuilter")).on("change", function() {
                myTable.column(colID).search($(this).val());
                // update the changes using draw() method
                myTable.column(colID).draw();
            });
            // Get the search cached data for the
            // first column add to the select list
            // using append() method
            // sort the data to display to user
            // all steps are done for EACH column
            mySelectList.append($('<option value="">' + $(title).html().trim() + '</option>'));
            myTable.column(colID).cache("search").unique().sort().each(function(param) {
                mySelectList.append($('<option value="' + param + '">' + param + "</option>"));
            });
        });
        // set custom search section
        $('<div class="mw-filter-bulk"><label for="bulk-action-selector-top" class="screen-reader-text">' + table_args.lang.select_bulk_action + '</label><select name="action" id="bulk-action-selector-top"><option value="-1">' + table_args.lang.bulk_actions + '</option><option value="sync_courses">' + table_args.lang.sync_course + '</option><option value="sync_create_product">' + table_args.lang.create_product + '</option><option value="sync_update_product">' + table_args.lang.update_product + '</option></select><button class="button-secondary bulk-action-select-apply" name="bulk-action-apply" type="button">' + table_args.lang.apply + '</button></div><div class="mw-header-search-section"><label class="moowoodle-course-search"><i class="dashicons dashicons-search"></i></label><input type="search" class="moowoodle-search-input" placeholder="' + table_args.lang.Search_Course + '" aria-controls="moowoodle_table"></div>').appendTo($('.search-bulk-action'));
        // fuilter by custom search
        $('.moowoodle-search-input').keyup(function() {
            myTable.column(0).search($(this).val()).draw();
        })
        //bulk action seclect all
        const selectAllCheckbox = document.querySelector(".bulk-action-select-all");
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener("change", function () {
                const checkedEnabledCheckboxes = document.querySelectorAll(".bulk-action-checkbox:checked:enabled");
                const uncheckedCheckboxes = document.querySelectorAll(".bulk-action-checkbox:not(:checked):enabled");
                if (checkedEnabledCheckboxes.length >= uncheckedCheckboxes.length) {
                    checkedEnabledCheckboxes.forEach(function(checkbox) {
                        if (!checkbox.disabled) checkbox.checked = false;
                    });
                } else {
                    uncheckedCheckboxes.forEach(function(checkbox) {
                        if (!checkbox.disabled) checkbox.checked = true;
                    });
                }
            });
        }
    });
})(jQuery);