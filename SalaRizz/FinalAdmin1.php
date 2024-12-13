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

// Modify the SQL query to exclude employees with SP_pos equal to "admin"
$sql = "SELECT * FROM employee WHERE SP_pos <> 'admin'";
$result = $conn->query($sql);

// Define an array to store the fetched data
$employeeList = array();

// Check if there are any records
if ($result->rowCount() > 0) {
    // Fetch data row by row
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        // Add each row to the employeeList array
        $employeeList[] = $row;
    }
}

// Close the database connection
$conn = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the request
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $homeAddress = $_POST['homeAddress'];
    $employment = $_POST['employment'];
    $salary = $_POST['salary'];
    $jobPosition = $_POST['jobPosition'];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Database connection
    $servername = "your_servername";
    $username = "your_username";
    $password = "your_password";
    $dbname = "your_dbname";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and bind the procedure call
    $stmt = $conn->prepare("CALL AddEmployee(?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssds", $name, $homeAddress, $email, $hashedPassword, $jobPosition, $salary, $employment);

    // Execute the statement
    if ($stmt->execute() === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
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
   
      <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
            transition: smooth;
            transition-delay: 1s;
        }
    .navbar {
        padding: 1rem 0; /* Adjust as needed */
    }

    .navbar-brand {
        font-size: 1.5rem; /* Adjust as needed */
    }

    .navbar-toggler {
        border-color: transparent; /* Remove the border */
    }

    .navbar-nav .nav-link {
        padding: .5rem 1rem; /* Adjust as needed */
    }

    .navbar-nav .nav-link:hover {
        color: #000; /* Change hover color as needed */
    }

    /* Adjustments for larger screens */
    @media (min-width: 768px) {
        .navbar-nav .nav-link {
            margin-right: .5rem; /* Adjust as needed */
        }
    }

    table, th, td {
        /* border: 1px solid black; */
        border-collapse: collapse;
    }
    table{
        width: 100%;
        height: auto;
        padding: 10px;

    }
    tr,td{
        /* border: 1px solid black; */
        text-align: center;
        padding: 10px;
    }
    .page-button {
        padding: 10px;
        margin: 5px;
        background-color: #26F7B2;
        color: #2b2b2b;
        border: none;
        border-radius: 5px;
    }
    .action-buttons {
        display: flex;
        
       height: 10px;
       width: 30px;
    }
    .type-of-employment {
        color: #26F7B2;
    }
    .Sort{
        width: 40px;
        height: 40px;
        padding: 5px;
    }
    .Sort:hover{
        cursor: pointer;

    }
    tr:nth-child(even) {
            background-color: #F7F6FE;
            width: 100%;
            
        }
        .rotate {
  display: inline-block;
    animation: rotate 2s linear infinite;
  height: 30px;
}

