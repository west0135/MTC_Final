//globals
var g_current_class_name = null;
var g_canned_query_json;
var g_current_schema_json;
var current_getList_method_name;
var g_isInsert;
var g_isClone;

var g_userid;
var g_ukey;
var g_permissions;
var g_json_labels;
var g_isDevMode = false;

//List blocking
var STANDARD_COUNT = 10;
var g_current_list_count;
var g_last_block_start;
var g_isUpdating = false;
var g_hasContent;

//Lookup dialog MtcMemberSafe search
//g_current_target = null;

//Permissions TODO get from server if possible
var NONE = 0;
var MEMBERSHIP = 1; 		
var RESERVATION = 2; 		
var COURT_CAPTAIN = 3;	
var EDITOR = 4; 	
var ADMIN = 5;		

$(document).ready(function ()
{
	$.support.cors = true;

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//       Tiny Mice HTML editer ver 4.1.7
//
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	tinymce.init({
		height : 600,
		selector: "textarea.tinymce-enabled",
		//selector: "textarea",
		theme: "modern",
		plugins: [
			"advlist autolink lists link image charmap print preview hr anchor pagebreak",
			"searchreplace wordcount visualblocks visualchars code fullscreen",
			"insertdatetime media nonbreaking save table contextmenu directionality",
			"emoticons template paste textcolor colorpicker textpattern jbimages"
		],
		convert_urls: false,
		relative_urls: false,
		toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image jbimages",
		toolbar2: "print preview media | forecolor backcolor emoticons",
		image_advtab: true,
		templates: [
			{title: 'Test template 1', content: 'Test 1'},
			{title: 'Test template 2', content: 'Test 2'}
		]
	});

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//       Tiny Mice HTML editer
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	hide("#tinyMice"); //Hide Editor to start
	
	g_isDevMode = !$('#showFieldValuesStatus').is(':checked');
	$('#showFieldValuesStatus').change(function() {
		g_isDevMode = !$(this).is(':checked');
    });

	//Set the standard count
	$("#block_count").val(STANDARD_COUNT);
	g_last_block_start = $("#block_start").val();
	
	//Get the label json
	getLabels();
	
	// Retrieve
	g_userid = localStorage.getItem("g_userid");
	g_ukey = localStorage.getItem("g_ukey");
	g_permissions = localStorage.getItem("g_permissions");
	
	$("#login_dialog").dialog(
	{
		autoOpen: false,
		buttons: [
		{
		  text: "Ok",
		  icons: {
			primary: "ui-icon-heart"
		  },
		  type: "submit",
		  form: "login_form", // <-- Make the association
		  click: function(ev)
		  {
			ev.preventDefault();
			$( this ).dialog( "close" );
			var myEmail = $("#my_email").val();
			var myPassword = $("#pswd").val(); 
			var data = {func: 'loginUser',email: myEmail,password: myPassword};
			loginServer(LOGIN_SERVER, data, function(json)
			{
				var msg = "SUCCESS" + JSON.stringify(json);
				//alert(msg.substring(0, 2500)); //Limit the string size
				g_userid = json.userid;
				g_ukey = json.ukey;
				g_permissions = json.permissions;
				
				if(g_permissions < COURT_CAPTAIN)
				{
					//alert("You must have at least COURT CAPTAIN permissions for these operations");
					openMessageBox("You must have at least COURT CAPTAIN permissions for these operations");
					return;
				}

				localStorage.setItem("g_userid", g_userid);
				localStorage.setItem("g_ukey", g_ukey);
				localStorage.setItem("g_permissions", g_permissions);
				
				$("#cheapLogOut").attr("data-status", "logged_in");
				$("#cheapLogOut").html("Log Out");
				
				show("#pageTypes");
				
			}, function(e)
			{
				//alert("ERROR logging in");
				openMessageBox("ERROR Logging in");
			});
		  }
		}
	  ]
	});
	
	$("#image_look_up_dialog" ).dialog(
	{
		autoOpen: false,
		resizable: true,
		modal:true,
		title: "Look Up",
		maxWidth: 800,
		minWidth: 600,
		buttons:{
			"Okay":function () {
				$(this).dialog('close');
			}
		}
	});

	//////////////////////////////// initialize after $("#login_dialog") /////////////////
	if(!g_ukey)
	{
		hide("#pageTypes");
		hide("#itemTypeLabel");
		
		show("#option_area");
		
		$("#cheapLogOut").attr("data-status", "logged_out");
		$("#cheapLogOut").html("Log In");
		
		show("#login_dialog");
		$("#login_dialog").dialog( "open" );
	}
	else
	{
		show("#pageTypes");
		
		hide("#option_area");
		$("#cheapLogOut").attr("data-status", "logged_in");
		$("#cheapLogOut").html("Log Out");
	}
	
	//Log Out
	$("#cheapLogOut").click(function(e)
	{
		e.preventDefault();
		var status = e.target.dataset.status;
		if("logged_out" == status)
		{
			hide("#option_area");
			$("#login_dialog").dialog( "open" );
		}
		else
		{
			var data = {func: 'logoutUser',ukey: g_ukey, userid: g_userid};
			loginServer(LOGIN_SERVER, data, function(json)
			{
				if(json.status == 1)
				{
					localStorage.removeItem("g_userid");
					localStorage.removeItem("g_ukey");
					localStorage.removeItem("g_permissions");
					g_userid = null;
					g_ukey = null;
					g_permissions = null;
					//openMessageBox("Logged Out");
					$("#cheapLogOut").attr("data-status", "logged_out");
					$("#cheapLogOut").html("Log In");
					
					//Close All Windows
					hide("#fields_list_container");
					hide("#editArea");
					hide("#createItem");
					
					hide("#pageTypes");
					hide("#itemTypeLabel")
					show("#option_area");
					$("#pswd").val("");
				}
				else
				{
					openMessageBox("ERROR Logging Out");
				}
				
			}, function(e)
			{
				//alert("ERROR logging in");
				openMessageBox("ERROR Logging Out");
			});
		}
	});
	
	//look_up_dialog
	$( "#look_up_dialog" ).dialog(
	{
		autoOpen: false,
		maxWidth: 800,
		minWidth: 200,
		buttons: [
		{
		  text: "Cancel",
		  icons: {
			primary: "ui-icon-heart"
		  },
		  type: "submit",
          form: "login_form", // <-- Make the association
		  click: function(ev)
		  {
			ev.preventDefault();
			$( this ).dialog( "close" );
		  }
		}
	  ]
	});
	
	$("#look_up_fields_list").click(function(e)
	{
		if("look_up_fields_list" != e.target.id)
		{
			//alert("set value:" + e.target.id + " to input id:" + e.target.dataset.target);
			$("#inputForm #" + e.target.dataset.target).val(e.target.id);
			$( "#look_up_dialog" ).dialog( "close" );
		}
	});

	//Pop up a Look Up dialog
	$("#inputForm").click(function(e)
	{
		e.preventDefault();
		if("inputForm" != e.target.id && e.target.type == "button")
		{
			$("#look_up_fields_list").html("Loading: " + e.target.id + " list");
			$( "#look_up_dialog" ).dialog( "open" );
			//alert("Pop Up a " + e.target.id + " Dialog");
			//Get the class name for lookup and input id to write the value to
			drawLookUpList(e.target.id, e.target.dataset.target);		
		}
	});
	
	//Get the next block of items to display
	$("#getNextBlock").click(function(e)
	{
		g_isUpdating = false;
		drawListForClass(g_current_class_name);
	});
	
	$("#setMemberPermission").click(function(e)
	{
		var $id = $("#test_member_id").val();
		var $my_permission = $("#test_permissions").val();
		var strUrl = JSON_API_URL;
		var data = { method:"MtcCourtReservationHelper.setMemberPermission",
					 member_id: $id, permissions: $my_permission};
		data['userid'] = g_userid;
		data['ukey'] = g_ukey;
		postData(strUrl, data, function(json)
		{
			alert(JSON.stringify(json).substring(0, 2500)); //Limit the string size
		}, error);
	});
	
	//Cancel
	$("#cancelButton").click(function(e)
	{
		e.preventDefault();
		show("#fields_list_container");
		hide("#editArea");
		show("#createItem");
		
		hide("#image_look_up_container");
	});
	
	//Open up a new window with the ATA Programs Catalog
	$("#AtaProgramCollection").click(function(e)
	{
		window.open(DOMAIN + "/to_be/ata/Collections.php?class=AtaProgramCollection");
		var strUrl = JSON_API_URL;
		var data = { method:"AtaProgramCollection.getList"};
		postData(strUrl, data, function(json)
		{
			//alert(JSON.stringify(json).substring(0, 2500)); //Limit the string size
		}, error);
	});
	
	//Put test code in here
	$("#showTestResults").click(function(e)
	{
		g_userid = 45;
		g_ukey = "goaxjwur9g6QD";
		g_permissions = 5;
	});
	
	//Draw List of items for selected Class
	$("#pageTypes").click(function(e)
	{
		//First Hide the canned query button
		hideInline("#mtc_member_first_name_btn");
		hideInline("#query_form_input_container");
		
		hide("#image_look_up_container");

		if(!g_permissions)
		{
			$("#login_dialog").dialog( "open" );
			return;
		}
		if(g_permissions < COURT_CAPTAIN)
		{
			//alert("You must have at least COURT CAPTAIN permissions for these operations");
			openMessageBox("You must have at least COURT CAPTAIN permissions for these operations");
			return;
		}
		//Show the create button
		show("#createItem");
		hide("#editArea");
		show("#fields_list_container");
		show("#itemTypeLabel");

		id = e.target.id;
		page_type = id;
		$("#itemTypeLabel").html("Item Class Name: " + page_type);
		
		//Reset the block_start to 0
		$("#block_start").val(0);
		
		$("#createItem").html("Create a New " + page_type);
		g_isUpdating = false;
		drawListForClass(page_type);
	});

	//Add a new Class Item
	$("#addItem").click(function(ev)
	{
		ev.preventDefault();
		var validateForm = true; 
		if(validateForm)
		{
			var form = ev.target.form;
			if(form.checkValidity())
			{
				addCurrentItem();
			}
			else
			{
				var foundLabel = getEmptyField(g_current_schema_json, g_json_labels);
				if(!foundLabel)
				{
					//alert("Missing Mandatory Field");
					openMessageBox("Missing Mandatory Field");
				}
				else
				{
					//alert("Missing Mandatory Field: " + foundLabel);
					openMessageBox("Missing Mandatory Field: " + foundLabel);
				}
			}
		}
		else
		{
			addCurrentItem();
		}
	});
	
	//Create a new item for current class
	$("#createItem").click(function(e)
	{
		g_isInsert = true;
		g_isClone = false;

		e.preventDefault(); //stop form validation ?
		hide("#fields_list_container");
		show("#editArea");
		hide("#createItem");
		drawBlankFormForCurrentClass();
	});
	
	$("#fields_list").click(function(e)
	{
		e.preventDefault();
		id = e.target.id;
		if(id && id !== "fields_list")
		{
			var mode = e.target.dataset.mode;
			if(mode == "edit")
			{
				g_isInsert = false;
				g_isClone = false;
				getDataAndDrawUpdateForm(id);
			}
			else if(mode == "clone")
			{
				getDataAndDrawUpdateForm(id);
				g_isClone = true;
			}
			else if(mode == "delete")
			{
				var dataLabel = e.target.dataset.label;
				deleteForCurrentClassAndId(id, dataLabel);
			}
		}
	});
	
	$("#canned_query_submit").click(function(e)
	{
		var strUrl = JSON_API_URL;
		var my_method = "MtcMemberSecure.search";
		var my_first_name = $("#mtc_member_first_name_input").val();
		var data = { method:my_method, first_name:my_first_name};
		postData(strUrl, data, function(json)
		{
			//alert(JSON.stringify(json).substring(0, 2500)); //Limit the string size
			drawListUsing(json);
		}, error);
	});
	
	$("#image_look_up").click(function(e)
	{
		e.preventDefault();
		openImageLookUp();
	});
	
	$("#search_by_first_name").click(function(e)
	{
		drawFirstNameList(name);
	});
});

