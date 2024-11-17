<?php

    namespace App\Controllers;

    use App\Models\FileModel;

    class FileEncryptor extends BaseController
    {
        public function index()
        {
            $model = new FileModel();
            $data['files'] = $model->findAll(); // Mengambil semua file yang diupload
            return view('upload', $data); // Tampilkan form upload dengan data file
        }

        public function upload()
        {
            $file = $this->request->getFile('userfile'); // Mendapatkan file dari form
        
            // Pastikan file ada dan valid
            if (!$file || !$file->isValid() || $file->getSize() === 0) {
                return redirect()->back()->with('error', 'File tidak valid atau tidak ditemukan.');
            }
        
            // Baca isi file
            $fileContent = file_get_contents($file->getTempName());
        
            // Ambil input kata kunci untuk Vigenère dan Caesar
            $vigenereKey = trim($this->request->getPost('vigenereKey'));
            $caesarShift = (int)$this->request->getPost('caesarKey');
        
            // Enkripsi isi file
            $encryptedContent = $this->encryptFile($fileContent, $vigenereKey, $caesarShift);
        
            // Simpan hasil enkripsi ke database
            $model = new FileModel();
            $model->save([
                'filename' => $file->getName(),
                'encrypted_content' => $encryptedContent,
            ]);
        
            // Path untuk menyimpan file terenkripsi
            $encryptedFilePath = 'C:/Xampp/htdocs/combine_chiper/encrypted_files/' . 'encrypted_' . $file->getName();
        
            // Pastikan folder ada, jika tidak, buat foldernya
            if (!is_dir('C:/Xampp/htdocs/combine_chiper/encrypted_files')) {
                mkdir('C:/Xampp/htdocs/combine_chiper/encrypted_files', 0755, true);
            }
        
            // Simpan file terenkripsi (file dalam bentuk base64)
            file_put_contents($encryptedFilePath, base64_encode($encryptedContent));
        
            return redirect()->back()->with('success', 'File berhasil diupload dan dienkripsi!');
        }
        

        public function download($id)
        {
            $model = new FileModel();
            $fileData = $model->find($id); // Ambil data file berdasarkan ID

            // Jika file ditemukan
            if ($fileData) {
                // Jika file terenkripsi, minta kata kunci untuk dekripsi
                if ($fileData['encrypted_content']) {
                    return view('enter_key', ['file' => $fileData]);
                } else {
                    return $this->response->setHeader('Content-Type', 'application/octet-stream')
                        ->setHeader('Content-Disposition', 'attachment; filename="' . $fileData['filename'] . '"')
                        ->setBody($fileData['encrypted_content']);
                }
            }

            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        public function downloadWithKey()
        {
            $fileId = trim($this->request->getPost('file_id')); // Ambil file ID dari post request
            $vigenereKey = trim($this->request->getPost('vigenereKey')); // Ambil kata kunci Vigenère
            $caesarShift = (int)$this->request->getPost('caesarKey'); // Ambil kata kunci Caesar

            $model = new FileModel();
            $fileData = $model->find($fileId); // Ambil data file berdasarkan ID

            if ($fileData) {
                try {
                    // Dekripsi file menggunakan kata kunci Vigenère dan Caesar
                    $decryptedContent = $this->decryptFile($fileData['encrypted_content'], $vigenereKey, $caesarShift);

                    // Kirim file dalam bentuk download
                    return $this->response->setHeader('Content-Type', 'application/octet-stream')
                        ->setHeader('Content-Disposition', 'attachment; filename="' . $fileData['filename'] . '"')
                        ->setBody($decryptedContent);
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', 'Gagal mendekripsi file. Periksa kata kunci yang dimasukkan.');
                }
            }

            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        public function delete($id)
        {
            $model = new FileModel();
            $fileData = $model->find($id); // Ambil data file berdasarkan ID

            if ($fileData) {
                // Hapus file terenkripsi dari sistem
                $encryptedFilePath = 'C:/Xampp/htdocs/combine_chiper/encrypted_files/encrypted_' . $fileData['filename'];
                if (file_exists($encryptedFilePath)) {
                    unlink($encryptedFilePath); // Menghapus file dari folder
                }

                // Hapus data file dari database
                $model->delete($id);

                return redirect()->back()->with('success', 'File berhasil dihapus!');
            }

            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        private function encryptFile($text, $keyword, $shift)
        {
            // Enkripsi dengan Vigenère Cipher terlebih dahulu
            $vigenereEncrypted = $this->vigenereCipher($text, strtoupper($keyword), 'encrypt');

            // Kemudian enkripsi hasilnya dengan Caesar Cipher
            return $this->caesarCipher($vigenereEncrypted, $shift, 'encrypt');
        }

        private function decryptFile($text, $keyword, $shift)
        {
            // Dekripsi dengan Caesar Cipher terlebih dahulu
            $caesarDecrypted = $this->caesarCipher($text, $shift, 'decrypt');

            // Kemudian dekripsi hasilnya dengan Vigenère Cipher
            return $this->vigenereCipher($caesarDecrypted, strtoupper($keyword), 'decrypt');
        }

        private function caesarCipher($text, $shift, $mode)
        {
            $result = '';
            $shift = $shift % 26; // Menghindari pergeseran lebih dari 26

            for ($i = 0; $i < strlen($text); $i++) {
                $char = $text[$i];
                if (ctype_alpha($char)) { // Hanya mengenkripsi huruf
                    $asciiOffset = ctype_upper($char) ? ord('A') : ord('a');
                    if ($mode === 'encrypt') {
                        $newChar = chr((ord($char) - $asciiOffset + $shift) % 26 + $asciiOffset);
                    } else { // mode == 'decrypt'
                        $newChar = chr((ord($char) - $asciiOffset - $shift + 26) % 26 + $asciiOffset);
                    }
                    $result .= $newChar;
                } else {
                    $result .= $char; // Karakter non-huruf tidak diubah
                }
            }

            return $result;
        }

        private function vigenereCipher($text, $keyword, $mode)
        {
            $result = '';
            $keywordLength = strlen($keyword);
            $keyIndex = 0;

            for ($i = 0; $i < strlen($text); $i++) {
                $char = $text[$i];
                if (ctype_alpha($char)) {
                    $asciiOffset = ctype_upper($char) ? ord('A') : ord('a');
                    $keywordShift = ord($keyword[$keyIndex % $keywordLength]) - ord('A');

                    if ($mode === 'encrypt') {
                        $newChar = chr((ord($char) - $asciiOffset + $keywordShift) % 26 + $asciiOffset);
                        $keyIndex++;
                    } else { // mode == 'decrypt'
                        $newChar = chr((ord($char) - $asciiOffset - $keywordShift + 26) % 26 + $asciiOffset);
                        if (ctype_alpha($char)) {
                            $keyIndex++;
                        }
                    }
                    $result .= $newChar;
                } else {
                    $result .= $char; // Karakter non-huruf tidak diubah
                }
            }

            return $result;
        }
    }
