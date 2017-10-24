<!doctype html>
<html>

<head>
    <title>Line Chart</title>
    <script src="js/chartjs/Chart.bundle.js"></script>
    <script src="js/chartjs/utils.js"></script>
    <style>
        canvas{
            -moz-user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }
    </style>
</head>

<body>
<div style="width:75%;">
    <canvas id="canvas"></canvas>
</div>
<br>
<br>
<script src="/js/jquery-3.2.1.min.js"></script>
<script>
    var prices = [], dates = [];
    <?php
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    ?>
        $.ajax({
            url: '/class/Historial.php',
            method: 'POST',
            data: {
                get: 'historial',
                product_id: <?php echo $_GET['id']; ?>
            },
            dataType: 'JSON',
            success: function (data) {
                console.log(data);
                data.historial.forEach(function (precio) {
                    prices.push(precio.price_new);
                    dates.push(precio.created_at);
                });

                var config = {
                    type: 'line',
                    data: {
                        labels: dates,
                        datasets: [{
                            label: "Precio ($)",
                            backgroundColor: window.chartColors.blue,
                            borderColor: window.chartColors.blue,
                            data: prices,
                            fill: false
                        }
                        ]
                    },
                    options: {
                        responsive: true,
                        title:{
                            display:true,
                            text:'Historial de precios'
                        },
                        tooltips: {
                            mode: 'index',
                            intersect: false
                        },
                        hover: {
                            mode: 'nearest',
                            intersect: true
                        },
                        scales: {
                            xAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Fecha'
                                }
                            }],
                            yAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Precio'
                                }
                            }]
                        }
                    }
                };

                var ctx = document.getElementById("canvas").getContext("2d");
                window.myLine = new Chart(ctx, config);
            },
            error: function (data) {
                console.log(data);
            }
        });
    <?php
        }
    ?>
</script>
</body>

</html>