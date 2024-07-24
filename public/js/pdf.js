let jsonData;

function checkFile() {
    const fileInput = document.getElementById('fileInput');
    const file = fileInput.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                jsonData = JSON.parse(e.target.result);
                document.querySelector('.downloadPDF').style.display = 'block';
            } catch (error) {
                alert('El archivo no es un JSON válido.');
            }
        }
        reader.readAsText(file);
    }
}
