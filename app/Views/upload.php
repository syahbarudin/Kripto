<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white shadow-md rounded-lg p-6 w-96">
        <!-- Header -->
        <h1 class="text-2xl font-bold mb-4 text-center">Upload File untuk Enkripsi</h1>

        <!-- Flash Messages -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline"><?php echo session()->getFlashdata('error'); ?></span>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline"><?php echo session()->getFlashdata('success'); ?></span>
            </div>
        <?php endif; ?>

        <!-- Upload Form -->
        <form id="uploadForm" action="<?php echo site_url('FileEncryptor/upload'); ?>" method="post" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="userfile" class="block text-gray-700 text-sm font-bold mb-2 flex items-center">
                    <i class="fa-solid fa-magnifying-glass mr-2"></i> Pilih File:
                </label>
                <input type="file" name="userfile" id="userfile" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"/>
            </div>
            <button type="button" onclick="showUploadModal()" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none flex items-center justify-center">
                <i class="fa-solid fa-cloud-arrow-up mr-2"></i> Upload
            </button>
        </form>

        <!-- File List -->
        <h2 class="text-lg font-bold mt-6">File yang Sudah Di-upload</h2>
        <?php if (!empty($files)): ?>
            <ul class="mt-4">
                <?php foreach ($files as $file): ?>
                    <li class="flex justify-between items-center mb-2">
                        <span><?php echo htmlspecialchars($file['filename']); ?></span>
                        <div class="flex space-x-2">
                            <a href="<?php echo site_url('FileEncryptor/encrypt/' . $file['id']); ?>" class="bg-yellow-500 hover:bg-yellow-700 text-white py-1 px-2 rounded">
                                Encrypt
                            </a>    
                            <button onclick="showDownloadDecryptModal('<?php echo $file['id']; ?>')" class="bg-blue-500 hover:bg-blue-700 text-white py-1 px-2 rounded">
                                Decrypt
                            </button>
                            <a href="<?php echo site_url('FileEncryptor/delete/' . $file['id']); ?>" class="bg-red-500 hover:bg-red-700 text-white py-1 px-2 rounded">
                                Delete
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-gray-500">Tidak ada file yang diupload.</p>
        <?php endif; ?>
    </div>

    <!-- Upload Modal -->
    <div id="uploadModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-80">
            <h2 class="text-xl font-bold mb-4">Masukkan Kunci Enkripsi</h2>
            <form id="uploadKeyForm" method="post" action="<?php echo site_url('FileEncryptor/upload'); ?>" enctype="multipart/form-data">
                <input type="hidden" name="userfile" id="hiddenFileInput"/>
                <div class="mb-4">
                    <label for="vigenereKeyUpload" class="block text-gray-700 text-sm font-bold mb-2">Kunci Vigenere:</label>
                    <input type="text" id="vigenereKeyUpload" name="vigenereKey" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"/>
                </div>
                <div class="mb-4">
                    <label for="caesarKeyUpload" class="block text-gray-700 text-sm font-bold mb-2">Kunci Caesar:</label>
                    <input type="number" id="caesarKeyUpload" name="caesarKey" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"/>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeUploadModal()" class="bg-gray-500 hover:bg-gray-700 text-white py-1 px-3 rounded">
                        Batal
                    </button>
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white py-1 px-3 rounded">
                        Upload
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Download Decrypt Modal -->
    <div id="downloadDecryptModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-80">
            <h2 class="text-xl font-bold mb-4">Masukkan Kunci untuk Decrypt</h2>
            <form id="decryptForm" method="post" action="<?php echo site_url('FileEncryptor/downloadDecrypt'); ?>">
                <input type="hidden" name="file_id" id="fileIdInputDownload" />
                <div class="mb-4">
                    <label for="vigenereKeyDecrypt" class="block text-gray-700 text-sm font-bold mb-2">Kunci Vigenere:</label>
                    <input type="text" id="vigenereKeyDecrypt" name="vigenereKey" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"/>
                </div>
                <div class="mb-4">
                    <label for="caesarKeyDecrypt" class="block text-gray-700 text-sm font-bold mb-2">Kunci Caesar:</label>
                    <input type="number" id="caesarKeyDecrypt" name="caesarKey" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"/>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeDownloadDecryptModal()" class="bg-gray-500 hover:bg-gray-700 text-white py-1 px-3 rounded">
                        Batal
                    </button>
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white py-1 px-3 rounded">
                        Download
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        function showUploadModal() {
            const fileInput = document.getElementById('userfile');
            const hiddenFileInput = document.getElementById('hiddenFileInput');

            if (!fileInput.files[0]) {
                alert('Harap pilih file terlebih dahulu!');
                return;
            }

            hiddenFileInput.files = fileInput.files;
            document.getElementById('uploadModal').classList.remove('hidden');
        }

        function closeUploadModal() {
            document.getElementById('uploadModal').classList.add('hidden');
        }

        function showDownloadDecryptModal(fileId) {
            document.getElementById('fileIdInputDownload').value = fileId;
            document.getElementById('downloadDecryptModal').classList.remove('hidden');
        }

        function closeDownloadDecryptModal() {
            document.getElementById('downloadDecryptModal').classList.add('hidden');
        }
    </script>

</body>
</html>
