var serverTime = 0;
var calendarLimitStart;
var calendarLimitEnd;
var calendarLimitByMonths = 12;
var currentTime;
var orderStartDate;
var orderEndDate;
var orderStartDateCellActive = false;
var daysInfo = [];
var admin;

var monthNames = ['Sausis', 'Vasaris', 'Kovas', 'Balandis', 'Gegužė', 'Birželis',
    'Liepa', 'Rugpjūtis', 'Rugsėjis', 'Spalis', 'Lapkritis', 'Gruodis'];


function initializeAdminCalendar(time)
{
    admin = true;
    initializeCalendar(time);
}

function retrieveMonthInfo(date)
{
    var start = getFirstDateOfMonth(date);
    var end = getLastDayOfMonth(date);
    var data = {startDate: start.getTime(), endDate: end.getTime()};
    retrieveDaysInfo(date, data);
}

function initializeUserCalendar(time)
{
    initializeCalendar(time);
    registerMouseLeaveListener();
}

function registerMouseLeaveListener()
{
    $( '#calendar' ).mouseleave(function(){
        if(orderStartDate != null)
        {
            var data = {startDate: orderStartDate.getTime(), endDate: orderEndDate.getTime()};
        }
    });
}

function retrieveDaysInfo(date, data)
{
    $.ajax({
        url: "/order/check",
        cache: false,
        type: 'POST',
        dataType: 'json',
        data: data,
        success: function(data){
            daysInfo[date.getTime()] = data;
            if(admin != null)
                updateDaysOrderLabels();
            else
                paintSelectedCells();
        }
    });
}
function initializeCalendar(time)
{
    serverTime = time * 1000;
    currentTime = new Date(serverTime);
    calendarLimitStart = getDateClone(currentTime);
    calendarLimitEnd = getDateClone(currentTime);
    calendarLimitEnd.setMonth(calendarLimitEnd.getMonth() + calendarLimitByMonths);
    renderMonth();

}

function renderMonth()
{
    var monthStartDay = getFirstDayOfMonth(currentTime);
    var cells = generateEmptyCells(monthStartDay - 1);
    var daysInCurrentMonth = getDaysInMonth(currentTime);

    retrieveMonthInfo(currentTime);

    $('#calendar_headline').html(monthNames[currentTime.getMonth()]);
    $('#calendar_year_label').html(currentTime.getFullYear());
    $('.day_container').remove();
    for(var i = 1; i <= daysInCurrentMonth; i++)
    {
        cells += formDayCell(i);
    }

    var cellsToFinish = 7 - getLastWeekDayOfMonth(currentTime);
    cellsToFinish = cellsToFinish === 7?0:cellsToFinish;
    cells += generateEmptyCells(cellsToFinish);

    $('#calendar').append(cells);
    updateCalendarListeners();
    if(orderStartDate !== null)
        paintSelectedCells();
}

function generateEmptyCells(cellCount) {
    cellCount = cellCount < 0 ? 6 : cellCount;
    var cells  = '';
    for (var i = 0; i < cellCount; i++) {
        cells += '<div class="calendar_element day_container empty"></div>';
    }
    return cells;
}


function formDayCell(number)
{
    return '<div class="calendar_element day_container day"><div class="day_number">' + number + '</div>' +
        '<div class="day_orders"></div></div>';
}

function updateCalendarListeners() {
    $('.day_container').click(function () {
        var index = $(this).index('.day');
        if(index !== -1) {
            var day = index + 1;
            updateOrderDates(day);
        }
    });
}

function updateOrderDates(day)
{
    var clickedDate = getDateClone(currentTime);
    clickedDate.setDate(day);
    if(orderStartDate == null)//On first click on calendar
    {
        orderStartDate = getDateClone(clickedDate);
        orderEndDate = getDateClone(clickedDate);
    }
    else if(isDatesEqual(clickedDate, orderStartDate) && !orderStartDateCellActive)//if clicked on order start date - highlight it and allow to change
    {
        orderStartDateCellActive = true;
    }
    else if(orderStartDateCellActive) {
        changeActiveCellsWhenFirstIsSelected(clickedDate);
    }
    else if(clickedDate > orderStartDate)
        orderEndDate = getDateClone(clickedDate);
    else if(clickedDate < orderStartDate)
        orderStartDate = getDateClone(clickedDate);

    updateOrderDatesInForm();
    paintSelectedCells();
}

