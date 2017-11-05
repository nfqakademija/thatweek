var months = [];
var currentDisplayedMonth = 0;
var selectedWeek = -1;
var selectedMonth = -1;

var monthNames = ['Sausis', 'Vasaris', 'Kovas', 'Balandis', 'Gegužė', 'Birželis',
    'Liepa', 'Rugpjūtis', 'Rugsėjis', 'Spalis', 'Lapkritis', 'Gruodis'];

function initializeCalendar(weekJson) {
    var weeks = JSON.parse(weekJson);
    var calendarStartingDate = weeks[0]['startDate'];

    for (var i = 0; i < weeks.length; i++) {
        var weekStartDate = weeks[i]['startDate'];
        var thisMonth = getMonthFromTimestamp(weekStartDate);
        var monthIndex = monthDiff(calendarStartingDate, weekStartDate);

        if (monthIndex >= months.length) {
            months.push(createMonth(monthNames[thisMonth], weekStartDate));
        }
        months[monthIndex].weeks.push(weeks[i]);
    }
    renderMonth();
    registerListeners();
    updateInfoLabels();
}

function renderMonth() {
    $('#calendar_header').empty().html(formMonthHeader());
    $('.calendarRow').remove();
    enableDisableButtons();

    var currentMonth = months[currentDisplayedMonth];
    var weekDay = currentMonth.startDay - 1;
    var tableRow = generateEmptyCells('', weekDay);
    var weekIndex = 0;

    for (var i = 1; i <= currentMonth.endDay; i++) {
        weekDay++;
        tableRow += '<td>' + i + '</td>';
        if (weekDay % 7 === 0 || i === currentMonth.endDay) {
            if(weekDay % 7 !== 0) {
                var cellsToFillRow = 7 - weekDay % 7;
                tableRow = generateEmptyCells(tableRow, cellsToFillRow);
            }
            $('#calendar').append(getFormatedTableRow(tableRow));
            weekIndex++;
            tableRow = '';
        }
    }
    updateCalendarRowListeners();
}

function formMonthHeader() {
    return months[currentDisplayedMonth].name + ' ' + months[currentDisplayedMonth].year;
}

function getFormatedTableRow(tableRow) {
    return '<tr class="calendarRow">' + tableRow + '</tr>';
}

function startsOnMonday(month) {
    var date = new Date(month.weeks[0]['startDate'] * 1000);
    return date.getDate() === 1;
}

function nextMonth(direction) {
    if (direction === -1 && currentDisplayedMonth > 0)
        currentDisplayedMonth--;
    else if (direction === 1 && currentDisplayedMonth < months.length - 1)
        currentDisplayedMonth++;
    else return;
    renderMonth();
}

function enableDisableButtons() {
    var leftButton = $('#calendarLeftBtn');
    var rightButton = $('#calendarRightBtn');
    if (currentDisplayedMonth === 0)//disable 'go backwards' button if user is in first month
        leftButton .toggleClass('disabled');
    else if (leftButton .hasClass('disabled'))//enable it if user went forward
        leftButton .toggleClass('disabled');

    if (currentDisplayedMonth === months.length - 1)//disable 'go forward' button if user is in last month
        rightButton.toggleClass('disabled');
    else if (rightButton.hasClass('disabled'))//enable it if user went backwards
        rightButton.toggleClass('disabled');
}

function registerListeners()
{
    $( '#calendar' ).mouseleave(function() {
        var week;
        if(selectedMonth !== -1 && selectedWeek !== -1) {
            week = months[selectedMonth].weeks[selectedWeek];
        }
        updateInfoLabels(week);
    });

}

function updateCalendarRowListeners()
{
    var calendarRow = $('.calendarRow');

    calendarRow.click(function(){
        disableCalendarRows();
        var thisRow = $(this);
        setWeek(thisRow);
    });

    calendarRow.mouseover( function() {
        var thisRow = $(this);
        showWeekData(thisRow);
    });
}

function getPreviousMonthLastWeek()
{
    var previousMonth = months[currentDisplayedMonth - 1];
    return previousMonth.weeks.length - 1;
}

function disableCalendarRows()
{
    $('.calendarRow').each( function () {
        if($(this).hasClass('active'))
        {
            $(this).toggleClass('active');
        }
    });
}