.divModal{
    margin-bottom: 10px;
    

}
.edit-button, .delete-button{
    border: 0;
    height: 30px;
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
</head>
<body>
    <div class="container-fluid">
        <div class="container-fluid">
            
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div style="display: flex; justify-content: space-evenly;" class="container">
                    <!-- Navbar brand -->
                    <div>
                        
                    </div>
                    <a style="text-align: center;" class="navbar-brand" href="AdminLegit.html"><span style="color: #2b2b2b;font-weight: 600;">ADMIN</span><img style="height: 60px;" src="img/logo.svg">Salarizz</a>
    
                    <!-- Navbar toggler -->
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
    
                    <!-- Navbar items -->
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <!-- Add any navbar items here -->
                        </ul>
                        <!-- Search form -->
                        <form class="d-flex">
                            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                            <button class="btn btn-outline-success" type="submit">Search</button>
                        </form>
                    </div>
                </div>
            </nav>
       

        </div>
    <div class="container">
        <div style="text-align: center;margin: 15px;">
            <h1>Employee List</h1>
        </div>
        <div>
            <table>
               

                    
                    <!----------------------------- Button trigger modal ------------------------------------>
                    <button style="margin-left: 85%; padding: 10px; background-color: #26F7B2; border: 0; font-weight: 600;" type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalCenter">
    <img style="height: 20px;" src="img/add icon.png"><span>ADD EMPLOYEE</span>
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel1" style="font-family: 'Poppins', sans-serif;"><img class="rotate" src="img/logo.svg">ADD EMPLOYEE</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="employeeForm">
            <div class="divModal">
                <div class="modal-body">
                    <div class="input-group mb-7">
                        <input id="modal-name" name="modal-name"type="text" class="form-control" placeholder="Name" aria-label="Name">
                    </div>
                    <div class="input-group mb-7">
                        <input type="text" class="form-control" id="modal-email" placeholder="Email" name="modal-email" aria-label="Username">
                        <span class="input-group-text">@</span>
                        <input type="password" class="form-control" id="pwd" placeholder="Enter password" name="pwd"> 
                          
                    </div>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon3">ADDRESS</span>
                        <input id="modal-homeAddress" name="modal-homeAddress"type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3 basic-addon4">
                    </div>
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="inputGroupSelect01">Employment</label>
                        <select class="form-select" name="inputGroupSelect01"  id="inputGroupSelect01">
                            <option id="modal-typeOfEmployment" selected>Choose...</option>
                            <option value="1">Full-Time</option>
                            <option value="2">Part-Time</option>
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text">₱</span>
                        <span class="input-group-text">Rate/Hour</span>
                        <input id="modal-salary" name="modal-salary" type="text" class="form-control" aria-label="Peso">
                    </div>
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="inputGroupSelect01">Position</label>
                        <select class="form-select" name="modal-jobPosition" id="modal-jobPosition">
                            <option selected>Choose...</option>
                            <option value="Janitor">Janitor</option>
                            <option value="CEO">CEO</option>
                            <option value="Print Man">Print Man</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button style="background-color:#26F7B2;font-weight: 500;" type="button" class="btn" data-bs-dismiss="modal">CANCEL</button>
                    <button style="background-color:#26F7B2;font-weight: 500;" type="button" class="btn" data-bs-dismiss="modal" id="addEmployeeBtn">ADD</button>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>

     <!----------------------------- Button trigger END MODAL ------------------------------------>
                <tr>
                    <th>Employee ID</th>
                    <th>Name </th>
                    <th>Job Position <span><img  class="Sort" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAABn0lEQVR4nO2YSy8EQRCAi79iqtaZuJBw4UDiceDAiQMHwoGDk9fNI+HGz7BV/BvxD0hEdqrGc2U27HovS+/20F9SSR+/6p7qqS6AQCAQCAQc0Boft5PyaRrpGrIExdKBxudkUiyFygUpd0EWiFR6UOWyLF9OguPIuA98BpOjAVLRN/KVJBKKZQR8JJfkh0qCH8k/Bqpc55THwCcilQlUvqkm/6wmbiOTKfABNJ4hlbsvy1c+p3tMZKGh8mQyl4p8W/55EspLjZFXXq5Z/HVdmGzWVR6NN35LnsrB2+7Ni8UmNNn7fXl5vKH4AIprzc78011yJU+Vk9hxlsCL9sDVKRifO0uAVFbTlsBZAsoxKa84SyAQ+O9QqYg/aZd9L2KsyzUqZ5n+kaHJluNWgnedyavsO20lnkDjdQc7vwn1JNPt9BNkMvvTBw2pLEIjQctP1/ykNJ4HH0Dl8e8+6tF4EnwiSmTwi2OVq1x8NAo+Qon0Vx1sJTwMPoOa735vtIjKBbR8L2R1uJvTw07IEi0FaSOVkzTSdaN9AoFAIPA3eQDew/rpJTt/ugAAAABJRU5ErkJggg=="></span></th>
                    <th>Salary <span><img class="Sort" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAABn0lEQVR4nO2YSy8EQRCAi79iqtaZuJBw4UDiceDAiQMHwoGDk9fNI+HGz7BV/BvxD0hEdqrGc2U27HovS+/20F9SSR+/6p7qqS6AQCAQCAQc0Boft5PyaRrpGrIExdKBxudkUiyFygUpd0EWiFR6UOWyLF9OguPIuA98BpOjAVLRN/KVJBKKZQR8JJfkh0qCH8k/Bqpc55THwCcilQlUvqkm/6wmbiOTKfABNJ4hlbsvy1c+p3tMZKGh8mQyl4p8W/55EspLjZFXXq5Z/HVdmGzWVR6NN35LnsrB2+7Ni8UmNNn7fXl5vKH4AIprzc78011yJU+Vk9hxlsCL9sDVKRifO0uAVFbTlsBZAsoxKa84SyAQ+O9QqYg/aZd9L2KsyzUqZ5n+kaHJluNWgnedyavsO20lnkDjdQc7vwn1JNPt9BNkMvvTBw2pLEIjQctP1/ykNJ4HH0Dl8e8+6tF4EnwiSmTwi2OVq1x8NAo+Qon0Vx1sJTwMPoOa735vtIjKBbR8L2R1uJvTw07IEi0FaSOVkzTSdaN9AoFAIPA3eQDew/rpJTt/ugAAAABJRU5ErkJggg=="></span></th>
                    <th>Email Address</th>
                    <th>Home Address</th>
                    <th>Type of Employment <span><img class="Sort" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAABn0lEQVR4nO2YSy8EQRCAi79iqtaZuJBw4UDiceDAiQMHwoGDk9fNI+HGz7BV/BvxD0hEdqrGc2U27HovS+/20F9SSR+/6p7qqS6AQCAQCAQc0Boft5PyaRrpGrIExdKBxudkUiyFygUpd0EWiFR6UOWyLF9OguPIuA98BpOjAVLRN/KVJBKKZQR8JJfkh0qCH8k/Bqpc55THwCcilQlUvqkm/6wmbiOTKfABNJ4hlbsvy1c+p3tMZKGh8mQyl4p8W/55EspLjZFXXq5Z/HVdmGzWVR6NN35LnsrB2+7Ni8UmNNn7fXl5vKH4AIprzc78011yJU+Vk9hxlsCL9sDVKRifO0uAVFbTlsBZAsoxKa84SyAQ+O9QqYg/aZd9L2KsyzUqZ5n+kaHJluNWgnedyavsO20lnkDjdQc7vwn1JNPt9BNkMvvTBw2pLEIjQctP1/ykNJ4HH0Dl8e8+6tF4EnwiSmTwi2OVq1x8NAo+Qon0Vx1sJTwMPoOa735vtIjKBbR8L2R1uJvTw07IEi0FaSOVkzTSdaN9AoFAIPA3eQDew/rpJTt/ugAAAABJRU5ErkJggg=="></span></th>
                    <th>Action</th>
                </tr>
                <?php foreach ($employeeList as $employee): ?>
    <tr>
        <td><?php echo $employee['Employee_ID']; ?></td>
        <td><?php echo $employee['Name']; ?></td>
        <td><?php echo $employee['Job_Title']; ?></td>
        <td><?php echo $employee['Rate']; ?></td>
        <td><?php echo $employee['Email']; ?></td>
        <td><?php echo $employee['Address']; ?></td>
        <td><?php echo $employee['Employment_type']; ?></td>
        <td>
            <!-- Add your action buttons here -->
            <button class="edit-button" data-employee-id="<?php echo $employee['Employee_ID']; ?>" data-bs-toggle="modal" data-bs-target="#edit-button" ><img class="edit-button" src="img/edit.png" alt=""></button>
            <button class="delete-button" data-employee-id="<?php echo $employee['Employee_ID']; ?>" data-bs-toggle="modal" data-bs-target="#modalDelete"><img class="delete-button" src="img/delete.png"> </button>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
            </table>

            <div id="pagination">
                <div style="text-align: center;">
                    <!-- <button class="page-button" onclick="changePage(-1)">Previous</button>
                    <button class="page-button" onclick="changePage(1)">Next</button> -->
                </div>
                
                <div style="text-align: center;padding: 10px;">
                    <p id="page-number"></p>
                </div>
            </div>
        </div>
    
        </div>
        </div>

        <!----------------------------- Button trigger modal for DELETE ------------------------------------>
        <div class="modal fade" id="modalDelete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel1" style="font-family: Raleway, sans-serif;"><img src="img/Warning.png"> DELETE USER</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center fs-6">
                <h5>You're About to Delete this User</h5>
            </div>
            <div class="modal-footer">
                <button style="background-color:#26F7B2;" type="button" class="btn" data-bs-dismiss="modal">CANCEL</button>
                <button style="background-color:#26F7B2;" type="button" class="btn delete-confirm-btn">OK</button>
            </div>
        </div>
    </div>
</div>
                
            </div>

  <!----------------------------- Button trigger modal for end DELETE ------------------------------------>

    <!----------------------------- Button trigger modal for EDIT ------------------------------------>
          <!-- Modal -->
  <div class="modal fade" id="edit-button" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel1" style="font-family: 'Poppins',sans-serif;"><img class="rotate" src="img/logo.svg">EDIT EMPLOYEE</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         
        </div>

       <div class="divModal">
        <div class="modal-body">
            <div >
                <div class="input-group mb-7">
                    <input id="Name1" type="text" class="form-control"  aria-label="Name">
                      
                 </div>
            
              </div>
         
            <div class="divModal">
                <div class="input-group mb-7">
                    <input id="username1" type="text" class="form-control" placeholder="Email" aria-label="Username">
                    <span class="input-group-text">@</span>   
            </div>

            <div class="divModal">
                <div class="input-group">
                    <span class="input-group-text" id="basic-addon3">ADDRESS</span>
                    <input id="address1" type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3 basic-addon4">
                  </div>
            </div>

            <div class="divModal">
                <div class="input-group mb-3">
                    <label class="input-group-text" for="inputGroupSelect011">Employment</label>
                    <select class="form-select" id="inputGroupSelect011">
                      <option selected>Choose...</option>
                      <option value="1">Full-Time</option>
                      <option value="2">Part-Time</option>
                    </select>
                  </div>
            </div>

            <div class="divModal">
                <div class="input-group mb-3">
                    <span class="input-group-text">₱</span>
                    <span class="input-group-text">Rate/Hour</span>
                    <input id="Rate1" type="text" class="form-control" aria-label="Peso">
                  </div>
            </div>

            <div class="divModal">
                <div class="input-group mb-3">
                    <label class="input-group-text" for="inputGroupSelect012">Position</label>
                    <select class="form-select" id="inputGroupSelect012">
                      <option selected>Choose...</option>
                      <option value="Janitor">Janitor</option>
                      <option value="CEO">CEO</option>
                      <option value="Print Man">Print Man</option>
                    </select>
                  </div>
            </div>
            <div class="divModal">
                <div class="input-group mb-3">
                    <span class="input-group-text">₱</span>
                    <span class="input-group-text">Deductions</span>
                    <input id="type" type="text" class="form-control" id="basic-url" placeholder="type of deduction" aria-describedby="basic-addon3 basic-addon4">
                    <input id="amount" type="text" class="form-control" placeholder="amount" aria-label="Peso">
                  </div>
            </div>
        </div>
        <div class="modal-footer">
            <button style="background-color:#26F7B2;font-weight: 500;" type="button" class="btn" data-bs-dismiss="modal">CANCEL</button>
            <button id="saveChangesBtn" style="background-color:#26F7B2;font-weight: 500;" type="button" class="btn" data-bs-dismiss="modal">SAVE CHANGES</button>
        </div>
      </div>
    </div>
      </div>
    </div>

    
     <!----------------------------- Button trigger END MODAL ------------------------------------>
       
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("addEmployeeBtn").addEventListener("click", function() {
        var form = document.getElementById("employeeForm");
        var formData = new FormData(form);

        // AJAX request
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "add_employee.php", true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Success action
                console.log("Response from server: " + xhr.responseText);
                // Optionally, close the modal here if everything is fine
                $('#exampleModalCenter').modal('hide');
            }
        };
        xhr.send(formData);

    });


    $(document).ready(function() {
        $('.delete-button').on('click', function() {
            // Get the employee ID from the button's data attribute
            var employeeID = $(this).data('employee-id');
            // Set the employee ID as a data attribute of the confirmation button
            $('.delete-confirm-btn').data('employee-id', employeeID);
        });

        // When the confirmation button is clicked
        $('.delete-confirm-btn').on('click', function() {
            // Get the employee ID from the confirmation button's data attribute
            var employeeID = $(this).data('employee-id');

            // Send an AJAX request to delete_employee.php
            $.ajax({
                type: 'POST',
                url: 'delete_employee.php',
                data: { employee_id: employeeID },
                success: function(response) {
                    // Handle the response from the server
                    alert(response); // Display the response message
                    // Reload the page or update the UI as needed
                },
                error: function(xhr, status, error) {
                    // Handle errors
                    console.error(xhr.responseText);
                }
            });
        });
    });

