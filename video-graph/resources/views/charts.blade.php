<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Video Chart</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <h1 style="text-align: center; color: red;" class="mt-2">Video Chart</h1>

    {{-- button groups --}}
    {{-- button groups --}}
    <div class="container" style="text-align: end">
        <div class="btn-group mb-5" role="group" aria-label="Basic radio toggle button group">
            <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
            <label class="btn btn-outline-primary" for="btnradio1">Day</label>
            
            <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
            <label class="btn btn-outline-primary" for="btnradio2">Week</label>
            
            <input type="radio" class="btn-check" name="btnradio" id="btnradio3" autocomplete="off">
            <label class="btn btn-outline-primary" for="btnradio3">Month</label>
        </div>
    </div>
    {{-- button groups --}}
    {{-- button groups --}}
    <div class="container">
        <canvas id="chart"></canvas>
    </div>
    <script>
        var ctx = document.getElementById("chart");
        console.log('labels:',{!!json_encode($labels)!!} );
        console.log('Datasets:',{!!json_encode($datasets)!!} );
        var assets = new Chart(ctx,{
            type: 'bar',
            data: {
                labels: {!!json_encode($labels)!!},
                datasets: {!!json_encode($datasets)!!}
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>