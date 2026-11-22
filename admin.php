<?php
require_once 'config.php';

// Fetch all student submissions with diagnostic breakdown
$query = "
    SELECT s.id as sub_id, st.full_name, st.student_id, s.type, s.total_score, s.max_score, s.percentage, s.submitted_at
    FROM submissions s
    JOIN students st ON s.student_id = st.student_id
    ORDER BY s.submitted_at DESC
";
$submissions = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Portal - Jerro Computer School Limited</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8fafc; color: #334155; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #cbd5e1; padding: 12px; text-align: left; }
        th { background: #1e293b; color: white; }
        tr:nth-child(even) { background: #f1f5f9; }
        .badge { padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 12px; }
        .badge-exam { background: #dbeafe; color: #1e40af; }
        .badge-test { background: #ccfbf1; color: #115e59; }
        .btn-back { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #1e293b; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Jerro Computer School - Admin Dashboard</h2>
        <p>Monitor student performance, failed diagnostics, and edit assessment criteria.</p>
        
        <h3>Submitted Student Assessments</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Type</th>
                    <th>Score</th>
                    <th>Percentage</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($submissions as $row): ?>
                <tr>
                    <td><?php echo date('M d, Y H:i', strtotime($row['submitted_at'])); ?></td>
                    <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><span class="badge badge-<?php echo $row['type']; ?>"><?php echo strtoupper($row['type']); ?></span></td>
                    <td><?php echo $row['total_score']; ?> / <?php echo $row['max_score']; ?></td>
                    <td><strong><?php echo number_format($row['percentage'], 1); ?>%</strong></td>
                    <td><a href="admin_diagnostic.php?sub_id=<?php echo $row['sub_id']; ?>">Diagnostic View</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <br>
        <a href="logout.php" class="btn-back">Logout</a>
    </div>
</body>
</html>