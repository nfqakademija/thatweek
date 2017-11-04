    var months = [];
    var currentDisplayedMonth = 4;

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
    }

    function renderMonth() {
        $('#calendar_header').empty().html(formMonthHeader());
        $('.calendarRow').remove();
        enableDisableButtons();

        var currentMonth = months[currentDisplayedMonth];
        var weekDay = currentMonth.startDay - 1;
        var tableRow = generateEmptyCells(weekDay);
        var weekIndex = 0;

        for (var i = 1; i <= currentMonth.endDay; i++) {
            weekDay++;
            tableRow += '<td>' + i + '</td>';
            if (weekDay % 7 == '0' || i == currentMonth.endDay) {
                if (weekIndex == '0' && startsAtPreviousMonth(currentMonth.weeks[0]) && i <= 7)
                    $('#calendar').append(getFormatedTableRow(-1, tableRow));
                else {
                    $('#calendar').append(getFormatedTableRow(weekIndex, tableRow));
                    weekIndex++;
                }
                tableRow = '';
            }
        }
    }

    function formMonthHeader() {
        return months[currentDisplayedMonth].name + ' ' + months[currentDisplayedMonth].year;
    }

    function getFormatedTableRow(weekIndex, tableRow) {
        return '<tr class="calendarRow" onmouseover="showWeekData(' + weekIndex + ')" onclick="setWeek(' + weekIndex + ')">' + tableRow + '</tr>';
    }

    function startsAtPreviousMonth(currentWeek) {
        var date = new Date(currentWeek['startDate'] * 1000);
        return date.getDate() != '1';
    }

    function nextMonth(direction) {
        if (direction == '-1' && currentDisplayedMonth > 0)
            currentDisplayedMonth--;
        else if (direction == '1' && currentDisplayedMonth < months.length - 1)
            currentDisplayedMonth++;
        else return;
        renderMonth();
    }

    function enableDisableButtons() {
        var leftButton = $('#calendarLeftBtn');
        var rightButton = $('#calendarRightBtn');
        if (currentDisplayedMonth == 0)//disable 'go backwards' button if user is in first month
            leftButton .toggleClass('disabled');
        else if (leftButton .hasClass('disabled'))//enable it if user went forward
            leftButton .toggleClass('disabled');

        if (currentDisplayedMonth == months.length - 1)//disable 'go forward' button if user is in last month
            rightButton.toggleClass('disabled');
        else if (rightButton.hasClass('disabled'))//enable it if user went backwards
            rightButton.toggleClass('disabled');
    }

    function showWeekData(index) {
        var week = months[currentDisplayedMonth].weeks[index];
        if (index < 0) {
            if (currentDisplayedMonth > 0) {
                var previousMonth = months[currentDisplayedMonth - 1];
                week = previousMonth.weeks[previousMonth.weeks.length - 1];
            }
            else {
                resetInfoLabels();
                return;
            }
        }
        updateInfoLabels(week);
    }

    function setWeek(index) {
        var week = months[currentDisplayedMonth].weeks[index];
        $('#appbundle_order_week').val(week['id']);
    }

    function updateInfoLabels(data) {
        $('#weekLabel').html(getFullDate(data['startDate']) + ' - ' + getFullDate(data['endDate']));
        $('#unitsSoldLabel').html('Užimtų vietų ' + data['unitsSold']);
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
        return difference <= 0 ? 0 : difference
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

    function generateEmptyCells(cells) {
        var row = '';
        cells = cells < 0 ? 6 : cells;
        for (var i = 0; i < cells; i++) {
            row += '<td></td>';
        }
        return row;
    }