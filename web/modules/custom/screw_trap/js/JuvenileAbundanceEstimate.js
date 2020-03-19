(function($, Drupal, drupalSettings) {
    Drupal.behaviors.jvAbundanceEstimate = {
        attach: function(context, settings) {
            var urlPrefix = drupalSettings.baseUrl 

            const TABLE_ID = 1;
            const CALCULATED_YEAR = 2020

            var location = $("#edit-location").val();

            var editor = new $.fn.dataTable.Editor({
                ajax: {
                    edit: {
                        type: "PUT",
                        url: urlPrefix+"/api/RecapSummary/UpdateCapturedAdjusted",
                        contentType: 'application/json',
                        data: function(data) {
                            

                            var idSource; 
                            var _NumberCapturedAdjusted;
                            var _OutmigrantAbundanceEstimate; 
                            var _EstimatedStandardError;
                            var exportData; 

                            $.each(data.data, function(key, value){
                                idSource = key; 
                                _NumberCapturedAdjusted = value.NumberCapturedAdjusted
                                _OutmigrantAbundanceEstimate = value.OutmigrantAbundanceEstimate
                                _EstimatedStandardError = value.EstimatedStandardError
                            })

                            var splitYear = idSource.split(" - ") 
                            

                            var exportData = {
                                Year: splitYear[1], 
                                NumberCapturedAdjusted: _NumberCapturedAdjusted,
                                OutmigrantAbundanceEstimate: _OutmigrantAbundanceEstimate,
                                EstimatedStandardError: _EstimatedStandardError
                            }

                            console.log(JSON.stringify(exportData))
                            

                            return JSON.stringify(exportData)
                        }
                    },
                    
                },
                table: '#jv-abundance-table',
                    idSrc: "Year",
                    fields: [
                        { label: "Year", name: "Year" },   
                        { label: "Number Captured", name: "NumberCaptured" },                  
                        { label: "Number Captured Adjusted", name: "NumberCapturedAdjusted" },
                        { label: "Number Pit Tagged", name: "NumberPitTagged" },
                        { label: "Season (Days)", name: "SeasonLength" },
                        { label: "Days Not Operating", name: "DaysNotOperating" },
                        { label: "Percent of Season Not Operated", name: "PercentOfSeasonNotOperated" },
                        { label: "Number Released" , name: "NumberReleased" },
                        { label: "Number Recaptured", name: "NumberRecaptured" },
                        { label: "Pooled Efficiency", name: "PooledEfficiency" },
                        { label: "Outmigrant Abundance Estimate", name: "OutmigrantAbundanceEstimate"},
                        { label: "Estimated Standard Error", name: "EstimatedStandardError"},
                        { label: "CV", name: "CV"},
                        { label: "CI", name: "CI"},
                        { label: "Production", name: "Production" }
                    ]
            })

            $('#jv-abundance-table').on('click', 'tbody td.editable', function(e){
                editor.inline( this )
            });

            var table = $('#jv-abundance-table').DataTable({
                //dom: "Bfrtip",
                ajax: {
                    url: urlPrefix+'/api/RecapSummary/JvAbundanceEstimate?location='+location,
                    dataSrc: '',
                },
                ordering: false,
                pageLength: 50,
                searching: false,
                columns: [  
                    { data: "Year" },
                    { data: "NumberCaptured" },
                    { data: "NumberCapturedAdjusted", className: "editable" },
                    { data: "NumberPitTagged" },
                    { data: "SeasonLength" },
                    { data: "DaysNotOperating" },
                    { data: "PercentOfSeasonNotOperated" },
                    { data: "NumberReleased" },
                    { data: "NumberRecaptured" },
                    { data: "PooledEfficiency" },
                    { data: "PooledEstimate" },
                    { data: "OutmigrantAbundanceEstimate", className: "editable"},
                    { data: "EstimatedStandardError", className: "editable"},
                    { data: "CV"},
                    { data: "CI"},
                    { data: "Production" }
                   
                ],
                createdRow: function( row, data, dataIndex, cells) {
                    var Years = data.Year.split(" - ")
                    var CurrentYear = parseInt(Years[1], 10)
                    
                    if(CurrentYear < CALCULATED_YEAR) {
                        $(cells[2]).removeClass("editable")
                        $(cells[11]).removeClass("editable")
                        $(cells[12]).removeClass("editable")
                        $(row).addClass("legacy-colors")                        
                    } else {
                        $(row).addClass("non-legacy")
                    }            
                }
                // select: {
                //     style:    'os',
                //     selector: 'td:first-child'
                // },
            })

            editor.on('preSubmit', function(e, o, action){
                if(action === 'edit') {
                    var year = this.field('Year')
                    console.log(year)
                    if(year.val() < 2020){
                        alert("Cannot Edit Values Before 2015")
                    }

                    if(this.inError()) {
                        return false;
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