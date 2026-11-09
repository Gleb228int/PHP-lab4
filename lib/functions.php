<?php
function formatMoney(float $amount): string {
    return number_format($amount, 2, ',', ' ') . ' грн';
}

function monthName(string $dateString): string {
    $months = [
        '01' => 'Січень', '02' => 'Лютий', '03' => 'Березень',
        '04' => 'Квітень', '05' => 'Травень', '06' => 'Червень',
        '07' => 'Липень', '08' => 'Серпень', '09' => 'Вересень',
        '10' => 'Жовтень', '11' => 'Листопад', '12' => 'Грудень'
    ];
    $monthNum = date('m', strtotime($dateString));
    return $months[$monthNum] ?? 'Невідомо';
}