////////////////////////////////  drawLookupList Moved to profile.js //////////////////////

////////////////////////////////////////  Draw First Name Search moved to profile.js /////////////////////////

///////////////////////////////////////////  Get the Label json ///////////////////////////

function getLabels()
{
	if(!g_json_labels)
	{
		var strUrl = JSON_API_URL;
		var data = { method:"Utils.getLabels", language:"en"};
		postData(strUrl, data, function(json)
		{
			g_json_labels = json;
			//alert(JSON.stringify(json).substring(0, 2500)); //Limit the string size
		}, error);
	}
}

////////////////////////////////////////////  Delete class using id ////////////////////////

function deleteForCurrentClassAndId(id, dataLabel)
{
	openConfirmBox("Delete: " + dataLabel, deleteCallBack);
}

function deleteCallBack()
{
	var strUrl = JSON_API_URL;
	var key_name = g_current_schema_json.schema.Primary_key;
	var methodName = g_current_class_name + ".delete";
	var data = { method:methodName};
	data[key_name] = id;
	data['userid'] = g_userid;
	data['ukey'] = g_ukey;
	postData(strUrl, data, function(json)
	{
		//var msg = "Update using this DATA: " + JSON.stringify(json);
		//alert(msg.substring(0, 2500)); //Limit the string size
		g_isUpdating = true;
		drawListForClass(g_current_class_name);
	}, error);
}

