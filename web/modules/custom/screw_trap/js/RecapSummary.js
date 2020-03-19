(function($, Drupal, drupalSettings) {
    Drupal.behaviors.recapSummary = {
      attach: function (context, settings) {
        var urlPrefix = drupalSettings.baseUrl 

        var location = $("#edit-location").val();
        var from = $("#screwtrap-from").val(); 
        var to = $("#screwtrap-to").val(); 
        var order = $("#edit-order").val();
        $('#edit-export').click(function() {
          alert('Pending')
        })

        var table = $('#example').DataTable({
            ajax: {
              url: urlPrefix+"/api/RecapSummary/GetRecapSummary?Location="
                            +location+"&DateFrom="
                            +from+"&DateTo="
                            +to+"&Order="
                            +order,
              dataSrc: ''
            },
            searching: false,
            ordering: false,
            pageLength: 50,
            columns: [
                { data: "Date"},
                { data: "SteelheadCaptured"},
                { data: "NumPitTag"},
                { data: "EfficiencyTestNo"},
                { data: "Recap"},
                { data: "ChinookCaptured"},
                { data: "CohoCaptured"},
                { data: "LampreyCaptured"}                
            ]
        });
      }
    }
})(jQuery, Drupal, drupalSettings)