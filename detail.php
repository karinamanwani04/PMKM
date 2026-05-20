<?php include("config/db.php");

$id = $_GET['id'];

$result = $conn->query("SELECT * FROM companies WHERE id=$id");

$row = $result->fetch_assoc();

$symbol = $row['stock_symbol'];
?>

<!DOCTYPE html>
<html>

<head>

    <title>Company Detail</title>

    <link rel="stylesheet" href="style.css?v=2">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>

    <div class="header">

        <div style="display:flex; justify-content:space-between; align-items:center; width:100%;">
            <h2 style="margin:0;">Smart Company Analytics and Stock Monitoring System <button id="darkBtn">
                    🌙 Dark Mode
                </button> </h2>
            <h4 style="text-align:right;">Presented by: <b>Karina Manwani & Payal Maghnani</b></h4>
        </div>

    </div>

    <div class="container">

        <!-- COMPANY CARD -->

        <div class="card">

            <div class="topActions">

                <button onclick="saveFavorite()">
                    ❤️ Favorite
                </button>

                <button onclick="downloadReport()">
                    📄 Download Report
                </button>

            </div>

            <h2><?php echo $row['name']; ?></h2>

            <p id="desc">
                <?php echo $row['description']; ?>
            </p>

            <button onclick="generateSummary()">
                know more...
            </button>

            <div id="summary"></div>

        </div>

        <!-- ANALYTICS CHART -->

        <div style="background:white; padding:20px; border-radius:14px; width:80%; margin:auto; margin-top:25px;">

            <h2>Company Analytics</h2>

            <div style="height:350px; width:100%;">

                <canvas id="analyticsChart"></canvas>

            </div>

        </div>

        <!-- STOCK CHART -->

        <div style="margin-top:25px;">

            <div style="background:white; padding:20px; border-radius:14px;">

                <h2>Last 7 Days Stock</h2>

                <div style="height:300px;">

                    <canvas id="weekChart"></canvas>

                </div>

            </div>

        </div>
        <div style="margin-top:25px;">

            <div style="background:white; padding:20px; border-radius:14px;">

                <h2>Last 1 Month Stock</h2>

                <div style="height:300px;">

                    <canvas id="monthChart"></canvas>

                </div>

            </div>

        </div>

    </div>

    <script>
        // AI SUMMARY

        function generateSummary() {

            let text = document.getElementById("desc").innerText;

            document.getElementById("summary").innerHTML = "Generating...";

            fetch("api/ai.php", {

                    method: "POST",

                    headers: {
                        "Content-Type": "application/json"
                    },

                    body: JSON.stringify({
                        text: text
                    })

                })

                .then(res => res.text())

                .then(data => {

                    document.getElementById("summary").innerHTML = data;

                });

        }

        // FAVORITE

        function saveFavorite() {

            let company = "<?php echo $row['name']; ?>";

            localStorage.setItem("favoriteCompany", company);

            alert(company + " saved to favorites!");

        }

        // DOWNLOAD REPORT

        function downloadReport() {

            let company =
                "<?php echo $row['name']; ?>";

            let description =
                document.getElementById("desc").innerText;

            let report = `

    <html>

    <head>

        <title>${company} Report</title>

        <style>

            body{
                font-family: Arial;
                padding: 30px;
                line-height: 1.8;
            }

            h1{
                color:#2563eb;
            }

        </style>

    </head>

    <body>

        <h1>${company}</h1>

        <h3>Company Report</h3>

        <p>${description}</p>

    </body>

    </html>

    `;

            let blob = new Blob(
                [report], {
                    type: "text/html"
                }
            );

            let a = document.createElement("a");

            a.href = URL.createObjectURL(blob);

            a.download = company + "-report.html";

            a.click();
        }

        // STOCK DATA

        async function loadStockData() {

            const apiKey = "";

            const response = await fetch(

                `https://www.alphavantage.co/query?function=TIME_SERIES_DAILY&symbol=<?php echo $symbol; ?>&apikey=${apiKey}`

            );

            const data = await response.json();

            const series = data["Time Series (Daily)"];

            if (!series) {

                alert("Stock data not available");

                return;
            }

            const labels = [];

            const prices = [];

            Object.keys(series)

                .slice(0, 7)

                .reverse()

                .forEach(date => {

                    labels.push(date);

                    prices.push(series[date]["4. close"]);

                });

            // STOCK CHART

            new Chart(document.getElementById('weekChart'), {

                type: 'line',

                data: {

                    labels: labels,

                    datasets: [{

                        label: '<?php echo $row['name']; ?> Stock',

                        data: prices,

                        borderColor: '#2563eb',

                        tension: 0.4

                    }]
                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false
                }

            });

            const monthLabels = [];

            const monthPrices = [];

            Object.keys(series)

                .slice(0, 30)

                .reverse()

                .forEach(date => {

                    monthLabels.push(date);

                    monthPrices.push(series[date]["4. close"]);

                });

            new Chart(document.getElementById('monthChart'), {

                type: 'line',

                data: {

                    labels: monthLabels,

                    datasets: [{

                        label: '<?php echo $row['name']; ?> Monthly Stock',

                        data: monthPrices,

                        borderColor: '#16a34a',

                        tension: 0.4

                    }]
                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false
                }

            });

            // ANALYTICS
            const highestPrice = Math.max(...prices);

            const lowestPrice = Math.min(...prices);

            const averagePrice =

                prices.reduce((a, b) => parseFloat(a) + parseFloat(b), 0)

                /

                prices.length;

            const growth =

                ((prices[prices.length - 1] - prices[0])

                    /

                    prices[0]) * 100;

            const analyticsData = [

                highestPrice,

                lowestPrice,

                averagePrice,

                growth.toFixed(2)

            ];

            new Chart(document.getElementById('analyticsChart'), {

                type: 'bar',

                data: {

                    labels: [

                        'Highest Price',

                        'Lowest Price',

                        'Average Price',

                        'Growth %'

                    ],

                    datasets: [{

                        label: '<?php echo $row['name']; ?> Analytics',

                        data: analyticsData,

                        backgroundColor: [

                            '#2563eb',

                            '#dc2626',

                            '#16a34a',

                            '#f59e0b'
                        ]

                    }]
                },

                options: {

                    responsive: true,

                    maintainAspectRatio: false
                }

            });

        }

        loadStockData();
    </script>

    <script>
        // DARK MODE

        document.getElementById("darkBtn").addEventListener("click", function() {

            document.body.classList.toggle("darkMode");

        });
    </script>

</body>

</html>