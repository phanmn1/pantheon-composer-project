(function($, Drupal, drupalSettings) {
    Drupal.behaviors.differentbehaviour = {
      attach: function (context, settings) {
        

       var urlPrefix = drupalSettings.baseUrl 


    //    $.ajax({
    //        url: 'http://dev.qwconsult.com/rest/api/TrapSamples/GetSelectLists',
    //        dataType: 'json',
    //        async: false,
    //        success: function(data) {
    //            console.log(data); 
    //            $.each(data.Facility, function(index, value){
    //             $('#edit-facility').append($('<option>', {
    //                 value: value.FacilityID, 
    //                 text: value.FacilityName
    //             }))
    //            })

    //            $('#edit-species').append($('<option>', {
    //             value: "All",
    //             text: "All"
    //             }))

    //            $.each(data.SpeciesCodes, function(index, value){
    //             $('#edit-species').append($('<option>', {
    //                 value: value.SppCode, 
    //                 text: value.SppCode
    //             }))
    //            })


    //        }           
    //    })
       
       console.log(drupalSettings)

        var options = {  
            hour: "2-digit", minute: "2-digit"  
           };  

        console.log(settings.username)

        var facilityCode = $('#edit-facility').val();
        var species = $("#edit-species").val(); 
        var from = $("#qa-demo-from").val(); 
        var to = $("#qa-demo-to").val(); 

        let getCurrentDate = function () {
            return new Date(Date.now()).toLocaleTimeString("en-us", {hour: "2-digit", minute: "2-digit"}  )
        }

        var editor = new $.fn.dataTable.Editor({
            ajax: {
                edit: {
                    type: "PUT", 
                    url: urlPrefix+"/api/VideoCounts/EditVideoCounts",
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
                        data.QC_ModifiedBy = settings.username
                        data.ID = idSource 
                        data.Facility_Selected = $('#edit-facility').val();
                        data.Species_Selected = $("#edit-species").val(); 
                        data.FromDate_Selected = $("#qa-demo-from").val();
                        data.ToDate_Selected = $("#qa-demo-to").val(); 
                        //data['data'][idSource]
                        console.log(data)

                        return JSON.stringify(data) 
                    }


                }, //end edit 
                remove: {
                    type: "POST", 
                    url: urlPrefix+"/api/VideoCounts/DeleteVideoCounts",
                    contentType: 'application/json', 
                    data: function(data) {
                            var fieldInstance = editor.field(editor.displayed()[0]);
                        
                            var idSource; 
                            var returndata = [];
                            $.each(data.data, function(key, value) {
                                idSource = key; 
                                
                                returndata.push(data['data'][idSource]);
                            }); 
                             
                            

                            //data = data['data'][idSource]
                            // data.HPID = editor.field('HPID').val(); 
                            // data.SampleSourceID = editor.field('SampleSourceID').val()
                            //data['data'][idSource]
                            console.log(returndata[0].QC_ModifiedBy)

                            return JSON.stringify(returndata) 
                    }
                }, 
                create: {
                    type: "PUT", 
                    url: urlPrefix+"/api/VideoCounts/CreateVideoCount",
                    contentType: 'application/json',
                    data: function(data) {
                        var fieldInstance = editor.field(editor.displayed()[0]);
                        
                        var idSource; 
                        
                            $.each(data.data, function(key, value) {
                                idSource = key; 
                            }); 
            
                            data = data['data'][idSource]

                            data.Facility_Selected = $('#edit-facility').val();
                            data.Species_Selected = $("#edit-species").val(); 
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
                    //{ label: "", name: "", type: "checkbox", separator: ",", multiple: true},
                    { label: "ID", name: "ID"},                   
                    { 
                      label: "PassDate", 
                      name: "PassDate", 
                      type: 'datetime', 
                      def: function() {return $("#qa-demo-to").val();},
                      //dateFormat: 'm/d/yy'
                      format: 'MM/DD/YYYY'
                    },                                         
                    { 
                       label: "PassTime", 
                       name: "PassTime", 
                       type:  'datetime',
                       format: 'h:mm A',
                       fieldInfo: '12 hour clock format',
                       //def: function () {return $("#qa-demo-to").val();}
                       def: getCurrentDate
                    }, 
                    { 
                        label: "LadCode", 
                        name: "LadCode", 
                        type: "select",
                        def: 'lf'
                     },                                        
                    { 
                        label: "SppCode", 
                        name: "SppCode", 
                        type: "select",
                        def: function() { return $("#edit-species").val(); },
                        def: 'wsth'
                    }, 
                    { label: "Viewer", name: "Viewer"}, 
                    { label: "EstFKLength", name: "EstFKLength"},
                    { label: "MarkID", name: "MarkID"},
                    { 
                        label: "Number of fish to add", 
                        name: "numFish",  
                        def: 1
                    },
                    { label: "HPID", name: "HPID"},
                    { label: "SampleSourceID", name: "SampleSourceID"},
                    { label: "QC Name", name: "QC_ModifiedBy", type: "readonly", def: settings.username},
                    { label: "QC Comment", name: "QC_Comments"}                   
                ], 
                // i18n: {
                //     create: {
                //         submit: "Add",
                //         title: "Add Counts" 
                //     }
                // }

            
        });


       

        // $('#qc-log-table').on( 'click', 'tbody td:not(:first-child)', function (e) {
        //     editor.inline( this );            
        // } );

        $('#qc-log-table').on('click', 'a.editor_edit', function(e) {
            e.preventDefault(); 

            
            editor.hide('numFish')
            //editor.field('QC_ModifiedBy').set(settings.username)
            editor.edit($(this).closest('tr'), {
                title: 'Edit record', 
                buttons: [{ label: 'Cancel', fn: function () { this.close(); } }, 'Update',]
                
                
            });



            
        });

        $('#edit-reset').on('click', function(){
            $('#edit-facility').val(1); 
            $('#edit-species').val("All")
            $('#qa-demo-from').val(new Date(new Date()
                    .setDate(new Date()
                    .getDate() - 7))
                    .toLocaleDateString("en-US", {month: '2-digit', day: '2-digit', year: 'numeric'}))
                    
                    
            
            $('#qa-demo-to').val(new Date(Date.now()).toLocaleDateString("en-US", {month: '2-digit', day: '2-digit', year: 'numeric'}))
        })

       

        $('#qc-log-table').on('InitCreate', function() {
            //editor.field('numFish').disable()
            editor.show('numFish')
            //editor.hide('PassDate')
            alert('does this thing work?')
        })

        editor.field('HPID').hide();
        editor.field('SampleSourceID').hide(); 
        editor.field('ID').hide(); 
       // editor.field('QC_ModifiedBy').hide();
        editor.field('QC_ModifiedBy').disable();

        //editor.field('QC_ModifiedBy').set(settings.username)

        editor.on('preSubmit', function(e,o, action) {
            if( action !== 'remove') {
                var passdate = this.field('PassDate');
                var passtime = this.field('PassTime'); 
                var viewer = this.field('Viewer'); 
                var estfklength = this.field('EstFKLength');
                var numfish = this.field('numFish');
                var MarkID = this.field('MarkID')

                console.log(estfklength.val() != "")

                if(!validateDate(passdate.val()))                   
                    passdate.error('Date cannot be empty or have invalid date format')
                
                if(!validateTime(passtime.val()))
                    passtime.error('Time cannot be empty or have invalid time format')
                 
                if(!validateViewer(viewer.val()))
                    viewer.error('Invalid viewer name')
                
                if(viewer !== undefined && validateLength(viewer.val(), 5))
                    viewer.error("Invalid character length. Viewer length can't be greater than 6 characters")

                if(!validateForkLength(estfklength.val()))
                    estfklength.error('Invalid fork length')

                if(!validateFishToAdd(numfish.val()))
                    numfish.error('Invalid Natural Number')

                if(MarkID !== undefined && validateLength(MarkID.val(), 6))
                    MarkID.error("Invalid character length. MarkID length can't be greater than 6 characters")


                if ( this.inError() ) {
                    return false;
                }


            }
        })

       

        console.log(to)


        
          
               

            

            var table = $('#qc-log-table').DataTable( {
                // fixedHeader: true,
                dom: "Bfrtip",
                pageLength: 25,
                info: true,
                searching: false,
                fixedHeader: true,
                ajax: urlPrefix+"/api/VideoCounts/GetFishCounts?facilityCode="
                    +facilityCode+"&Species="
                    +species+"&startdate="
                    +from+"&enddate="+to,   
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
                            [ 4, 'asc' ]
                        ],
                columns: [
                    {
                        data: null,
                        defaultContent: '',
                        className: 'select-checkbox',
                        orderable: false                       
                    },
                    { data: "ID", visible: false},
                    { 
                        data: null, 
                        className: "center", 
                        render: function( data, type, row) {
                            //if(Date(data["PassDate"])
                            var year = new Date(data["PassDate"]).getFullYear()
    
                            if(year < 2017) {
                                //console.log("Year less than 2017")
                                return ''//data 
                            } else {
                                return  '<a href="" class="editor_edit">Edit</a>'
                            }
                            //console.log(year)
                            //return data;
                        }
                        // defaultContent:  '<a href="" class="editor_edit">Edit</a>'                               
                    },
                    { data: "PassDate" },                   
                    { data: "PassTime"},                    
                    { data: "LadCode", editField: "LadCode" },
                    { data: "SppCode", editField: "SppCode" },
                    { data: "Viewer" },
                    { data: "EstFKLength" },
                    { data: "MarkID"},                 
                    { data: "HPID", visible: false}, 
                    { data: "SampleSourceID", visible: false}, 
                    { data: "QC_ModifiedBy", visible: false}, 
                    { data: "QC_Comments"},                  
                ],
                
                select: {
                    style:    'multi',
                    selector: 'td:first-child'
                }, 
                buttons: [
                    { 
                      extend: "create", 
                      editor: editor,                                                                    
                      text: "Add",                      
                      action: function () {
                          editor.show('numFish')
                          editor.create({
                            title: 'Add', 
                            buttons: [{ label: 'Cancel', fn: function () { this.close(); } },'Add Counts']                          
                        })                        
                      }
                     },
                     //{ extend: "edit",   editor: editor },
                    { 
                       extend: "remove", 
                       editor: editor, 
                       
                     }
                ],
                createdRow: function( row, data, dataIndex ) {
                    //console.log(row)               
                    var year = new Date(data["PassDate"]).getFullYear()
                    //console.log(year)
                    if(year < 2017) {
                        console.log($(row).children())
                        //jQuery Navigation to the first child of the node list and remove the class
                        $(row).children().removeClass("select-checkbox")
    
                    }
                    
                    
    
                }
                
            } );

            


/********************************************************************************************************************************
 *                                              Validation Functions 
 * 
 *                  
 * 
 ********************************************************************************************************************************/

            
            //RegExp taken from Chris West Blog (http://cwestblog.com/) 
            function validateDate(strDate) {              
                if(moment(strDate, 'MM/DD/YYYY',true).isValid() || moment(strDate,'YYYY-MM-DD',true).isValid())
                    return true
                else   
                    return false 
              }

            //RegExp taken from https://www.safaribooksonline.com/library/view/regular-expressions-cookbook/9781449327453/ch04s06.html
            function validateTime(strTime) {
                var t1 = /^(1[0-2]|0?[1-9]):([0-5]?[0-9])([\s]?[AP]M)?$/;
                var t2 = /^(2[0-3]|[01]?[0-9]):([0-5]?[0-9])$/;
                var match; 

                if(strTime.match(t1) || strTime.match(t2))
                    return true
                 else 
                    return false
                
                    
                return match; 
            }


            function validateViewer (strViewer) {
                /*
                var match = /^[a-zA-Z]*$/
                if(strViewer.match(match) || strViewer == "") 
                    return true; 
                else 
                */
                    return true; 
            }
            
            //RegExp taken https://www.w3resource.com/javascript/form/decimal-numbers.php
            function validateForkLength(strFkLength){
                var match = /^\d+(\.\d+)?$/
                if(strFkLength.match(match) || strFkLength == "")
                    return true;
                else 
                    return false; 
            }


            function validateFishToAdd(strNumFishToAdd){
                var match = /^(0|([1-9]\d*))$/;

                if(strNumFishToAdd.match(match))
                    return true
                else 
                    return false
            }

            function validateLength(value, length) {
                if(value.length > length) 
                    return true
                else    
                    return false
            }


        
      }
    }
})(jQuery, Drupal, drupalSettings)