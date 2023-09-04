

(function ($) {
    'use strict';
  $(document).ready(function() {


  
    var myTable = $("#moowoodle_table").DataTable({
      paging: false,
      searching: true,
      info: false,
    });

    //remove all default filteration
    $('.dataTables_filter').remove();
    //filter with seclect
    myTable
      .columns()
      .flatten()
      .each(function (colID) {
        //get the title of the column header
        var title = myTable.column( colID ).header();
        //Manage column is not filterable
        if(table_args.non_filterable_column.some(v => ($(title).html().trim()).includes(v))) return;
        // Create the select list in the
        // header column header
        // On change of the list values,
        // perform search operation
        var mySelectList = $("<select />")
          .prependTo($(".moowoodle-table-fuilter"))
          .on("change", function () {
            myTable.column(colID).search($(this).val());

            // update the changes using draw() method
            myTable.column(colID).draw();
          });

        // Get the search cached data for the
        // first column add to the select list
        // using append() method
        // sort the data to display to user
        // all steps are done for EACH column
        mySelectList.append(
          $('<option value="">' + $(title).html().trim() + '</option>')
        );
        myTable
          .column(colID)
          .cache("search")
          .unique()
          .sort()
          .each(function (param) {
            mySelectList.append(
              $('<option value="' + param + '">'
                + param + "</option>")
            );
          });
      });
    // set custom search section
    $('<div class="mw-header-search-section"><label class="moowoodle-course-search"><i class="dashicons dashicons-search"></i></label><input type="search" class="moowoodle-search-input" placeholder="Search Course" aria-controls="moowoodle_table"></div>')
    .appendTo($('.mw-header-search-wrap'));
    // fuilter by custom search
    $('.moowoodle-search-input').keyup(function(){
      myTable.column( 0 ).search($(this).val()).draw() ;
    })
  });
})(jQuery);