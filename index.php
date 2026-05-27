<?php

session_start();

include("config/db.php");


$currentUser = null;

if(isset($_SESSION['user_id'])){

    $uid = $_SESSION['user_id'];

    $currentUser = $conn->query(

    "SELECT * FROM users WHERE id=$uid"

    )->fetch_assoc();
}

?>
<?php include("config/db.php"); ?>

<?php

// 🔥 Total Companies
$totalCompaniesResult = $conn->query("SELECT COUNT(*) as total FROM companies");
$totalCompanies = $totalCompaniesResult->fetch_assoc()['total'];

// 🔥 Average Rating
$avgRatingResult = $conn->query("SELECT AVG(rating) as avgRating FROM companies");
$avgRating = round($avgRatingResult->fetch_assoc()['avgRating'], 1);

// 🔥 Top Rated Company
$topCompanyResult = $conn->query("SELECT name, rating FROM companies ORDER BY rating DESC LIMIT 1");
$topCompany = $topCompanyResult->fetch_assoc();

?>

<!DOCTYPE html>
<html>

<head>
    <title>Company Directory</title>
    <link rel="stylesheet" href="style.css?v=2">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    
</head>

<body id="body">

    <div class="header">
        <div style="display:flex; justify-content:space-between; align-items:center; width:100%;">
            <h2 style="margin:0;">Smart Company Analytics and Stock Monitoring System <button id="darkBtn">
                    🌙 Dark Mode
                </button> </h2>
            <h4 style="text-align:right;">Presented by: <b>Karina Manwani & Payal Maghnani</b></h4>
        </div>
<div style="display:flex; gap:12px; align-items:center;">

<?php

if(!$currentUser){

?>

<a href="login.php">

    <button>

        Sign In

    </button>

</a>

<a href="register.php">

    <button>

        Create Account

    </button>

</a>

<?php

}else{

    if($currentUser['role'] == 'admin'){

?>

<a href="admin.php">

    <button>

        Admin Dashboard

    </button>

</a>

<?php

    }else{

?>

<a href="dashboard.php">

    <button>

        User Dashboard

    </button>

</a>

<?php

    }

?>

<a href="logout.php">

    <button>

        Logout

    </button>

</a>

<?php } ?>

