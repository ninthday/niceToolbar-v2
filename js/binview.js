$(document).ready(function () {
    showTimeSeries();
    
    $("#select-subbin").click(function () {
        var langs = $("input[name='language']:checkbox:checked").map(function () {
            return $(this).val();
        }).get();
        console.log(langs.join(', '));

        var condition = getSubBinCondition();

        $("#sbin-date").text(condition.dateStart + ' ~ ' + condition.dateEnd);
        $("#sbin-search").text(condition.searchKeyword);
        $("#sbin-user").text(condition.fromUser);
        $("#sbin-lang").text(condition.langs.join(', '));
        $("#sbin-bookmark").removeAttr("disabled");

        showTimeSeries();
    });

    $("#sbin-bookmark").click(function () {
        $(this).attr("disabled", "disabled");
    });

    function getSubBinCondition() {
        var langs = $("input[name='language']:checkbox:checked").map(function () {
            return $(this).val();
        }).get();
        var dateStart = $("input[name='startday']").val();
        var dateEnd = $("input[name='endday']").val();
        var searchKeyword = $("input[name='search-keyword']").val();
        var fromUser = $("input[name='from-user']").val();
        var res = $("input[name='resolution']:checked").val();

        var condition = {
            dateStart: dateStart,
            dateEnd: dateEnd,
            searchKeyword: searchKeyword,
            fromUser: fromUser,
            langs: langs,
            res: res
        };

        return condition;
    }

    function drawTimeSeries(contents) {
        $("#timeseries-chart").highcharts({
            chart: {
                type: 'line'
            },
            title: {
                text: 'Overview of Sub-Bin Data'
            },
            xAxis: {
                categories: contents.xCategory
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -50,
                y: 0,
                floating: true,
                borderWidth: 1,
                backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
            },
            plotOptions: {
                line: {
                    marker: {
                        enabled: false
                    }
                }
            },
            series: [{
                    name: 'Tweets',
                    color: '#55acee',
                    data: contents.series.nrOfTweets
                }, {
                    name: 'Users',
                    color: '#dc5034',
                    data: contents.series.nrOfUsers
                }, {
                    name: 'Retweets',
                    color: '#f2af00',
                    data: contents.series.nrOfRetweets
                }
            ]
        });
    }

    function drawMention() {

    }

    function drawHashtag() {

    }

    function drawMedia() {

    }

    function drawLanguage() {

    }

    function showTimeSeries() {
        var condition = getSubBinCondition();
        var binID = $("#bin-id").val();
        var strLangs = condition.langs.join('+');
        var rtn;
        var jqxhr = $.getJSON('ajax_binview.php', {
            op: 'ts',
            bid: binID,
            ds: condition.dateStart,
            de: condition.dateEnd,
            sk: condition.searchKeyword,
            fu: condition.fromUser,
            lg: strLangs,
            res: condition.res
        });
        jqxhr.done(function (data) {
            if (data.rsStat) {
                showMessage('success', 'Sub-Bin has been updated.');
                drawTimeSeries(data.rsContents);
            } else {
                showMessage('danger', data.rsContents);
            }
        });
        return rtn;
    }

    function getContainData() {

    }

    function getLanguageData() {

    }


    /**
     * 
     * @param {string} msgType
     * @param {type} msgContent
     * @returns {undefined}
     */
    function showMessage(msgType, msgContent) {
        $("#alert-message").removeClass().addClass("alert").addClass("alert-dismissible").addClass("alert-" + msgType);
        $("#alert-content").text('').text(msgContent);
        $("#alert-message").fadeIn(300).delay(6000).fadeOut(500);
    }
    ;
});