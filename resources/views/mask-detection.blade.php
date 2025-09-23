<!DOCTYPE html>
<html>
<head>
    <title>Mask Detection</title>
</head>
<body>
    <h2>Upload Foto untuk Deteksi Masker</h2>

    <form action="/mask-detection" method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="image" accept="image/*" required>
        <button type="submit">Deteksi</button>
    </form>

    @if(isset($result))
        <h3>Hasil Deteksi:</h3>
        <pre>{{ json_encode($result, JSON_PRETTY_PRINT) }}</pre>
    @endif
</body>
</html>
