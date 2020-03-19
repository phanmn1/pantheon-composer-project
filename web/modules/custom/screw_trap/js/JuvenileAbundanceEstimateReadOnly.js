(function($, Drupal, drupalSettings) {
    Drupal.behaviors.jvAbundanceEstimate = {
        attach: function(context, settings) {
            var urlPrefix = drupalSettings.baseUrl 

            const TABLE_ID = 1;
            var location = $("#edit-location").val();

            var table = $('#jv-abundance-table').DataTable({
                //dom: "Bfrtip",
                ajax: {
                    url: urlPrefix+'/api/RecapSummary/JvAbundanceEstimate?Location='+location,
                    dataSrc: '',
                },
                ordering: false,
                pageLength: 50,
                searching: false,
                columns: [  
                    { data: "Year" },
                    { data: "NumberCaptured" },
                    { data: "NumberCapturedAdjusted"},
                    { data: "NumberPitTagged" },
                    { data: "SeasonLength" },
                    { data: "DaysNotOperating" },
                    { data: "PercentOfSeasonNotOperated" },
                    { data: "NumberReleased" },
                    { data: "NumberRecaptured" },
                    { data: "PooledEfficiency" },
                    { data: "PooledEstimate" },
                    { data: "OutmigrantAbundanceEstimate"},
                    { data: "EstimatedStandardError"},
                    { data: "CV"},
                    { data: "CI"},
                    { data: "Production" }
                   
                ],
               
            })

            
            
            // $.ajax({
            //     url: urlPrefix+'/api/ScrewTrap/Notes?TABLE_ID='+TABLE_ID,
            //     type: 'GET',
            //     success: function(data){
            //         console.log(data)
            //     }
            // })

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