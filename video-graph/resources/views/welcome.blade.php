<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Video Chart</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <style>
       
        select {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center; color: red;" class="mt-2">Video Chart</h1>

    {{-- button groups --}}
    {{-- button groups --}}
    <div class="container" style="text-align: end">
        <div class="btn-group mb-5" role="group" aria-label="Basic radio toggle button group">
            <input type="radio" class="btn-check" name="btnradio" id="day" autocomplete="off">
            <label class="btn btn-outline-primary" for="day">Day</label>

            <input type="radio" class="btn-check" name="btnradio" id="week" autocomplete="off">
            <label class="btn btn-outline-primary" for="week">Week</label>

            <input type="radio" class="btn-check" name="btnradio" id="month" autocomplete="off">
            <label class="btn btn-outline-primary" for="month">Month</label>
        </div>
    </div>
    {{-- ..button groups --}}
    {{-- ..button groups --}}
    <label for="mySelect">Select an Option:</label>
    <select id="mySelect" name="mySelect">
        
        <option value="none">Select options </option>
        @foreach($sports as $sport)
        <option value="{{ $sport->sport }}">{{ $sport->sport }}</option>
        @endforeach
    </select>

    <div class="container">
        <canvas id="chart"></canvas>
    </div>
    
    <script>
        $(document).ready(function () {
            function fetchData(url) {
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
                var selectedInterval = $(this).attr('id');
                console.log(selectedInterval);
                var selectedSport = $('#mySelect').val();
                fetchData('/chart?interval=' + selectedInterval + '&selectedSport=' + selectedSport);
            });


            $('#mySelect').on('change', function () {
                var selectedInterval = $('input[name="btnradio"]:checked').attr('id');
                if (selectedInterval) {
                    var selectedSport = $(this).val();
                    fetchData('/chart?interval=' + selectedInterval + '&selectedSport=' + selectedSport);
                }
            });

            fetchData('/chart?interval=day&selectedSport=none');
       

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
                    }
                });
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>

