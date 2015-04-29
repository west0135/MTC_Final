//globals

var g_userid;
var g_ukey;
var g_permissions;
var g_canned_query;
var g_class_name;
var g_schema_json;
var g_json_labels;
var g_query_result_json;

var g_tbody = false; //don't rebuild header

var g_last_canned_query_data;

var g_isDevMode = false;

var g_isClone;

//Lookup dialog MtcMemberSafe search
//g_current_target = null;

$(document).ready(function ()
{
	//alert(g_canned_query_id);
	
	$.support.cors = true;
	
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
				//openMessageBox(msg.substring(0, 2500)); //Limit the string size
				g_userid = json.userid;
				g_ukey = json.ukey;
				g_permissions = json.permissions;

				if(g_permissions < COURT_CAPTAIN)
				{
					openMessageBox("You must have at least COURT CAPTAIN permissions for these operations")
					g_userid = null;
					g_ukey = null;
					g_permissions = null;
					return;
				}

				localStorage.setItem("g_userid", g_userid);
				localStorage.setItem("g_ukey", g_ukey);
				localStorage.setItem("g_permissions", g_permissions);
				
				$("#cheapLogOut").attr("data-status", "logged_in");
				$("#cheapLogOut").html("Log Out");
				show("#main_container");
				
			}, function(e)
			{
				openMessageBox("ERROR logging in");
			});
		  }
		}
	  ]
	});
	
	
	// Define the Dialog and its properties.
    $("#edit_profile").dialog({
		autoOpen: false,
		resizable: false,
		modal: true,
		//title: obj.action + " " + obj.class_name,
		resize:"auto",
		width: 720
	});

	//////////////////////////////// initialize after $("#login_dialog") /////////////////
	g_userid = localStorage.getItem("g_userid");
	g_ukey = localStorage.getItem("g_ukey");
	g_permissions = localStorage.getItem("g_permissions");

	//////////////////////////////// initialize after $("#login_dialog") /////////////////
	if(!g_ukey)
	{
		$("#cheapLogOut").attr("data-status", "logged_out");
		$("#cheapLogOut").html("Log In");
		show("#login_dialog");
		$("#login_dialog").dialog( "open" );
	}
	else
	{
		$("#cheapLogOut").attr("data-status", "logged_in");
		$("#cheapLogOut").html("Log Out");
	}

///////////////////////////////////////////////// Canned Query Selection List //////////////////
	
	//makeCannedQuerySelection()
	
	//Get the labels then draw result list
	getLabels();

	
/////////////////////////////////////////////  events  //////////////////////////////////////

	//Log Out
	$("#cheapLogOut").click(function(e)
	{
		e.preventDefault();
		var status = e.target.dataset.status;
		if("logged_out" == status)
		{
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
					$("#cheapLogOut").attr("data-status", "logged_out");
					$("#cheapLogOut").html("Log In");
					$("#pswd").val("");
					hide("#main_container");
				}
				else
				{
					openMessageBox("ERROR Logging Out");
				}
				
			}, function(e)
			{
				openMessageBox("ERROR Logging Out");
			});
		}
	});
	
	/////////////////////////////////////////  Lookup Form  ////////////////////////////////////
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
	
	$("#listItems_submit").click(function(e)
	{
		e.preventDefault();
		var buttonClicked = true;
		runCannedQuery(buttonClicked);
	});
	
	$("#tablesorter-list").delegate('tr', 'click', function(e) {
		var my_class_name = e.currentTarget.dataset.class_name;
		if(my_class_name)
		{
			//Custom class name substitution
			if(my_class_name == "MtcMember")
			{
				my_class_name = "MtcMemberSecure";
			}
			var my_primary_key_name = e.currentTarget.dataset.primary_key_name;
			var my_id = e.currentTarget.dataset.id;
			var obj = {class_name: my_class_name, primary_key_name: my_primary_key_name, id: my_id, action: "Edit"};
			//alert(msg);
			openEditSelectBox(obj, function(e)
			{
				//Edit Content
				var strUrl = JSON_API_URL;
				var key_name = g_schema_json.schema.Primary_key;
				var methodName = obj.class_name + ".get";
				var data = { method:methodName};
				data[key_name] = obj.id;
				postData(strUrl, data, function(json)
				{
					g_isClone = false;
					$("#inputForm").html(setFields(g_schema_json, json, g_json_labels));
					openEditProfile(obj);
				}, error);
			}, function(e)
			{
				//Delete Item
				var strUrl = JSON_API_URL;
				var key_name = obj.primary_key_name;
				var methodName = obj.class_name + ".delete";
				var data = { method:methodName};
				data[key_name] = obj.id;
				data['userid'] = g_userid;
				data['ukey'] = g_ukey;
				postData(strUrl, data, function(json)
				{
					//Redraw List Now
					runCannedQuery(false);
				}, error);
			}, function(e)
			{
				var strUrl = JSON_API_URL;
				var key_name = g_schema_json.schema.Primary_key;
				var methodName = obj.class_name + ".get";
				var data = { method:methodName};
				data[key_name] = obj.id;
				postData(strUrl, data, function(json)
				{
					g_isClone = true;
					$("#inputForm").html(setFields(g_schema_json, json, g_json_labels));
					openEditProfile(obj);
				}, error);
	
			});
		}
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
				var foundLabel = getEmptyField(g_schema_json, g_json_labels);
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

	//Cancel
	$("#cancelButton").click(function(e)
	{
		e.preventDefault();
		$("#edit_profile").dialog("close");
	});
	
	$("#search_by_first_name").click(function(e)
	{
		drawFirstNameList(name);
	});

});

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
			getCannedQuery();
		}, error);
	}
}

