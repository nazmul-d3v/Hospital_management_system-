<?php
require_once 'includes/header.php';
require_once 'includes/db_connect.php';

$doctor_count = 0;
$patient_count = 0;
$appointment_count = 0;

$result_doctors = $conn->query("SELECT COUNT(*) AS count FROM doctors");
if ($result_doctors) {
    $doctor_count = $result_doctors->fetch_assoc()['count'];
}

$result_patients = $conn->query("SELECT COUNT(*) AS count FROM patients");
if ($result_patients) {
    $patient_count = $result_patients->fetch_assoc()['count'];
}

$result_appointments = $conn->query("SELECT COUNT(*) AS count FROM appointments");
if ($result_appointments) {
    $appointment_count = $result_appointments->fetch_assoc()['count'];
}

$conn->close();
?>
    <h1>Dashboard</h1>
    <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    <div class="dashboard-stats">
        <div class="stat-card">
            <h3>Total Doctors</h3>
            <span class="count"><?php echo $doctor_count; ?></span>
        </div>
        <div class="stat-card">
            <h3>Total Patients</h3>
            <span class="count"><?php echo $patient_count; ?></span>
        </div>
        <div class="stat-card">
            <h3>Total Appointments</h3>
            <span class="count"><?php echo $appointment_count; ?></span>
        </div>
    </div>
<?php
require_once 'includes/footer.php';
?>