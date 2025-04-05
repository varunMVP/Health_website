<?php
$db = new SQLite3('symptoms.db');

$symptom = strtolower($_GET['symptom'] ?? '');
$symptomInfo = null;

if ($symptom) {
    $stmt = $db->prepare('SELECT * FROM symptoms WHERE name = :symptom');
    $stmt->bindValue(':symptom', $symptom, SQLITE3_TEXT);
    $result = $stmt->execute();
    $symptomInfo = $result->fetchArray(SQLITE3_ASSOC);

    if ($symptomInfo) {
        $stmtRemedies = $db->prepare('SELECT remedy FROM remedies WHERE symptom_id = :id');
        $stmtRemedies->bindValue(':id', $symptomInfo['id'], SQLITE3_INTEGER);
        $resultRemedies = $stmtRemedies->execute();

        $stmtMedicines = $db->prepare('SELECT medicine FROM medicines WHERE symptom_id = :id');
        $stmtMedicines->bindValue(':id', $symptomInfo['id'], SQLITE3_INTEGER);
        $resultMedicines = $stmtMedicines->execute();

        $symptomInfo['remedies'] = [];
        $symptomInfo['medicines'] = [];

        while ($row = $resultRemedies->fetchArray(SQLITE3_ASSOC)) {
            $symptomInfo['remedies'][] = $row['remedy'];
        }

        while ($row = $resultMedicines->fetchArray(SQLITE3_ASSOC)) {
            $symptomInfo['medicines'][] = $row['medicine'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Symptom Checker</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f0f8ff;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #4CAF50;
        }
        #result {
            margin-top: 20px;
            padding: 15px;
            background-color: #e7f3e8;
            border-radius: 5px;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Symptom Checker</h1>
        <div id="result">
            <?php if ($symptomInfo): ?>
                <h2><?= ucfirst($symptom) ?></h2>
                <p><strong>Description:</strong> <?= $symptomInfo['description'] ?></p>
                <h3>Home Remedies:</h3>
                <ul>
                    <?php foreach ($symptomInfo['remedies'] as $remedy): ?>
                        <li><?= $remedy ?></li>
                    <?php endforeach; ?>
                </ul>
                <h3>Suggested Medicines:</h3>
                <ul>
                    <?php foreach ($symptomInfo['medicines'] as $medicine): ?>
                        <li><?= $medicine ?></li>
                    <?php endforeach; ?>
                </ul>
                <p><strong>Note:</strong> Always consult with a healthcare professional before starting any treatment.</p>
            <?php else: ?>
                <p>Sorry, we don't have information about "<?= htmlspecialchars($symptom) ?>" in our database. Please consult a healthcare professional for accurate diagnosis and treatment.</p>
            <?php endif; ?>
        </div>
        <a href="index.html" class="back-link">Back to Home</a>
    </div>
</body>
</html>