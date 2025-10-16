<!DOCTYPE html>
<html lang="ru">
<head>
    @vite(['resources/js/app.js'])
    <meta charset="UTF-8">
    <title>Загрузка Excel</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            font-family: sans-serif;
            margin: 2rem;
        }

        form {
            border: 1px solid #ddd;
            padding: 1rem;
            border-radius: 6px;
            width: 400px;
        }

        input[type=file] {
            margin-bottom: 1rem;
        }

        .result {
            margin-top: 1rem;
            white-space: pre;
            font-size: 14px;
        }
    </style>
</head>
<hr>
<h2>Загрузка Excel-файла</h2>

<form id="uploadForm" enctype="multipart/form-data">
    <input type="file" name="file" accept=".xlsx,.xls" required>
    <button type="submit">Загрузить</button>
</form>

<h3>Upload Status</h3>
<div class="result" id="result">{}</div>

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

<hr>
<a href="{{ URL::route('rows.index', ['from_date' => '2020-10-14', 'to_date' => '2020-10-20']) }}">Show Rows</a>
<hr>

<h3>Row Import Status</h3>
<!-- Rows import status container -->
<ul id="rows-list" class="list-group">
    <li class="list-group-item">Waiting...</li>
</ul>

<script>
    // if window.Echo not created, try again
    function setupListener() {
        if (typeof window.Echo !== 'undefined') {
            const list = document.getElementById('rows-list');
            const channel = window.Echo.channel('rows');

            channel.listen('.row.created', (e) => {
                console.log('row:', e);

                // Add item to list
                const newItem = document.createElement('li');
                newItem.className = 'list-group-item';
                newItem.textContent = 'Imported row id: ' + JSON.stringify(e.id);

                list.insertBefore(newItem, list.firstChild);
            });

            channel.listen('.row.updated', (e) => {
                console.log('row:', e);

                // Add item to list
                const newItem = document.createElement('li');
                newItem.className = 'list-group-item';
                newItem.textContent = 'Updated row id: ' + JSON.stringify(e.id);

                list.insertBefore(newItem, list.firstChild);
            });
        } else {
            setTimeout(setupListener, 100);
        }
    }

    setupListener();

</script>

</body>
</html>
