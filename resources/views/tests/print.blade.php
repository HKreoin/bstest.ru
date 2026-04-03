<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Протокол теста — {{ $attempt->test->title }}</title>
    <style>
        @page { size: A4; margin: 20mm; }
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
            color: #1f2937;
            line-height: 1.5;
            font-size: 14px;
        }
        h1, h2 {
            margin: 0;
            font-weight: 600;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 10px;
            vertical-align: top;
            text-align: left;
        }
        .status {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
            color: #fff;
        }
        .status.success { background: #16a34a; }
        .status.fail { background: #dc2626; }
        .signature-row {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 12px;
        }
        .signature-line {
            flex: 1 1 200px;
            border-bottom: 1px solid #111827;
            height: 24px;
        }
        .signature-name {
            min-width: 160px;
            font-style: italic;
        }
        @media print {
            body { color: #000; }
            .status { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
@php
    $completedAtLocal = $attempt->completed_at?->setTimezone('Europe/Moscow');
    $completedAtText = $completedAtLocal?->format('d.m.Y H:i') ?? '—';
    $timeSpentSeconds = $attempt->calculatedTimeSpentSeconds();
    $timeSpent = $timeSpentSeconds !== null ? gmdate('H:i:s', $timeSpentSeconds) : '—';
    $scorePercent = $attempt->score_percent !== null ? number_format((float) $attempt->score_percent, 2) . ' %' : '—';
    $nameParts = preg_split('/\s+/u', trim($attempt->participant_name ?? ''), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $signature = ($nameParts[0] ?? '') . ' ' . collect($nameParts)->slice(1)->map(fn ($part) => mb_substr($part, 0, 1) . '.')->join(' ');
@endphp

<h1>Протокол прохождения теста</h1>
<h2>«{{ $attempt->test->title }}»</h2>

<table>
    <tr>
        <th>Участник</th>
        <td>{{ $attempt->participant_name }}</td>
    </tr>
    <tr>
        <th>Дата завершения (GMT+3)</th>
        <td>{{ $completedAtText }}</td>
    </tr>
    <tr>
        <th>Затрачено времени</th>
        <td>{{ $timeSpent }}</td>
    </tr>
    <tr>
        <th>Результат</th>
        <td>
            {{ $scorePercent }} / проходной минимум {{ $attempt->test->passing_score_percent }} %
            <br>Итог: {{ $attempt->passed ? 'ТЕСТ ПРОЙДЕН' : 'ТЕСТ НЕ ПРОЙДЕН' }}
        </td>
    </tr>
    <tr>
        <th>Статистика</th>
        <td>Правильных ответов: {{ $attempt->correct_questions }} из {{ $attempt->total_questions }}</td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th>№</th>
            <th>Вопрос</th>
            <th>Выбранные ответы</th>
            <th>Правильные ответы</th>
            <th>Баллы</th>
            <th>Статус</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($attempt->questionAttempts as $index => $questionAttempt)
            @php
                $correctOptions = $questionAttempt->question->answerOptions
                    ->where('is_correct', true)
                    ->pluck('text')
                    ->filter()
                    ->implode('; ');
            @endphp
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $questionAttempt->question->text }}</td>
                <td>{{ $questionAttempt->selected_option_text }}</td>
                <td>{{ $correctOptions }}</td>
                <td>{{ $questionAttempt->points_awarded }} / {{ $questionAttempt->question->points }}</td>
                <td>
                    <span class="status {{ $questionAttempt->is_correct ? 'success' : 'fail' }}">
                        {{ $questionAttempt->is_correct ? 'Верно' : 'Неверно' }}
                    </span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="signature-row">
    <span>Подпись участника</span>
    <span class="signature-line"></span>
    <span class="signature-name">/{{ trim($signature) !== '' ? trim($signature) : $attempt->participant_name }}/</span>
</div>
<div class="signature-row">
    <span>Дата</span>
    <span class="signature-line"></span>
    <span class="signature-name">{{ $completedAtText }}</span>
</div>
</body>
</html>