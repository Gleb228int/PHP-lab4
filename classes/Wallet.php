<?php
require_once __DIR__ . '/Transaction.php';
require_once __DIR__ . '/RecurringExpense.php';

class Wallet {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getTransactions(): array {
        $stmt = $this->pdo->query("SELECT * FROM transactions ORDER BY date DESC");
        $rows = $stmt->fetchAll();
        $transactions = [];

        foreach ($rows as $row) {
            if ($row['type'] === 'recurring') {
                $transactions[] = new RecurringExpense(
                    (float)$row['amount'],
                    $row['category'],
                    $row['date'],
                    $row['frequency'] ?? 'Щомісячно',
                    (int)$row['id']
                );
            } else {
                $transactions[] = new Transaction(
                    (float)$row['amount'],
                    $row['category'],
                    $row['date'],
                    (int)$row['id']
                );
            }
        }

        return $transactions;
    }

    public function addTransaction(Transaction $tx, string $type = 'standard', ?string $frequency = null): void {
        $stmt = $this->pdo->prepare("INSERT INTO transactions (amount, category, date, type, frequency) VALUES (:amount, :category, :date, :type, :frequency)");
        $stmt->execute([
            ':amount' => $tx->getAmount(),
            ':category' => $tx->getCategory(),
            ':date' => $tx->getDate(),
            ':type' => $type,
            ':frequency' => $frequency
        ]);
    }

    public function totalBalance(): float {
        $stmt = $this->pdo->query("SELECT SUM(amount) as total FROM transactions");
        $result = $stmt->fetch();
        return (float)($result['total'] ?? 0);
    }

    public function totalByCategory(string $category): float {
        $stmt = $this->pdo->prepare("SELECT SUM(amount) as total FROM transactions WHERE category = :category");
        $stmt->execute([':category' => $category]);
        $result = $stmt->fetch();
        return (float)($result['total'] ?? 0);
    }
}