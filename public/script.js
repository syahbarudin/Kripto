// Mendapatkan elemen modal
var uploadModal = document.getElementById("uploadModal");
var keyModal = document.getElementById("keyModal");

// Mendapatkan tombol yang membuka modal upload
var uploadBtn = document.getElementById("uploadBtn");

// Mendapatkan elemen span yang menutup modal
var closeUploadBtn = document.getElementsByClassName("close-key")[0];
var closeKeyBtn = document.getElementsByClassName("close-key")[1];

// Ketika pengguna mengklik tombol, buka modal upload
uploadBtn.onclick = function() {
    uploadModal.style.display = "block";
}

// Ketika pengguna mengklik span (x), tutup modal upload
closeUploadBtn.onclick = function() {
    uploadModal.style.display = "none";
}

// Ketika pengguna mengklik span (x), tutup modal kata kunci download
closeKeyBtn.onclick = function() {
    keyModal.style.display = "none";
}

// Ketika pengguna mengklik di luar modal, tutup modal upload atau download sesuai dengan targetnya.
window.onclick = function(event) {
     if (event.target == uploadModal) {
         uploadModal.style.display = "none";
     } else if (event.target == keyModal) {
         keyModal.style.display = "none";
     }
}

// Fungsi untuk membuka modal kata kunci download dengan ID file yang sesuai.
function openKeyModal(fileId) {
     document.getElementById('file_id').value = fileId; // Set ID file ke input hidden.
     keyModal.style.display = "block"; // Tampilkan modal.
}
