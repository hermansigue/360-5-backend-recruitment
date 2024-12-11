<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Data</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <div class="container" style="max-width: 800px; margin: 0 auto; padding: 20px;">
        <h1>Product Data!</h1>

        <!-- Column Select -->
        <div style="margin-bottom: 20px;">
            <label for="variant-select">Choose a Variant:</label>
            <select id="variant-select" name="variant" style="width: 100%; padding: 8px;">
                <option value=""></option>
            </select>
        </div>

        <!-- Tabel -->
        <table id="table-set">
            <thead>
                <tr>
                    <th >Variant ID</th>
                    <th >Variant Title</th>
                    <th >Quantity</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>    
    <script src="/js/script.js"></script>
</body>
</html>