////////////////////// Modal Confirm Dialog  /////////////////////
function openConfirmBox(msg, callBack) {
    
	var html = '<span class="confirm_msg">' + msg + '</span>';
	$("#dialog-confirm").html(html);

    // Define the Dialog and its properties.
    $("#dialog-confirm").dialog({
        resizable: false,
        modal: true,
        title: "Confirm Action",
		resize:"auto",
        //height: 200,
        width: 400,
        buttons: {
            "Yes": function () {
                $(this).dialog('close');
				callBack();
            },
                "No": function () {
                $(this).dialog('close');
            }
        }
    });
}

//dialog-message
////////////////////// Modal Msg Dialog  /////////////////////
function openMessageBox(msg) {
    $("#dialog-message").html(msg);

    // Define the Dialog and its properties.
    $("#dialog-message").dialog({
        resizable: false,
        modal: true,
        title: "Message",
		resize:"auto",
        //height: 200,
        width: 400,
        buttons: {
            "Okay": function () {
                $(this).dialog('close');
            }
        }
    });
}

//////////////////////////////////////////////  Image Look Up  //////////////////////////////////
function makeImageGrid(json)
{
	var html="";
	for(var i=0; i < json.fields.length; i++)
	{
		var field = json.fields[i];
		html += '<div class="lookup_img_container"><img src="' + field.url + '" alt="test" title="test"></div>';
	}
	return html;
}

