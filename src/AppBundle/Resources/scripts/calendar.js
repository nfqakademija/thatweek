calendar = function(time, isAdmin) {
    var serverTime = 0,
        calendarLimitStart,
        calendarLimitEnd,
        calendarLimitByMonths = 12,
        currentTime,
        orderStartDate,
        orderEndDate,
        tmpStartDate,
        tmpEndDate,
        tmpSet = false,
        orderStartDateCellActive = false,
        daysInfo = [],
        lastDateClicked,
        admin = false,
        list,
        manager,
        startingParticipantCount = 0,
        originalStartDate,
        originalEndDate;

    var monthNames = ['Sausis', 'Vasaris', 'Kovas', 'Balandis', 'Gegužė', 'Birželis',
        'Liepa', 'Rugpjūtis', 'Rugsėjis', 'Spalis', 'Lapkritis', 'Gruodis'];

    function retrieveMonthInfo(date) {
        var start = getFirstDateOfMonth(date);
        var end = getLastDayOfMonth(date);
        var data = {startDate: start.getTime(), endDate: end.getTime()};
        retrieveDaysInfo(date, data);
    }

    function registerMouseLeaveListener() {
        $('#calendar').mouseleave(function () {
            if (orderStartDate != null) {
                var data = {startDate: orderStartDate.getTime(), endDate: orderEndDate.getTime()};
            }
        });
    }

    function retrieveDaysInfo(date, data) {

        var key = date.getTime();
        if (!(key in daysInfo)) {
            $.ajax({
                url: "/order/check",
                cache: false,
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function (data) {
                    setDaysInfo(key, data);
                }
            });
        }
    }

    function setDaysInfo(key, data) {
        daysInfo[key] = data;
        if (!admin)
            paintSelectedCells();
        else updateDaysOrderLabels();
    }

    this.initializeParticipantManager = function(JsonData)
    {
        manager.stringToArray(JsonData);
        startingParticipantCount = manager.getCheckedCount();
    };

    this.initialize = function(isAdmin) {
        admin = isAdmin;
        manager = new participantManager(this);

        if(!admin)
            registerMouseLeaveListener();
        else
            list = new orderList(this);

        initializeCurrentTime();
        serverTime = getDateClone(currentTime);
        calendarLimitStart = getDateClone(currentTime);
        calendarLimitEnd = getDateClone(currentTime);
        calendarLimitEnd.setMonth(calendarLimitEnd.getMonth() + calendarLimitByMonths);
        renderMonth();

    };

    function initializeCurrentTime()
    {
        currentTime = new Date();
        currentTime.setUTCHours(0);
        currentTime.setUTCMinutes(0);
        currentTime.setUTCSeconds(0);
        currentTime.setUTCMilliseconds(0);
    }

    function renderMonth() {
        var monthStartDay = getFirstDayOfMonth(currentTime);
        var cells = generateEmptyCells(monthStartDay - 1);
        var daysInCurrentMonth = getDaysInMonth(currentTime);

        retrieveMonthInfo(currentTime);

        $('#calendar_headline').html(monthNames[currentTime.getMonth()]);
        $('#calendar_year_label').html(currentTime.getFullYear());
        $('.day_container').remove();
        for (var i = 1; i <= daysInCurrentMonth; i++) {
            cells += formDayCell(i);
        }

        var cellsToFinish = 7 - getLastWeekDayOfMonth(currentTime);
        cellsToFinish = cellsToFinish === 7 ? 0 : cellsToFinish;
        cells += generateEmptyCells(cellsToFinish);

        $('#calendar').append(cells);

        updateCalendarListeners();
        paintSelectedCells();
    }

    function generateEmptyCells(cellCount) {
        cellCount = cellCount < 0 ? 6 : cellCount;
        var cells = '';
        for (var i = 0; i < cellCount; i++) {
            cells += '<div class="calendar_element day_container empty"></div>';
        }
        return cells;
    }


    function formDayCell(number) {
        return '<div class="calendar_element day_container day"><div class="day_number">' + number + '</div>' +
            '<div class="day_orders_container"><div class="day_orders"></div></div></div>';
    }

    function updateCalendarListeners() {
        $('.day_container').click(function () {
            var index = $(this).index('.day');
            if (index !== -1) {
                var day = index + 1;
                updateOrderDates(day);
            }
        });
    }

    function updateOrderDates(day) {
        var clickedDate = getDateClone(currentTime);
        clickedDate.setDate(day);
        if (orderStartDate != null && isDatesEqual(clickedDate, orderStartDate))//if clicked on order start date - highlight it and allow to change
        {
            if (orderStartDateCellActive) {
                orderStartDateCellActive = false;
                setBothDates(clickedDate);
            }
            else orderStartDateCellActive = true;
        }

        else if (orderStartDate == null || isDatesEqual(lastDateClicked, clickedDate))//On first click on calendar
            setBothDates(clickedDate);
        else if (orderStartDateCellActive)
            changeActiveCellsWhenFirstIsSelected(clickedDate);
        else if (clickedDate > orderStartDate)
            orderEndDate = getDateClone(clickedDate);
        else if (clickedDate < orderStartDate)
            orderStartDate = getDateClone(clickedDate);
        if(admin) {
            if (isDatesEqual(orderStartDate, orderEndDate))
                list.getOrders(clickedDate);
            else list.clearList();
        }
        lastDateClicked = clickedDate;
        updateOrderDatesInForm();
        paintSelectedCells();
    }

    function setBothDates(date) {
        orderStartDate = getDateClone(date);
        orderEndDate = getDateClone(date);
    }

    function changeActiveCellsWhenFirstIsSelected(clickedDate) {
        if (isDatesEqual(orderStartDate, orderEndDate)) {//when only one cell is active, move both dates to clicked date
            orderEndDate = getDateClone(clickedDate);
            orderStartDate = getDateClone(clickedDate);
        }
        else if (clickedDate < orderEndDate) {
            orderStartDate = getDateClone(clickedDate);
        }
        else if (clickedDate > orderEndDate) {//swap start and end dates, if user moved starting date to later date than ending
            orderStartDate = getDateClone(orderEndDate);
            orderEndDate = getDateClone(clickedDate);
        }
        else {
            orderStartDate = getDateClone(orderEndDate);
        }
        orderStartDateCellActive = false;
    }

     this.paintSelectedCells = function() {
        resetPaintedCells();
        updateDaysOrderLabels();
        var date = getDateClone(currentTime);
        var key = currentTime.getTime();
        if (key in daysInfo) {
            var month = daysInfo[key];
            var counter = 0;
            $('.day_container').each(function () {
                var field = $(this);
                var dayNumber = field.index('.day') + 1;
                if (dayNumber > 0) {
                    date.setDate(dayNumber);
                    var end = counter >= month.length ? true : false;
                    var isEmpty = true;
                    var isDayFull = false;
                    var day;

                    if (!end) {
                        day = month[counter];
                        isEmpty = !(dayNumber === getDayFromTimestamp(day['date']));

                        if(!isEmpty)
                            counter++;

                        if(date <= serverTime)
                            isEmpty = true;

                        else if (!isEmpty) {
                            if (day['capacity'] === 0)
                                isEmpty = true;
                            else if (isNotSelectedDayFull(day))
                                isDayFull = true;
                        }
                    }

                    if (orderStartDate <= date && date <= orderEndDate) {
                        field.toggleClass('active');

                        if (isDatesEqual(orderStartDate, date) || isDatesEqual(orderEndDate, date)) {
                            if (isDatesEqual(orderStartDate, date) && orderStartDateCellActive === true)
                                field.toggleClass('selected');
                            field.toggleClass('edge');
                        }
                    }

                    if (admin === false) {
                        if (isEmpty)
                            field.toggleClass('not_assigned');

                        if (isDayFull)
                            field.toggleClass('full');
                    }
                }
            });
        }
    };

    function isNotSelectedDayFull(day)
    {
        if(getAlreadyExisting(day) === 0)
            if (day['capacity'] === day['participantCount'])
                return true;

        return day['capacity'] < day['participantCount'] + getCheckedParticipantsLength() - getAlreadyExisting(day);
    }

    function getAlreadyExisting(day)
    {
        var date = timestampToDate(day['date']);

        if(originalStartDate <= date && date <= originalEndDate)
            return startingParticipantCount;

        return 0;
    }

    this.setDatesFromJson = function(dataString)
    {
        var dates = JSON.parse(dataString);
        this.setDates(dates['startDate'], dates['endDate']);
        originalStartDate = timestampToDate(dates['startDate']);
        originalEndDate = timestampToDate(dates['endDate']);
    };

    this.setDates = function(startTimestamp, endTimestamp)
    {
        orderStartDate = timestampToDate(startTimestamp);
        orderEndDate = timestampToDate(endTimestamp);
        tmpSet = false;
        this.paintSelectedCells();
        updateOrderDatesInForm();
    };

    function updateOrderDatesInForm() {
        $('#app_order_startDate').val(orderStartDate.getTime());
        $('#app_order_endDate').val(orderEndDate.getTime());
        $('#app_day_update_startDate').val(orderStartDate.getTime());
        $('#app_day_update_endDate').val(orderEndDate.getTime());
    }

    function resetPaintedCells() {
        $('.day_container').each(function () {
            var cell = $(this);
            if (cell.hasClass('active'))
                cell.toggleClass('active');
            if (cell.hasClass('edge'))
                cell.toggleClass('edge');
            if (cell.hasClass('selected'))
                cell.toggleClass('selected');
            if (cell.hasClass('full'))
                cell.toggleClass('full');
            if (cell.hasClass('not_assigned'))
                cell.toggleClass('not_assigned');
        });
    }

    this.nextMonth = function (direction) {
        var nextMonth = getDateClone(currentTime);
        nextMonth.setMonth(nextMonth.getMonth() + direction);
        if (monthDiff(calendarLimitStart, nextMonth) >= 0 && monthDiff(calendarLimitEnd, nextMonth) <= 0) {
            currentTime = getDateClone(nextMonth);
            renderMonth();
        }
    };

    function updateDaysOrderLabels(){
        var key = currentTime.getTime();
        if (key in daysInfo) {
            var month = daysInfo[key];
            var counter = 0;
            $('.day_orders').each(function (index) {
                var field = $(this);
                var matched = false;
                if (counter < month.length)
                    matched = setDayOrderLabel(counter, month, field, index);

                if (matched)
                    counter++;
                else if(admin)
                    field.html('0');
            });
        }
    }

    function setDayOrderLabel(counter, month, field, index) {
        var day = month[counter];
        if (getDayFromTimestamp(day['date']) === index + 1) {
            if(admin)
                field.html(day['participantCount'] + '/' + day['capacity']);
            else if(timestampToDate(day['date']) > serverTime)
                field.html(getUnitsLeft(day));

            return true;
        }
        return false
    }

    function getUnitsLeft(day)
    {
        var count = 0;
        var date = timestampToDate(day['date']);

        if(orderStartDate <= date && date <= orderEndDate)
            count = day['capacity'] - day['participantCount'] - getCheckedParticipantsLength() + getAlreadyExisting(day);
        else count = day['capacity'] - day['participantCount'];

        return count < 0 ? 0 : count;
    }

    function getCheckedParticipantsLength() {
        return manager.getCheckedCount();
    }

    this.setTemporaryDates = function(startDate, endDate)
    {
        if(!tmpSet) {
            tmpStartDate = getDateClone(orderStartDate);
            tmpEndDate = getDateClone(orderEndDate);
            tmpSet = true;
        }
        orderStartDate = getDateClone(startDate);
        orderEndDate = getDateClone(endDate);
        paintSelectedCells();
    };

    this.resetTemporaryDates = function()
    {
        if(tmpSet) {
            orderStartDate = getDateClone(tmpStartDate);
            orderEndDate = getDateClone(tmpEndDate);
            tmpSet = false;
            paintSelectedCells();
        }
    };

    function getDayFromTimestamp(timestamp) {
        var date = timestampToDate(timestamp);
        return date.getDate();
    }

    function timestampToDate(timestamp)
    {
        return new Date(timestamp * 1000);
    }

    function getFirstDayOfMonth(date) {
        return getFirstDateOfMonth(date).getDay();
    }

    function getFirstDateOfMonth(date) {
        var y = date.getFullYear();
        var x = date.getMonth();
        return new Date(y, x, 1);
    }

    function getDaysInMonth(date) {
        return getLastDayOfMonth(date).getDate();
    }

    function getLastWeekDayOfMonth(date) {
        return getLastDayOfMonth(date).getDay();
    }

    function getLastDayOfMonth(date) {
        var newDate = getDateClone(date);
        var y = newDate.getFullYear();
        var x = newDate.getMonth();
        var lastDay = new Date(y, x + 1, 0);
        newDate.setUTCDate(lastDay.getDate());
        return newDate;
    }

    function monthDiff(first, second) {
        var difference;
        difference = (second.getFullYear() - first.getFullYear()) * 12;
        difference -= first.getMonth();
        difference += second.getMonth();
        return difference;
    }

    function isDatesEqual(date1, date2) {
        if(date1 === undefined || date2 === undefined)
            return false;
        return date1.getTime() === date2.getTime();
    }

    function getDateClone(date) {
        return new Date(date.getTime());
    }

    this.getManager = function(){
        return manager;
    };

    this.setIsAdmin = function(bool)
    {
        admin = bool;
    };

    return this;
};