//////////////////////////////////  Get the Canned Query and Schema ////////////////////////

function getCannedQuery()
{
	var strUrl = JSON_API_URL;
	var data = { method:"CannedQuery.get", canned_query_id: g_canned_query_id};
	postData(strUrl, data, function(json)
	{
		g_canned_query = json;
		g_class_name = json.field.class_list;
		
		//Now get the schema
		var strMethod = g_class_name + ".getSchema";
		var strUrl = JSON_API_URL;
		var data = { method:strMethod};
		postData(strUrl, data, function(json)
		{
			g_schema_json = json;
			
			//Draw the selected form
			drawQueryForm(g_canned_query);
			show("#query_form_container");
			
			g_tbody = false;	 //prepare to create a new header
			
			$("#tablesorter-list").html(""); //clear out table
			hide("#tablesorter-list");
			
			g_last_canned_query_data = null;										

		},error);
	}, error);

}

//////////////////////////////////  Draw the query Form  ///////////////////////////////////

function drawQueryForm(json)
{
	$("#query_form_input_container").html("");
	var html = '<input type="hidden" id="key" value="' + json.field.key + '">';
	//Delete reservation older than %$date$date%Test",
	var strForm = json.field.form;
	var arr = strForm.split("%");
	for(var i=0; i < arr.length; i++)
	{
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

////////////////////////////////////////////////  Add or Update ///////////////////////////////////

function addCurrentItem()
{
	var strUrl = JSON_API_URL;
	var mode = ".update";
	if(g_isClone)
	{
		mode = ".create";
	}
	
	var class_name = g_class_name;
	//TODO Hack Here
	if("MtcMember" == g_class_name)
	{
		class_name = "MtcMemberSecure";
	}
	
	var methodName = class_name + mode;		
	var data = {method:methodName};
	
	data = getFormValues(data, g_schema_json, "#inputForm", g_isClone);
	if(!data)
	{
		openMessageBox("Missing Mandatory Values");
		return;
	}
	
	data['userid'] = g_userid;
	data['ukey'] = g_ukey;
	
	postData(strUrl, data, function(json)
	{
		$("#edit_profile").dialog("close");
		//Redraw the list
		runCannedQuery(false);
	}, error);
}

//////////////////////////////////////   Look Up List moved to profile.js /////////////////////////////

//////////////////////////////////  special case draw First Names List ////////////

////////////////////////////////////////  Draw First Name Search moved to profile.js /////////////////////////

//////////////////////////////////// Run Canned Query /////////////////////////////
function runCannedQuery(buttonClicked)
{
	var strUrl = JSON_API_URL;
	if(buttonClicked || !g_last_canned_query_data)
	{
		var data = { method:"CannedQueryHelper.runCannedQuery"};
		data['userid'] = g_userid;
		data['ukey'] = g_ukey;
		$("#query_form_input_container input").each(function(index, element){
			var test;
			data[element.id] = element.value;
		});
		g_last_canned_query_data = data;
	}
	postData(strUrl, g_last_canned_query_data, function(json)
	{
		//openMessageBox(JSON.stringify(json).substring(0, 2500)); //Limit the string size
		g_query_result_json = json;
			
		drawHeader();
		
		//Ready to draw table now
		drawTable();

		//TODO Could implement class list if neccessary
		/*
		var arr = g_class_list;
		if(arr.length > 0)g_schema_array = [];
		for(var i=0; i < arr.length; i++)
		{
			var className = arr[i];
			var strMethod = className + ".getSchema";
			var strUrl = JSON_API_URL;
			var data = { method:strMethod};
			postData(strUrl, data, function(json)
			{
				g_schema_array.push(json.schema);
			},error);
		}
		*/
	}, error);
}
//////////////////////////////////// Draw Header  /////////////////////////////////
function drawHeader()
{
	if(!g_tbody)
	{
		var fields = g_query_result_json.fields;
		var html = "";
		if(fields.length > 0)
		{
			html += makeHeader(fields[0]);
			html += '<tbody></tbody>';
		}
		// append new html to table 
		$("#tablesorter-list").append(html);
		g_tbody = true;
	}
}

/////////////////////////////////// Draw the table /////////////////////////////////
function drawTable()
{
	show("#tablesorter-list");
	
	$("#tablesorter-list tbody").html(""); //clear out any old rows in the body
	var fields = g_query_result_json.fields;
	var html = "";
	for(var i=0; i < fields.length; i++)
	{
		var field = fields[i];
		html += makeRow(field);
	}
	// append new html to table body 
	$("#tablesorter-list tbody").append(html);
	
	$("#tablesorter-list").tablesorter({ theme : 'blue', sortList: [[2,1],[0,0]], widgets: ['zebra'] });

	// let the plugin know that we made a update, then the plugin will
	// automatically sort the table based on the header settings
	$("#tablesorter-list").trigger("update");

	//$("#tablesorter-demo").html(html);
	//$("#tablesorter-demo").tablesorter({sortList:[[0,0],[2,1]], widgets: ['zebra']});
	//$("#options").tablesorter({sortList: [[0,0]], headers: { 3:{sorter: false}, 4:{sorter: false}}});

}

//////////////////////////////////  Draw Header //////////////////////////////////
function makeHeader(field)
{
	var html = '<thead><tr>';
	for(var name in field) {
		var label = getLabelFor(g_class_name, name, g_json_labels)
		html += '<th>' + label + '</th>';
	}
	html += '</tr></thead>';
	return html;
}

//////////////////////////////////  Draw Header //////////////////////////////////
function makeRow(field)
{
	var primary_key_name = g_query_result_json.primary_key_name;
	var class_name = g_query_result_json.class_name;
	var html = '<tr data-primary_key_name="' + primary_key_name + '" data-class_name="' + 
			class_name + '" data-id="' + field[primary_key_name] + '">';
	for(var name in field) {
		html += '<td>' + field[name] + '</td>';
	}
	html += '</tr>';
	return html;
}

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

////////////////////// Modal Confirm Dialog  /////////////////////
function openConfirmBox(msg, yesCallBack, noCallBack) {
    
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
				yesCallBack();
            },
                "No": function () {
                $(this).dialog('close');
				noCallBack();
            }
        }
    });
}

