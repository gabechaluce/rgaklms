<?php
include 'includes/session.php';

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Query to get return records with student and book details
$sql = "SELECT r.id, s.student_id, CONCAT(s.firstname, ' ', s.lastname) AS student_name, 
               b.title AS book_title, b.author, r.date_return 
        FROM returns r
        JOIN students s ON r.student_id = s.id
        JOIN books b ON r.book_id = b.id
        ORDER BY r.date_return DESC";

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Return Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .no-results {
            color: #666;
            font-style: italic;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Book Return Report</h1>
    <p>Generated on: <?php echo date('Y-m-d H:i:s'); ?></p>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Return ID</th>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Book Title</th>
                    <th>Author</th>
                    <th>Return Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                    <td><?php echo htmlspecialchars($row['author']); ?></td>
                    <td><?php echo htmlspecialchars($row['date_return']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-results">No book returns found in the system.</p>
    <?php endif; ?>

</body>
</html>

<?php
mysqli_close($conn);
?>