function openImageLookUp()
{
	var strUrl = JSON_API_URL;
	var data = { method:"Utils.getImageUrlList"};
	postData(strUrl, data, function(json)
	{
		var html = makeImageGrid(json);
		$("#image_look_up_dialog" ).html(html);
		$("#image_look_up_dialog" ).dialog('open');
	}, error);
}
/////////////////////////////////////////////////  Draw the input Form //////////////////////////////////////

function drawBlankFormForCurrentClass()
{
	$("#inputForm").html(getFields(g_current_schema_json, g_current_class_name, g_json_labels));
	show("#actionButtons");
	if(g_hasContent)
	{
		show("#image_look_up_container");
	}
}

function getDataAndDrawUpdateForm(id)
{
	var strUrl = JSON_API_URL;
	var key_name = g_current_schema_json.schema.Primary_key;
	var methodName = g_current_class_name + ".get";
	var data = { method:methodName};
	data[key_name] = id;
	postData(strUrl, data, function(json)
	{
		//var msg = "Update using this DATA: " + JSON.stringify(json);
		//alert(msg.substring(0, 2500)); //Limit the string size
		$("#inputForm").html(setFields(g_current_schema_json, json, g_json_labels));
		
		hide("#fields_list_container");
		show("#editArea");
		hide("#createItem");
		
		if(g_hasContent)
		{
			show("#image_look_up_container");
		}

	}, error);
}



