//////////////////////  Following functions used for creating Profile Form  ////////////////////////////

///////////////////////////////////////// get Label for Field Name /////////////////////////

function getLabelFor(class_name, field_name, json_labels)
{
	//var field_name = field.name;
	for(var i=0; i < json_labels.fields.length; i++)
	{
		obj = json_labels.fields[i];
		var key = class_name + "." + field_name; 
		if(obj.name == key)
		{
			//return field_name = obj.value;
			return obj.value;
		}
	}
	return field_name;
}

//////////////////////////////////// Set Field values from json  ///////////////////////////////////////

function setFields(schema_json, json, json_labels)
{
	var fields = schema_json.schema.fields;
	var primary_key = schema_json.schema.Primary_key;
	var class_name = schema_json.schema.class_name;
	var html = "";
	for(var i = 0; i < fields.length; i++)
	{
		var field = fields[i];
		if(field.type == "longtext")
		{
			if(tinymce)
			{
				tinymce.get('txtContent').setContent(json.field[field.name]);
				show("#tinyMice");
			}
			else
			{
				html += makeInput(class_name, field, json.field[field.name], primary_key, json_labels);
			}
		}
		else
		{
			html += makeInput(class_name, field, json.field[field.name], primary_key, json_labels);
		}
	}
	return html;
}

//////////////////////////////  Get Empty Fields   ////////////////////////////////////////////////

function getFields(schema_json, current_class_name, json_labels)
{
	var fields = schema_json.schema.fields;
	var primary_key = schema_json.schema.Primary_key;
	var class_name = schema_json.schema.class_name;
	var html = "";
	for(var i = 0; i < fields.length; i++)
	{
		var field = fields[i];
		if(field.type == "longtext")
		{
			if(tinymce)
			{
				show("#tinyMice");
			}
			else
			{
				html += makeInput(class_name, field, "", primary_key, json_labels);
			}
		}
		else
		{
			html += makeInput(class_name, field, "", primary_key, json_labels);
		}
	}
	//Add additional custom fields here
	if("MtcOpenDatesHelper" == current_class_name)
	{
		html += '<caption><strong>End Day:</strong></caption>';
		html += '<input id="end_day" type="text" maxlength="10" size="10">&nbsp;<caption>Creates all entries between Day and End Day automatically (leave empty for one day)<br>'; 
	}
	return html;
}

/////////////////////////////////////////////// Draw input Field ///////////////////////////
//  NOTE one global value g_isDevMode show Labels if not defined

