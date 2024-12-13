<?php
session_start();
if (!isset($_SESSION['visited_login']) || !$_SESSION['visited_login']) {
    // Redirect the user back to the login page
    header("Location: Login.php");
    exit();
}

require 'salarizz PHP/connect.php';
// Get the logged-in user's ID
$user_id = $_SESSION['Employee_ID'];

// Calculate the start and end dates of the past month
$last_month_start = date('Y-m-01', strtotime('last month'));
$last_month_end = date('Y-m-t', strtotime('last month'));

// Query to fetch past month's net paycheck for the logged-in user
$sql = "SELECT Net_Pay FROM paycheck WHERE Employee_ID = :user_id AND Period_Start_Date = :start AND Period_End_Date = :end";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->bindParam(':start', $last_month_start);
$stmt->bindParam(':end', $last_month_end);
$stmt->execute();
$net_pay = $stmt->fetchColumn();

// Close the statement
$stmt = null;

// Calculate the total net pay for recent paychecks
$total_net_pay = $net_pay;

// Query to fetch net pay for all past paychecks for the logged-in user
$sql = "SELECT Net_Pay FROM paycheck WHERE Employee_ID = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$paychecks = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Close the statement
$stmt = null;

// Calculate the total net pay for all past paychecks
foreach ($paychecks as $paycheck) {
    $total_net_pay += $paycheck;
}

$sql = "SELECT MONTH(Period_Start_Date) AS month, SUM(Net_Pay) AS total_net_pay FROM paycheck WHERE Employee_ID = :user_id GROUP BY MONTH(Period_Start_Date)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$graph_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Close the statement
$stmt = null;

// Fetch user information based on user ID
$user_id = $_SESSION['Employee_ID']; // Assuming you store user ID in session
$sql = "SELECT * FROM employee WHERE Employee_ID = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

// Check if user information exists
if ($stmt->rowCount() > 0) {
    // Fetch user data
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $username = $row['Name'];
    $position = $row['Job_Title'];
    $address = $row['Address'];
    $email = $row['Email'];
} else {
    // Handle case where user information is not found
    $username = "Unknown";
    $position = "Unknown";
    $address = "Unknown";
    $email = "Unknown";
}

// Close the statement
$stmt = null;

// Close the database connection
$conn = null;
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
   
    <link rel="preconnect" href="https://fonts.googleapis.com">
   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="icon" href="img/mainlogo.svg" type="image/icon type">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
<title>Salarizz</title>
</head>

<style>

  

 body{

margin: 10px;
padding: 0;
box-sizing: border-box;
font-family: 'Poppins', sans-serif;
background-color: #2B2B2B;
height: 100vh;



}
.wrap{
align-items: center;
justify-content: space-around;
display: flex;
background-color: white;
height: 40vh;
border-radius: 5px 5px 0px 0px;
 
}
.wrap1{
align-items: center;
justify-content: space-evenly;
display: flex;
background-color: white;
height: 50vh;
border-radius: 0px 0px 5px 5px;
}
.profileInfo{

display: flex;
color:white;
background-color: #2B2B2B;
height: 200px;
width: 400px;
border-radius: 10px;

}

.profileInfo:hover{

    background-color: #26F7B2;
    color:#2B2B2B;
    
    height: 215px;
    width: 415px;
    transition: 0.5s;
}

.Paycheck{
/* display: flex; */
background-color: #2B2B2B;
height: 200px;
width: 400px;
border-radius: 10px;
margin-left: 20px;
}

.Paycheck:hover{

    background-color: #26F7B2;
    color:#2B2B2B;
    
    height: 215px;
    width: 415px;
    transition: 1s;

    .paycheck1:hover{
        color: #2B2B2B;
    }
    .paycheck2:hover{
        color: #2B2B2B;
    }

}

.Calendar1{
/* display: flex; */
background-color: #2B2B2B;
height: 250px;
width: 300px;
border-radius: 10px;
/* margin-left: 20px; */
}


.cointanerfirst{
display: flex;
justify-content: center;
align-items: center;

background-color: #2b2b2b;
padding: 2%;


}


.recentPaycheck{
padding: 10px;
display: flex;
background-color: #2B2B2B;
height: 350px;
width: 400px;
border-radius: 10px;

}

.statcheck{

    padding: 10px;
display: flex;
background-color: #2B2B2B;
height: 350px;
width: 500px;
border-radius: 10px;
}

