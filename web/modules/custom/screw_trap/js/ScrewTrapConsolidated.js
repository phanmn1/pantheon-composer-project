(function($, Drupal, drupalSettings) {
    Drupal.behaviors.screwTrapConsolidated = {
      attach: function (context, settings) {
        var urlPrefix = drupalSettings.baseUrl 
        var gisID = drupalSettings.arcgisid

        var location = $("#edit-locations").val();
        var from = $("#screwtrap-from").val(); 
        var to = $("#screwtrap-to").val(); 
        var order = $("#edit-order").val();

        $('#edit-reset').on('click', function(){
        $('#edit-locations').val("Upper Toppenish"); 
        $('#screwtrap-from').val(new Date(new Date()
                  .setDate(new Date()
                  .getDate() - 30))
                  .toLocaleDateString("en-US", {month: '2-digit', day: '2-digit', year: 'numeric'}))
                            
        $('#screwtrap-to').val(new Date(Date.now()).toLocaleDateString("en-US", {month: '2-digit', day: '2-digit', year: 'numeric'}))
      })

        /* Formatting function for row details - modify as you need */
        function format ( d, BulkCountSteelhead ) {
          // `d` is the original data object for the row
          var tableData = ''; 
          d.forEach(function(item){

            Object.keys(item).forEach(function(i){
              if(item[i] == null)
                item[i] = ''
            })

            tableData += '<tr>'+
                            '<td>'+item.Species+'</td>'+
                            '<td>'+item.Length+'</td>'+
                            '<td>'+item.Weight+'</td>'+
                            '<td>'+item.Smolted+'</td>'+
                            '<td>'+item.Recap+'</td>'+
                            '<td>'+item.Mortality+'</td>'+
                            '<td>'+item.PITTagNo+'</td>'+
                            '<td>'+item.IndividualFishComments+'</td>'+
                            '<td>'+item.DNAVialNo+'</td>'+
                            '<td>'+item.ScaleCardNo+'</td>'+
                            '<td>'+item.ConditionFactor+'</td>'+
                          '</tr>'
          })

          var BulkHTMLVariable = '<div></div>'

          if(BulkCountSteelhead > 0){
            BulkHTMLVariable = '<div class="bulkhead-counts-note">Juvenile Bulk Fish Count: '+BulkCountSteelhead+'</div>'
          } 

          return   BulkHTMLVariable +
          '<table class="fish-counts-table" cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">'+
              '<thead>'+
                  '<tr>'+
                    '<th>Species</th>'+
                    '<th>Length</th>'+
                    '<th>Weight</th>'+
                    '<th>Smolted</th>'+
                    '<th>Recap</th>'+
                    '<th>Mortality</th>'+
                    '<th>PitTagNo</th>'+
                    '<th>Individual Fish Comments</th>'+
                    '<th>DNAVialNo</th>'+
                    '<th>Scale Card No</th>'+
                    '<th>Condition Factor</th>'+
                  '</tr>'+
              '</thead>'+
              '<tbody>'+
                tableData+
              '</tbody>'+
              '</table>'
          ;
        }

        var table = $('#screw-trap-table').DataTable({
            ajax: {
              url: urlPrefix+"/api/ScrewTrap/GetScrewTrapConsolidated?Location="+location
                            +"&FromDate="+from+"&ToDate="+to+"&Order="+order,
              dataSrc: ''
            },
            ordering: false,
            pageLength: 50,
            searching: false,
            columns: [
                {
                  "className":      '',
                  "orderable":      false,
                  "data":           null,
                  //"defaultContent": 'Edit',
                  "render": function(data, type, row, meta){
                    if(!data.Legacy) {
                      data = '<a href="https://survey123.arcgis.com/share/'+gisID+'?mode=edit&objectId='+data.ObjectID+'" target="_blank">Edit</a>'
                      return data; 
                    } else {
                      return '';
                    }
                  } 
                },             
                // { data: "Location"},
                { data: "Date"},
                { data: "Time"},
                { data: "Initials"},
                { data: "Fishing"},
                { data: "Rotating"},
                { data: "SecondsPerOneFinalRotation"},
                { data: "StaffGage"},
                { data: "WaterTemp"},
                { data: "AirTemp" },
                { data: "DebrisSizeDiameter"},
                { data: "WaterClarity"},
                { data: "CloudPercentage"},
                { data: "GeneralComments"},
                { data: "EfficiencyTest"},
                { data: "EfficiencyTestNo"},
                { data: "PitTagFile"},
                { data: "PitTagVial1"},
                { data: "PitTagVial2"},
                { data: "FishCount"},
                { data: "NumberTagged"},
                { data: "EfficiencyRecaps"},
                { data: "Mortality"},
                { data: "AvgLength"},
                { data: "AvgWeight"},
                { data: "MortalityPercentage"},
                { data: "MinMaxRatio"}
                // { data: "MinLength" },
                // { data: "MaxLength" }
            ],
            columnDefs: [{
              targets: 19,
              createdCell: function(td, cellData, rowData, row, col){
                if(cellData > 0){
                  $(td).addClass('list-link')
                }
              }
            }]
            

        });

        $('#screw-trap-table tbody').on('click', 'td.list-link', function () {
          var tr = $(this).closest('tr');
          var row = table.row( tr );
   
          if ( row.child.isShown() ) {
              // This row is already open - close it
              row.child.hide();
              tr.removeClass('shown');
          }
          else {
              // Open this row
              $.ajax({
                url: urlPrefix+"/api/ScrewTrap/GetConsolidatedFishLogs?GUID="+row.data().GlobalID,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                  row.child(format(data, row.data().BulkCountSteelhead)).show();
                  tr.addClass('shown');
                }

              })
              
          }
      } );


      }      
    }
})(jQuery, Drupal, drupalSettings)