function changeActiveCellsWhenFirstIsSelected(clickedDate)
{
    if(isDatesEqual(orderStartDate, orderEndDate)) {//when only one cell is active, move both dates to clicked date
        orderEndDate = getDateClone(clickedDate);
        orderStartDate = getDateClone(clickedDate);
    }
    else if(clickedDate < orderEndDate) {
        orderStartDate = getDateClone(clickedDate);
    }
    else if(clickedDate > orderEndDate){//swap start and end dates, if user moved starting date to later date than ending
        orderStartDate = getDateClone(orderEndDate);
        orderEndDate = getDateClone(clickedDate);
    }
    else {
        orderStartDate = getDateClone(orderEndDate);
    }
    orderStartDateCellActive = false;
}

function paintSelectedCells()
{
    resetPaintedCells();
    var date = getDateClone(currentTime);
    var key = currentTime.getTime();
    if(key in daysInfo) {
        var month = daysInfo[key];
        var counter = 0;
        $('.day_container').each(function () {
            var field = $(this);
            var dayNumber = field.index('.day') + 1;
            if (dayNumber > 0) {
                date.setDate(dayNumber);
                var end = counter>=month.length?true:false;
                var isEmpty = true;
                if (!end) {
                    var day = month[counter];
                    isEmpty = !(dayNumber === getDayFromTimestamp(day['date']));
                    if (!isEmpty) {
                        if (day['capacity'] === 0)
                            field.toggleClass('not_assigned');
                        else if (day['capacity'] <= day['orderCount'])
                            field.toggleClass('full');
                        counter++;

                    }
                }
                if(isEmpty)
                    field.toggleClass('not_assigned');
                if (orderStartDate <= date && date <= orderEndDate) {
                    field.toggleClass('active');

                    if (isDatesEqual(orderStartDate, date) || isDatesEqual(orderEndDate, date)) {
                        if (isDatesEqual(orderStartDate, date) && orderStartDateCellActive === true)
                            field.toggleClass('selected');
                        field.toggleClass('edge');
                    }
                }
            }
        });
    }
}

function updateOrderDatesInForm()
{
    $('#app_calendar_startDate').val(orderStartDate.getTime());
    $('#app_calendar_endDate').val(orderEndDate.getTime());
}

function resetPaintedCells()
{
    $('.day_container').each(function () {
        var cell = $(this);
        if(cell.hasClass('active'))
            cell.toggleClass('active');
        if(cell.hasClass('edge'))
            cell.toggleClass('edge');
        if(cell.hasClass('selected'))
            cell.toggleClass('selected');
        if(cell.hasClass('full'))
            cell.toggleClass('full');
        if(cell.hasClass('not_assigned'))
            cell.toggleClass('not_assigned');
    });
}

function nextMonth(direction)
{
    var nextMonth = getDateClone(currentTime);
    nextMonth.setMonth(nextMonth.getMonth() + direction);
    if(monthDiff(calendarLimitStart, nextMonth) >= 0 && monthDiff(calendarLimitEnd, nextMonth) <= 0)
    {
        currentTime = getDateClone(nextMonth);
        renderMonth();
    }
}

function updateDaysOrderLabels()
{
    var key = currentTime.getTime();
    if(key in daysInfo)
    {
        var month = daysInfo[key];
        var counter = 0;
        $('.day_orders').each(function(index)
        {
            var field = $(this);
            var matched = false;
            if(counter < month.length)
                matched = setDayOrderLabel(counter, month, field, index);

            if(matched)
                counter++;
            else
                field.html('0');
        });
    }
}

function setDayOrderLabel(counter, month, field, index)
{
    var day = month[counter];
    if (getDayFromTimestamp(day['date']) === index + 1) {
        field.html(day['orderCount'] + '/' + day['capacity']);
        return true;
    }
    return false
}


function getDayFromTimestamp(timestamp)
{
    var date = new Date(timestamp * 1000);
    return date.getDate();
}

function getFirstDayOfMonth(date) {
    return getFirstDateOfMonth(date).getDay();
}

function getFirstDateOfMonth(date)
{
    var y = date.getFullYear();
    var x = date.getMonth();
    return new Date(y, x, 1);
}

function getDaysInMonth(date)
{
    return getLastDayOfMonth(date).getDate();
}
function getLastWeekDayOfMonth(date) {
    return getLastDayOfMonth(date).getDay();
}

function getLastDayOfMonth(date) {
    var y = date.getFullYear();
    var x = date.getMonth();
    return new Date(y, x + 1, 0);
}

function monthDiff(first, second) {
    var difference;
    difference = (second.getFullYear() - first.getFullYear()) * 12;
    difference -= first.getMonth();
    difference += second.getMonth();
    return difference;
}

function isDatesEqual(date1, date2)
{
    return date1.getTime() === date2.getTime();
}

function getDateClone(date)
{
    return new Date(date.getTime());
}