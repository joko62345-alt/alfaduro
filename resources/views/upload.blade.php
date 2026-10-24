
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload dan Manipulasi Gambar</title>
    <style>
        body {
            margin: 0;
            padding: 32px 16px;
            background: #f4f7fb;
            color: #243247;
            font: 15px/1.6 Arial, sans-serif;
        }

        main {
            max-width: 600px;
            margin: 0 auto;
            padding: 24px;
            border: 1px solid #e1e7ef;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 8px 24px rgb(36 50 71 / 8%);
        }

        h1 {
            margin: 0 0 20px;
            font-size: 24px;
        }

        form,
        form > div {
            display: grid;
            gap: 10px;
        }

        form {
            gap: 18px;
        }

        input,
        select,
        button {
            box-sizing: border-box;
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font: inherit;
        }

        button {
            border: 0;
            background: #2563eb;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .notice {
            margin: 0 0 16px;
            padding: 10px 12px;
            border-radius: 7px;
            background: #ecfdf3;
            color: #166534;
        }

        .errors {
            padding-left: 32px;
            color: #b42318;
        }

        .hint {
            color: #64748b;
        }

        .result {
            display: block;
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 14px rgb(36 50 71 / 14%);
        }

        @media (max-width: 480px) {
            body {
                padding: 16px 10px;
            }

            main {
                padding: 18px;
            }
        }
    </style>
</head>
<body>
    <main>
        <h1>Upload dan Manipulasi Gambar</h1>

        @if (session('success'))
            <p class="notice" role="status">{{ session('success') }}</p>
        @endif

        @if ($errors->any())
            <ul class="errors" role="alert">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <form action="{{ route('image.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="field">
                <label for="photo">Pilih gambar JPEG, JPG, atau PNG (maksimal 2 MB):</label>
                <input id="photo" type="file" name="photo" accept=".jpg,.jpeg,.png,image/jpeg,image/png" required>
                @error('photo')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <div class="field">
                <label for="rotation">Putar gambar:</label>
                <select id="rotation" name="rotation" required>
                    <option value="0" @selected(old('rotation', '90') === '0')>Tidak diputar</option>
                    <option value="90" @selected(old('rotation', '90') === '90')>90° searah jarum jam</option>
                    <option value="180" @selected(old('rotation', '90') === '180')>180° searah jarum jam</option>
                    <option value="270" @selected(old('rotation', '90') === '270')>270° searah jarum jam</option>
                </select>
                @error('rotation')
                    <p>{{ $message }}</p>
                @enderror
            </div>

            <button type="submit">Upload dan Manipulasi Gambar</button>
        </form>

        <p class="hint">
            Gambar akan dipotong dari tengah dan diubah menjadi 300 × 300 piksel,
            lalu diputar sesuai pilihan dan diberi watermark ”.
        </p>

        @if (session('image_url'))
            <h2>Hasil manipulasi</h2>
            <img class="result" src="{{ session('image_url') }}" alt="Hasil gambar setelah dimanipulasi" width="300" height="300">
        @endif
    </main>
</body>
</html>
