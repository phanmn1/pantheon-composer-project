(function($, Drupal, drupalSettings) {
    Drupal.behaviors.smoltPerSpawner = {
        attach: function(context, settings) {
            var urlPrefix = drupalSettings.baseUrl

            var location = $("#edit-location").val();

            const TABLE_ID = 2;

            const CALCULATED_YEAR = 2020;

            var editor = new $.fn.dataTable.Editor({
                ajax: {                   
                    edit: {
                        type: "PUT",
                        url: urlPrefix+"/api/RecapSummary/SmoltPerSpawner",
                        contentType: 'application/json',
                        data: function(data) {
                            var result = {};
                            var idSource; 
                            
                            $.each(data.data, function(key, value){
                                idSource = key;
                            })

                            data = data['data'][idSource]
                            console.log(data)
                            result.Year = idSource
                            result.Redds = data['Redds'];
                            result.Location = location
                            editor.field('Redds').hide()
                            return JSON.stringify(result);
                        }
                    }
                },
                table: '#smolt-per-spawner',
                idSrc: "Year",
                fields: [
                    { label: "Redds", name: "Redds"}
                ]
            })

            $('#smolt-per-spawner').on('click', 'tbody td.editable', function(e){
                editor.field('Redds').show()
                editor.inline(this) 
            })

            editor.field('Redds').hide();

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
                    { data: "Redds" , className: "editable"},
                    { data: "Spawner" },
                    { data: "SmoltPerRedd" },
                    { data: "SmoltPerSpawner" }
                ],
                createdRow: function(row, data, dataIndex,cells){
                    var Years = data.Year;

                    if(data.Year < CALCULATED_YEAR) {
                        $(cells[7]).removeClass("editable")
                        $(row).addClass("legacy-colors")
                    } else {
                        $(row).addClass("non-legacy")
                    }
                }                
            })

            var notesEditor = new $.fn.dataTable.Editor({
                ajax: {
                    create: {
                        type: "POST",
                        url: urlPrefix+"/api/ScrewTrap/Notes",
                        contentType: 'application/json',
                        data: function(data) {
                            
                            var note = { 
                                Notes: data.data[0].Notes,
                                TableType: TABLE_ID 
                            }

                            console.log(note)
                            
                            return JSON.stringify(note);
                        }
                    },
                    remove: {
                        type: "POST",
                        url: urlPrefix+"/api/ScrewTrap/DeleteNotes",
                        contentType: 'application/json',
                        data: function(data) {
                            
                            //var data = data.data; 
                            var ids = []
                            // data.forEach(element => {
                            //     ids.push(element.ID)
                            // });

                            $.each(data.data, function(key, value) {
                               ids.push(value.ID);                               
                            })

                            console.log(ids)

                            return JSON.stringify(ids) 
                        }
                    }
                    
                },
                table: '#notes-table',
                idSrc: "ID",
                fields: [
                    { label: "Notes", name: "Notes" },   
                ]
            })

            var notesTable = $('#notes-table').DataTable({
                dom: "Bfrtip",
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
                        defaultContent: '',
                        className: 'select-checkbox',
                        orderable: false,
                        width: "1%"                       
                    },
                    { data: "Notes", width: "97%"}
                    
                ], 
                select: {
                    style: 'os', 
                    selector: 'td:first-child'
                },
                buttons: [                    
                    { extend: "create", text: "Add Note" , editor: notesEditor },
                    { extend: "remove", editor: notesEditor}
                ]

                
            })

           
        }
    }
})(jQuery, Drupal, drupalSettings)