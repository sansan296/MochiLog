<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>アクセス権がありません</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans JP', sans-serif;
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            color: #1f2937;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            text-align: center;
            background: white;
            padding: 3rem 4rem;
            border-radius: 1.5rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            max-width: 480px;
        }
        .error-code {
            font-size: 5rem;
            font-weight: 700;
            color: #ef4444;
        }
        .error-message {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .back-link {
            display: inline-block;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: background 0.3s ease;
        }
        .back-link:hover {
            background: #2563eb;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">403</div>
        <div class="error-message">🚫 アクセス権がありません</div>
        <p>このページを表示するには管理者権限が必要です。</p>
        <a href="{{ url('/') }}" class="back-link">🏠 ホームへ戻る</a>
    </div>
</body>
</html>
