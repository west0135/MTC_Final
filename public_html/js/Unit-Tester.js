//globals
var current_class_name = null;
var current_schema_json;
var current_getList_method_name;
var isInsert;

//var DOMAIN = "http://geopad.ca";
var DOMAIN = "http://clients.edumedia.ca";

var JSON_API_URL = DOMAIN + "/to_be/json-api/";


$(document).ready(function ()
{
	$.support.cors = true;

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
//       Tiny Mice HTML editer
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
			"emoticons template paste textcolor colorpicker textpattern"
		],
		toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
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
	//Cancel
	$("#cancelButton").click(function(e)
	{
		show("#fields_list");
		hide("#editArea");
		show("#createItem");
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
	
	//Open PHP page for selected Class
	$("#showGeneric").click(function(e)
	{
		if(!current_class_name)
		{ 
			alert("Please select a type of page you want to view, then push this button");
			return;
		}
		window.open(DOMAIN + "/to_be/ata/Generic.php?class=" + current_class_name);
	});
	
	//Draw List of items for selected Class
	$("#pageTypes").click(function(e)
	{
		//Show the create button
		show("#createItem");
		hide("#editArea");
		show("#fields_list");

		id = e.target.id;
		page_type = id;
		$("#itemTypeLabel").html("Item Class Name: " + page_type);

		$("#createItem").html("Create a New " + page_type);
		drawListForClass(page_type);
	});

	//Add a new Class Item
	$("#addItem").click(function(e)
	{
		var validateForm = $('#validationStatus').is(':checked'); 
		if(validateForm)
		{
			var form = e.target.form;
			if(form.checkValidity())
			{
				addCurrentItem();
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
		isInsert = true;
		e.preventDefault(); //stop form validation ?
		hide("#fields_list");
		show("#editArea");
		hide("#createItem");
		drawBlankFormForCurrentClass();
	});
	
	$("#fields_list").click(function(e)
	{
		id = e.target.id;
		if(id && id !== "fields_list")
		{
			var mode = e.target.dataset.mode;
			if(mode == "edit")
			{
				isInsert = false;
				getDataAndDrawUpdateForm(id);
			}
			else if(mode == "delete")
			{
				deleteForCurrentClassAndId(id);
			}
		}
	});
	
});

function getDataAndDrawUpdateForm(id)
{
	var strUrl = JSON_API_URL;
	var key_name = current_schema_json.schema.Primary_key;
	var methodName = current_class_name + ".get";
	var data = { method:methodName};
	data[key_name] = id;
	postData(strUrl, data, function(json)
	{
		//var msg = "Update using this DATA: " + JSON.stringify(json);
		//alert(msg.substring(0, 2500)); //Limit the string size
		$("#inputForm").html(setFields(current_schema_json, json));
		
		hide("#fields_list");
		show("#editArea");
		hide("#createItem");

	}, error);
}

function deleteForCurrentClassAndId(id)
{
	if(confirm("Delete selected " + current_class_name))
	{
		var strUrl = JSON_API_URL;
		var key_name = current_schema_json.schema.Primary_key;
		var methodName = current_class_name + ".delete";
		var data = { method:methodName};
		data[key_name] = id;
		postData(strUrl, data, function(json)
		{
			//var msg = "Update using this DATA: " + JSON.stringify(json);
			//alert(msg.substring(0, 2500)); //Limit the string size
			drawListForClass(current_class_name);
		}, error);
	}
}

function drawBlankFormForCurrentClass()
{
	//$("#itemTypeLabel").html(current_schema_json.schema.name);
	$("#inputForm").html(getFields(current_schema_json));
	show("#actionButtons");
	//$("#fields_list").html(""); //clear the list
}

function drawListForClass(page_type)
{
	hide("#tinyMice");
	tinymce.get('txtContent').setContent("");
	
	current_getList_method_name = page_type + ".getList";
	current_class_name = page_type;
	var getSchemaMethodName = page_type + ".getSchema";
	var strUrl = JSON_API_URL;
	var data = { method:getSchemaMethodName};
	postData(strUrl, data, function(json)
	{
		current_schema_json = json; //Save the current schema data
		//TODO TEST AREA NOW
		listCurrentItems();
		
	}, error);
}

function makeInput(field, value, primary_key_name)
{
	if("timestamp" == field.type)
	{
		return "";
	}
	var html = "";
	if(primary_key_name !== field.name) //Don't make a label for primary key
	{
		html += '<label>' + field.name + ' type: ' + field.type + ' optional: ' + field.optional + '</label>';
	}
	var widthHtml = "";
	var required = "";
	var val = "";
	//TODO
	var isPrimary = -1 !== field.type.indexOf("[PRIMARY_KEY]") ? true : false;
	if(isPrimary)
	{
		if(value === "") value="NULL";
	}
	if(field.optional == "NO")
	{
		required = " required ";
	}
	var type = field.type; 
	switch(type)
	{
		case "date":
			type = "date";
			break;
		case "time":
			type = "time";
			break;
		case "datetime":
			//type = "datetime";
			type = "datetime-local";
			break;
		default:
			var tempHtml = "";
			var arr = type.split("(");
			var typ = arr[0].trim();
			if(arr.length > 1)
			{
				var temp = arr[1].split(")");
				var len = temp[0].trim();
				tempHtml = ' maxlength="' + len + '" style="width:' + len + '" ';
			}
			switch(typ)
			{
				case "int":
					type = "int";
					break;
				case "varchar":
					type = "text";
					widthHtml = tempHtml;
					break;
				default:
					type = "text";
					break;
			}
			break;
	}
	var cls = "";
	if(primary_key_name == field.name)
	{
		type = "hidden";
	}
	return html + '<input ' + val + ' id="' + field.name + '" type="' + type + '" ' + widthHtml + ' ' + required + 
					' value="' + value + '" ><br>';

}

function setFields(schema_json, json)
{
	var fields = schema_json.schema.fields;
	var primary_key = schema_json.schema.Primary_key;
	var html = "";
	for(var i = 0; i < fields.length; i++)
	{
		var field = fields[i];
		if(field.type == "longtext")
		{
			tinymce.get('txtContent').setContent(json.field[field.name]);
			show("#tinyMice");
		}
		else
		{
			html += makeInput(field, json.field[field.name], primary_key);
		}
	}
	return html;
}

function getFields(schema_json)
{
	var fields = schema_json.schema.fields;
	var primary_key = schema_json.schema.Primary_key;
	var html = "";
	for(var i = 0; i < fields.length; i++)
	{
		var field = fields[i];
		if(field.type == "longtext")
		{
			show("#tinyMice");
		}
		else
		{
			html += makeInput(field, "", primary_key);
		}
	}
	return html;
}

function addCurrentItem()
{
	var strUrl = JSON_API_URL;
	var mode = ".update";
	if(isInsert)
	{
		mode = ".create";
	}
	var methodName = current_class_name + mode;		
	var data = {method:methodName};
	var fields = current_schema_json.schema.fields;
	for(var i = 0; i < fields.length; i++)
	{
		var field = fields[i];
		if(field.type == "longtext")
		{
			data[field.name] = tinyMCE.get('txtContent').getContent();
		}
		else
		{
			data[field.name] = $("#" + field.name).val();
		}
	}
	postData(strUrl, data, function(json)
	{
		alert(JSON.stringify(json).substring(0, 2500)); //Limit the string size
		//tinymce.get('txtContent').setContent("");
		show("#fields_list");
		hide("#editArea");
		show("#createItem");
		drawListForClass(current_class_name);
	}, error);
}

function listCurrentItems()
{
	var strUrl = JSON_API_URL;
	var data = { method:current_getList_method_name};
	postData(strUrl, data, function(json)
	{
		var key_name = current_schema_json.schema.Primary_key;
		$("#fields_list").html(""); //clear the list
		var html = "";
		$("#fields_list").html("");
		for(var i=0; i < json.fields.length; i++)
		{
			var schema_fields = current_schema_json.schema.fields;
			var field = json.fields[i];
			var str= "";
			for(var j=0; j < schema_fields.length ; j++)
			{
				var name = schema_fields[j].name;
				str += name + "=" + field[name] + ",  ";  
			}
			html += '<div class="box"><button data-mode="edit" class="editItem" id = "' +  field[key_name] + '" >Edit</button>' + 
				'<button data-mode="delete" class="deleteItem" id = "' +  field[key_name] + '" >Delete</button><br><br>' + str + '</div>';
		}
		$("#fields_list").html(html);
	}, error);

}

///////////////////////////////////   Utiliy Functions   //////////////////////////////////////

function postData(strUrl, data, success, error)
{
  	var jqxhr = $.post(strUrl, data);
	// results
	jqxhr.done(function(json)
	{
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
		console.log("jqxhr.always");
		console.log(JSON.stringify(json));

  		//$("#jqxhr_always").val(JSON.stringify(json));
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
	alert(json.status);
}

function error(json)
{
	var errMsg = json.status + " " + json.errMsg + " " + json.xtndErrMsg;
	alert(errMsg.substring(0, 2500)); //Limit the string size
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


////////////////////////////////////////////////////    Obsolete  /////////////////////////////////////////////