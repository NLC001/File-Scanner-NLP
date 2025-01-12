function showPreview() {
const fileInput = document.getElementById("fileToUpload");
        const filePreview = document.getElementById("filePreview");
        // Clear previous preview
        filePreview.innerHTML = "";
        // Loop through selected files and display names
        for (let i = 0; i < fileInput.files.length; i++) {
const file = fileInput.files[i];
        const listItem = document.createElement("p");
        listItem.textContent = "File selected: " + file.name;
        filePreview.appendChild(listItem);
}

// Show the preview area
filePreview.style.display = "block";
        }

fetch('upload_preview.php', {
method: 'POST',
        body: formData
})
        .then(response => {
        if (!response.ok) {
        throw new Error(`Network response was not ok: ${response.statusText}`);
        }
        return response.text();
        })
        .then(data => {
        filePreview.innerHTML = `<strong>Preview:</strong><br>${data}`;
        })
        .catch(error => {
        console.error('Error fetching preview:', error);
                filePreview.innerHTML = '<p>Error loading preview.</p>';
        });
} else {
// Hide preview if no file selected
filePreview.style.display = 'none';
}
}