///////////////////////////////////////// get Label for Field Name /////////////////////////
//  Moved to profile.js

/////////////////////////////////////////////// Draw input Field ///////////////////////////
//  Moved to profile.js

//////////////////////////////////// Set Field values from json  ///////////////////////////////////////
//  Moved to profile.js

//////////////////////////////  Get Empty Fields   ////////////////////////////////////////////////
//  Moved to profile.js

/////////////////////////////////////  Get Field values from form  ///////////////////////////////
//  Moved to profile.js

////////////////////////////////////////////////  Add or Update ///////////////////////////////////

function addCurrentItem()
{
	var strUrl = JSON_API_URL;
	var mode = ".update";
	if(g_isInsert)
	{
		mode = ".create";
	}
	else if(g_isClone)
	{
		mode = ".create";
	}
	var methodName = g_current_class_name + mode;		
	var data = {method:methodName};
	
	data = getFormValues(data, g_current_schema_json, "#inputForm", g_isClone);
	if(!data)
	{
		openMessageBox("Missing Mandatory Values");
		return;
	}
	
	data['userid'] = g_userid;
	data['ukey'] = g_ukey;
	
	//Add values for any extra custom fields here
	if("MtcOpenDatesHelper" == g_current_class_name)
	{
		var selector = "#inputForm #end_day";
		var value = $(selector).val();
		if(value != "" && value != "undefined")
		{
			data['end_day'] = $(selector).val();
		}
	}

	postData(strUrl, data, function(json)
	{
		//alert(JSON.stringify(json).substring(0, 2500)); //Limit the string size
		//tinymce.get('txtContent').setContent("");
		show("#fields_list_container");
		hide("#editArea");
		show("#createItem");
		if(g_hasContent)
		{
			show("#image_look_up_container");
		}
		
		g_isUpdating = true;
		drawListForClass(g_current_class_name);
	}, error);
}

///////////////////////////////////////  Validate Fields ////////////////////////////////////////////
//  Moved to profile.js

//////////////////////////////////////////  List Item Helpers functions /////////////////////////////
function getSchemaThenListCurrentItems()
{
	current_getList_method_name = g_current_class_name + ".getList";
	var getSchemaMethodName = g_current_class_name + ".getSchema";
	var strUrl = JSON_API_URL;
	var data = { method:getSchemaMethodName};
	postData(strUrl, data, function(json)
	{
		g_current_schema_json = json; //Save the current schema data
		//TODO TEST AREA NOW
		listCurrentItems();
		
	}, error);
}

/*
////////// Draws a form for executing canned query
function drawQueryForm(json)
{
	$("#query_form_input_container").html("");
	var html = '<input type="hidden" id="key" value="' + json.field.key + '">';
	//Delete reservation older than %$date$date%Test",
	var strForm = json.field.form;
	var arr = strForm.split("%");
	for(var i=0; i < arr.length; i++)
	{
		//alert(arr[i]);
		var test = arr[i].split("$");
		if(test.length > 1) //An input
		{
			html += '<input id="' + test[0] + '" type="' + test[1] + '">';
		}
		else
		{
			html += '<label>' + test[0] + '</label>';		
		}
	}
	$("#query_form_input_container").html(html);
}
*/

///////////////////////////////////////////////////////// List Items //////////////////////////////////
function drawListForClass(page_type)
{
	hide("#tinyMice");
	tinymce.get('txtContent').setContent("");
	
	g_current_class_name = page_type;
	
	//////////////////////////////////////  Get Custom search data  ////////////////////////////
	//TODO remove hard coded canned_query_id = How ???
	if(g_current_class_name == "MtcMemberSecure")//class_name == "MtcMemberSecure")
	{
		var strUrl = JSON_API_URL;
		var data = { method: "CannedQuery.get", canned_query_id: 9}		
		postData(strUrl, data, function(json)
		{
			//alert(JSON.stringify(json).substring(0, 2500)); //Limit the string size
			g_canned_query_json = json;
			
			//Now Build the list
			getSchemaThenListCurrentItems();
		}, error);
	}
	else //Show no custom search
	{
		getSchemaThenListCurrentItems();
	}
}

