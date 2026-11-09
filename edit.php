<?php
require_once 'lib/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM transactions WHERE id = :id");
$stmt->execute([':id' => $id]);
$transaction = $stmt->fetch();

if (!$transaction) {
    header('Location: index.php');
    exit;
}

$errors = [];
$amount = $transaction['amount'];
$category = $transaction['category'];
$date = $transaction['date'];
$type = $transaction['type'];
$frequency = $transaction['frequency'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = trim($_POST['amount'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $type = trim($_POST['type'] ?? 'standard');
    $frequency = trim($_POST['frequency'] ?? '');

    if ($amount === '' || !is_numeric($amount) || floatval($amount) <= 0) {
        $errors['amount'] = 'Сума має бути числом більшим за 0.';
    }

    $allowedCategories = ['Продукти', 'Техніка', 'Транспорт', 'Одяг', 'Розваги', 'Комунальні послуги', 'Інше'];
    if ($category === '' || !in_array($category, $allowedCategories)) {
        $errors['category'] = 'Будь ласка, оберіть коректну категорію.';
    }

    if ($date === '') {
        $errors['date'] = 'Дата обов\'язкова.';
    }

    if (empty($errors)) {
        $updateStmt = $pdo->prepare("UPDATE transactions SET amount = :amount, category = :category, date = :date, type = :type, frequency = :frequency WHERE id = :id");
        $updateStmt->execute([
            ':amount' => floatval($amount),
            ':category' => $category,
            ':date' => $date,
            ':type' => $type,
            ':frequency' => $type === 'recurring' ? $frequency : null,
            ':id' => $id
        ]);

        header('Location: index.php?success=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редагування транзакції</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Редагувати транзакцію</h1>
        
        <div class="form-card">
            <form method="post" action="">
                <div class="form-group">
                    <label for="type">Тип витрати:</label>
                    <select id="type" name="type">
                        <option value="standard" <?= $type === 'standard' ? 'selected' : '' ?>>Звичайна витрата</option>
                        <option value="recurring" <?= $type === 'recurring' ? 'selected' : '' ?>>Регулярна витрата</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="amount">Сума (грн):</label>
                    <input type="number" id="amount" name="amount" step="0.01" min="0.01" value="<?= htmlspecialchars($amount) ?>" required>
                    <?php if (isset($errors['amount'])): ?>
                        <span class="error"><?= $errors['amount'] ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="category">Категорія:</label>
                    <select id="category" name="category" required>
                        <?php foreach (['Продукти', 'Техніка', 'Транспорт', 'Одяг', 'Розваги', 'Комунальні послуги', 'Інше'] as $cat): ?>
                            <option value="<?= $cat ?>" <?= $category === $cat ? 'selected' : '' ?>><?= $cat ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group" id="frequencyGroup" style="display: <?= $type === 'recurring' ? 'block' : 'none' ?>;">
                    <label for="frequency">Регулярність:</label>
                    <input type="text" id="frequency" name="frequency" value="<?= htmlspecialchars($frequency) ?>">
                </div>

                <div class="form-group">
                    <label for="date">Дата:</label>
                    <input type="date" id="date" name="date" value="<?= htmlspecialchars($date) ?>" required>
                </div>

                <button type="submit" class="btn">Зберегти зміни</button>
                <a href="index.php" class="btn" style="background-color: #6c757d; text-decoration: none; display: inline-block; margin-left: 10px;">Скасувати</a>
            </form>
        </div>
    </div>

    <script>
        const typeSelect = document.getElementById('type');
        const frequencyGroup = document.getElementById('frequencyGroup');
        typeSelect.addEventListener('change', () => {
            frequencyGroup.style.display = typeSelect.value === 'recurring' ? 'block' : 'none';
        });
    </script>
</body>
</html>