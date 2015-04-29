//globals

var g_userid;
var g_ukey;
var g_permissions;

var g_canned_query;
var g_class_name;
var g_schema_json;
var g_json_labels;

var g_canned_query_list_json;

$(document).ready(function ()
{
	$.support.cors = true;
	
	// Retrieve
	g_userid = localStorage.getItem("g_userid");
	g_ukey = localStorage.getItem("g_ukey");
	g_permissions = localStorage.getItem("g_permissions");
	
	//Get the labels one time op
	getLabels();
	
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
	
//////////////////////////////////////////// Respond to clicks on the table rows /////////////////////////////

	$("#tablesorter-list").delegate('tr', 'click', function(e) {
		var m_key = e.currentTarget.dataset.key;
		if(m_key)
		{
			var m_id = e.currentTarget.id;
			// similar behavior as an HTTP redirect
			window.location.replace("list_utils.php?id=" + m_id);
			// similar behavior as clicking on a link
			//window.location.href = "http://stackoverflow.com";
		}
		
		/*
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
		}
		*/
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
			
			makeCannedQueryTable();
			
		}, error);
	}
}

/////////////////////////////////////////  Draw the table of Canned Queries  /////////////////////////////////

function makeCannedQueryTable()
{
	$("#tablesorter-list").html(""); //clear out table
	var strUrl = JSON_API_URL;
	//var data = { method:"CannedQuery.getList"};
	var data = { method:"CannedQueryHelper.getSortedList"};
	postData(strUrl, data, function(json)
	{
		g_canned_query_list_json = json;
		drawTable();
	}, error);
}

/////////////////////////////////// Draw the table /////////////////////////////////
function drawTable()
{
	//$("#tablesorter-list tbody").html(""); //clear out any old rows in the body
	var fields = g_canned_query_list_json.fields;
	var html = "";
	var html = "";
	if(fields.length > 0)
	{
		html += makeHeader(fields[0]);
	}
	html += '<tbody>';
	for(var i=0; i < fields.length; i++)
	{
		var field = fields[i];
		html += makeRow(field);
	}
	html += "</tbody>";
	
	console.log(html);
	// append new html to table body 
	$("#tablesorter-list").append(html);
	
	$("#tablesorter-list").tablesorter({ theme : 'blue', sortList: [[2,1],[0,0]], widgets: ['zebra'] });

	// let the plugin know that we made a update, then the plugin will
	// automatically sort the table based on the header settings
	$("#tablesorter-list").trigger("update");

}

//////////////////////////////////  Draw Header //////////////////////////////////
function makeHeader(field)
{
	var html = '<thead><tr>';
	
	var label = getLabelFor("CannedQuery", "class_list", g_json_labels)
	html += '<th>' + label + '</th>';

	var label = getLabelFor("CannedQuery", "name", g_json_labels)
	html += '<th>' + label + '</th>';

	var label = getLabelFor("CannedQuery", "description", g_json_labels)
	html += '<th>' + label + '</th>';

	html += '</tr></thead>';
	return html;
}

//////////////////////////////////  Draw Row //////////////////////////////////
function makeRow(field)
{
	var html = '<tr id="' + field.canned_query_id + '" data-key="' + field.key + '">';
	html += '<td>' + field.class_list + '</td>';
	html += '<td>' + field.name + '</td>';
	html += '<td>' + field.description + '</td>';
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