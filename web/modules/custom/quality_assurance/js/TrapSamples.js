(function($, Drupal, drupalSettings) {
    Drupal.behaviors.newbehavior = {
      attach: function (context, settings) {
        //var urlPrefix = variable_get('APIURL', NULL); 
        //var urlPrefix = 'http://10.1.10.21/rest'
        //var urlPrefix = 'http://localhost:58529'

        console.log(drupalSettings)

        var urlPrefix = drupalSettings.baseUrl;
        
        /*  
        var tableHTML = `<table id="qc-log-table" class="display" cellspacing="0" style="max-width: 100%" width="100%">
        <thead>
            <tr>
                <th></th>
                <th>ID</th>
                <th>Edit</th>
                <th>LadCode</th>
                <th>PassDate</th>
                <th>PassTime</th>
                <th>SppCode</th>
                <th>CarcassNo</th>
                <th>PitTag</th>
                <th>JvPittag</th>
                <th>Status</th>
                <th>Origin</th>
                <th>Age</th>
                <th>Sex</th>
                <th>Forklen</th>
                <th>Pohlen</th>
                <th>Mehlen</th>
                <th>Weight</th>
                <th>Scalesmpl</th>
                <th>DNASample</th>
                <th>Injection</th>
                <th>Comments</th>
                <th>CWT</th>
                <th>ElastomerColor</th>
                <th>OCTSNT</th>
                <th>BodyLocation</th>
                <th>Mort</th>
                <th>Channel</th>
                <th>Release</th>
                <th>GonadStudy</th>
                <th>AdClip</th>
                <th>Ventral</th>
                <th>RTChannel</th>
                <th>RTCode</th>
                <th>Brightness</th>
                <th>Tube</th>
                <th>Drax</th>
                <th>Operator</th>
                <th>QC_Comments</th>
                <th>HPID</th>
                <th>SampleSourceID</th>
            </tr>

        </thead>`

        document.getElementById('table-wrapper').innerHTML = tableHTML; 
        */

        var hpid; 
        var SampleSourceID; 

        $.ajax({
            url: urlPrefix+'/api/TrapSamples/GetSelectLists',
            dataType: 'json',
            async: false,
            success: function(data) {
                console.log(data); 
               
                $('#edit-species').append($('<option>', {
                 value: "All",
                 text: "All"
                 }))
 
                $.each(data.SpeciesCodes, function(index, value){
                 $('#edit-species').append($('<option>', {
                     value: value.SppCode, 
                     text: value.SppCode
                 }))
                })
 
 
            }           
        })


        function ajaxPreprocess (data) {
            var fieldInstance = editor.field(editor.displayed()[0]);
            
            var idSource; 
            
                $.each(data.data, function(key, value) {
                    idSource = key; 
                    
                }); 

                data = data['data'][idSource]

                
                

                console.log(data)
                
                //console.log(data)
                if(data.Mort == "") {
                    data.Mort = false;                     
                } 
                 
                if(data.Channel == "") {
                    data.Channel = false;
                }


                if(data.Adclip == ""){
                    data.Adclip = false; 
                } 

                if(data.CWT == "") {
                    data.CWT = false
                }


                    data.facilityCode_Selected =  $("#facility").val();
                    
                    data.SppCode_Selected = $("#edit-species").val(); 
                    data.startdate = $("#qa-demo-from").val(); 
                    data.enddate = $("#qa-demo-to").val(); 
                    
                    // data.forkLengthFrom = parseFloat( $('#edit-forklen').val(), 10 ) 
                    // data.forkLengthTo = parseFloat( $('#edit-forklento').val(), 10 )
                    // data.weightFrom = parseFloat($('#edit-weight-from').val(), 10)
                    // data.weightTo = parseFloat($('#edit-weight-to').val(), 10)

                    data.forkLengthFrom = $('#edit-forklen').val()
                    data.forkLengthTo = $('#edit-forklento').val()
                    data.weightFrom = $('#edit-weight-from').val()
                    data.weightTo = $('#edit-weight-to').val()

                    data.DNASample_Selected = $('#edit-dnasample').val();
                    data.PitTag_Selected = $('#edit-pittag').val();
                    data.JvPitTag_Selected = $('#edit-jvpittag').val(); 

                    data.pohLengthFrom = $('#edit-pohlen').val(); 
                    data.pohLengthTo = $('#edit-pohlen-to').val();
                   
                    data.Adclip_Selected = $('#edit-adclip:checked').val() === undefined ? false : true 

                    data.CWT_Selected = $('#edit-cwtsnout:checked').val() === undefined ? false : true

                    data.startTime = $('#qa-demo-timefrom-modal').val(); 
                    data.endTime = $('#qa-demo-timeto-modal').val();                

                    data.Comment_Selected = $('#edit-comment').val();

                    data.Sex_Selected = $('#edit-sex').val(); 
                    data.Status_Selected = $('#edit-status').val(); 
                    data.Origin_Selected = $('#edit-origin').val(); 

                    
                
 
                data.QC_ModifiedBy = settings.username

                //console.log(data)
                return JSON.stringify(data)
        }

        var editor = new $.fn.dataTable.Editor({
            ajax: {
                edit: {
                    type: 'POST', 
                    url: urlPrefix+'/api/TrapSamples/EditTrapSample',
                    contentType: 'application/json',
                    data: function(data){
                        // var fieldInstance = editor.field(editor.displayed()[0]);
                        
                        // var idSource; 
                        
                        //     $.each(data.data, function(key, value) {
                        //         idSource = key; 
                        //     }); 
            
                        //     data = data['data'][idSource]
                        //     return JSON.stringify(data)
                        //console.log(data)
                        return ajaxPreprocess(data)
                    }
                },
                create: {
                    type: 'POST',
                    url: urlPrefix+"/api/TrapSamples/CreateTrapSample",
                    contentType: 'application/json', 
                    data: function(data){
                        // var fieldInstance = editor.field(editor.displayed()[0]);
                        
                        // var idSource; 
                        
                        //     $.each(data.data, function(key, value) {
                        //         idSource = key; 
                        //     }); 
            
                        //     data = data['data'][idSource]
                        
                        
                        console.log(data.PassTime)
                            return ajaxPreprocess(data)
                    }, 
                },
                remove: {
                    type: 'POST', 
                    url: urlPrefix+'/api/TrapSamples/DeleteTrapSample',
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
                }
               
            },
            table: '#qc-log-table', 
            idSrc: "ID",           
            fields: [
                { label: "ID", name: "ID" },    
                { 
                    label: "LadCode", 
                    name: "LadCode", 
                    type: "select",
                    def: function () { return $("#facility").val();}
                },           
                { 
                    label: "PassDate", 
                    name: "PassDate",
                    type: 'datetime', 
                    def: function () {return $("#qa-demo-to").val();},
                    format: 'MM/DD/YYYY'
                },
                { 
                    label: "PassTime", 
                    name: "PassTime", 
                    type:  'datetime',
                    format: 'h:mm A',
                    fieldInfo: '12 hour clock format',
                },
                { 
                    label: "SppCode", 
                    name: "SppCode", 
                    type: "select", 
                    def: 'wsth'  
                },      
                { label: "CarcassNo", name: "CarcassNo"},           
                { label: "PitTag", name: "PitTag" }, 
                { label: "JvPittag", name: "JvPitTag" },
                { label: "Status", name: "Status", type: "select"},
                { label: "Origin", name: "Origin", type: "select"},
                { label: "Age", name: "Age" },
                { label: "Sex", name: "Sex", type: "select", def: "U"},
                { label: "Forklen", name: "Forklgth" },
                { label: "Pohlen", name: "Pohlgth" },
                { label: "Mehlgth", name: "Mehlgth" },
                { label: "Weight", name: "Weight" }, 
                { label: "Scalesmpl",  name: "Scalesmpl" },
                { label: "DNASample", name: "DNASample" },
                { label: "Injection",  name: "Injection" }, 
                { label: "Comments", name: "Comments" },
                { 
                    label: "CWT", 
                    name: "CWT",
                    type: "checkbox", 
                    separator: "|",
                    options:   [
                        { label: '', value: true }
                    ]
                 },
                //{ label: "CWTSnout", name: "CWTSnout"  },
                { label: "ElastomerColor", name: "ElastomerColor", type: "select" },
                { label: "OCTSNT", name: "OCTSNT" },
                { label: "BodyLocation", name: "BodyLocation", type: "select" },
                { 
                    label: "Mort", 
                    name: "Mort",
                    type: "checkbox", 
                    separator: "|",
                    options:   [
                        { label: '', value: true }
                    ]
                },
                { 
                    label: "Channel", 
                    name: "Channel",
                    type: "checkbox",
                    separator: "|", 
                    options: [
                        { label: '', value: true}
                    ] 
                },
                { 
                    label: "Release", 
                    name: "Release",
                    type: "checkbox", 
                    separator: "|", 
                    options: [
                        { label: '', value: true}
                    ] 
                }, 
                { 
                    label: "GonadStudy", 
                    name: "GonadStudy",
                    type: "checkbox", 
                    separator: "|", 
                    options: [
                        { label: '', value: true}
                    ]  
                },
                { 
                    label: "AdClip",  
                    name: "AdClip",
                    type: "checkbox", 
                    separator: "|", 
                    options: [
                        { label: '', value: true}
                    ]                       
                },
                { label: "Ventral", name: "VentralClip" },
                { label: "RTChannel", name: "RTChannel" }, 
                { label: "RTCode", name: "RTCode" }, 
                { label: "Brightness", name: "Brightness", type: "select" },
                { 
                    label: "Tube", 
                    name: "Tube",
                    type: "checkbox", 
                    separator: "|", 
                    options: [
                        { label: '', value: true}
                    ]  
                }, 
                { 
                    label: "Drax",  
                    name: "Drax",
                    type: "checkbox", 
                    separator: "|", 
                    options: [
                        { label: '', value: true}
                    ]  
                },               
                { label: "Operator", name: "Operator" }, 
                { label: "QC Name", name: "QC_ModifiedBy", type: "readonly", def: settings.username},
                { label: "QC Comment" , name: "QC_Comments"},
                { label: "HPID", name: "HPID" },
                { label: "SampleSourceID",  name: "SampleSourceID" }
            ]
                   
                   
               

        })

        editor.field('ID').hide(); 
        editor.field('HPID').hide();
        editor.field('SampleSourceID').hide();
        editor.field('QC_Comments').set('poop');

        // $('#advanced-filter').on('click', function(e) {
        //     //alert("I am an alert box!");
        // })

        
        


        $('#qc-log-table').on('click', 'a.editor_edit', function(e) {
            e.preventDefault(); 

            
            //editor.hide('QC_Comments')
            // editor.field('QC_ModifiedBy').val("blank")
            // editor.field('QC_Comments').val("");

            // editor.field('QC_Comments').multiSet( 'blank')

            
                          
            editor.edit($(this).closest('tr'), {
                title: 'Edit record', 
                buttons: [{ label: 'Cancel', fn: function () { this.close(); } }, 'Update',]
            });

            
        });
        
        /*

        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                //var min = parseFloat( $('#edit-forklen').val(), 10 );
                //var max = parseFloat( $('#edit-forklento').val(), 10 );
                //var forklen = parseFloat( data[9] ) || 0; // use data for the age column
                //var pohlgth = parseFloat( data[10] ) || 0; 
                //var mehlgth = parseFloat( data[])

                //console.log(data); 
                return filter(data); 
                // if ( ( isNaN( min ) && isNaN( max ) ) ||
                //      ( isNaN( min ) && age <= max ) ||
                //      ( min <= age   && isNaN( max ) ) ||
                //      ( min <= age   && age <= max ) )
                // {
                //     return true;
                // }
                //     return false;
            }
        );


        function evaluateValue(minValue, maxValue, data) {
                if ( 
                     ( isNaN( minValue ) && isNaN( maxValue ))  ||  ( isNaN( minValue ) && data <= maxValue ) ||
                     ( minValue <= data   && isNaN( maxValue )) ||   ( minValue <= data   && data <= maxValue )
                    )
                                        
                { return true; }
                    
                                  
                return false;
        }



        function filter(data) {
            
            //Evaluate Fork Length
            var forkLength =  evaluateValue( 
                               parseFloat( $('#edit-forklen').val(), 10 ), 
                               parseFloat( $('#edit-forklento').val(), 10 ), 
                               parseFloat( data[9]) 
                              );
            
            //Evaluate Poh Length
            var pohLength = evaluateValue( 
                                parseFloat( $('#edit-pohlen').val(), 10 ), 
                                parseFloat( $('#edit-pohlento').val(), 10 ), 
                                parseFloat( data[10]) 
                            );
            //Evalueate meh Length 
            var mehLength = evaluateValue(
                                parseFloat( $('#edit-weight-from').val(), 10 ), 
                                parseFloat( $('#edit-weight-to').val(), 10 ), 
                                parseFloat( data[12]) 
                            );

            var sppCode = $('#edit-species--2').val() == 'All' ?  true: data[4] == $('#edit-species--2').val(); 
            var ladCode = ($('#edit-facility--2').val() == data[36]) ||  ($('#facility').val() == data[36]) ? true: false; 

            $("#facility").val($('#edit-facility--2').val());
            console.log(data)
            console.log( data[36])
            console.log( $('#facility').val() )
            if (forkLength && pohLength && mehLength && sppCode && ladCode) {
                return true
            }

            return false; 
                            

            // var min = parseFloat( $(minValue).val(), 10 );
            // var max = parseFloat( $(minValue).val(), 10 );
            // var data = parseFloat( dataArray[9] );

            // return evaluateValue(min, max, data);

            

        }

        $('#button-filter').on('click', function(){
            $("#facility").val($('#edit-facility--2').val());
        })


        function evaluateMehLength(minValue, maxValue, data) {

        } */

      

        var facilityCode = $("#facility").val();
        var species = $("#edit-species").val(); 
        var from = $("#qa-demo-from").val(); 
        var to = $("#qa-demo-to").val(); 
       

        $('#edit-facility--2').val($("#facility").val());
        $('#edit-species--2').val($("#edit-species").val()); 
        $('#qa-demo-from-modal').val($("#qa-demo-from").val()); 
        $('#qa-demo-to-modal').val($("#qa-demo-to").val());



        //Dynamic selections that change when you run the advanced query. 

        $('#facility').on('change', function() {
            $('#edit-facility--2').val($("#facility").val());
            
        })

        $('#edit-species').on('change', function() {
            $('#edit-species--2').val($("#edit-species").val()); 
        })

        $('#qa-demo-from').on('change', function() {
            $('#qa-demo-from-modal').val($("#qa-demo-from").val()); 
        })

        $('#qa-demo-to').on('change', function(){
            $('#qa-demo-to-modal').val($("#qa-demo-to").val());
        })

        // $('#edit-facility--2').on('change', function(){
        //     $("#facility").val($('#edit-facility--2').val());
        // })

        // $('#edit-species--2').on('change', function(){
        //     $("#edit-species").val($('#edit-species--2').val());
        // })

        // $('#qa-demo-from-modal').on('change', function() {
        //     $("#qa-demo-from").val($('#qa-demo-from-modal').val());
        // })


        // $('#qa-demo-to-modal').on('change', function() {
        //     $("#qa-demo-to").val($('#qa-demo-to-modal').val()); 
        // })

        editor.field('QC_ModifiedBy').disable();

        


        $('#edit-reset').on('click', function(){
            //$('#facility').val('ca'); 
            $('#edit-species').val("All")
            $('#qa-demo-from').val(new Date(new Date()
                    .setDate(new Date()
                    .getDate() - 7))
                    .toLocaleDateString("en-US", {month: '2-digit', day: '2-digit', year: 'numeric'}))
                    
                    
            
            $('#qa-demo-to').val(new Date(Date.now()).toLocaleDateString("en-US", {month: '2-digit', day: '2-digit', year: 'numeric'}))
        })

       

        
        
        url = urlPrefix+"/api/TrapSamples/GetTrapSamples?facilityCode="
        +facilityCode+"&SppCode="
        +species+"&startdate="
        +from+"&enddate="
        +to+"&order=acending"

        var display; 
         

        console.log(url)

        var table = $('#qc-log-table').DataTable( {
            dom: "Bfrtip",
            pageLength: 25,
            //autoWidth: false,
            //scrollX: true,
            //   "info" : false,
            //   "paging": false,
            //   "autoWidth": true,
            //   "scrollX" : false,
            //   "processing": true,
            rowId: 'HPID',
            ajax: {
                url: urlPrefix+"/api/TrapSamples/GetTrapSamples",
                /*?facilityCode="
                +facilityCode+"&SppCode="
                +species+"&startdate="
                +from+"&enddate="
                +to+"&order=acending",*/
                type: "POST",
                contentType: 'application/json',
                data: function(data) {

                    display = data;  

                    data.facilityCode =  $("#facility").val();
                    data.SppCode = $("#edit-species").val(); 
                    data.startdate = $("#qa-demo-from").val(); 
                    data.enddate = $("#qa-demo-to").val(); 
                    
                    // data.forkLengthFrom = parseFloat( $('#edit-forklen').val(), 10 ) 
                    // data.forkLengthTo = parseFloat( $('#edit-forklento').val(), 10 )
                    // data.weightFrom = parseFloat($('#edit-weight-from').val(), 10)
                    // data.weightTo = parseFloat($('#edit-weight-to').val(), 10)

                    data.forkLengthFrom = $('#edit-forklen').val()
                    data.forkLengthTo = $('#edit-forklento').val()
                    data.weightFrom = $('#edit-weight-from').val()
                    data.weightTo = $('#edit-weight-to').val()

                    data.DNASample = $('#edit-dnasample').val();
                    data.PitTag = $('#edit-pittag').val();
                    data.JvPitTag = $('#edit-jvpittag').val(); 

                    data.pohLengthFrom = $('#edit-pohlen').val(); 
                    data.pohLengthTo = $('#edit-pohlen-to').val();
                   
                    data.Adclip = $('#edit-adclip:checked').val() === undefined ? false : true 

                    data.CWT = $('#edit-cwtsnout:checked').val() === undefined ? false : true

                    data.startTime = $('#qa-demo-timefrom-modal').val(); 
                    data.endTime = $('#qa-demo-timeto-modal').val();                

                    data.Comment = $('#edit-comment').val();

                    data.Sex = $('#edit-sex').val(); 
                    data.Status = $('#edit-status').val(); 
                    data.Origin = $('#edit-origin').val(); 


                    //Call Display function here and put it in the correct spot. 
                    displaySelections(data)



                    console.log(data)

                    return JSON.stringify(data); 
                }
            },

            scrollX: true,
            info: false,
            searching: true,
            retrieve: true,
            autoWidth: false,
            
            
            order: [
                //[ 3, 'asc' ],
                [4, 'asc'],
                [5, 'asc'],
                [6, 'asc']

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

                        if(year < 2010) {
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
                { data: "LadCode", editField: "LadCode" },
                { data: "PassDate" },
                { data: "PassTime" },
                { data: "SppCode", editField: "SppCode" },  
                { data: "CarcassNo" },             
                { data: "PitTag"},
                { data: "JvPitTag"}, 
                { data: "Labels.Status", editField: "Status", defaultContent: ""},
                { data: "Labels.Origin", editField: "Origin", defaultContent: ""},
                { data: "Age"},
                { data: "Labels.Sex", editField: "Sex", defaultContent: ""}, 
                { data: "Forklgth"},
                { data: "Pohlgth"},
                { data: "Mehlgth"}, 
                { 
                    data: "Weight",
                    render: function( data, type, row) {
                        if(data == null)
                            return data 
                        else 
                            return data.toFixed(2)
                    }
                }, 
                { data: "Scalesmpl"}, 
                { data: "DNASample"}, 
                { data: "Injection"}, 
                { data: "Comments"},
                { data: "CWT",
                    render: function(data, type, row) {
                        if( type === 'display') {
                            return '<input type="checkbox"  class="editor-cwt">';
                        }
                    
                        
                        if(data != true){
                            data = false
                        }

                        
                        return data;
                    }, 
                    className: 'dt-body-center'}, 
                //{ data: "CWTSnout"}, 
                { data: "Labels.ElastomerColor", editField: "ElastomerColor", defaultContent: ""}, 
                { data: "OCTSNT"}, 
                { data: "Labels.BodyLocation", editField: "BodyLocation", defaultContent: "" },
                { 
                    data: "Mort", 
                    render: function (data, type, row) {
                        if( type === 'display') {
                            return '<input type="checkbox"  class="editor-mort">';
                        }
                    
                        
                        if(data != true){
                            data = false
                        }

                        
                        return data;
                    }, 
                    className: 'dt-body-center'
                }, 
                { 
                    data: "Channel",
                    render: function(data, type, row) {
                        if( type === 'display') {
                            return '<input type="checkbox"  class="editor-channel">';
                        }
                    
                        
                        if(data != true){
                            data = false
                        }

                        
                        return data;
                    }, 
                    className: 'dt-body-center'
                
                }, 
                { 
                    data: "Release",
                    render: function(data, type, row) {
                        if( type === 'display') {
                            return '<input type="checkbox"  class="editor-release">';
                        }
                    
                        
                        if(data != true){
                            data = false
                        }

                        
                        return data;
                    }, 
                    className: 'dt-body-center'
                }, 
                { 
                    data: "GonadStudy",
                    render: function(data, type, row) {
                        if( type === 'display') {
                            return '<input type="checkbox"  class="editor-gonad">';
                        }
                    
                        
                        if(data != true){
                            data = false
                        }

                        
                        return data;
                    }, 
                    className: 'dt-body-center'
                }, 
                { 
                    data: "AdClip",
                    render: function(data, type, row) {
                        if( type === 'display') {
                            return '<input type="checkbox"  class="editor-adclip">';
                        }
                    
                        
                        if(data != true){
                            data = false
                        }

                       
                        return data;
                    }, 
                    className: 'dt-body-center'
                }, 
                { data: "VentralClip"}, 
                { data: "RTChannel"}, 
                { data: "RTCode"}, 
                { data: "Labels.Brightness", editField: "Brightness", defaultContent: ""}, 
                { 
                    data: "Tube",
                    render: function(data, type, row) {
                        if( type === 'display') {
                            return '<input type="checkbox"  class="editor-tube">';
                        }
                    
                        
                        if(data != true){
                            data = false
                        }

                        
                        return data;
                    }, 
                    className: 'dt-body-center'
                }, 
                { 
                    data: "Drax",
                    render: function(data, type, row) {
                        if( type === 'display') {
                            return '<input type="checkbox"  class="editor-drax">';
                        }
                    
                        
                        if(data != true){
                            data = false
                        }

                        
                        return data;
                    }, 
                    className: 'dt-body-center'
                }, 
                { data: "Operator"}, 
                { data: "QC_Comments"},
                { data: "QC_ModifiedBy", visible: false}, 
                { data: "HPID", visible: false}, 
                { data: "SampleSourceID", visible: false},
                { data: "LadCode", visible: false}
               
            ],
            select:{
                
                style:    'multi',
                selector: 'td:first-child'              
            }, 
            buttons: [
                {   extend: "create", 
                    editor: editor,
                    text: "Add",                      
                    action: function () {
                          //editor.show('numFish')
                          editor.create({
                            title: 'Add', 
                            buttons: [{ label: 'Cancel', fn: function () { this.close(); } },'Add Counts']                          
                        })        
                    }
                },
                //{ extend: "edit",   editor: editor },
                { extend: "remove", editor: editor }
            ],
            rowCallback: function ( row, data ) {
                // Set the checked state of the checkbox in the table
                //$('input.editor-active', row).prop( 'checked', data.Mort == true );
                
                $('input.editor-mort', row).prop( 'checked', data.Mort == true );
                $('input.editor-channel', row).prop( 'checked', data.Channel == true );
                $('input.editor-release', row).prop( 'checked', data.Release == true );
                $('input.editor-tube', row).prop( 'checked', data.Tube == true );
                $('input.editor-drax', row).prop( 'checked', data.Drax == true );
                $('input.editor-gonad', row).prop( 'checked', data.GonadStudy == true );
                $('input.editor-adclip', row).prop('checked', data.AdClip == true);
                $('input.editor-cwt', row).prop('checked', data.CWT == true);
                
            },
            createdRow: function( row, data, dataIndex ) {
                //console.log(row)               
                var year = new Date(data["PassDate"]).getFullYear()
                //console.log(year)
                if(year < 2010) {
                    console.log($(row).children())
                    //jQuery Navigation to the first child of the node list and remove the class
                    $(row).children().removeClass("select-checkbox")

                }
                
                

            }
        } ); 

        $("#button-filter").on("click", function(){
            //table.draw(); 
            /*
            $("#facility").val($('#edit-facility--2').val());
            $("#edit-species").val($('#edit-species--2').val()); 
            $("#qa-demo-from").val($('#qa-demo-from-modal').val()); 
            $("#qa-demo-to").val($('#qa-demo-to-modal').val()); 
            */

            
            // if($("#facility").val() != 'ca')
            //     alert("Facility not Ca") 
                
            $("#facility").val($('#edit-facility--2').val())
            $("#edit-species").val($('#edit-species--2').val()); 
            $("#qa-demo-from").val($('#qa-demo-from-modal').val()); 
            $("#qa-demo-to").val($('#qa-demo-to-modal').val()); 

            var returnString = ""; 
            var startDate = new Date($('#qa-demo-from-modal').val())
            var endDate = new Date($('#qa-demo-to-modal').val())

            //Check Date range
            if(startDate > endDate) 
                returnString += "Date Range not allowed\n"; 
            
            //Check Forklen
            console.log($('#edit-forklen').val())
            if(($('#edit-forklen').val() != "" && $('#edit-forklento').val() != "") && 
               ($('#edit-forklen').val() > $('#edit-forklento').val()))
               returnString += "Fork length range not allowed\n"
            if(!validateFloat($('#edit-forklen').val()) || !validateFloat($('#edit-forklento').val()))
               returnString += "Invalid Fork length number\n"
 
            if(($('#edit-pohlen').val() != "" && $('#edit-pohlento').val() != "") && 
               ($('#edit-pohlen').val() > $('#edit-pohlento').val()))
               returnString += "Fork length range not allowed\n"
            if(!validateFloat($('#edit-pohlen').val()) || !validateFloat($('#edit-pohlento').val()))
               returnString += "Invalid Poh length number\n"

            if(($('#edit-weight-from').val() != "" && $('#edit-weight-to').val() != "") && 
               ($('#edit-weight-from').val() > $('#edit-weight-to').val()))
               returnString += "Fork length range not allowed\n"
            if(!validateFloat($('#edit-weight-from').val()) || !validateFloat($('#edit-weight-to').val()))
               returnString += "Invalid weight number\n"
           
            

            


            if(returnString != "")
                alert(returnString); 
            else {
                $('#myModal').modal('toggle'); 
                table.ajax.reload();
            }

            

            console.log(startDate);
            console.log(endDate) 

            
        })

        /*
        $('#qa-demo-to-modal').on('change', function() {
            $("#qa-demo-to").val($('#qa-demo-to-modal').val()); 
        })*/
        $("#button-clear").on("click", function(){


             
                    //$("#facility").val("ca"); 

                    // $('#edit-facility--2').val("ca");
                    
                    $("#edit-species").val("All"); 

                    $('#edit-species--2').val("All"); 
            
                    $('#qa-demo-from').val(new Date(new Date()
                                                        .setDate(new Date()
                                                        .getDate() - 7))
                                                        .toLocaleDateString("en-US", {month: '2-digit', day: '2-digit', year: 'numeric'}))
                    
                    
            
                    $('#qa-demo-to').val(new Date(Date.now()).toLocaleDateString("en-US", {month: '2-digit', day: '2-digit', year: 'numeric'}))
                    
                    $('#qa-demo-to-modal').val($("#qa-demo-to").val());

                    $('#qa-demo-from-modal').val($("#qa-demo-from").val());
                   
            
                    
                    $('#edit-forklen').val("");  
                    $('#edit-forklento').val("")
                    $('#edit-weight-from').val("")
                    $('#edit-weight-to').val("")

                    $('#edit-dnasample').val("")
                    $('#edit-pittag').val("")
                    $('#edit-jvpittag').val(""); 

                    $('#edit-pohlen').val(""); 
                    $('#edit-pohlen-to').val("");
                   
                    $('#edit-adclip').prop("checked", false)

                    $('#edit-cwtsnout').prop("checked", false)

                    $('#qa-demo-timefrom-modal').val(""); 
                    $('#qa-demo-timeto-modal').val("");                

                    $('#edit-comment').val("");

                    $('#edit-sex').val(""); 
                    $('#edit-status').val(""); 
                    $('#edit-origin').val(""); 
                    
        })

        $('#example').on( 'change', 'input.editor-active', function () {
            editor
                .edit( $(this).closest('tr'), false )
                .set( 'active', $(this).prop( 'checked' ) ? true : false )
                .submit();
        } );

        editor.on('preSubmit', function(e,o, action){
            if( action !== 'remove') {

                var ForkLength = this.field('Forklgth'); 
                var PohLength = this.field('Pohlgth');
                var MehLength = this.field('Mehlgth');
                var Weight = this.field('Weight');
                var Ventral = this.field('VentralClip');
                var PassTime = this.field('PassTime');
                var CarcassNo = this.field('CarcassNo'); 
                var PitTag = this.field('PitTag'); 
                var JvPitTag = this.field('JvPitTag'); 
                var Age = this.field('Age'); 
                var Scalesmpl = this.field('Scalesmpl'); 
                var DNASample = this.field('DNASample'); 
                var Injection = this.field('Injection'); 
                var OCTSNT = this.field('OCTSNT'); 
                var RTChannel = this.field('RTChannel'); 
                var RTCode = this.field('RTCode'); 
                var Operator = this.field('Operator'); 
                var PassDate = this.field("PassDate"); 



                if(ForkLength !== undefined && !validateFloat(ForkLength.val()))
                    ForkLength.error('Invalid Fork length format')
                }

                if(PohLength !== undefined && !validateFloat(PohLength.val())){
                    PohLength.error('Invalid Poh Length format')
                }

                if(MehLength !== undefined && !validateFloat(MehLength.val())){
                    MehLength.error('Invalid Meh length format')
                }

                if(Weight !== undefined && !validateFloat(Weight.val())){
                    Weight.error('Invalid Weight length format')
                }

                if(PassTime !== undefined && !validateTime(PassTime.val()))
                    PassTime.error('Invalid time format')

                if(Ventral !== undefined && validateLength(Ventral.val(), 1))
                    Ventral.error('Ventral Clip is 1 character?')

                if(CarcassNo !== undefined && validateLength(CarcassNo.val(), 50))
                    CarcassNo.error("Invalid character length. Carcass number can't be more than 50 characters")
                
                if(PitTag !== undefined && validateLength(PitTag.val(), 14))
                    PitTag.error("Invalid character length. Pittag can't be more than 14 characters")

                if(JvPitTag !== undefined && validateLength(JvPitTag.val(), 14))
                    JvPitTag.error("Invalid character length. JvPitTag can't be more than 14 characters")

                if(Age !== undefined && validateLength(Age.val(), 3))
                    Age.error("Invalid character length. Age can't be more than 3 characters")

                if(Scalesmpl !== undefined && validateLength(Scalesmpl.val(), 10))
                    Scalesmpl.error("Invalid character length. Scale sample can't be more than 10 characters")
                
                if(DNASample !== undefined && validateLength(DNASample.val(), 9))
                    DNASample.error("Invalid character length. DNASample can't be more than 10 characters")

                if(Injection !== undefined && validateLength(Injection.val(), 10))
                    Injection.error("Invalid character length. Injection can't be more than 10 characters")

                if(OCTSNT !== undefined && validateLength(OCTSNT.val(), 3))
                    OCTSNT.error("Invalid character length. OCTSNT can't be more than 3 characters")
                
                if(RTChannel !== undefined && validateLength(RTChannel.val(), 255))
                    RTChannel.error("Invalid character length. Radio Tag channel can't me more than 255 characters")
                
                if(RTCode !== undefined && validateLength(RTCode.val(), 255))
                    RTCode.error("Invalid character length. Radio Tag Code can't be more than 255 characters")

                if(Operator !== undefined && validateLength(Operator.val(), 10))
                    Operator.error("Invalid character length. Operator name can't be more than 10 characters")

                // if(new Date(PassDate).getFullYear() < 2017) 
                //     Operator.error("Cannot edit Data from before 2017")
                    
                //if(ForkLength !== undefined && )
                
                //console.log(Ventral.val().length)

                


                if ( this.inError() ) {
                    return false;
                }


            })

        function displaySelections (data) {
            

            var returnString = '' 

            // returnString += ', Facility Code: ' + data.facilityCode
            // returnString += ', Species Code: ' + data.SppCode
            // returnString += ', Date Range: ' + data.startdate + ' - ' + data.enddate

            if(data.Adclip == true) 
                returnString += ', Adclip: True'
           
            if(data.CWT == true) 
                returnString += ', CWT: True'

            if(data.Comment != "") 
                returnString += ', Comment: ' + data.Comment
            
            if(data.DNASample != "") 
                returnString += ', DNASample: ' + data.DNASample

            if(data.PitTag != "")
                returnString += ', PitTag: ' + data.PitTag

            if(data.JvPitTag != "")
                returnString += ', JvPitTag: ' + data.JvPitTag

            if(data.Origin != "")
                returnString += ', Origin: ' + $('#edit-origin').find(":selected").text()

            if(data.Status != "")
                returnString += ', Status: ' + $('#edit-status').find(':selected').text() 

            if(data.Sex != "")
                returnString += ', Sex: ' + $('#edit-sex').find(':selected').text()

            if(data.startTime != "" && data.endTime != "")
                returnString += ', PassTime: ' + data.startTime + ' - ' + data.endTime
            else if (data.startTime == "" && data.endTime != "")
                returnString += ', PassTime: <=' + data.endTime 
            else if (data.startTime != "" && data.endTime == "") 
                returnString += ', PassTime: >=' + data.startTime


            if(data.forkLengthFrom != "" && data.forkLengthTo != "")
                returnString += ', ForkLength: ' + data.forkLengthFrom + ' - ' + data.forkLengthTo
            else if(data.forkLengthFrom == "" && data.forkLengthTo != "" )
                returnString += ', ForkLength: <=' + data.forkLengthTo
            else if(data.forkLengthFrom != "" && data.forkLengthTo == "")
                returnString += ', Forklength: >=' + data.forkLengthFrom
        
            if(data.weightFrom != "" && data.weightTo != "") 
                returnString += ', Weight: ' + data.weightFrom + ' - ' + data.weightTo
            else if(data.weightFrom == "" && data.weightTo != "")
                returnString += ', Weight: <=' + data.weightTo
            else if (data.weightFrom != "" && data.weightTo == "")
                returnString += ', Weight: >=' + data.weightFrom


            if(data.pohLengthFrom != "" && data.pohLengthTo) 
                returnString += ', PohLength: ' + data.pohLengthFrom + ' - ' + data.pohLengthTo
            else if(data.pohLengthFrom == "" && data.pohLengthTo != "")
                returnString += ', PohLength: <=' + data.pohLengthTo
            else if(data.pohLengthFrom != "" && data.pohLengthTo == "")
                returnString += ', PohLength: >=' + data.pohLengthFrom


            
            
            
            
                // if(data.)

           console.log(data)
           

            returnString = returnString.substr(2); 

            $('#filter-output').html(returnString); 
            
        }


        

/********************************************************************************************************************************
 *                                              Validation Functions 
 * 
 *                  
 * 
 ********************************************************************************************************************************/
        
        function validateFloat(value){
            var match = /^\d+(\.\d+)?$/
            console.log(value)
            
            if (value === undefined || value.match(match) || value == "")
                return true
            else 
                return false
        }


        function validateInteger(value){
            var match = /^(0|([1-9]\d*))$/;

            if(value.match(match))
                return true
            else 
                return false
        }

        function validateTime(strTime) {
            var t1 = /^(1[0-2]|0?[1-9]):([0-5]?[0-9])([\s]?[AP]M)?$/;
            var t2 = /^(2[0-3]|[01]?[0-9]):([0-5]?[0-9])$/;
            var match; 

            if(strTime.match(t1) || strTime.match(t2) || strTime == "")
                return true
             else 
                return false
            
                
            //return match; 
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