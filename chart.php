<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            width: 400px;
            height: 400px;
            margin: auto;
            margin-top: 100px;
        }

        #btn_chart_excel_download {
            border: 0;
            background-color: #007f1b;
            color: white;
            font-weight: bold;
            padding: 5px;
            border-radius: 10px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-polyfill/6.26.0/polyfill.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-piechart-outlabels"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.2.0/exceljs.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.0/FileSaver.min.js"></script>
    <select onchange="changeChart(this.value)">
        <option value="line">라인</option>
        <option value="pie">파이</option>
        <option value="doughnut">도넛</option>
        <option value="bar">막대(세로)</option>
        <option value="horizontalBar">막대(가로)</option>
    </select>
    <input type="button" value="엑셀 다운로드" id="btn_chart_excel_download" class="btn_excel"/>
    <canvas id="myChart" width="100" height="100"></canvas>
    <script>
        // tooltip을 렌더링 이후 바로 표시하기 위해서는 다음 소스가 추가되어야함
        Chart.plugins.register({
            beforeRender: function (chart) {
                if (chart.config.options.showAllTooltips) {
                    // create an array of tooltips
                    // we can't use the chart tooltip because there is only one tooltip
                    // per chart
                    chart.pluginTooltips = [];
                    chart.config.data.datasets.forEach(function (dataset, i) {
                        chart.getDatasetMeta(i).data.forEach(function (sector, j) {
                            chart.pluginTooltips.push(new Chart.Tooltip({
                                _chart: chart.chart,
                                _chartInstance: chart,
                                _data: chart.data,
                                _options: chart.options.tooltips,
                                _active: [sector]
                            }, chart));
                        });
                    });

                    // turn off normal tooltips
                    chart.options.tooltips.enabled = false;
                }
            },
            afterDraw: function (chart, easing) {
                if (chart.config.options.showAllTooltips) {
                    // we don't want the permanent tooltips to animate, so don't do
                    // anything till the animation runs atleast once
                    if (!chart.allTooltipsOnce) {
                        if (easing !== 1)
                            return;
                        chart.allTooltipsOnce = true;
                    }

                    // turn on tooltips
                    chart.options.tooltips.enabled = true;
                    Chart.helpers.each(chart.pluginTooltips, function (tooltip) {
                        tooltip.initialize();
                        tooltip.update();
                        // we don't actually need this since we are not animating
                        // tooltips
                        tooltip.pivot();
                        tooltip.transition(easing).draw();
                    });
                    chart.options.tooltips.enabled = false;
                }
            }
        });
    </script>
    <script>
        let data = {
            labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
            datasets: [{
                fill: false,
                label: '# of Votes',
                data: [12, 19, 3, 5, 2, 3],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        };

        let options = {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
            legend: {
                display: false, // label 숨기기
            },
            title: {
                display: true, // title 표시
                text: '# title', // title 명
            },
            maintainAspectRatio: false, // 비율유지
            showAllTooltips: false, // tooltip 항상표시
            tooltips: {
                enabled: true, // tooltip 표시(기본은 마우스 hover)
            },
            plugins: {
                legend: true, // legend 표시
                outlabels: {
                    text: '%l \n %v',
                    borderRadius: 15,
                    color: 'black',
                    stretch: 30,
                    font: {
                        resizable: true,
                        minSize: 12,
                        maxSize: 18
                    },
                    textAlign: "center"
                } // pie, doughnut 때문에 외부 라이브러리 사용(tooltip을 항상 표시 하는 경우 겹쳐서)
            },
            layout: {
                padding: {
                    left: 25,
                    right: 25,
                    top: 0,
                    bottom: 0
                }
            }
        };

        function chartUpdate(type, options, title) {
            if (type == "doughnut" || type == "pie") {
                delete options.scales;
                options.title.text = '';
                options.plugins.outlabels = {
                    text: '%l \n %v',
                    borderRadius: 15,
                    color: 'black',
                    stretch: 30,
                    font: {
                        resizable: true,
                        minSize: 12,
                        maxSize: 18
                    },
                    textAlign: "center"
                };
                options.layout.padding.top = 50;
                options.layout.padding.bottom = 50;
            } else {
                options.title.text = title;
                options.scales = {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
                delete options.plugins.outlabels;
                options.layout.padding.top = 50;
                options.layout.padding.bottom = 50;
            }
        }

        function changeChart(value) {
            myChart.destroy();

            chartUpdate(value, options, '# title');

            myChart = new Chart(ctx, {
                type: value,
                data: data,
                options: options,
            });
        };
        const ctx = document.getElementById('myChart'); // getContext('2d') 를 하는 경우가 있는데 없어야 엑셀다운가능
        let myChart = new Chart(ctx, {
            type: 'line',
            data: data,
            options: options
        });

        $(function () {
            $("#btn_chart_excel_download").click(function () {
                // 캔버스에 그려진 이미지를 data url로 변환
                let base64Image = ctx.toDataURL(1.0);

                // excel js 객체 생성
                let workbook = new ExcelJS.Workbook();

                // 워크시트 생성
                let worksheet = workbook.addWorksheet('Sheet');

                // 흰 배경을 만들기 위해 셀 병합
                worksheet.mergeCells('A1:H25');

                // 가상의 파일 읽기
                workbook.xlsx.readFile("chartExample.xlsx");

                // 이미지 등록
                let imageId = workbook.addImage({
                    base64: base64Image,
                    extension: 'png',
                });

                // 병합했던 셀에 이미지 추가 (엑셀 파일 열면 위치 이동가능)
                worksheet.addImage(imageId, 'A1:H25');

                // 파일 다운로드
                workbook.xlsx.writeBuffer().then(function (data) {
                    let blob = new Blob([data], {type: "application/vnd.ms-excel;charset=utf-8"});
                    saveAs(blob, "chartReal.xlsx");
                });
            });
        });
    </script>
</head>

<body>
</body>

</html>