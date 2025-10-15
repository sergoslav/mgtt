<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Загрузка Excel</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { font-family: sans-serif; margin: 2rem; }
        form { border: 1px solid #ddd; padding: 1rem; border-radius: 6px; width: 400px; }
        input[type=file] { margin-bottom: 1rem; }
        .result { margin-top: 1rem; white-space: pre; font-size: 14px; }
    </style>
</head>
<body>
<h2>Загрузка Excel-файла</h2>

<form id="uploadForm" enctype="multipart/form-data">
    <input type="file" name="file" accept=".xlsx,.xls" required>
    <button type="submit">Загрузить</button>
</form>

<div class="result" id="result"></div>

<script>
    document.getElementById('uploadForm').addEventListener('submit', async e => {
        e.preventDefault();
        const form = e.target;
        const result = document.getElementById('result');

        const formData = new FormData(form);

        result.textContent = 'Загрузка...';

        const res = await fetch('{{ route('upload') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData,
        });

        const data = await res.json();
        result.textContent = JSON.stringify(data, null, 2);
    });
</script>
</body>
</html>
