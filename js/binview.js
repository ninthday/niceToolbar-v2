$(document).ready(function () {
    showTimeSeries();
    showContain();
    showLanguagePercentage();
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

        showSubBinBasic();
        showTimeSeries();
        showContain();
        showLanguagePercentage();
    });

    $("#redraw-resolution").click(function () {
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

    function drawMention(contents) {
        var hasContain = Math.floor((contents.nrOfMentions / contents.nrOfTweets) * 10000) / 100;
        var notContain = 100 - hasContain;
        $("#mention-num").text(contents.nrOfMentions.toLocaleString());
        $('#mention-chart').highcharts({
            chart: {
                type: 'pie'
            },
            title: {
                text: null
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.2f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            series: [{
                    name: "Contain Mention",
                    colorByPoint: true,
                    data: [{
                            name: "No",
                            color: '#0057b8',
                            y: notContain
                        }, {
                            name: "Yes",
                            color: '#e53238',
                            y: hasContain
                        }]
                }]
        });
    }

    function drawHashtag(contents) {
        var hasContain = Math.floor((contents.nrOfHashtags / contents.nrOfTweets) * 10000) / 100;
        var notContain = 100 - hasContain;
        $("#hashtag-num").text(contents.nrOfHashtags.toLocaleString());
        $('#hashtag-chart').highcharts({
            chart: {
                type: 'pie'
            },
            title: {
                text: null
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.2f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            series: [{
                    name: "Contain Hastag",
                    colorByPoint: true,
                    data: [{
                            name: "No",
                            color: '#0057b8',
                            y: notContain
                        }, {
                            name: "Yes",
                            color: '#e53238',
                            y: hasContain
                        }]
                }]
        });
    }

    function drawMedia(contents) {
        var hasContain = Math.floor((contents.nrOfMedias / contents.nrOfTweets) * 10000) / 100;
        var notContain = 100 - hasContain;
        $("#media-num").text(contents.nrOfMedias.toLocaleString());
        $('#media-chart').highcharts({
            chart: {
                type: 'pie'
            },
            title: {
                text: null
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.2f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            series: [{
                    name: "Contain Media",
                    colorByPoint: true,
                    data: [{
                            name: "No",
                            color: '#0057b8',
                            y: notContain
                        }, {
                            name: "Yes",
                            color: '#e53238',
                            y: hasContain
                        }]
                }]
        });
    }

    function drawLanguage(contents) {
        var dataLangs = [];
        var arrColor = ['#ce1126', '#f2af00', '#7ab800', '#0085c3', '#b7295a', '#71c6c1', '#009bbb'];
        for (var i = 0, len = contents.length; i < len; ++i) {
            var langCount = {
                name: contents[i].lang,
                color: arrColor[i],
                y: contents[i].cnt
            };
            dataLangs.push(langCount);
        }
        $('#language-chart').highcharts({
            chart: {
                type: 'pie'
            },
            title: {
                text: null
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.2f}%</b>'
            },
            legend: {
                align: 'right',
                verticalAlign: 'top',
                layout: 'vertical',
                x: 0,
                y: 10
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            series: [{
                    name: "Language",
                    colorByPoint: true,
                    data: dataLangs
                }]
        });
    }

    function showTimeSeries() {
        var condition = getSubBinCondition();
        var binID = $("#bin-id").val();
        var strLangs = condition.langs.join('+');
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
                drawTimeSeries(data.rsContents);
            } else {
                showMessage('danger', data.rsContents);
            }
        });
    }

    function showContain() {
        var condition = getSubBinCondition();
        var binID = $("#bin-id").val();
        var strLangs = condition.langs.join('+');
        var jqxhr = $.getJSON('ajax_binview.php', {
            op: 'ctn',
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
                drawMention(data.rsContents);
                drawHashtag(data.rsContents);
                drawMedia(data.rsContents);
            } else {
                showMessage('danger', data.rsContents);
            }
        });
    }

    function showLanguagePercentage() {
        var condition = getSubBinCondition();
        var binID = $("#bin-id").val();
        var strLangs = condition.langs.join('+');
        var jqxhr = $.getJSON('ajax_binview.php', {
            op: 'lang',
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
                drawLanguage(data.rsContents);
            } else {
                showMessage('danger', data.rsContents);
            }
        });
    }

    function showSubBinBasic() {
        var condition = getSubBinCondition();
        var binID = $("#bin-id").val();
        var strLangs = condition.langs.join('+');
        var jqxhr = $.getJSON('ajax_binview.php', {
            op: 'bi',
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
                $("#basic-tweetnum").text(data.rsContents.nrOfTweets.toLocaleString());
                $("#basic-usernum").text(data.rsContents.nrOfUsers.toLocaleString());
            } else {
                showMessage('danger', data.rsContents);
            }
        });
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