.calendar {
        height: 30px;
        width: 50px;
       margin: 30px 20px 20px 50px;

     
    }

    .month {
        text-align: center;
        
        font-weight: bolder;
        color: white;
        margin-bottom: 10px;
    }
    .month:hover{
        
        color: #26F7B2;
        font-weight: bolder;
        transition: 0.5s;
    }

    .days {
        font-size: 5px;
        display: grid;
        grid-template-columns: repeat(7, 1fr);
    }
  

    .day {
        font-size: 10px;
        justify-content: center;
        align-content: flex-start;
        padding: 5px;
        text-align: center;
        color: #26F7B2;
    }
    .day:hover{
        background-color: #26F7B2;
        color: #2b2b2b;
        font-weight: bolder;
        transition: 0.5s;
    }
    .day-today {
        font-size: 10px;
        font-weight: bolder;
        border-radius: 5%;
        border: 1px solid white;
        padding: 5px;
        text-align: center;
        background-color: #26F7B2;
        color: #2b2b2b;
        
    }

  
    .Date{
      
        font-size: 10px;
        justify-content: center;
      
    }

  

    .prevcheck{
        display:flex;
        background-color: #2B2B2B;
        height: 350px;
        width: 600px;
    }
    .statcheck{
        display:flex;
        background-color: #2B2B2B;
        height: 350px;
        width: 600px;
        border-radius: 5px;
    
    }
    .logout:hover{
        
        
        width: 35px;
        transition: 1s;
        
    }
    .paycheck1{
       color:white;

    
    }
    .paycheck2{
       color:#26F7B2;
    }
    
    button:hover{
        background-color: #2b2b2b;
        color: white;
        transition: 1s;
    
    }

    .Stats{
        height: 300px;width: 700px;background-color: white;color: #f0fcf8;border-radius: 5px;display:flex;
    }
    

    .rotate {
  display: inline-block;
  height: 50px;
  animation: rotate 4s linear infinite; 
}

