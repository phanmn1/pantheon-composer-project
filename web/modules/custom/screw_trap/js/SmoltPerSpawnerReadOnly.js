(function($, Drupal, drupalSettings) {
    Drupal.behaviors.smoltPerSpawnerReadOnly = {
        attach: function(context, settings) {
            var urlPrefix = drupalSettings.baseUrl

            var location = $("#edit-location").val();

            const TABLE_ID = 2;

            var table = $('#smolt-per-spawner').DataTable({
                ajax: {
                    url: urlPrefix+"/api/RecapSummary/SmoltPerSpawner?Location="+location,
                    dataSrc: ''
                },
                ordering: false,
                pageLength: 50, 
                searching: false,
                columns: [
                    { data: "Year" },
                    { data: "SmoltNumberAtTrap" },
                    { data: "Age1" },
                    { data: "Age2" },
                    { data: "Age3" },
                    { data: "YearClass" },
                    { data: "JvPerYearClass" },
                    { data: "Redds"},
                    { data: "Spawner" },
                    { data: "SmoltPerRedd" },
                    { data: "SmoltPerSpawner" }
                ],                
            })

            var notesTable = $('#notes-table').DataTable({
                ajax: {
                    url: urlPrefix+"/api/ScrewTrap/Notes?TABLE_ID="+TABLE_ID,
                    dataSrc: ''
                }, 
                lengthChange: false,
                ordering: false,
                searching: false,
                columns: [  
                    //{ data: "ID" },
                    //{ data: "TableType"},
                    { 
                        data: null,
                        defaultContent: '-',
                        className: 'numbered-item',
                        orderable: false,
                        width: "1%"                       
                    },
                    { data: "Notes", width: "97%"}
                    
                ], 
            })
        }
    }
})(jQuery, Drupal, drupalSettings)