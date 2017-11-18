orderList = function(cal)
{
    var row = '.order_list_row';
    var table = '#order_list_table';

    var daysInfo = [],
        currentDay,
        calendar = cal;

    this.getOrders = function (date) {
        var key = date.getTime();
        currentDay = key;
        this.clearList();
        if (!(key in daysInfo)) {
            var data = {date: key};
            $.ajax({
                url: "/order/get",
                cache: false,
                type: 'POST',
                dataType: 'json',
                data: data,
                success: function (data) {
                    if (!(key in daysInfo)) {
                        daysInfo[key] = data;
                        updateList();
                    }
                }
            });
        }
        else
            updateList();
    };

    function updateList()
    {
        var orders = daysInfo[currentDay];
        var rowString;
        var length = orders.length;
        for(var i = 0; i < length; i++)
        {
            rowString = getRow(orders[i]);
            $(table).append(rowString);
        }
        updateListener();
    }

    function updateListener()
    {
        $(row).mouseover(function (){
            var index = $(this).index() - 1;
            var orders = daysInfo[currentDay];
            var order = orders[index];
            var startDate = timestampToDate(order['startDate']);
            var endDate = timestampToDate(order['endDate']);
            calendar.setTemporaryDates(startDate, endDate);
        });

        $(row).click(function (){
            var index = $(this).index() - 1;
            var id = daysInfo[currentDay][index]['id'];
            var url = "/admin/order/edit/" + id;
            window.location.href = url;
            /*$('#order_list_table').hide();
            $('#participants').show();
            getParticipants(daysInfo[currentDay][index]);*/
        });

        $(table).mouseleave(function()
        {
            calendar.resetTemporaryDates();
        });
    }

    function getParticipants(order) {
        var url = "/order/get/" + order['id'];
            $.ajax({
                url: url,
                cache: false,
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    setUpdateFields(data);
                }
            });
    }

    function setUpdateFields(data)
    {
        calendar.setDates(data['startDate'], data['endDate']);
        calendar.getManager().updateList(data);

    }

    this.clearList = function()
    {
        $('.order_list_row').remove();
    };

    function getRow(order)
    {
        return '<tr class="order_list_row">' +
            '<td>' + order['firstName'] + '</td>' +
            '<td>' + order['lastName'] + '</td>' +
            '<td>' + timestampToStringWithTime(order['orderedAt']) + '</td>' +
            '<td>' + timestampToString(order['startDate']) + '</td>' +
            '<td>' + timestampToString(order['endDate']) + '</td>' +
            '<td>' + order['participantCount'] + '</td>' +
            '</tr>';
    }

    function timestampToString(timestamp)
    {
        return dateToString(timestampToDate(timestamp));
    }

    function timestampToDate(timestamp)
    {
        var date = new Date(timestamp * 1000);
        return date;
    }

    function timestampToStringWithTime(timestamp)
    {
        var date = timestampToDate(timestamp);
        var dateString = dateToString(date);
        dateString += ' ' + formTimeField(date.getHours()) + ':' + formTimeField(date.getMinutes());
        return dateString;
    }

    function formTimeField(time)
    {
        if(time < 10)
            return '0' + time;
        return time;
    }

    function dateToString(date)
    {
        return date.getFullYear() + '-' + date.getMonth() + '-' + date.getDate();
    }
};