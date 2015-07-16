// set each of the select functions to have its correct initial value
cj.each(cj('select'), function(){
        cj(this).val(cj(this).attr("selectedValue"));
});

// add the functionality to save the new phone type if it's changed 
cj(document).on('change', '.setPhoneType', function() {
        var phone_id = cj(this).attr("phone_id");
        var new_value = cj(this).attr("value");
        CRM.api('Phone','update',{ id:phone_id, phone_type_id:new_value }
                ,{ success:function (data){
                }
        });
        return false;
});

// make the delete phone record links work
cj(document).on('click', '.button_delete', function(){
        var phone_id = cj(this).attr('phone_id');
        cj().crmAPI ('Phone','delete',{ id:phone_id }
                ,{ success:function (data){
                        cj("." + phone_id).fadeOut();
                }
        });
        return false;
});

// make the hide phone record links work
cj(document).on('click', '.button_hide', function() {
        var phone_id = cj(this).attr('phone_id');
        cj(this).parent().parent().fadeOut(); 
        return false;
});     

// handle the showing and the hiding of the get
cj('.regexSelector').click(function (){
    var numCheckedRegexes = cj(':checked.regexSelector').size();
    if (numCheckedRegexes > 0){
        cj('#getInvalidPhones').removeAttr('disabled');
    } else {
        cj('#getInvalidPhones').attr('disabled','disabled');
    }
});

// make table row
function makeTableRow(contactId, display_name, phoneId, phoneNumber, phoneTypeId, phoneExt){

    var viewContactLink = '<a title="View ' + display_name + '\'s contact record." href="/civicrm/contact/view?reset=1&cid=' + contactId + '">' + display_name + '</a>';

    var phoneNumberString = '<span id="phone-' + phoneId + '" class="crmf-phone crm-editable">' + phoneNumber + '</span>';

    var phoneTypeString = '<select class="setPhoneType" phone_id="' + phoneId + '" selectedValue="' + phoneTypeId + '">';

    // add phone type details from the selector at the top of the page
    cj('#selectedPhoneType').children('option').each(function (){
        if (cj(this).val() != '') {
            var selectedString = "";
            if(cj(this).val() == phoneTypeId){
                selectedString = "selected";
            }
            phoneTypeString += "<option value='" + cj(this).val() + "' " + selectedString + ">" + cj(this).text() + "</option>";
        }
    });

    phoneTypeString += '</select>';

    var phoneExtString = '<span id="phone-' + phoneId + '" class="crmf-phone-ext crm-editable">' + phoneExt + '</span>';

    var editContactUrl = CRM.url('civicrm/contact/add', {"reset":1, "action":"update", "cid":contactId});

    var actionsString = '<a title="Edit ' + display_name + '\'s contact record." href="' + editContactUrl + '">edit contact</a> | ';
        actionsString += '<a title="Remove this phone number forever from the contact\'s record. Doesn\'t touch the rest of the contact\'s details!" class="button_delete" href="#" phone_id="' + phoneId + '">delete phone</a> | ';
        actionsString += '<a title="Hide this phone number from view for now." class="button_hide" href="#" phone_id="' + phoneId+ '">hide</a>';

    return "<tr id='phone-" + phoneId + "' class='crm-entity " + phoneId + "'><td>" + viewContactLink + "</td><td>" + phoneNumberString + "</td><td>" + phoneExtString + "</td><td>" + phoneTypeString + "</td><td>" + actionsString+ "</td></tr>";

}

// the main retrieval function
cj('#getInvalidPhones').click(function(){            
    // Clear old table entries and add spinner.
    cj('#invalidPhonesDisplay').empty();
    cj('#invalidPhonesDisplay').append('<img src="' + resource_base + 'i/loading.gif">');
    cj('#invalidPhonesCountDisplay').empty();
            
    // Get params
    var selectedRegexIds = [];
    cj('input:checked.regexSelector').each(function() {
        selectedRegexIds.push(cj(this).val());
    });

    var selectedAllowCharacters = [];
    cj('input:checked.allowSelector').each(function() {
        selectedAllowCharacters.push(cj(this).val());
    });

    var selectedPhoneTypeId = cj('#selectedPhoneType').val();
    var selectedContactTypeId = cj('#selectedContactType').val();            

    retrieveInvalidPhoneNumbersCount(selectedRegexIds, selectedAllowCharacters, selectedPhoneTypeId, selectedContactTypeId);

    retrieveInvalidPhoneNumbers(selectedRegexIds, selectedAllowCharacters, selectedPhoneTypeId, selectedContactTypeId);
});

function retrieveInvalidPhoneNumbersCount(selectedRegexIds, selectedAllowCharacters, selectedPhoneTypeId, selectedContactTypeId){
    // Get and insert the new entries.
    CRM.api('PhoneNumberValidator', 'Getinvalidphonescount', {
        'sequential': 1, 
        'selectedRegexIds': selectedRegexIds, 
        'selectedAllowCharacters': selectedAllowCharacters, 
        'selectedPhoneTypeId': selectedPhoneTypeId, 
        'selectedContactTypeId': selectedContactTypeId
    },
        {success: function(data) {
            var brokenPhoneNumbersCount = data.values[0];
            if (data.values[0] == 0) {    
                cj('#invalidPhonesCountDisplay').append('<div>No broken phone numbers to display.</div>');
                cj('#invalidPhonesCountDisplay').empty(); // remove spinner.
            } else if (data.values[0] > 50) {
                cj('#invalidPhonesCountDisplay').append('<div>Showing first 50 of ' + data.values[0] + ' broken phone numbers.</div>');
            } else {
                cj('#invalidPhonesCountDisplay').append('<div>Showing ' + data.values[0] + ' broken phone numbers.</div>');
            }
        }, error: function(data){
            cj('#invalidPhonesCountDisplay').empty();
            cj('#invalidPhonesCountDisplay').append("<em>Error: " + data['error_message'] + "</em>");
        }
    }
    );
}    

function retrieveInvalidPhoneNumbers(selectedRegexIds, selectedAllowCharactersIds, selectedPhoneTypeId, selectedContactTypeId){
    // Get and insert the new entries.
    CRM.api('PhoneNumberValidator', 'Getinvalidphones', {
        'sequential': 1, 
        'selectedRegexIds': selectedRegexIds, 
        'selectedAllowCharactersIds': selectedAllowCharactersIds, 
        'selectedPhoneTypeId': selectedPhoneTypeId, 
        'selectedContactTypeId': selectedContactTypeId
    },
        {success: function(data) {
            cj('#invalidPhonesDisplay').empty(); // Remove spinner.
                
            cj('#invalidPhonesDisplay').append("<table id='invalidPhonesTable'>");
            cj('#invalidPhonesTable').append("<tr><th>contact name</th><th>phone</th><th>extension</th><th>type</th><th>actions</th></tr>");

            cj.each(data.values, function(key, value) {
                cj('#invalidPhonesTable').append(makeTableRow(value['contact_id'],value['display_name'],value['phone_id'],value['phone_number'],value['phone_type_id'],value['phone_ext']));
            });
            cj('#invalidPhonesDisplay').append("</table>");
            cj('.crm-editable').crmEditable();

        }, error: function(data){
            cj('#invalidPhonesDisplay').append("<em>Error: " + data['error_message'] + "</em>");
        }
    }
    );
}    