(function($, Drupal, drupalSettings) {
    Drupal.behaviors.ageddata = {
      attach: function (context, settings) {
        
        
        var urlPrefix = drupalSettings.baseUrl; 

        var facilityCode = $('#facility').val();
        var species = $("#edit-species").val(); 
        var from = $("#qa-demo-from").val(); 
        var to = $("#qa-demo-to").val(); 
        var dnasample = $("#edit-dna").val(); 
        var pittag = $("#edit-pittag").val(); 
        var jvpittag = $("#edit-jvpittag").val();

        let getCurrentDate = function () {
            return new Date(Date.now()).toLocaleTimeString("en-us", {hour: "2-digit", minute: "2-digit"}  )
        }


        var editor = new $.fn.dataTable.Editor({
            ajax: {
                edit: {
                    type: "PUT", 
                    url: urlPrefix+"/api/TrapSamples/EditAgedData",
                    contentType: 'application/json',
                    data : function(data) {
                        var fieldInstance = editor.field(editor.displayed()[0]);

                        var idSource; 

                        $.each(data.data, function(key, value) {
                            idSource = key; 
                        }); 

                        data = data['data'][idSource]
                        data.HPID = editor.field('HPID').val(); 
                        data.SampleSourceID = editor.field('SampleSourceID').val()

                        //data.Scalesmpl = editor.field('Scalesmpl').val(); 
                        data.QC_ModifiedBy = settings.username
                        // data.ID = idSource 
                        // data.Facility_Selected = $('#edit-facility').val();
                        // data.Species_Selected = $("#edit-species").val(); 
                        // data.FromDate_Selected = $("#qa-demo-from").val();
                        // data.ToDate_Selected = $("#qa-demo-to").val(); 
                        // data['data'][idSource]
                        console.log(data)

                        return JSON.stringify(data) 
                    }


                }, //end edit 
                // remove: {
                //     type: "POST", 
                //     url: urlPrefix+"/api/VideoCounts/DeleteVideoCounts",
                //     contentType: 'application/json', 
                //     data: function(data) {
                //             var fieldInstance = editor.field(editor.displayed()[0]);
                        
                //             var idSource; 
                //             var returndata = [];
                //             $.each(data.data, function(key, value) {
                //                 idSource = key; 
                                
                //                 returndata.push(data['data'][idSource]);
                //             }); 
                             
                            

                //             // data = data['data'][idSource]
                //             // data.HPID = editor.field('HPID').val(); 
                //             // data.SampleSourceID = editor.field('SampleSourceID').val()
                //             // data['data'][idSource]
                //             console.log(returndata[0].QC_ModifiedBy)

                //             return JSON.stringify(returndata) 
                //     }
                // }, 
                // create: {
                //     type: "PUT", 
                //     url: urlPrefix+"/api/VideoCounts/CreateVideoCount",
                //     contentType: 'application/json',
                //     data: function(data) {
                //         var fieldInstance = editor.field(editor.displayed()[0]);
                        
                //         var idSource; 
                        
                //             $.each(data.data, function(key, value) {
                //                 idSource = key; 
                //             }); 
            
                //             data = data['data'][idSource]

                //             data.Facility_Selected = $('#edit-facility').val();
                //             data.Species_Selected = $("#edit-species").val(); 
                //             data.FromDate_Selected = $("#qa-demo-from").val();
                //             data.ToDate_Selected = $("#qa-demo-to").val(); 
                //             // data.HPID = editor.field('HPID').val(); 
                //             // data.SampleSourceID = editor.field('SampleSourceID').val()
                //             //data['data'][idSource]
                //             console.log(data)
            
                //             return JSON.stringify(data);
                //     }                   
                // }
               
                

                }, // end ajax block 
                table: '#qc-log-table', 
                idSrc: "ID",
                fields: [
                    //{ label: "", name: "", type: "checkbox", separator: ",", multiple: true},
                    { label: "ID", name: "ID"},                   
                    { 
                      label: "PassDate", 
                      name: "PassDate", 
                      type: 'datetime', 
                      def: function() {return $("#qa-demo-to").val();},
                      dateFormat: 'm/d/yy',
                      format: 'MM/DD/YYYY',
                    //   type: "readonly"
                    },  
                    { label: "DNASample", name: "DNASample"}, 
                    { label: "PitTag", name: "PitTag"},
                    { label: "Juvenile Pittag", name: "JvPitTag" }, 
                    // { label: "Fork Length", name: "Forklgth"}, 
                    // { label: "Poh Length", name: "Pohlgth"}, 
                    // { label: "Weight", name: "Weight" },                                           
                    // { label: "Status", name: "Status"},
                    { label: "Age", name: "Age"}, 
                    { label: "Scale Sample", name: "Scalesmpl"}, 
                    // { label: "Comments", name: "Comments"},                   

                    { label: "HPID", name: "HPID"}, 
                    { label: "SampleSourceID", name: "SampleSourceID"},
                    { label: "QC Name", name: "QC_ModifiedBy", type: "readonly", def: settings.username },
                    // { label: "QC Comment", name: "QC_Comments" }                   
                ], 
                // i18n: {
                //     create: {
                //         submit: "Add",
                //         title: "Add Counts" 
                //     }
                // }

            
        });



       


        $('#qc-log-table').on( 'click', 'tbody td.editable', function (e) {                       

            editor.inline( this );
        } );

        $("#qc-log-table").on('mouseenter', 'tbody td.editable', function(e){         
            $(this).css('background-color', '#C7C2BD')         
        })

        $("#qc-log-table").on('mouseleave', 'tbody td.editable', function(e){             
            $(this).css('background-color', 'transparent')         
        })

        $('#edit-reset').on('click', function(){
            $('#facility').val("pb"); 
            $('#edit-species').val("All")
            $('#qa-demo-from').val(new Date(new Date()
                    .setDate(new Date()
                    .getDate() - 7))
                    .toLocaleDateString("en-US", {month: '2-digit', day: '2-digit', year: 'numeric'}))

            $('#edit-dna').val("");
            $('#edit-pittag').val("");
            $('#edit-jvpitag').val(""); 
            
                    
                    
            
            $('#qa-demo-to').val(new Date(Date.now()).toLocaleDateString("en-US", {month: '2-digit', day: '2-digit', year: 'numeric'}))
        })

        var table = $('#qc-log-table').DataTable( {
            //dom: "Bfrtip",
            pageLength: 10,
            info: true,
            searching: false,
            responsive: true,
            fixedHeader: true,
            ajax: urlPrefix+"/api/TrapSamples/GetPagedData?facilityCode="+facilityCode+
                "&sppCode="+species+
                "&startdate="+from+
                "&enddate="+to+   
                "&DNASample="+dnasample+
                "&PitTag="+pittag+
                "&JvPitTag="+jvpittag,
            initComplete: function(settings, json) {
                console.log(json)
                $.each(json.data, function(key,value) {
                    //value.QC_ModifiedBy = settings.username
                    //console.log(value)
                    value.QC_ModifiedBy = drupalSettings.username
                    
                    
                    //console.log(drupalSettings.username)
                })
            },                             
            scrollX: true,
            // order: [
            //             [ 3, 'asc' ],
            //             [ 4, 'asc' ]
            //         ],
            columns: [
                // {
                //     data: null,
                //     defaultContent: '',
                //     className: 'select-checkbox',
                //     orderable: false                       
                // },               
                // { 
                //     data: null, 
                //     className: "center", 
                //     render: function( data, type, row) {                       
                //             return  '<a href="" class="editor_edit">View Data</a>'
                //         //console.log(year)
                //         //return data;
                //     }, 
                //     orderable: false
                //     // defaultContent:  '<a href="" class="editor_edit">Edit</a>'                               
                // },    
                { data: "SppCode" },                      
                { data: "PassDate" , hidden: true},
                { data: "PassTime" },
                { data: "DNASample", className: "editable" }, 
                { data: "PitTag", className: "editable" }, 
                { data: "JvPitTag", className: "editable" }, 
                { data: "Forklgth" },
                { data: "Mehlgth" },
                { data: "Weight" }, 
                { data: "Labels.Status", visible: false},                
                { data: "Labels.Sex" },
                { data: "Age", className: "editable"}, 
                { data: "Scalesmpl", className: "editable"},               
                { data: "Comments"},  
                { data: "ID" }, 
                { data: "AdClip", visible: false },                
                { data: "CarcassNo", visible: false},
                { data: "Labels.Origin", visible: false},               
                { data: "Pohlgth", visible: false }, 
                { data: "Injection", visible: false },
                { data: "CWT", visible: false},
                { data: "Labels.ElastomerColor", visible: false },
                { data: "OCTSNT", visible: false },
                { data: "Labels.BodyLocation", visible: false },
                { data: "Mort", visible: false }, 
                { data: "Channel", visible: false },
                { data: "Release", visible: false }, 
                { data: "GonadStudy", visible: false }, 
                { data: "VentralClip", visible: false} , 
                { data: "RTChannel", visible: false },
                { data: "RTCode", visible: false  }, 
                { data: "Labels.Brightness", visible: false },
                { data: "Tube", visible: false},
                { data: "Drax", visible: false }, 
                { data: "Operator" , visible: false},
                //{ data: "Archive", visible: false},                  
                // { data: "PassTime"},                    
                // { data: "LadCode", editField: "LadCode" },
                // { data: "SppCode", editField: "SppCode" },
                // { data: "Viewer" },
                // { data: "EstFKLength" },
                // { data: "MarkID"},                 
                { data: "HPID", visible: false}, 
                { data: "SampleSourceID", visible: false}, 
                { data: "QC_ModifiedBy", visible: false}, 
                // { data: "QC_Comments", className: 'editable'},                  
            ],
            
            select: {
                style:    'os',
                selector: 'td:first-child'
            }, 
            // keys: {
            //     columns: ':not(:first-child)',
            //     editor:  editor
            // },
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.modal( {
                        header: function ( row ) {
                            //var data = row.data();
                            //console.log(data)
                            return 'Sample Data';
                        }
                       
                    } ),
                    renderer: function( api, rowIdx, columns) {

                        
                       console.log(columns)

                       var data = `<table class="modal-table">
                       <tr>                            
                            <td><strong> `+columns[1].title+` </strong>: `+columns[1].data+`</td>
                            <td><strong> `+columns[2].title+` </strong>: `+columns[2].data+`</td>
                            <td><strong> `+columns[3].title+` </strong>: `+columns[3].data+`</td>
                            <td><strong> `+columns[4].title+` </strong>: `+columns[4].data+`</td>
                        </tr>
                        <tr>                           
                            <td><strong> `+columns[5].title+` </strong>: `+columns[5].data+`</td>
                            <td><strong> `+columns[6].title+` </strong>: `+columns[6].data+`</td>
                            <td><strong> `+columns[7].title+` </strong>: `+columns[7].data+`</td>
                            <td><strong> `+columns[8].title+` </strong>: `+columns[8].data+`</td>
                        </tr>
                        <tr>                         
                            <td><strong> `+columns[9].title+` </strong>: `+columns[9].data+`</td>
                            <td><strong> `+columns[10].title+` </strong>: `+columns[10].data+`</td>
                            <td><strong> `+columns[11].title+` </strong>: `+columns[11].data+`</td>
                            <td><strong> `+columns[12].title+` </strong>: `+columns[12].data+`</td>
                        </tr>
                        <tr>                           
                            <td><strong> `+columns[13].title+` </strong>: `+columns[13].data+`</td>
                            <td><strong> `+columns[15].title+` </strong>: `+columns[15].data+`</td>
                            <td><strong> `+columns[16].title+` </strong>: `+columns[16].data+`</td>
                            <td><strong> `+columns[17].title+` </strong>: `+columns[17].data+`</td>
                        </tr>
                        <tr>
                            
                            <td><strong> `+columns[18].title+` </strong>: `+columns[18].data+`</td>
                            <td><strong> `+columns[19].title+` </strong>: `+columns[19].data+`</td>
                            <td><strong> `+columns[20].title+` </strong>: `+columns[20].data+`</td>
                            <td><strong> `+columns[21].title+` </strong>: `+columns[21].data+`</td>
                        </tr>
                        <tr>                         
                            
                            <td><strong> `+columns[22].title+` </strong>: `+columns[22].data+`</td>
                            <td><strong> `+columns[23].title+` </strong>: `+columns[23].data+`</td>
                            <td><strong> `+columns[24].title+` </strong>: `+columns[24].data+`</td>
                            <td><strong> `+columns[25].title+` </strong>: `+columns[25].data+`</td>
                            </tr>
                        <tr>                           
                            
                            <td><strong> `+columns[26].title+` </strong>: `+columns[26].data+`</td>
                            <td><strong> `+columns[27].title+` </strong>: `+columns[27].data+`</td>
                            <td><strong> `+columns[28].title+` </strong>: `+columns[28].data+`</td>
                            <td><strong> `+columns[29].title+` </strong>: `+columns[29].data+`</td>
                       </tr>
                        <tr>
                            
                            <td><strong> `+columns[30].title+` </strong>: `+columns[30].data+`</td>
                            <td><strong> `+columns[31].title+` </strong>: `+columns[31].data+`</td>
                            <td><strong> `+columns[32].title+` </strong>: `+columns[32].data+`</td>
                            <td><strong> `+columns[33].title+` </strong>: `+columns[33].data+`</td>
                        </tr>
                        <tr>                                                      
                            <td><strong> `+columns[34].title+` </strong>: `+columns[34].data+`</td>     
                            <td colspan="3"><strong> `+columns[14].title+` </strong>: `+columns[14].data+`</td>                                            
                        </tr>
                                                                 
                       </table>`      
                    return data;
                   
                    }
                    
                        
                    
                }
            },
            
            // buttons: [
            //     { 
            //       extend: "create", 
            //       editor: editor,                                                                    
            //       text: "Add",                      
            //       action: function () {
            //           editor.show('numFish')
            //           editor.create({
            //             title: 'Add', 
            //             buttons: [{ label: 'Cancel', fn: function () { this.close(); } },'Add Counts']                          
            //         })                        
            //       }
            //      },
            //      //{ extend: "edit",   editor: editor },
            //     { 
            //        extend: "remove", 
            //        editor: editor,                    
            //      }
            // ],
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



        // $('#qc-log-table tbody').on('click', 'a.editor_edit', function(e) {
        //     e.preventDefault(); 

        //    console.log(table.row(this));
           

        // });


        //  $('#qc-log-table tbody').on('click', 'tr', function(e) {
        //     e.preventDefault(); 
        //     var data = table.row(this).data()

        //     $('#qc-log-table').on('click', 'a.editor_edit', function(e){
        //         console.log(data);
        //         console.log(table.responsive)
        //     })

           
           

        // });

        



        editor.on('preSubmit', function(e,o, action) {
            if( action !== 'remove') {
                var passdate = this.field('PassDate');
                console.log(passdate)
                if(!validateDate(passdate.val())){                   
                    passdate.error('Cannot edit samples that are before 2010')
                    console.log("TEST")
                    alert("Cannot Edit Age Data before 2010")
                }
                           
                if ( this.inError() ) {
                    return false;
                }


            }
        })

        //https://www.datatables.net/forums/discussion/comment/105467/
       
        editor.on( 'close', function () {
            editor.off( 'postSubmit.editorInline' );
        });


        function validateDate(passdate) {
            var newdate = new Date(passdate); 
            if(newdate.getFullYear() <= 2010)
                return false
            else 
                return true; 
        }


        function formatModalData(api, rowIdx, columns) {
            
            
            

            
        }
       

       

        


        
      }
    }
})(jQuery, Drupal, drupalSettings)