@keyframes rotate {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>

<body>

    
<div class="containerfirst">
        <div class="container">
            <div class="wrap">
                <div class="profileInfo">
                    <!-- Display user information -->
                    <div style="margin-left: 5%;">
                        <img src="https://img.bleacherreport.net/img/images/photos/003/380/882/hi-res-21c6d59f5b8b18b9a2896d68ea6564ac_crop_exact.jpg?w=1200&h=1200&q=75" style="height: 70px; border-radius: 50%;width: 70px;margin:15%;">
                        <h1 style="font-size: 10px; margin-left: 35%;"><?php echo $user_id; ?></h1>
                        <img style="display: flex; margin:15%;color: #2B2B2B;" src="img/ProfileIcon.svg">
                    </div>
                    <div style="margin-left: 15%;padding: 2%;">
                        <h1 style=" font-size: 30px; margin: 5px 5px 5px 5px; "><?php echo $username; ?></h1>
                        <h6 style=" font-size: 10px; margin: 5px 5px 5px 5px;font-weight: light;">Position</h1>
                        <h4 style=" font-size: 15px; margin: 5px 5px 5px 5px;"><?php echo $position; ?></h4>
                        <h6 style=" font-size: 10px; margin: 5px 5px 5px 5px;font-weight: light;">Address</h1>
                        <h4 style=" font-size: 15px; margin: 5px 5px 5px 5px;"><?php echo $address; ?></h4>
                        <h6 style=" font-size: 10px; margin: 5px 5px 5px 5px;font-weight: light;">Email Address</h1>
                        <h4 style=" font-size: 15px; margin: 5px 5px 5px 5px;"><?php echo $email; ?></h4>
                    </div>
                </div>

                <div class="Paycheck">
        <div style="display: flex; margin: 10px;">
            <h4 class="paycheck1" style="font-size: 15px; margin: 5px 5px 5px 5px;">Next PAYCHECK in: </h4>
            <h4 class="paycheck2" style="font-size: 15px; margin: 5px 5px 5px 5px;" id="countdown"></h4>
        </div>
        <div style="display: flex; margin: 10px;">
            <h4 class="paycheck1" style="font-size: 15px; margin: 5px 5px 5px 5px;">Projected value: <img style="height: 20px; width: 15px;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABgAAAAYCAYAAADgdz34AAAACXBIWXMAAAsTAAALEwEAmpwYAAAArklEQVR4nGNgGAXkANXvGxPUvm/6jxtvfKX6bdNe1W8bIhj+/2ekgQWb4Fj126aNev93cpNtAQM6+P+fUePHVjW17xu71L5t+gNVt4Aog4l1tRoBjNuCb5t2Q/FVuIZvcDGsWPXbps9QtY9gYpQFERpQ+77pOMQhG2cxEAI094EareOA2FSk/mODOlmpaEDygRp2/FL128Y9at82hVOckxloAUYtIAhGg2gUMJACAHVLuOTEpwPCAAAAAElFTkSuQmCC"></h4>
            <?php 
            require 'salarizz PHP/connect.php';
                // Fetch the gross pay from the most recent paycheck
                $sql = "SELECT Gross_Pay FROM paycheck WHERE Employee_ID = :user_id ORDER BY Period_Start_Date DESC LIMIT 1";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':user_id', $user_id);
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $gross_pay = $row['Gross_Pay'];

                // Use the gross pay for projection
                // You can perform any necessary calculations here
                $projected_value = $gross_pay * 2; // For example, doubling the gross pay as projection

                // Output the projected value
                echo "<h4 class='paycheck2' style='font-size: 15px; margin: 5px 5px 5px 5px;'>$projected_value</h4>";
            ?>
        </div>
    
                        <div>

                            
                            <!-- <button style=" border-radius: 10px; height: 30px; width: 100px; margin: 10px; font-size: 15px; font-weight:600;"></button> -->
                             <!-- MODAL  -->
 <button style="background-color: #26F7B2; height: 30px;width: 100px;font-size: 15px;text-align: center;margin-top: 10px;margin-left: 10px;" type="button" class="btn" data-bs-toggle="modal" data-bs-target="#modal2" id="view1">
                            
    View
   </button>
 
 <!-- Modal -->
 <div class="modal fade" id="modal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered">
 <div class="modal-content">
 <div class="modal-header">
   
   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
 </div>
 <div class="modal-body text-center fs-6">
 <?php include 'paycheck.php'; ?>
 </div>
 
 
 <div class="modal-footer">
   <button style="background-color:#26F7B2;" type="button" class="btn" data-bs-dismiss="modal">CLOSE</button>
 </div>
 </div>
 </div>
 </div>
 
 </div>
 <!-- Modal Ends Here -->
  
                        
                    </div>
                    <div class="Calendar1">
                        
                            <div class="calendar">
                                <div class="month" id="month"></div>
                                <div class="days" id="days"></div>
                            
                        </div>
                        
                    </div>
                  
                    <div style="margin-bottom: 20%;">

                        <!-- MODAL  -->
                        <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#modal1" id="Res3">
                            
                            <img class="logout" style="height: 30px;cursor: pointer;" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAACXBIWXMAAAsTAAALEwEAmpwYAAABVUlEQVR4nO3aMU7DMBTGccPAigQrB4i+fzx1Qj0Yaxco3IQTsCAOwBDowMZKuUaQUTogpZHdqvBs/EkeLb2fXpvET3aSTiTdAZ9Ab2StgWWozcUGuDVQ+LZ1kwIJ+r5t20tnJMB805mUTd96Zyyk1lUhBw61I8ZC7YixSLoq4qkVAixcCZCkVMgBI+keeJjNZqdZdwTohrqeozEWId77C+B9qK2TdJYlZCeMVcgI5mUSYxmShLEOGcM0TXPucoREYXKBjGBef2BygkxicoNsxewJOZK0MjA26vaFHIc/XgmQYn5adh7DZATxU++SXCC+hBeij/nesg7xsZ/zliE+5UxiFeJLOFj5Uo66wFsRwwfgCXjMfhy0UyrkF0IJQ2xgkVSXYUhfIZZC7Yix8J87sh42zZ2xSzWSPlI2LQ2Mc0aXpOtoSLjcNWA2nfnzFToRECkXz74ARqB6zcUz/XoAAAAASUVORK5CYII=">
                          </button>
                     
                  <!-- Modal -->
<div class="modal fade" id="modal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel1" style="font-family: Raleway, sans-serif;"><img class="rotate" src="img/logo.svg">LOGOUT</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center fs-6">
                <h5>You're about to Logout!</h5>
            </div>
            <div class="modal-footer">
                <button style="background-color:#26F7B2;" type="button" class="btn" data-bs-dismiss="modal">CANCEL</button>
                <form action="logout.php" method="post">
                    <button style="background-color:#26F7B2;" type="submit" class="btn" name="logout">OK</button>
                </form>
            </div>
        </div>
    </div>
</div>
                        
                    </div>
            <!-- Modal Ends Here -->