function listCurrentItems()
{
	var strUrl = JSON_API_URL;
	var data = { method:current_getList_method_name};
	
	var my_start = parseInt($("#block_start").val());
	var my_count = parseInt($("#block_count").val());
	if(g_isUpdating) //An update operation just happened
	{
		//Unwind the counter
		my_start = g_last_block_start;
		g_isUpdating = false; //update over
		$("#block_start").val(my_start);
	}
	data['start'] = my_start;
	data['count'] = my_count;
	postData(strUrl, data, function(json)
	{
		drawListUsing(json);
	}, error);
}

function drawListUsing(json)
{
	g_hasContent = false;
	var key_name = g_current_schema_json.schema.Primary_key;
	//NOTE this gives the base public class name
	//use g_current_class_name for derived classes - see example below
	var class_name = g_current_schema_json.schema.class_name;

	//Set the block count label - shows how many items to display at a time
	$("#item_type_label").html(getLabelFor(class_name, key_name, g_json_labels) + "s");
	
	//////////////////////////////////  Canned Query section  ////////////////////////////
	$("#search_query_input").html("");
	if(g_current_class_name == "MtcMemberSecure")
	{
		showInline("#mtc_member_first_name_btn");
		showInline("#query_form_input_container");
	}
	
	///////////////////////////////// End Canned Query Section /////////////////////
	var html = "";
	$("#fields_list").html("");
	g_current_list_count = json.fields.length; 
	for(var i=0; i < json.fields.length; i++)
	{
		var schema_fields = g_current_schema_json.schema.fields;
		var field = json.fields[i];
		var str = ""; //Holds labe html ie <strong>' + field[name] + ' </strong>
		var strDataLabel = "";
		for(var j=0; j < schema_fields.length ; j++)
		{
			var name = schema_fields[j].name;
			if(g_isDevMode) //Show all field names and values for developers
			{
				str += name + "=" + field[name] + ",  ";
				strDataLabel += field[name] + " ";  
			}
			else
			{
				if("content" == name)
				{
					//str will contain all the html content from TinyMCE editor
					str += field[name];
					
					//Show the image look up
					g_hasContent = true;
				}
				else if("MtcCourtReservation" == class_name)
				{
					if("status" == name)
					{
						str += '<strong>' + field[name] + ' </strong>';
						strDataLabel += field[name] + " ";
					}
					else if("date" == name)
					{
						str += '<strong>Date: ' + field[name] + ' </strong>';
						strDataLabel += "Date: " + field[name] + " ";
					}
					else if("start_time" == name)
					{
						str += '<strong>Start: ' + field[name] + ' </strong>';
						strDataLabel += "Start: " + field[name] + " ";
					}
					else if("end_time" == name)
					{
						str += '<strong>End: ' + field[name] + ' </strong>';
						strDataLabel += "End: " + field[name] + " ";
					}
				}
				else if("MtcOpenDates" == class_name)
				{
					if("name" == name)
					{
						str += '<strong>' + field[name] + ' </strong>';
						strDataLabel += field[name] + " ";
					}
					else if("day" == name)
					{
						str += '<strong>' + field[name] + ' </strong>';
						strDataLabel += field[name] + " ";
					}
					else if("start_time" == name)
					{
						str += '<strong>Open: ' + field[name] + ' </strong>';
						strDataLabel += "Open: " + field[name] + " ";
					}
					else if("end_time" == name)
					{
						str += '<strong> to ' + field[name] + ' </strong>';
						strDataLabel += " to " + field[name] + " ";
					}
				}
				else if(name.indexOf("name") != -1)
				{
					str += '<strong>' + field[name] + ' </strong>';
					strDataLabel += field[name] + " ";
				}
			}
		}
		html += '<div class="box"><button data-mode="edit" class="editItem" data-label="' + strDataLabel + '" id = "' +  field[key_name] + '" >Edit</button>' + 
			'<button data-mode="delete" class="deleteItem" data-label="' + strDataLabel + '" id = "' +  field[key_name] + '" >Delete</button>' +
			'<button data-mode="clone" class="cloneItem" data-label="' + strDataLabel + '" id = "' +  field[key_name] + '" >Clone</button><br>' + str + '</div>';
	}
	$("#fields_list").html(html);
	
	//Calculate the position of the block counter
	var my_start = parseInt($("#block_start").val());
	var my_count = parseInt($("#block_count").val());
	//update the counter
	g_last_block_start = my_start;
	var next_start = my_start + my_count;
	if(g_current_list_count < my_count)
	{
		next_start = 0;
	}
	$("#block_start").val(next_start);
	
}