function makeInput(class_name, field, value, primary_key_name, json_labels)
{
	if(value == "null") //Banish the nulls
	{
		value = "";
	}
	if("timestamp" == field.type)
	{
		return "";
	}
	var html = "";
	if(primary_key_name !== field.name) //Don't make a label for primary key
	{
		if(g_isDevMode)
		{
			html += '<label><strong>' + field.name + '</strong> type: ' + field.type +
				 ' optional: ' + field.optional + '</label>';
		}
		else
		{
			html += '<label><strong>' + getLabelFor(class_name, field.name, json_labels) + ':</strong></label>';
		}
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
	var type = field.type; //data type
	var typ; //input type
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
			//Fix the format
			var res = value.split(" ");
			value = res[0] + "T" + res[1];
			break;
		default:
			var tempHtml = "";
			var arr = type.split("(");
			typ = arr[0].trim();
			if(arr.length > 1)
			{
				var temp = arr[1].split(")");
				var len = temp[0].trim();
				//tempHtml = ' maxlength="' + len + '" style="width:' + len + 'px" ';
				var max_len = len;
				if(len > 45 && len <= 255)
				{
					len = 45;
				}
				else if(len > 255)
				{
					//TODO Special avatar_url should go away until image upload is supplied 
					if("avatar_url" != field.name)
					{
						typ = "textarea"; 	
					}
					else
					{
						len = 45;
					}
				}
				tempHtml = ' maxlength="' + max_len + '" size="' + len + '" ';

			}
			switch(typ)
			{
				case "tinyint":
					type = "checkbox";
				case "int":
					type = "int";
					break;
				case "varchar":
					type = "text";
					widthHtml = tempHtml;
					if("password" == field.name || "password_hint_answer" == field.name)
					{
						type = "password";
					}
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
	var lookUp = "";
	if(field.class != "")
	{
		lookUp = '<button id="' + field.class + '" data-target="' + field.name + '" class="lookUpBtn" type="button">Look Up</button>';
	}
	if("textarea" == typ)
	{
		return html + '<br><textarea id="' + field.name + '" ' + required + ' >' + value + '</textarea><br>';
	}
	else
	{
		return html + '<input ' + val + ' id="' + field.name + '" type="' + type + '" ' + widthHtml + ' ' + required + 
					' value="' + value + '" autocomplete="on">' + lookUp + '<br>';
	}

}

//////////////////////////////////////  Saving Data ///////////////////////////////////////

/////////////////////////////////////  Get Field values from form  ///////////////////////////////

function getFormValues(data, current_schema_json, inputForm, isClone)
{
	var isValid = true;
	var fields = current_schema_json.schema.fields;
	var primary_key_name = current_schema_json.schema.Primary_key;
	for(var i = 0; i < fields.length; i++)
	{
		var field = fields[i];
		if(field.type == "longtext" && tinyMCE)
		{
			data[field.name] = tinyMCE.get('txtContent').getContent();
		}
		else
		{
			var selector = inputForm + " #" + field.name;
			//var selector = "#inputForm #" + field.name;
			var value = $(selector).val();
			if(value != "" && value != "undefined")
			{
				$(selector).css('background-color', 'white');
				if(isClone)
				{
					if(field.name == primary_key_name)
					{
						data[field.name] = "NULL";
					}
					else
					{
						data[field.name] = $(selector).val();
					}
				}
				else
				{
					data[field.name] = $(selector).val();
				}
			}
			else
			{
				if(field.optional == "NO" && value == "")
				{
					$(selector).css('background-color', '#ED8F90');
					isValid = false;
				}
				else if(value == "")
				{
					data[field.name] = $(selector).val();
				}
				else
				{
					//what to do with undefined
				}
			}
		}
	}
	if(isValid)
	{
		return data;
	}
	else
	{
		return false;
	}
}

///////////////////////////////////////  Validate Fields ////////////////////////////////////////////
function getEmptyField(schema_json, json_labels)
{
	var fields = schema_json.schema.fields;
	var primary_key = schema_json.schema.Primary_key;
	var class_name = schema_json.schema.class_name;
	var html = "";
	for(var i = 0; i < fields.length; i++)
	{
		var field = fields[i];
		if("NO" == field.optional)
		{
			$val = $("#" + field.name).val();
			if("" == $val)
			{
				return getLabelFor(class_name, field.name, json_labels);
			}
		}
	}
	return false;
}

////////////////////////////////////////////////// Look Up dialog -- still moving ////////////////////////////
function getIdValue(data)
{
	var str = "";
	for(var name in data) {
   		if(name.indexOf("_id") != -1)
		{
			return data[name];
		}
	}
	return false;
}

function getNameValue(data)
{
	var str = "";
	for(var name in data) {
   		if(name.indexOf("name") != -1)
		{
			str += data[name] + " ";
		}
	}
	return str;
}

////////////////////////////////////////  Draw the Look Up List  //////////////////////////
//look_up_fields_list
function drawLookUpList(className, target)
{
	g_current_target = null;
	console.log("className:" + className);
	if("MtcMemberSecure" == className)
	{
		g_current_target = target;
		$("#look_up_fields_list").html("");
		//Show the search field
		show("#search_input_container");
	}
	else
	{
		var strUrl = JSON_API_URL;
		var my_method = className + ".getList";
		var data = { method:my_method};
		postData(strUrl, data, function(json)
		{
			var html = "";
			//$("#look_up_fields_list").html(JSON.stringify(json));
			if("SUCCESS" == json.status)
			{
				for(var i=0; i < json.fields.length; i++)
				{
					var field = json.fields[i];
					var name_val = getNameValue(field);
					//////////////////  Add some conditional cases Here
					if("MtcMembershipCategory" == className)
					{
						name_val += ": " + field.fee;
					}
					html += '<div id="' + getIdValue(field) + '" class="lookUpItem" data-target="' + target +
														 '" data-class_name="' + className + '" >' + name_val + '</div>';
				}
			}
			$("#look_up_fields_list").html(html);
		}, error);
	}
}

////////////////////////////////////////  NOTE GLOBAL  ////////////////////////////////////

//Lookup dialog MtcMemberSafe search
var g_current_target;

////////////////////////////////////////  Draw First Name Search  /////////////////////////
function drawFirstNameList()
{
	var my_first_name = $("#search_input_first_name_input").val();
	var strUrl = JSON_API_URL;
	var my_method = "MtcMemberSecure.search";
	var data = { method:my_method, first_name: my_first_name};
	postData(strUrl, data, function(json)
	{
		var html = "";
		if("SUCCESS" == json.status)
		{
			for(var i=0; i < json.fields.length; i++)
			{
				var field = json.fields[i];
				var name_val = getNameValue(field);
				html += '<div id="' + getIdValue(field) + '" class="lookUpItem" data-target="' + g_current_target + '">' + name_val + '</div>';
			}
		}
		$("#look_up_fields_list").html(html);
	}, error);
}