</div>

    </div>

    <div class="container">

        <h2 class="pageTitle">Company Directory</h2>



        <div class="statsGrid">

            <div class="statCard">
                <h3>Total Companies</h3>
                <p><?php echo $totalCompanies; ?></p>
            </div>

            <div class="statCard">
                <h3>Average Rating</h3>
                <p><?php echo $avgRating; ?></p>
            </div>

            <div class="statCard">
                <h3>Top Rated</h3>
                <p><?php echo $topCompany['name']; ?></p>
            </div>

        </div>
        <!-- 🔥 DASHBOARD SECTION -->

        <div class="dashboardSection">

            <!-- 🔥 LEFT SIDE -->
            <div class="leftSection">

                <!-- 🔥 TOP SMALL CARDS -->
                <div class="topCards">

                    <!-- ❤️ FAVORITE -->
                    <div class="favoriteBox">

                        <h3>❤️ Favorite Company</h3>

                        <p id="favoriteCompany">
                            No favorites yet
                        </p>

                    </div>

                    <!-- 📊 QUICK STATS -->
                    <div class="quickStats">

                        <h3>📊 Quick Stats</h3>

                        <p>✔ IT : 10</p>

                        <p>✔ Fintech : 7</p>

                        <p>✔ E-commerce : 5</p>

                        <p>✔ Automotive : 8</p>

                    </div>

                </div>

                <!-- 📄 EXPLANATION -->
                <div class="descriptionBox">

                    <h3>📄 Graph Explanation</h3>

                    <p>🔵 IT Companies</p>

                    <p>🟠 Automotive Companies</p>

                    <p>🟡 E-commerce Companies</p>

                    <p>🔴 Fintech Companies</p>

                </div>

            </div>

            <!-- 📊 RIGHT GRAPH -->
            <div class="graphBox">

                <canvas id="categoryChart"></canvas>

            </div>
            

        </div>
    </div>





    </div>
    <div class="searchSection">
        <input type="text" id="searchInput" placeholder="Search company...">
    </div>

    <div class="mainLayout">

        <div class="tableBox">

            <h3>Company List</h3>

            <table>

                <tr>
                    <th onclick="sortTable(0)">Name ↕</th>
                    <th>Category</th>
                    <th>Location</th>
                    <th onclick="sortTable(3)">Rating ↕</th>
                    <th>Favorite</th>
                </tr>

                <?php

                $limit = 20;

                $page = isset($_GET['page']) ? $_GET['page'] : 1;

                $start = ($page - 1) * $limit;

                $totalResult = $conn->query("SELECT COUNT(*) as total FROM companies");
                $totalRow = $totalResult->fetch_assoc();

                $totalCompanies = $totalRow['total'];

                $totalPages = ceil($totalCompanies / $limit);

                $result = $conn->query("SELECT * FROM companies LIMIT $start, $limit");

                while ($row = $result->fetch_assoc()) {
                ?>

                    <tr class="companyRow">

                        <td>
                            <a href="detail.php?id=<?php echo $row['id']; ?>">
                                <?php echo $row['name']; ?>
                            </a>
                        </td>

                        <td><?php echo $row['category']; ?></td>

                        <td><?php echo $row['location']; ?></td>

                        <td>
                            <?php
                            if ($row['rating'] >= 4.5) {
                                echo "<span class='greenRating'>⭐ {$row['rating']}</span>";
                            } elseif ($row['rating'] >= 3) {
                                echo "<span class='yellowRating'>⭐ {$row['rating']}</span>";
                            } else {
                                echo "<span class='redRating'>⭐ {$row['rating']}</span>";
                            }
                            ?>
                        </td>

                        <td>
                            <button onclick="saveFavorite('<?php echo $row['name']; ?>')">
                                ❤️
                            </button>
                        </td>

                    </tr>

                <?php } ?>

            </table>

            <div class="pagination">

                <?php if ($page > 1) { ?>
                    <a href="?page=<?php echo $page - 1; ?>">
                        <button>Previous</button>
                    </a>
                <?php } ?>

                <?php for ($i = 1; $i <= $totalPages; $i++) { ?>

                    <a href="?page=<?php echo $i; ?>">
                        <button><?php echo $i; ?></button>
                    </a>

                <?php } ?>

                <?php if ($page < $totalPages) { ?>
                    <a href="?page=<?php echo $page + 1; ?>">
                        <button>Next</button>
                    </a>
                <?php } ?>

            </div>

        </div>

        <div class="filterBox">

            <h3>Filters</h3>

            <label>Categories</label>

            <select id="categoryFilter">
                <option value="">All Categories</option>
                <option value="IT Services">IT Services</option>
                <option value="Fintech">Fintech</option>
                <option value="E-commerce">E-commerce</option>
                <option value="Automotive">Automotive</option>
                <option value="FMCG">FMCG</option>
            </select>

            <label>Location</label>

            <select id="locationFilter">
                <option value="">All Locations</option>
                <option value="Bangalore">Bangalore</option>
                <option value="Mumbai">Mumbai</option>
                <option value="Noida">Noida</option>
                <option value="Gurgaon">Gurgaon</option>
            </select>

        </div>

    </div>

    </div>

    <script>
        function saveFavorite(company) {
            localStorage.setItem("favoriteCompany", company);
            alert(company + " added to favorites!");
        }

        const searchInput = document.getElementById("searchInput");
        const categoryFilter = document.getElementById("categoryFilter");
        const locationFilter = document.getElementById("locationFilter");

        function filterCompanies() {

            let searchValue = searchInput.value.toLowerCase();
            let categoryValue = categoryFilter.value.toLowerCase();
            let locationValue = locationFilter.value.toLowerCase();

            let rows = document.querySelectorAll(".companyRow");

            rows.forEach(row => {

                let text = row.innerText.toLowerCase();

                let matchSearch = text.includes(searchValue);
                let matchCategory = categoryValue === "" || text.includes(categoryValue);
                let matchLocation = locationValue === "" || text.includes(locationValue);

                if (matchSearch && matchCategory && matchLocation) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }

            });
        }

        searchInput.addEventListener("keyup", filterCompanies);
        categoryFilter.addEventListener("change", filterCompanies);
        locationFilter.addEventListener("change", filterCompanies);

        let sortDirection = true;

        function sortTable(columnIndex) {

            let table = document.querySelector("table");
            let rows = Array.from(table.rows).slice(1);

            rows.sort((a, b) => {

                let valA = a.cells[columnIndex].innerText.toLowerCase();
                let valB = b.cells[columnIndex].innerText.toLowerCase();

                if (columnIndex == 3) {
                    valA = parseFloat(valA);
                    valB = parseFloat(valB);
                }

                if (valA < valB) return sortDirection ? -1 : 1;
                if (valA > valB) return sortDirection ? 1 : -1;

                return 0;
            });

            sortDirection = !sortDirection;

            rows.forEach(row => table.appendChild(row));
        }

        new Chart(document.getElementById('categoryChart'), {

            type: 'doughnut',

            data: {

                labels: ['IT', 'Fintech', 'E-commerce', 'Automotive'],

                datasets: [{

                    data: [10, 7, 5, 8],

                    backgroundColor: [

                        '#36A2EB',
                        '#FF6384',
                        '#FF9F40',
                        '#FFCD56'

                    ],

                    borderWidth: 0

                }]
            },

            options: {

                responsive: true,

                plugins: {

                    legend: {

                        display: false

                    }

                }

            }

        });
    </script>
    <script>
        document.getElementById("darkBtn").onclick = function() {

            if (document.body.style.backgroundColor == "black") {

                document.body.style.backgroundColor = "#f3f4f8";

            } else {

                document.body.style.backgroundColor = "black";

            }

        }
        window.onload = function() {

            let favorite = localStorage.getItem("favoriteCompany");

            let favBox = document.getElementById("favoriteCompany");

            if (favorite && favBox) {

                favBox.innerHTML = favorite;

            }

        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let favorite = localStorage.getItem("favoriteCompany");

        if (favorite) {

            document.getElementById("favoriteCompany").innerHTML = favorite;

        }
    </script>
</body>

</html>