///////////////////////////////////// Open Edit Select Dialog  /////////////////////////////
//
function openEditSelectBox(obj, editCallBack, deleteCallBack, cloneCallBack)
{
	//var html = '<span>' + msg + '</span>';
	//$("#edit_select").html(html);

    // Define the Dialog and its properties.
    $("#edit_select").dialog({
		resizable: false,
		modal: true,
		title: "Select Action",
		resize:"auto",
		//height: 200,
		width: 420,
		buttons:{
				"Edit": function () {
				$(this).dialog('close');
				editCallBack();
			},
				"Delete": function () {
				$(this).dialog('close');
				deleteCallBack();
			},
				"Clone": function () {
				$(this).dialog('close');
				cloneCallBack();
			},
				"Cancel": function () {
				$(this).dialog('close');
			}
		}
	});
}

//edit_profile
///////////////////////////////////// Open Edit Select Dialog  /////////////////////////////
//
function openEditProfile(obj)//, saveCallBack)
{
	$("#edit_profile").dialog("open");
	//var html = '<span>' + obj.class_name + '</span>';
	//$("#edit_profile").html(html);

}

///////////////////////////////////   Utiliy Functions   //////////////////////////////////////
function logOjectProperties(data)
{
	for(var name in data) {
   		console.log(name + ": " + data[name]);
	}
}

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
		console.log("RESPONSE");
		console.log(JSON.stringify(json));

		var str = "Status: " + json.status; 
		if(json.status == "SUCCESS")
		{
			success(json);
		}
		else
		{
			error(json);
		}
	});
	
	jqxhr.always(function(json)
	{
		//console.log("RESPONSE");
		//console.log(JSON.stringify(json));
	});
	
	jqxhr.fail(function(e)
	{
		console.log("jqxhr.fail");
		console.log("Error status: " + e.status + " Error: " + e.statusText);

		$("#error_responseText").html(e.responseText);
	});

}

function success(json)
{
	openMessageBox(json.status);
}

function error(json)
{
	var errMsg = json.errMsg + " " + json.xtndErrMsg;
	openMessageBox(errMsg.substring(0, 2500)); //Limit the string size
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

////////////////////////////////////////////////////    Login Server  /////////////////////////////////////////////
function loginServer(strUrl, data, success, login_error)
{
  	var jqxhr = $.post(strUrl, data);
	// results
	jqxhr.done(function(json)
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
	});
	
	jqxhr.always(function(json)
	{
		console.log("jqxhr.always");
		console.log(JSON.stringify(json));
	});
	
	jqxhr.fail(function(e)
	{
		console.log("jqxhr.fail");
		console.log("Error status: " + e.status + " Error: " + e.statusText);
		$("#error_responseText").html(e.responseText);
	});

}

function login_error(json)
{
	openMessageBox("Failed Login: " + JSON.stringify(json).substring(0, 2500)); //Limit the string size
}