</div>
            <div class="wrap1">
            <div class="prevcheck" style="height: 350px; width: 400px; border-radius: 5px;">
    <div>
        <h1 style="color: white; font-size: 30px;text-align: center; padding: 10px;">Recent Paychecks</h1>
        <?php
        // Check if there are paychecks available
        if (count($paychecks) > 0) {
            // Loop through the recent paychecks data and display them
            foreach ($paychecks as $paycheck) {
                // Get the month and year of the paycheck
                $paycheck_month = date('F', strtotime($last_month_start));
                $paycheck_year = date('Y', strtotime($last_month_start));
            
                // Display the paycheck amount with month and year
                echo "<div style=\"text-align:center;\">";
                echo "<h4 style=\"color:black; font-size: 20px; margin:5px 5px 5px 5px;height: 30px;margin-left: 15px;background-color: #26F7B2;border-radius: 10px; text-align:center;margin-bottom: 0;\">₱" . $paycheck . " $paycheck_month $paycheck_year</h4>";
                
                echo "</div>";
                
                // Calculate the start and end dates of the previous month for the next iteration
                $last_month_start = date('Y-m-01', strtotime('-1 month', strtotime($last_month_start)));
            }
        } else {
            // If no paychecks available, display a message
            echo "<h4 style=\"color: white; font-size: 25px; margin: 10px;\">No recent paychecks available</h4>";
        }
        ?>
    </div>
</div>

<?php
// Check if there are paychecks available
if (count($paychecks) > 0) {
?>
    <div class="statcheck">
        <div class="Stats">
            <canvas id="myChart" style="width:100%;max-width:600px"></canvas>
            <script>
                // Update this part with the dynamic data fetched from PHP
const graphData = <?php echo json_encode($graph_data); ?>;

// Map month numbers to month names
const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

// Extract x and y values from the PHP data
const xyValues = graphData.map(data => ({ x: monthNames[data.month - 1], y: data.total_net_pay }));

// Chart.js initialization with dynamic data
new Chart("myChart", {
    type: "bar",
    data: {
        labels: xyValues.map(data => data.x),
        datasets: [{
            label: 'Salary Statistics',
            backgroundColor: "#26F7B2",
            data: xyValues.map(data => data.y)
        }]
    },
    options: {
        scales: {
            yAxes: [{ ticks: { min: 0, max: <?php echo max(array_column($graph_data, 'total_net_pay')); ?> } }]
        }
    }
});
            </script>
        </div>
    </div>
    <?php
}
?>
</div>
            
              
    
        </div>

    </div>
    

       <script>
      
         function generateCalendar(year, month) {
        var date = new Date(year, month - 1, 1);
        var daysInMonth = new Date(year, month, 0).getDate(); 
        var currentDate = new Date();
        var dayOfMonth = currentDate.getDate();
        var monthElement = document.getElementById("month");
        monthElement.classList.add("Date");
        monthElement.textContent = date.toLocaleString('default', { month: 'long' }) + " " + year;

        var daysElement = document.getElementById("days");
        daysElement.innerHTML = ""; 

       
        var daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        for (var i = 0; i < daysOfWeek.length; i++) {
            var dayElement = document.createElement("div");
            dayElement.classList.add("day");
            dayElement.textContent = daysOfWeek[i];
            daysElement.appendChild(dayElement);
        }

      
        for (var i = 0; i < date.getDay(); i++) {
            var emptyDayElement = document.createElement("div");
            emptyDayElement.classList.add("day");
            daysElement.appendChild(emptyDayElement);
        }

        
        for (var i = 1; i <= daysInMonth; i++) {
            var dayElement = document.createElement("div");
            dayElement.classList.add("day");
            dayElement.textContent = i;

            
            if (i === dayOfMonth) {
                var elementDay = document.createElement("div");
                elementDay.classList.add("highlight");
                dayElement.classList.add("day-today");
            }

            daysElement.appendChild(dayElement);
        }
    }

    
    generateCalendar(2024, 5);

    function calculateTimeUntilEndOfMonth() {
        var now = new Date();
        var endOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0); // Last day of the current month
        var timeRemaining = endOfMonth - now;

        // Calculate remaining days, hours, minutes, and seconds
        var days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
        var hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);

        // Display the countdown
        document.getElementById("countdown").innerHTML = days + "d " + hours + "h " + minutes + "m " + seconds + "s ";
    }

    // Update the countdown every second
    setInterval(calculateTimeUntilEndOfMonth, 1000);
    
       </script> 
       
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>
