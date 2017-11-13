
var participants = [];
var checkedParticipants = [];
function initializeParticipantManager(participantsJson)
{
    updateParticipantList(JSON.parse(participantsJson));
}

function updateParticipantList(newList)
{
    participants = newList;
    $('.participantsRow').remove();
    createParticipantList();
}

function createParticipantList()
{
    var tableRow;
    for(var i = 0; i < participants.length; i++)
    {
        tableRow = formParticipantRow(participants[i], i);
        $('#addParticipantRow').before(tableRow);
        tableRow = '';
    }
    calculateCheckedParticipants();
}

function formParticipantRow(data, thisIndex)
{
    var checkBox = '<input type="checkbox" class="checkbox" id="participantNr' + thisIndex + '">'
    return '<tr class="participantsRow">' + formParticipantCell(data['firstName']) + formParticipantCell(data['lastName']) +
        formParticipantCell(data['age']) + formParticipantCell(convertToFullGender(data['gender'])) +
        formParticipantCell(checkBox) + '</tr>';
}

function formParticipantCell(data)
{
    return '<td>' + data + '</td>';
}

function convertToFullGender(char)
{
    if(char === 'm')
        return 'Vyras'
    return 'Moteris';
}

function calculateCheckedParticipants()
{
    $('input:checkbox').change(function(){
        checkedParticipants = [];
        for(var i = 0; i < participants.length; i++)
        {
            var checkBoxId = '#participantNr' + i;
            if($(checkBoxId).is(':checked'))
                checkedParticipants.push(participants[i]['id']);
        }
        $('#app_calendar_participants').val(checkedParticipants.toString());
    });

}

$.fn.serializeObject = function()
{
    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};


$(document).on("click", '#appbundle_participant_submit', function(e){

    if($("[name='appbundle_participant']").parsley().validate()) {
        data = $("[name='appbundle_participant']").serializeObject();
        $('#participant_submit_spinner').show();
        $('#appbundle_participant_submit').hide();
        createNewParticipant(data);
    }
    });

function resetParticipantForm()
{
    $("[name='appbundle_participant']")[0].reset();
    $('.parsley-success').toggleClass('parsley-success');
}

function createNewParticipant(data)
{
    $.ajax({
        url: "/order/show",
        cache: false,
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function(data){
            $('#participant_submit_spinner').hide();
            $('#appbundle_participant_submit').show();
            resetParticipantForm();
            updateParticipantList(data);
        }
    });
}