///////////////////////////////////   Utiliy Functions   //////////////////////////////////////

function logOjectProperties(data)
{
	for(var name in data) {
   		console.log(name + ": " + data[name]);
	}
}

/////////////////////////////////////  getNameValue getIdValue Moved to profile.js ////////////////////////

function postData(strUrl, data, success, error)
{
  	var my_str = "method:" + data.method;
	console.log("postData(" + strUrl + ")");
	console.log(my_str);
	console.log("data = {");
		logOjectProperties(data);
	console.log("}");
	var jqxhr = $.post(strUrl, data);
	// results
	jqxhr.done(function(json)
	{
		if(json)
		{
			var str = "status: " + json.status; 
			if(json.status == "SUCCESS")
			{
				success(json);
			}
			else
			{
				error(json);
			}
		}
		else
		{
			console.log("ERROR null json Object");
			console.log("postData(" + strUrl + ")");
			console.log(my_str);
			console.log("data = {");
			logOjectProperties(data);
			console.log("}");
		}
	});
	
	jqxhr.always(function(json)
	{
		console.log("RESPONSE");
		console.log(JSON.stringify(json));
	});
	
	jqxhr.fail(function(e)
	{
		console.log("jqxhr.fail");
		console.log("Error status: " + e.status + " Error: " + e.statusText);
		var str = my_str + " Error status: " + e.status + " Error: " + e.statusText + " responseText: " + e.responseText;
		//alert(str.substring(0, 2500)); //Limit the string size
	});

}

//TODO just Lint ?
function success(json)
{
	//alert(json.status);
	openMessageBox(json.status);
}

function error(json)
{
	var errMsg = json.errMsg + " " + json.xtndErrMsg;
	openMessageBox(errMsg.substring(0, 2500));
	console.log(json.status + " " + json.errMsg + " " + json.xtndErrMsg);
}

function show(id)
{
	$(id).removeClass("hide");
	$(id).addClass("show");
}

function hide(id)
{
	$(id).removeClass("show");
	$(id).addClass("hide");
}

function showInline(id)
{
	$(id).removeClass("hide");
	$(id).addClass("show_inline");
}

function hideInline(id)
{
	$(id).removeClass("show_inline");
	$(id).addClass("hide");
}



////////////////////////////////////////////////////    Login Server  /////////////////////////////////////////////
function loginServer(strUrl, data, success, login_error)
{
	console.log("data = {");
		logOjectProperties(data);
	console.log("}");
	var jqxhr = $.post(strUrl, data);
	// results
	jqxhr.done(function(json)
	{
		if(json)
		{
			var str = "Status: " + json.status; 
			if(json.status == "1")
			{
				success(json);
			}
			else
			{
				login_error(json);
			}
		}
		else
		{
			console.log("ERROR null json Object");
			console.log("postData(" + strUrl + ")");
			console.log(my_str);
			console.log("data = {");
			logOjectProperties(data);
			console.log("}");
		}
	});
	
	jqxhr.always(function(json)
	{
		console.log("RESPONSE");
		console.log(JSON.stringify(json));
	});
	
	jqxhr.fail(function(e)
	{
		console.log("jqxhr.fail");
		var str = "Error status: " + e.status + " Error: " + e.statusText;
		console.log(str);
		//alert(str);
		openMessageBox(str);
	});
}
