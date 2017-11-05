
var participants = [];
var checkedParticipants = [];
function initializeParticipantManager(participantsJson)
{
    participants = JSON.parse(participantsJson);

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
    return '<tr>' + formParticipantCell(data['firstName']) + formParticipantCell(data['lastName']) +
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
        $('#appbundle_order_participants').val(checkedParticipants.toString());
    });

}
