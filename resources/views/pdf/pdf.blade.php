<div class="container">
    <form action="{{ route('generate-pdf') }}" method="POST" enctype="multipart/form-data">
    @csrf
        <div class="insertJSON">
            <label for="fileInput" class="fileLabel">Selecciona un archivo JSON:</label>
            <input type="file" id="fileInput" name="json_data" accept=".json" class="fileInput" onchange="checkFile()">
        </div>
        <div class="downloadPDF" style="display:none;">
            <button type="submit" class="btn btn-primary">Generar PDF</button>
        </div>
    </form>
</div>