function setWeek(row) {
    var index = row.index() - 1;
    var currentMonth = months[currentDisplayedMonth];
    var week;
    if (!startsOnMonday(currentMonth))
    {
        if(index === 0) {
            if(currentDisplayedMonth > 0)
            {
                setDate(currentDisplayedMonth - 1, getPreviousMonthLastWeek());
                week = months[selectedMonth].weeks[selectedWeek];
                selectWeek(week, row);
            }
            else{
                setDate(-1, -1);//if it's current month, we don't have any data about the last week from previous month
                setFormsWeek(-1);
            }
            return;
        }
        index--;//if months doesn't start on monday, it has one additional week at beginning from other month,
        // we have to decrease it, so that index = 0 would be equal to first week of this month (first week that start on monday)
    }
    setDate(currentDisplayedMonth, index);
    week = currentMonth.weeks[index];
    selectWeek(week, row);
}

/*function highlightWeek()
{
    if()
    $('.calendarRow').each(function() {
        var thisRow = $(this);
        if(thisRow.index())
    });
}*/

function setDate(month, week)
{
    selectedMonth = month;
    selectedWeek = week;
}
function showWeekData(row) {
    var index = row.index() - 1;
    var currentMonth = months[currentDisplayedMonth];
    var week;
    if(!startsOnMonday(currentMonth)) {
        if (index == '0') {
            if (currentDisplayedMonth > 0) {
                week = months[currentDisplayedMonth - 1].weeks[getPreviousMonthLastWeek()];
            }
            else {
                week = null;
            }
            updateInfoLabels(week);
            return;
        }
        index--;
    }
    week = months[currentDisplayedMonth].weeks[index];
    updateInfoLabels(week);
}

function selectWeek(week, row)
{
    setFormsWeek(week['id']);
    row.toggleClass('active');
}

function setFormsWeek(id)
{
    $('#appbundle_order_week').val(id);
}

function updateInfoLabels(data) {
    var weekLabel = $('#weekLabel');
    var unitsSoldLabel = $('#unitsSoldLabel');
    if(data == null)
    {
        weekLabel.html('Nepasirinkta savaitė');
        unitsSoldLabel.html('');
    }
    else {
        weekLabel.html(getFullDate(data['startDate']) + ' - ' + getFullDate(data['endDate']));
        unitsSoldLabel.html('Užimtų vietų ' + data['unitsSold']);
    }
}

function resetInfoLabels() {
    $('#weekLabel').empty();
    $('#unitsSoldLabel').empty();
}

function getFullDate(timestamp) {
    var date = new Date(timestamp * 1000);
    return date.getFullYear() + '/' + (date.getMonth() + 1) + '/' + date.getDate();
}

function getMonthFromTimestamp(timestamp) {
    var date = new Date(timestamp * 1000);
    return date.getMonth();
}

function getYearFromTimestamp(timestamp) {
    var date = new Date(timestamp * 1000);
    return date.getFullYear();
}

function getFirstDayOfMonth(timestamp) {
    var date = new Date(timestamp * 1000);
    var y = date.getFullYear();
    var x = date.getMonth();
    var firstDayDate = new Date(y, x, 1);
    return firstDayDate.getDay();
}

function getLastDayOfMonth(timestamp) {
    var date = new Date(timestamp * 1000);
    var y = date.getFullYear();
    var x = date.getMonth();
    var lastDayDate = new Date(y, x + 1, 0);
    return lastDayDate.getDate();
}

function monthDiff(first, second) {
    var difference;
    first = new Date(first * 1000);
    second = new Date(second * 1000);
    difference = (second.getFullYear() - first.getFullYear()) * 12;
    difference -= first.getMonth();
    difference += second.getMonth();
    return difference <= 0 ? 0 : difference;
}

function createMonth(name, weekStartDate) {
    return {
        name: name,
        year: getYearFromTimestamp(weekStartDate),
        weeks: [],
        startDay: getFirstDayOfMonth(weekStartDate),
        endDay: getLastDayOfMonth(weekStartDate)
    };
}

function generateEmptyCells(row, cells) {
    cells = cells < 0 ? 6 : cells;
    for (var i = 0; i < cells; i++) {
        row += '<td></td>';
    }
    return row;
}