(function($, Drupal, drupalSettings) {
    Drupal.behaviors.lampreyDataInputBehaviorQA = {
      attach: function (context, settings) {


        console.log(drupalSettings)
        var urlPrefix = drupalSettings.baseUrl 

        var facilityCode = $('#edit-facility').val();
        var species = $("#edit-species").val(); 
        var from = $("#qa-demo-from").val(); 
        var to = $("#qa-demo-to").val(); 


        var editor = new $.fn.dataTable.Editor({
            ajax: {
                edit: {
                    type: "POST", 
                    url: urlPrefix+"/api/Lamprey/EditLamprey?facility="+facilityCode+                       
                        "&startdate="+from+
                        "&enddate="+to,                      
                    contentType: 'application/json',
                    data : function(data) {
                        var fieldInstance = editor.field(editor.displayed()[0]);

                        var idSource; 

                        $.each(data.data, function(key, value) {
                            idSource = key; 
                        }); 

                        data = data['data'][idSource]
                        //data.HPID = editor.field('HPID').val(); 
                        //data.SampleSourceID = editor.field('SampleSourceID').val()
                        data.QC_ModifiedBy = settings.username
                        data.ID = idSource 
                        // data.Facility_Selected = $('#edit-facility').val();
                        // data.Species_Selected = $("#edit-species").val(); 
                        // data.FromDate_Selected = $("#qa-demo-from").val();
                        // data.ToDate_Selected = $("#qa-demo-to").val(); 
                        // data['data'][idSource]
                        console.log(data)

                        return JSON.stringify(data) 
                    }


                }, //end edit 
                remove: {
                    type: "POST", 
                    url: urlPrefix+"/api/Lamprey/DeleteLamprey",
                    contentType: 'application/json', 
                    data: function(data) {
                            var fieldInstance = editor.field(editor.displayed()[0]);
                        
                            var idSource; 
                            var returndata = [];
                            $.each(data.data, function(key, value) {
                                idSource = key; 
                                data['data']['SampleSourceID']; 
                                returndata.push(data['data'][idSource]);
                            }); 
                             
                            

                            //data = data['data'][idSource]
                            // data.HPID = editor.field('HPID').val(); 
                            // data.SampleSourceID = editor.field('SampleSourceID').val()
                            //data['data'][idSource]
                            console.log(returndata)

                            return JSON.stringify(returndata) 
                    }
                }, 
                create: {
                    type: "PUT", 
                    url: urlPrefix+"/api/Lamprey/CreateLampreyCounts",
                    contentType: 'application/json',
                    data: function(data) {
                        var fieldInstance = editor.field(editor.displayed()[0]);
                        
                        var idSource; 
                        
                            $.each(data.data, function(key, value) {
                                idSource = key; 
                            }); 
            
                            data = data['data'][idSource]
                            

                            data.Facility_Selected = $('#edit-facility').val();
                            // data.Species_Selected = $("#edit-species").val(); 
                            data.FromDate_Selected = $("#qa-demo-from").val();
                            data.ToDate_Selected = $("#qa-demo-to").val(); 
                            // data.HPID = editor.field('HPID').val(); 
                            // data.SampleSourceID = editor.field('SampleSourceID').val()
                            //data['data'][idSource]
                            console.log(data)
            
                            return JSON.stringify(data);
                    }                   
                }
               
                

                }, // end ajax block 
                table: '#qc-log-table', 
                idSrc: "ID",
                fields: [
                    // { label: "", name: "", type: "checkbox", separator: ",", multiple: true},
                    { label: "ID", name: "ID" },     
                    { 
                        label: "LadCode", 
                        name: "LadCode", 
                        type: "select",
                        def: 'lf'
                     },                                   
                    { 
                      label: "Date", 
                      name: "PassDate", 
                      type: 'datetime', 
                      def: function() {return $("#qa-demo-to").val()},
                      
                      //dateFormat: 'm/d/yy',
                      format: 'MM/DD/YYYY'
                    },                                                          
                                                         
                   
                    // { 
                    //     label: "Lamprey Count",                         
                    //     name: "numFish",  
                    //     def: 1
                    // },
                    { label: "HPID", name: "HPID"},
                    { label: "SampleSourceID", name: "SampleSourceID"},
                    { label: "Comments", name: "Comments"}, 
                    
                    { label: "QC_ModifiedBy", name: "QC_ModifiedBy", type: "readonly", def: drupalSettings.username},
                    { label: "QC Comments", name: "QC_Comments", def: ""}
                                  
                ], 
                // i18n: {
                //     create: {
                //         submit: "Add",
                //         title: "Add Counts" 
                //     }
                // }

            
        });

        var table = $('#qc-log-table').DataTable( {
            dom: "Bfrtip",
            pageLength: 25,
            info: true,
            searching: false,
            fixedHeader: true,
            ajax: urlPrefix+"/api/Lamprey/EditLampreyCountsQA?startdate="+from+"&enddate="+to+"&facilityid="+facilityCode,
               
            initComplete: function(settings, json) {
                console.log(json)
                $.each(json.data, function(key,value) {
                    //value.QC_ModifiedBy = settings.username
                    //console.log(value)
                    value.QC_ModifiedBy = drupalSettings.username
                    
                    
                    console.log(drupalSettings.username)
                })
            },                             
            scrollX: true,
            order: [
                        [ 3, 'asc' ],
                        [ 4, 'asc' ],
                        [ 6, 'asc' ]
                    ],
            columns: [
                {
                    data: null,
                    defaultContent: '',
                    className: 'select-checkbox',
                    orderable: false                       
                },
                { data: "ID", visible: false },
                { 
                    data: null, 
                    className: "center",                    
                    defaultContent:  '<a href="" class="editor_edit">Edit</a>'                               
                },
                //{ data: "ID", visible: false},
                { data: "PassDate",
                  render: function (data, type, row) {
                      //var date = new Date(data);
                      //date.getDate
                      

                      function pad(s) { return (s < 10) ? '0' + s : s; }
                      var d = new Date(data);
                      console.log([pad(d.getMonth()+1), pad(d.getDate()), d.getFullYear()].join('/'))
                           
                    return [pad(d.getMonth()+1), pad(d.getDate()), d.getFullYear()].join('/');
                      //return date. 
                  }
            
                },                   
                // { data: "PassTime"},          
                { data: null, 
                  render: function(data, type, row) {
                      console.log(row["LadCode"])
                      if(row["LadCode"] == "vl1" || row["LadCode"] == "vl2")
                          return "Left"
                       else 
                          return "Right"
                      
                
                  }  
                },          
                {
                    data: null, 
                    render: function(data, type, row) {
                        if(row["LadCode"] == "vr1" || row["LadCode"] == "vl1")
                            return "Lower"
                        else
                            return "Upper"
                        
                    }
                },
                { data: "LadCode", editField: "LadCode" },
                // { data: "NumofLamprey"},
                // { data: ""},
                { data: "Comments"},
                // { data: "SppCode", editField: "SppCode" },
                // { data: "Viewer" },
                // { data: "EstFKLength" },
                // { data: "MarkID"},                 
                //  { data: "HPID"}, 
                // { data: "SampleSourceID", visible: false}, 
                // {data: "QC_ModifiedBy"}
                 { data: "QC_Comments", visible: false},                  
            ],
            
            select: {
                style:    'multi',
                selector: 'td:first-child'
            }, 
            buttons: [
                // { 
                //   extend: "create", 
                //   editor: editor,                                                                    
                //   text: "Add",                      
                //   action: function () {
                      
                //       editor.create({
                //         title: 'Add', 
                //         buttons: [{ label: 'Cancel', fn: function () { this.close(); } },'Add Counts']                          
                //     })                        
                //   }
                //  },
                //{ extend: "edit",   editor: editor },
                { 
                   extend: "remove", 
                   editor: editor, 
                   
                 }
            ],
            // createdRow: function( row, data, dataIndex ) {
            //     //console.log(row)               
            //     var year = new Date(data["PassDate"]).getFullYear()
            //     //console.log(year)
            //     if(year < 2017) {
            //         console.log($(row).children())
            //         //jQuery Navigation to the first child of the node list and remove the class
            //         $(row).children().removeClass("select-checkbox")

            //     }
                
                

            // }
            
        } );


        $('#qc-log-table').on('click', 'a.editor_edit', function(e) {
            e.preventDefault(); 

            
            editor.hide('ID')
            editor.hide('HPID')
            editor.hide('SampleSourceID')
            //editor.field('QC_ModifiedBy').set(settings.username)
            editor.field("QC_Comments").set("");
           
            editor.edit($(this).closest('tr'), {
                title: 'Edit record', 
                buttons: [{ label: 'Cancel', fn: function () { this.close(); } }, 'Update',]
                
                
            });



            
        });


        editor.on('preSubmit', function(e, o, action){
            if(action !== 'remove') {
                var date = this.field('PassDate')
               // var LadCode = this.field('LadCode')
                //var Comments = this.field('Comments')
                //var numFish = this.field('numFish')

                if(!validateDate(date.val()))
                    date.error('Date cannot be empty or have invalid date format')

                // if(!validateFishToAdd(numFish.val()) && numFish.val() != "")
                //     numFish.error('Invalid Natural Number')

                // if(numFish.val() == "")
                //     numFish.error('Number cannot be empty')
                
                if( this.inError() ) 
                    return false; 
            }
        })

        function validateDate(strDate) {              
            if(moment(strDate, 'MM/DD/YYYY',true).isValid() || moment(strDate,'YYYY-MM-DD',true).isValid() || 
               moment(strDate, 'M/DD/YYYY', true).isValid() || moment(strDate, 'M/D/YYYY', true).isValid() ||
               moment(strDate, 'MM/D/YYYY', true).isValid())
                return true
            else   
                return false 
          }

          function validateFishToAdd(strNumFishToAdd){
            var match = /^(0|([1-9]\d*))$/;

            if(strNumFishToAdd.match(match))
                return true
            else 
                return false
        }

      }
    }
})(jQuery, Drupal, drupalSettings)