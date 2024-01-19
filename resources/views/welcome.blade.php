<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Video Chart</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <style>
        select {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .d-flex {
            display: flex !important;
            align-content: flex-start;
            justify-content: space-between;
            flex-direction: row-reverse;
        }
        
    </style>
    
</head>
<body>
    <h1 style="text-align: center; color: red;" class="mt-2">Video Chart</h1>

    <h2 id="userinfo" style="text-align: center; color: red;">  </h2>
    <div class="container d-flex my-3">
        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
            <input type="radio" class="btn-check" name="btnradio" id="day" autocomplete="off">
            <label class="btn btn-outline-primary" for="day">Day</label>

            <input type="radio" class="btn-check" name="btnradio" id="week" autocomplete="off">
            <label class="btn btn-outline-primary" for="week">Week</label>

            <input type="radio" class="btn-check" name="btnradio" id="month" autocomplete="off">
            <label class="btn btn-outline-primary" for="month">Month</label>
        </div>

        <select id="mySelect" name="mySelect">
            <option value="none">Select options </option>
            @foreach($sports as $sport)
                <option value="{{ $sport->sport }}">{{ $sport->sport }}</option>
            @endforeach
        </select>

        <div class="input-daterange d-flex" id="date-picker">
            <input type="text" id="start" class="form-control" placeholder="End Date" />
            <span class="mx-2">to</span>
            <input type="text" id="datepicker-end" class="form-control" placeholder="Start Date" />
        </div>

    </div>
    <div class="container">
        <div class="row"> 
           <select id="monthDropdown" class="col-2 mx-3">
                <option value="">Select Month</option>
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}">{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                @endfor
            </select>

            <select id="yearDropdown" class="col-2">
                <option value="">Select Year</option>
                <option value="2022">2022</option>
                <option value="2023">2023</option>
            </select>
        </div>
    </div>
    <div class="container">
        <canvas id="chart"></canvas>
    </div>

    <div class="container">
        <div class="modal" tabindex="-1" id="myModal">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Modal title</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <p>Modal body text goes here.</p>
                  <div class="container">
                        <canvas id="pieChart"></canvas>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>
    </div>
    <script>
        $(document).ready(function () {
            $('.input-daterange input').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });

            function fetchData(url) {
                console.log(url);
                $.ajax({
                    url: url,
                    method: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        updateChart(response.datasets, response.labels);
                    },
                    error: function (error) {
                        console.error('Error fetching chart data:', error);
                    }
                });
            }

            $('input[name="btnradio"]').on('click', function () {
                $('.input-daterange input').show();
                $('.input-daterange span').show();
                updateChartWithDateRange();
            });

            $('#mySelect').on('change', function () {
                updateChartWithDateRange();
            });
            function updateChartWithDateRange() {
                var selectedInterval = $('input[name="btnradio"]:checked').attr('id');
                var selectedSport = $('#mySelect').val();
                var startDate = $('#start').val();
                var endDate = $('#datepicker-end').val();
                fetchData(`/chart?interval=${selectedInterval}&selectedSport=${selectedSport}&startDate=${startDate}&endDate=${endDate}`);
            }
  

            var assets = null;
            function updateChart(datasets, labels) {
                var ctx = document.getElementById("chart");
                if (assets !== null) {
                    assets.destroy();
                }
                console.log('labels:', labels);
                console.log('Datasets:', datasets);
                assets = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: datasets
                    },options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        },
                        onClick: function (event, elements) {
                            if (elements.length > 0) {
                                var clickedBarIndex = elements[0].index;
                                var clickedDatasetIndex = elements[0].datasetIndex;
                                var clickedUserData = this.data.datasets[clickedDatasetIndex].data[clickedBarIndex];
                                var clickedUser = clickedUserData.labels;
                                var clickedLabel = this.data.labels[clickedBarIndex];
                                var modalBody = 'User Data: ' + clickedUserData;
                                var selectedInterval = $('input[name="btnradio"]:checked').attr('id');
                                document.getElementById('myModal').querySelector('.modal-title').innerText = clickedLabel;
                                document.getElementById('myModal').querySelector('.modal-body p').innerText = modalBody;
                                var myModal = new bootstrap.Modal(document.getElementById('myModal'));
                                myModal.show();
                                piechartdata(`/pie?datadate=${clickedLabel}&interval=${selectedInterval}`);
                            }
                        }
                    }
                });
            }
            function piechartdata(url) {
                console.log(url);
                $.ajax({
                    url: url,
                    method: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        PieChart(response.datasets, response.labels);
                    },
                    error: function (error) {
                        console.error('Error fetching chart data:', error);
                    }
                });
            }
            var charts = null;
            function PieChart(datasets, labels) {
                var ctx = document.getElementById("pieChart");
                if (charts !== null) {
                    charts.destroy();
                }
                console.log('labels:', labels);
                console.log('Datasets:', datasets);
                charts = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: datasets
                    }
                });
            }

            // dropdown section 
            // dropdown section 
            $('#monthDropdown,  #yearDropdown').on('change', function () {
                $('.input-daterange input').hide();
                $('.input-daterange span').hide();
            });
            $('#monthDropdown, #mySelect, #yearDropdown').on('change', function () {
                var selectedMonth = $("#monthDropdown").val();
                var selectedSport = $('#mySelect').val();
                var year =$('#yearDropdown').val();

                if (selectedMonth !== '') {
                    fetchData(`/chart?interval=month&selectedmonth=${selectedMonth}&selectedSport=${selectedSport}&year=${year}`);
                } else {
                    fetchData(`/chart?interval=month&selectedSport=none&startDate=&endDate=`);
                }
            });
            fetchData(`/chart?interval=day&selectedSport=none&startDate=&endDate=`);
            // .dropdown section 
            // .dropdown section 

            


        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
