```php
<?php
session_start();
require "../../config/config.php";
require "../../config/functions.php";

// Protect page
if (!isLoggedIn() || $_SESSION['role'] !== 'user') {
    header("Location: ../../index.php");
    exit();
}

# ADD CHICKEN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_chicken'])) {

    $tag_number = trim($_POST['tag_number']);
    $age = intval($_POST['age']);
    $status = $_POST['status'];

    # Validate status
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

# STATISTICS
$total_chickens = mysqli_num_rows($q);

$avg = mysqli_query($conn,"SELECT AVG(age) as avg_age FROM chickens");
$avg_row = mysqli_fetch_assoc($avg);
$avg_age = round($avg_row['avg_age'],2);

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
$age_query = mysqli_query($conn,"SELECT chicken_id,age FROM chickens");

$ids = [];
$ages = [];

while($row = mysqli_fetch_assoc($age_query)){

    $ids[] = $row['chicken_id'];
    $ages[] = $row['age'];

}
?>

<link rel="stylesheet" href="../../assets/css/style.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<h2>User Dashboard</h2>

<a href="../auth/logout.php">Logout</a>

<br><br>

<!-- CARDS -->

<div style="display:flex;gap:20px;margin-bottom:30px;">

<div style="background:#eee;padding:20px;border-radius:8px;width:200px;">
<h3>Total Chickens</h3>
<p style="font-size:22px;"><?php echo $total_chickens;?></p>
</div>

<div style="background:#eee;padding:20px;border-radius:8px;width:200px;">
<h3>Average Age</h3>
<p style="font-size:22px;"><?php echo $avg_age;?> months</p>
</div>

</div>

<!-- CHARTS -->

<div style="display:flex;gap:40px;margin-bottom:40px;flex-wrap:wrap;">

<div style="width:400px;">
<h3>Chicken Ages</h3>
<canvas id="lineChart"></canvas>
</div>

<div style="width:400px;">
<h3>Status Distribution</h3>
<canvas id="barChart"></canvas>
</div>

</div>

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

<!-- TABLE -->

<h3>Chicken List</h3>

<table border="1" cellpadding="6">

<tr>
<th>ID</th>
<th>Tag Number</th>
<th>Age</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($row=mysqli_fetch_assoc($q)){ ?>

<tr>

<td><?php echo $row['chicken_id']; ?></td>
<td><?php echo $row['tag_number']; ?></td>
<td><?php echo $row['age']; ?></td>
<td><?php echo $row['status']; ?></td>

<td>
<a href="?delete=<?php echo $row['chicken_id'];?>"
onclick="return confirm('Delete this chicken?')">Delete</a>
</td>

</tr>

<?php } ?>

</table>


<script>

const ids = <?php echo json_encode($ids); ?>;
const ages = <?php echo json_encode($ages); ?>;

const active = <?php echo $active;?>;
const inactive = <?php echo $inactive;?>;

# LINE GRAPH

new Chart(document.getElementById("lineChart"),{

type:'line',

data:{
labels:ids,
datasets:[{
label:'Chicken Age',
data:ages,
borderColor:'blue',
fill:false
}]
}

});


# BAR GRAPH

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
```
