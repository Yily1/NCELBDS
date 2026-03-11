<?php
session_start();
require "../../config/config.php";
require "../../config/functions.php";

if (!isLoggedIn() || $_SESSION['role'] !== 'user') {
    header("Location: ../../index.php");
    exit();
}

# ADD CHICKEN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_chicken'])) {

    $tag_number = trim($_POST['tag_number']);
    $age = intval($_POST['age']);
    $status = $_POST['status'];

    if (!in_array($status, ['Active','Inactive'])) {
        $status = "Active";
    }

    $stmt = $conn->prepare("INSERT INTO chickens (tag_number, age, status) VALUES (?,?,?)");
    $stmt->bind_param("sis",$tag_number,$age,$status);
    $stmt->execute();

    header("Location: user_dashboard.php");
    exit();
}

# DELETE CHICKEN
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);

    $stmt = $conn->prepare("DELETE FROM chickens WHERE chicken_id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();

    header("Location: user_dashboard.php");
    exit();
}

# FETCH CHICKENS
$q = mysqli_query($conn,"SELECT * FROM chickens");

# STATUS COUNT
$status_query = mysqli_query($conn,"SELECT status, COUNT(*) as total FROM chickens GROUP BY status");

$active = 0;
$inactive = 0;

while($s = mysqli_fetch_assoc($status_query)){
    if($s['status']=="Active"){
        $active = $s['total'];
    }
    if($s['status']=="Inactive"){
        $inactive = $s['total'];
    }
}

# LINE CHART DATA
$age_query = mysqli_query($conn,"SELECT chicken_id, age FROM chickens");

$ids = [];
$ages = [];

while($row = mysqli_fetch_assoc($age_query)){
    $ids[] = $row['chicken_id'];
    $ages[] = $row['age'];
}

if(empty($ids)){
    $ids = [0];
    $ages = [0];
}
?>

<!DOCTYPE html>
<html>
<head>
<title>User Dashboard</title>

<link rel="stylesheet" href="../../assets/css/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* Center charts container */
.chart-container {
    display: flex;
    justify-content: center;
    gap: 60px;
    flex-wrap: wrap;
    margin-top: 20px;
    margin-bottom: 60px;
}
.chart-container div {
    width: 400px;
}
</style>

</head>
<body>

<h2 style="text-align:center;">User Dashboard</h2>
<div style="text-align:center;">
<a href="../auth/logout.php">Logout</a>
</div>

<br><br>

<!-- ADD CHICKEN -->
<h3>Add Chicken</h3>

<form method="POST">
<input type="text" name="tag_number" placeholder="Tag Number" required>
<input type="number" name="age" placeholder="Age" required>
<select name="status" required>
<option value="">Select Status</option>
<option value="Active">Active</option>
<option value="Inactive">Inactive</option>
</select>
<button type="submit" name="add_chicken">Add Chicken</button>
</form>

<br>

<!-- CHICKEN TABLE -->
<h3>Chicken List</h3>

<table border="1" cellpadding="6" style="width:100%;">
<tr>
<th>ID</th>
<th>Tag Number</th>
<th>Age</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($row=mysqli_fetch_assoc($q)){ ?>
<tr>
<td><?php echo htmlspecialchars($row['chicken_id']); ?></td>
<td><?php echo htmlspecialchars($row['tag_number']); ?></td>
<td><?php echo htmlspecialchars($row['age']); ?></td>
<td><?php echo htmlspecialchars($row['status']); ?></td>
<td>
<a href="?delete=<?php echo $row['chicken_id'];?>"
onclick="return confirm('Delete this chicken?')">Delete</a>
</td>
</tr>
<?php } ?>
</table>

<!-- CHICKEN STATISTICS -->
<h3 style="text-align:center;">Chicken Statistics</h3>

<div class="chart-container">
    <div>
        <h4 style="text-align:center;">Chicken Ages</h4>
        <canvas id="lineChart"></canvas>
    </div>
    <div>
        <h4 style="text-align:center;">Status Distribution</h4>
        <canvas id="barChart"></canvas>
    </div>
</div>

<script>
const ids = <?php echo json_encode($ids); ?>;
const ages = <?php echo json_encode($ages); ?>;
const active = <?php echo $active;?>;
const inactive = <?php echo $inactive;?>;

// Line Chart
new Chart(document.getElementById("lineChart"),{
type:'line',
data:{
labels:ids,
datasets:[{
label:'Chicken Age',
data:ages,
borderColor:'blue',
backgroundColor:'rgba(0,0,255,0.1)',
fill:true
}]
}
});

// Bar Chart
new Chart(document.getElementById("barChart"),{
type:'bar',
data:{
labels:['Active','Inactive'],
datasets:[{
label:'Chicken Status',
data:[active,inactive],
backgroundColor:['green','red']
}]
}
});
</script>

</body>
</html>