// Listen to click events on edit buttons
document.querySelectorAll('.edit-button').forEach(button => {
        button.addEventListener('click', function() {
            var employeeID = this.dataset.employeeId;

            // Make an AJAX call to fetch data for the selected employee
            $.ajax({
                url: 'get_employee.php',
                type: 'POST',
                data: { employee_id: employeeID },
                dataType: 'json',
                success: function(employee) {
                    // Populate the edit form fields
                    document.getElementById('Name1').value = employee.Name;
                    document.getElementById('username1').value = employee.Email;
                    document.getElementById('address1').value = employee.Address;
                    document.getElementById('Rate1').value = employee.Rate;
                    document.querySelector('#inputGroupSelect011').value = employee.Employment_type;
                    document.querySelector('#inputGroupSelect012').value = employee.Job_Title;

                    // Set the employee ID as a data attribute of the save changes button
                    document.getElementById('saveChangesBtn').dataset.employeeId = employeeID;
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching employee data: " + xhr.responseText);
                }
            });
        });
    });

    document.getElementById('saveChangesBtn').addEventListener('click', function() {
    var employeeID = this.dataset.employeeId;
    var formData = new FormData();
    formData.append('Name', document.getElementById('Name1').value);
    formData.append('username', document.getElementById('username1').value);
    formData.append('address', document.getElementById('address1').value);
    formData.append('Rate', document.getElementById('Rate1').value);
    formData.append('type', document.getElementById('type').value);
    formData.append('amount', document.getElementById('amount').value);
    formData.append('inputGroupSelect011', document.getElementById('inputGroupSelect011').value);
    formData.append('inputGroupSelect012', document.getElementById('inputGroupSelect012').value);
    formData.append('employee_id', this.dataset.employeeId);
    formData.append('employee_id', employeeID);

    fetch('update_employee.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        
        alert("Server response: " + text);
        $('#edit-button').modal('hide');
        location.reload();
    })
    .catch(error => alert("Failed to update employee: " + error));
});
    });



   
</script>

</body>
</html>