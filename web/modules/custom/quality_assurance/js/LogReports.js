(function($, Drupal, drupalSettings) {
    Drupal.behaviors.yourbehavior = {
      attach: function (context, settings) {
        var urlPrefix = drupalSettings.baseUrl 
        var HTMLLayout; 
        
        console.log(drupalSettings.quality_assurance.datatables_library.test_variable);
                


        var facilityCode = $('#edit-facility').val(); 
        var fromDate = $('#qa-demo-from').val(); 
        var toDate = $('#qa-demo-to').val(); 
        var filter = $('#edit-filter').val(); 
        console.log(fromDate)
        // var url = urlPrefix+'/api/LogController/GetQALog?facilityCode='
        //           +facilityCode+'&startdate='
        //           +fromDate+'&enddate='
        //           +toDate+'&order='
        //           +order
                  
        



     


      function nullCase(value) {
        return value == null ? '' : value; 
      }

       var table = $("#qc-log-table").DataTable({        
          pageLength: 10,
          info: true,
          searching: false,
          fixedHeader: true,
            // responsive: true,
          ajax: urlPrefix+'/api/LogController/GetQALog?facilityCode='
          +facilityCode+'&startdate='
          +fromDate+'&enddate='
          +toDate+'&filter='
          +filter, 
          scrollX: true,
          autoWidth: false,
          columns: [
            {data: "SppCode"}, 
            {data: "PassDate"}, 
            {data: "PassTime"}, 
            {data: "DNASample"},
            {data: "Sex"}, 
            {data: "Age"}, 
            {data: "HPID"}, 
            {data: "SampleSourceID"},
            {data: "ModifiedDate"},
            {data: "QC_ModifiedBy"},
            {data: "QC_Comments", "width": "300px"},
            {data: "PitTag", hidden: true}, 
            {data: "JvPitTag", hidden: true}, 
            {data: "Forklgth", hidden: true}, 
            {data: "Mehlgth", hidden: true}, 
            {data: "Weight", hidden: true}, 
            {data: "Status", hidden: true},            
            {data: "Scalesmpl", hidden: true}, 
            {data: "Comments", hidden: true}, 
            {data: "ID", hidden: true}, 
            {data: "AdClip", hidden: true}, 
            {data: "CarcassNo", hidden: true},
            {data: "Origin", hidden: true}, 
            {data: "Pohlgth", hidden: true}, 
            {data: "Injection", hidden: true}, 
            {data: "CWT", hidden: true}, 
            {data: "ElastomerColor", hidden: true}, 
            {data: "OCTSNT", hidden: true},
            {data: "BodyLocation", hidden: true},
            {data: "Mort", hidden: true},
            {data: "Channel", hidden: true},
            {data: "Release", hidden: true},
            {data: "GonadStudy", hidden: true},
            {data: "VentralClip", hidden: true},
            {data: "RTChannel", hidden: true},
            {data: "RTCode", hidden: true},
            {data: "Brightness", hidden: true},
            {data: "Tube", hidden: true},
            {data: "Drax", hidden: true},
            {data: "Operator", hidden: true}
          
          ],
          responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.modal( {
                    header: function ( row ) {
                        var data = row.data();
                        return 'LOG History';
                    }
                } ),
                renderer: function( api, rowIdx, columns){
                  var output = ''; 

                  console.log(columns[6].data)
                  console.log(columns[7].data)
                  $.ajax({
                    url: urlPrefix+'/api/LogController/GetIDLog?HPID='                   
                        +columns[6].data+'&SampleSourceID='+columns[7].data,
                    async: false,
                    success: function(result) {
                      console.log(result.data)
                      
                      var resultdata = result.data;
                      
                      $.each(resultdata, function(index, value){
                        var table = `<hr>
                        
                                <h1><b>Modified Date</b> - <i>`+nullCase(value.ModifiedDate)+`</i></h1>
                                <h2><b>Modified By</b> - <i>`+nullCase(value.QC_ModifiedBy)+`</i></h2>
                                <h2><b>QC Comments</b> - <i>`+nullCase(value.QC_Comments)+`</i></h2>
                                <table class="modal-table">
                                <tr>                           
                                    <td><strong>PassDate</strong>: `+nullCase(value.PassDate)+`</td>
                                    <td><strong>PassTime</strong>: `+nullCase(value.PassTime)+`</td>
                                    <td><strong>DNA Sample</strong>: `+nullCase(value.DNASample)+`</td>
                                    <td><strong>PitTag</strong>: `+nullCase(value.PitTag)+`</td>
                                </tr>   
                                <tr>
                                    <td><strong>Juvenile Pittag</strong>: `+nullCase(value.JvPitTag)+`</td>
                                    <td><strong>Fork Length</strong>: `+nullCase(value.Forklgth)+`</td>
                                    <td><strong>Meh Length</strong>: `+nullCase(value.Mehlgth)+`</td>
                                    <td><strong>Weight </strong>: `+nullCase(value.Weight)+`</td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong>: `+nullCase(value.Status)+`</td>
                                    <td><strong>Sex</strong>: `+nullCase(value.Sex)+`</td>
                                    <td><strong>Age</strong>: `+nullCase(value.Age)+`</td>
                                    <td><strong>Scale Sample</strong>: `+nullCase(value.Scalesmpl)+`</td>
                                </tr>
                                <tr>
                                    <td><strong>Comments</strong>: `+nullCase(value.Comments)+`</td>
                                    <td><strong>Adipose Clip</strong>: `+nullCase(value.AdClip)+`</td>
                                    <td><strong>CarcassNo</strong>: `+nullCase(value.CarcassNo)+`</td>
                                    <td><strong>Origin</strong>: `+nullCase(value.Origin)+`</td>
                                </tr>    
                                <tr>
                                    <td><strong>Poh Length</strong>: `+nullCase(value.Pohlgth)+`</td>
                                    <td><strong>Injection</strong>: `+nullCase(value.Injection)+`</td>
                                    <td><strong>CWT</strong>: `+nullCase(value.CWT)+`</td>
                                    <td><strong>Elastomer Color</strong>: `+nullCase(value.ElastomerColor)+`</td>
                                </tr>
                                <tr>
                                    <td><strong>OCTSNT</strong>: `+nullCase(value.OCTSNT)+`</td>
                                    <td><strong>Body Location</strong>: `+nullCase(value.BodyLocation)+`</td>
                                    <td><strong>Mort</strong>: `+nullCase(value.Mort)+`</td>
                                    <td><strong>Channel</strong>: `+nullCase(value.Channel)+`</td>
                                </tr>
                                <tr>
                                    <td><strong>Release</strong>: `+nullCase(value.Release)+`</td>
                                    <td><strong>Gonad Study</strong>: `+nullCase(value.GonadStudy)+`</td>
                                    <td><strong>Ventral Clip</strong>: `+nullCase(value.VentralClip)+`</td>
                                    <td><strong>RTChannel</strong>: `+nullCase(value.RTChannel)+`</td>
                                </tr>
                                <tr>
                                    <td><strong>RTCode</strong>: `+nullCase(value.RTCode)+`</td>
                                    <td><strong>Brightness</strong>: `+nullCase(value.Brightness)+`</td>
                                    <td><strong>Tube</strong>: `+nullCase(value.Tube)+`</td>
                                    <td><strong>Drax</strong>: `+nullCase(value.Drax)+`</td>
                                </tr>
                                <tr>
                                    <td><strong>Operator</strong>: `+nullCase(value.Operator)+`</td>
                                    <td><strong>ID</strong>: `+nullCase(value.ID)+`</td>
                                    
                                </tr>             
                                </table>`
                          return output += table;
                      })
                      
                      
                    }
                  });
                  
                  return output;

                }//End Renderer   
            }
        }
        });

        



        // Add event listener for opening and closing details
  //   $('#table-location tbody').on('click', 'td.details-control', function () {
  //     var tr = $(this).closest('tr');
  //     var row = table.row( tr );

  //     if ( row.child.isShown() ) {
  //         // This row is already open - close it
  //         row.child.hide();
  //         tr.removeClass('shown');
  //     }
  //     else {
  //         // Open this row
  //         row.child( format(row.data()) ).show();
  //         tr.addClass('shown');
  //     }
  // } );


function DateFormat(nonFormattedDate, select) {
  var options = {  hour: "2-digit", minute: "2-digit" }; 

  var formattedDate

  if(select == 1) 
    var formattedDate = new Date(nonFormattedDate).toLocaleDateString("en-us")
  else if (select == 2) 
    var formattedDate = new Date(nonFormattedDate).toLocaleTimeString("en-us", options)
      
  if (formattedDate == "12/31/1969") 
    formattedDate = ""
  

  console.log(formattedDate)
    return formattedDate
  }

          
    }
};
})(jQuery, Drupal, drupalSettings)