$(document).ready(function () {

    showDashboardList();

    function getBinCard(content) {
        var card = '';
        card += '<div class="col-md-4 col-sm-6"><div class="card">';
        card += '<div class="title"><h3>' + content.bin_name + '</h3></div>';
        card += '<div class="shop-item-image" id="bin-' + content.bin_id + '"  style="min-width: 200px; height: 200px; margin: 0 auto"></div>';
        card += '<div class="bin-info">';
        card += '<div class="info-item" title="Total tweets"><i class="fa fa-twitter"></i>&nbsp; ' + content.bin_pharses.join(', ') + '</div>';
        card += '<div class="info-item" title="Bin duration"><i class="fa fa-calendar"></i>&nbsp; ' + content.data_start + ' ~ ' + content.data_end + '</div>';
        card += '<div class="info-item" title="Bin start time"><i class="fa fa-clock-o"></i>&nbsp; ' + content.period_start + '</div>';
        card += '<div class="info-item" title="Bin end time"><i class="fa fa-archive"></i>&nbsp; ' + content.peroid_end + '</div>';
        card += '</div>';
        card += '<div class="description"><p>' + content.bin_comment + '</p></div>';
        card += '<div class="actions"><a class="btn btn-info btn-small" href="#">See More Information&nbsp; <i class="fa fa-chevron-circle-right"></i></a></div>';
        card += '</div></div>';

        return card;
    }

    function buildRow(rowContent) {
        var prefix = '<div class="row">';
        var postfix = '</div>';
        return prefix + rowContent + postfix;
    }

    function showDashboardList() {
        jqxhr = $.getJSON('ajax_dashboard.php', {
            op: 'top',
            nr: 9
        });
        jqxhr.done(function (data) {
            if (data.rsStat) {
                var i = 1, delay = 0;
                var row = '';
                for (var key in data.rsContents) {
                    row += getBinCard(data.rsContents[key]);
                    if (i % 3 === 0) {
                        delay = Math.floor(i / 3) * 1000;
                        $("#page-wrapper").append(buildRow(row)).children(':last').hide().delay(delay).fadeIn(1000);
                        row = '';
                    }
                    i++;
                }

                delay = Math.floor(i / 3) * 1000;
                $("#page-wrapper").append(buildRow(row)).children(':last').hide().delay(delay).fadeIn(1000, function () {
                    drawAllChart();
                });

            } else {
                $("#msg_dialog").html('<img src="images/non-apply.gif" />&nbsp;' + data.rsStr);
                $("#msg_dialog").slideDown('slow');
                setTimeout(function () {
                    $("#msg_dialog").slideUp('slow', function () {
                        $("#msg_dialog").html('');
                    });
                }, 3000);
            }
        });
    }

    function drawAllChart() {
        $("div[id^='bin-']").each(function () {
            var bin_id = $(this).attr("id").replace('bin-', '');
            drawBinChart(bin_id);
        });
    }

    function drawBinChart(binID) {
        jqxhr = $.getJSON('ajax_dashboard.php', {
            op: 'chart',
            id: binID
        });
        jqxhr.done(function (data) {
            if (data.rsStat) {
                $('div#bin-' + binID).highcharts({
                    chart: {
                        type: 'column'
                    },
                    colors: [
                        '#0085c3',
                        '#7ab800',
                        '#f2af00',
                        '#dc5034',
                        '#ce1126',
                        '#6e2585'
                    ],
                    title: {
                        text: null
                    },
                    xAxis: {
                        categories: ['Tweets', 'Users', 'URLs', 'Mentions', 'Hashtags', 'Medias']
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Number of Tweets',
                            align: 'high'
                        },
                        labels: {
                            overflow: 'justify'
                        }
                    },
                    plotOptions: {
                        bar: {
                            dataLabels: {
                                enabled: true
                            }
                        },
                        column: {
                            colorByPoint: true
                        }
                    },
                    legend: {
                        enabled: false
                    },
                    series: [{
                            data: [data.rsContents.nrOfTweets, data.rsContents.nrOfUsers,
                                data.rsContents.nrOfURLs, data.rsContents.nrOfMentions,
                                data.rsContents.nrOfHashtags, data.rsContents.nrOfMedias],
                            dataLabels: {
                                enabled: true
                            }
                        }]
                });
            } else {
                $("#msg_dialog").html('<img src="images/non-apply.gif" />&nbsp;' + data.rsStr);
                $("#msg_dialog").slideDown('slow');
                setTimeout(function () {
                    $("#msg_dialog").slideUp('slow', function () {
                        $("#msg_dialog").html('');
                    });
                }, 3000);
            }
        });
    }

    function formatNumber(num) {
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
    }

});


