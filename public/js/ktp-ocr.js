// public/js/ktp-ocr.js

class KTPOCRHandler {
    constructor() {
        this.worker = null;
        this.isProcessing = false;
        this.stream = null;
        this.debug = true;
    }

    log(message, data) {
        if (this.debug) {
            if (data !== undefined) {
                console.log('[KTP-OCR] ' + message, data);
            } else {
                console.log('[KTP-OCR] ' + message);
            }
        }
    }

    async initWorker() {
        if (this.worker) return this.worker;
        
        this.worker = await Tesseract.createWorker('ind', 1, {
            logger: m => {
                if (m.status === 'recognizing text') {
                    this.updateProgress(Math.round(m.progress * 100));
                }
            }
        });
        
        await this.worker.setParameters({
            tessedit_pageseg_mode: '6',
            preserve_interword_spaces: '1'
        });
        
        return this.worker;
    }

    updateProgress(percent) {
        var progressBar = document.getElementById('ocr-progress-bar');
        var progressText = document.getElementById('ocr-progress-text');
        if (progressBar) {
            progressBar.style.width = percent + '%';
            progressBar.setAttribute('aria-valuenow', percent);
        }
        if (progressText) {
            var currentText = progressText.textContent;
            if (currentText.indexOf('Mencoba') !== -1) {
                progressText.textContent = currentText.split('...')[0] + '... ' + percent + '%';
            } else {
                progressText.textContent = 'Memproses: ' + percent + '%';
            }
        }
    }

    clearFormFields() {
        var fields = ['nik', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'gender_id', 'agama_id', 'alamat', 'status_perkawinan_id'];
        for (var i = 0; i < fields.length; i++) {
            var field = document.getElementById(fields[i]);
            if (field) {
                if (field.tagName === 'SELECT') {
                    field.selectedIndex = 0;
                } else {
                    field.value = '';
                }
            }
        }
    }

    // ============ PREPROCESSING STRATEGIES ============
    preprocessHighContrast(ctx, imageData) {
        var data = imageData.data;
        for (var i = 0; i < data.length; i += 4) {
            var gray = data[i] * 0.299 + data[i + 1] * 0.587 + data[i + 2] * 0.114;
            data[i] = data[i + 1] = data[i + 2] = gray;
        }
        var contrast = 2.0;
        var factor = (259 * (contrast * 100 + 255)) / (255 * (259 - contrast * 100));
        for (var i = 0; i < data.length; i += 4) {
            data[i] = Math.min(255, Math.max(0, factor * (data[i] - 128) + 128));
            data[i + 1] = Math.min(255, Math.max(0, factor * (data[i + 1] - 128) + 128));
            data[i + 2] = Math.min(255, Math.max(0, factor * (data[i + 2] - 128) + 128));
        }
        for (var i = 0; i < data.length; i += 4) {
            var v = data[i] > 128 ? 255 : 0;
            data[i] = data[i + 1] = data[i + 2] = v;
        }
        return imageData;
    }
    
    preprocessSharpen(ctx, imageData) {
        var data = imageData.data;
        var width = imageData.width;
        var height = imageData.height;
        for (var i = 0; i < data.length; i += 4) {
            var gray = data[i] * 0.299 + data[i + 1] * 0.587 + data[i + 2] * 0.114;
            data[i] = data[i + 1] = data[i + 2] = gray;
        }
        var original = new Uint8ClampedArray(data);
        var kernel = [0, -1, 0, -1, 5, -1, 0, -1, 0];
        for (var y = 1; y < height - 1; y++) {
            for (var x = 1; x < width - 1; x++) {
                var sum = 0;
                for (var ky = -1; ky <= 1; ky++) {
                    for (var kx = -1; kx <= 1; kx++) {
                        var idx = ((y + ky) * width + (x + kx)) * 4;
                        sum += original[idx] * kernel[(ky + 1) * 3 + (kx + 1)];
                    }
                }
                var idx = (y * width + x) * 4;
                var v = Math.min(255, Math.max(0, sum));
                data[idx] = data[idx + 1] = data[idx + 2] = v;
            }
        }
        var contrast = 1.3;
        var factor = (259 * (contrast * 100 + 255)) / (255 * (259 - contrast * 100));
        for (var i = 0; i < data.length; i += 4) {
            data[i] = Math.min(255, Math.max(0, factor * (data[i] - 128) + 128));
            data[i + 1] = Math.min(255, Math.max(0, factor * (data[i + 1] - 128) + 128));
            data[i + 2] = Math.min(255, Math.max(0, factor * (data[i + 2] - 128) + 128));
        }
        return imageData;
    }
    
    preprocessDenoise(ctx, imageData) {
        var data = imageData.data;
        var width = imageData.width;
        var height = imageData.height;
        for (var i = 0; i < data.length; i += 4) {
            var gray = data[i] * 0.299 + data[i + 1] * 0.587 + data[i + 2] * 0.114;
            data[i] = data[i + 1] = data[i + 2] = gray;
        }
        var original = new Uint8ClampedArray(data);
        for (var y = 1; y < height - 1; y++) {
            for (var x = 1; x < width - 1; x++) {
                var neighbors = [];
                for (var dy = -1; dy <= 1; dy++) {
                    for (var dx = -1; dx <= 1; dx++) {
                        var idx = ((y + dy) * width + (x + dx)) * 4;
                        neighbors.push(original[idx]);
                    }
                }
                neighbors.sort(function(a, b) { return a - b; });
                var idx = (y * width + x) * 4;
                data[idx] = data[idx + 1] = data[idx + 2] = neighbors[4];
            }
        }
        var contrast = 1.5;
        var factor = (259 * (contrast * 100 + 255)) / (255 * (259 - contrast * 100));
        for (var i = 0; i < data.length; i += 4) {
            data[i] = Math.min(255, Math.max(0, factor * (data[i] - 128) + 128));
            data[i + 1] = Math.min(255, Math.max(0, factor * (data[i + 1] - 128) + 128));
            data[i + 2] = Math.min(255, Math.max(0, factor * (data[i + 2] - 128) + 128));
        }
        // Otsu threshold
        var histogram = new Array(256).fill(0);
        for (var i = 0; i < data.length; i += 4) histogram[Math.round(data[i])]++;
        var total = width * height, sum = 0;
        for (var i = 0; i < 256; i++) sum += i * histogram[i];
        var sumB = 0, wB = 0, maxVar = 0, threshold = 128;
        for (var i = 0; i < 256; i++) {
            wB += histogram[i];
            if (wB === 0) continue;
            var wF = total - wB;
            if (wF === 0) break;
            sumB += i * histogram[i];
            var mB = sumB / wB, mF = (sum - sumB) / wF;
            var variance = wB * wF * (mB - mF) * (mB - mF);
            if (variance > maxVar) { maxVar = variance; threshold = i; }
        }
        for (var i = 0; i < data.length; i += 4) {
            var v = data[i] > threshold ? 255 : 0;
            data[i] = data[i + 1] = data[i + 2] = v;
        }
        return imageData;
    }
    
    preprocessLight(ctx, imageData) {
        var data = imageData.data;
        for (var i = 0; i < data.length; i += 4) {
            var gray = data[i] * 0.299 + data[i + 1] * 0.587 + data[i + 2] * 0.114;
            data[i] = data[i + 1] = data[i + 2] = gray;
        }
        var contrast = 1.2;
        var factor = (259 * (contrast * 100 + 255)) / (255 * (259 - contrast * 100));
        for (var i = 0; i < data.length; i += 4) {
            data[i] = Math.min(255, Math.max(0, factor * (data[i] - 128) + 128));
            data[i + 1] = Math.min(255, Math.max(0, factor * (data[i + 1] - 128) + 128));
            data[i + 2] = Math.min(255, Math.max(0, factor * (data[i + 2] - 128) + 128));
        }
        return imageData;
    }

    createPreprocessedImage(imageSource, strategy) {
        var self = this;
        return new Promise(function(resolve) {
            var img = new Image();
            img.crossOrigin = 'Anonymous';
            img.onload = function() {
                var canvas = document.createElement('canvas');
                var ctx = canvas.getContext('2d');
                var maxWidth = 2000, maxHeight = 1200;
                var width = img.width, height = img.height;
                if (width > maxWidth) { height = Math.round(height * maxWidth / width); width = maxWidth; }
                if (height > maxHeight) { width = Math.round(width * maxHeight / height); height = maxHeight; }
                canvas.width = width;
                canvas.height = height;
                ctx.drawImage(img, 0, 0, width, height);
                var imageData = ctx.getImageData(0, 0, width, height);
                switch(strategy) {
                    case 'highContrast': imageData = self.preprocessHighContrast(ctx, imageData); break;
                    case 'sharpen': imageData = self.preprocessSharpen(ctx, imageData); break;
                    case 'denoise': imageData = self.preprocessDenoise(ctx, imageData); break;
                    case 'light': imageData = self.preprocessLight(ctx, imageData); break;
                }
                ctx.putImageData(imageData, 0, 0);
                canvas.toBlob(function(blob) { resolve(blob); }, 'image/png', 1.0);
            };
            if (imageSource instanceof Blob || imageSource instanceof File) {
                img.src = URL.createObjectURL(imageSource);
            } else {
                img.src = imageSource;
            }
        });
    }

    countExtractedFields(data) {
        var count = 0;
        if (data.nik && data.nik.length === 16) count += 3;
        if (data.nama && data.nama.length >= 3) count += 2;
        if (data.tempat_lahir) count += 1;
        if (data.tanggal_lahir) count += 1;
        if (data.gender) count += 1;
        if (data.agama) count += 1;
        if (data.alamat_lengkap) count += 1;
        if (data.status_kawin) count += 1;
        return count;
    }

    preResizeForMobile(imageFile, maxDimension) {
        var self = this;
        return new Promise(function(resolve) {
            if (imageFile.size < 2 * 1024 * 1024) { resolve(imageFile); return; }
            var img = new Image();
            img.onload = function() {
                if (img.width <= maxDimension && img.height <= maxDimension) {
                    URL.revokeObjectURL(img.src); resolve(imageFile); return;
                }
                var canvas = document.createElement('canvas');
                var ctx = canvas.getContext('2d');
                var width = img.width, height = img.height;
                if (width > height) {
                    if (width > maxDimension) { height = Math.round(height * maxDimension / width); width = maxDimension; }
                } else {
                    if (height > maxDimension) { width = Math.round(width * maxDimension / height); height = maxDimension; }
                }
                canvas.width = width; canvas.height = height;
                ctx.drawImage(img, 0, 0, width, height);
                URL.revokeObjectURL(img.src);
                canvas.toBlob(function(blob) { resolve(blob || imageFile); }, 'image/jpeg', 0.92);
            };
            img.onerror = function() { URL.revokeObjectURL(img.src); resolve(imageFile); };
            img.src = URL.createObjectURL(imageFile);
        });
    }

    sleep(ms) { return new Promise(function(resolve) { setTimeout(resolve, ms); }); }

    async processKTPImage(imageFile) {
        if (this.isProcessing) {
            Swal.fire({ text: 'Sedang memproses, harap tunggu...', icon: 'info' });
            return null;
        }
        this.isProcessing = true;
        this.showProcessingUI(true);

        try {
            var progressText = document.getElementById('ocr-progress-text');
            if (progressText) progressText.textContent = 'Mengoptimalkan gambar...';
            var optimizedImage = await this.preResizeForMobile(imageFile, 2500);
            await this.initWorker();
            
            var strategies = ['light', 'highContrast', 'sharpen', 'denoise'];
            var bestResult = null, bestScore = -1;
            
            this.log('=== TRYING STRATEGIES ===');
            
            for (var s = 0; s < strategies.length; s++) {
                var strategy = strategies[s];
                if (progressText) progressText.textContent = 'Mencoba metode ' + (s + 1) + '/' + strategies.length + ' (' + strategy + ')...';
                
                try {
                    if (s > 0) await this.sleep(100);
                    var processedImage = await this.createPreprocessedImage(optimizedImage, strategy);
                    var result = await this.worker.recognize(processedImage);
                    var text = result.data.text;
                    
                    var extractedData = this.parseKTPText(text);
                    var score = this.countExtractedFields(extractedData);
                    
                    this.log('Strategy ' + strategy + ' score: ' + score);
                    
                    if (score > bestScore) {
                        bestScore = score;
                        bestResult = extractedData;
                    }
                    if (score >= 10) break;
                } catch (e) {
                    this.log('Strategy ' + strategy + ' failed: ' + e.message);
                }
            }
            
            this.clearFormFields();
            if (bestResult) this.fillFormFields(bestResult);
            
            this.isProcessing = false;
            this.showProcessingUI(false);
            
            var filledCount = 0;
            if (bestResult) {
                if (bestResult.nik) filledCount++;
                if (bestResult.nama) filledCount++;
                if (bestResult.tempat_lahir) filledCount++;
                if (bestResult.tanggal_lahir) filledCount++;
                if (bestResult.gender) filledCount++;
                if (bestResult.agama) filledCount++;
                if (bestResult.alamat_lengkap) filledCount++;
                if (bestResult.status_kawin) filledCount++;
            }
            
            if (filledCount >= 5) {
                Swal.fire({ title: 'Berhasil!', text: filledCount + ' data berhasil dibaca.', icon: 'success' });
            } else if (filledCount >= 2) {
                Swal.fire({ title: 'Sebagian Berhasil', text: filledCount + ' data terbaca. Lengkapi sisanya.', icon: 'warning' });
            } else {
                Swal.fire({ title: 'Gagal Membaca', text: 'Coba foto ulang dengan pencahayaan lebih baik.', icon: 'error' });
            }
            
            return bestResult;
        } catch (error) {
            this.log('OCR Error: ' + error.message);
            this.isProcessing = false;
            this.showProcessingUI(false);
            Swal.fire({ title: 'Gagal', text: 'Pastikan foto KTP jelas.', icon: 'error' });
            return null;
        }
    }

    // ============ ROBUST KTP TEXT PARSER ============
    parseKTPText(text) {
        var self = this;
        
        this.log('========== RAW OCR TEXT ==========');
        this.log(text);
        
        var lines = text.split('\n').map(function(line) { return line.trim(); }).filter(function(line) { return line.length > 0; });
        
        this.log('========== LINES (' + lines.length + ') ==========');
        for (var i = 0; i < lines.length; i++) {
            this.log('[' + i + '] "' + lines[i] + '"');
        }
        
        var fullText = text.toUpperCase();
        
        var result = {
            nik: null, nama: null, tempat_lahir: null, tanggal_lahir: null,
            gender: null, agama: null, alamat: null, rt_rw: null,
            kel_desa: null, kecamatan: null, status_kawin: null, alamat_lengkap: null
        };

        // ========== 1. EXTRACT NIK ==========
        var nikMatch = text.match(/\d{16}/);
        if (nikMatch) {
            result.nik = nikMatch[0];
            this.log('NIK found: ' + result.nik);
        }

        // ========== 2. EXTRACT NAMA (AGGRESSIVE) ==========
        // Keywords yang BUKAN nama
        var notNameKeywords = ['NAMA', 'NAME', 'NARNA', 'NAMU', 'NIK', 'PROVINSI', 'KABUPATEN', 'KOTA', 
            'TEMPAT', 'TTL', 'TGL', 'LAHIR', 'JENIS', 'KELAMIN', 'ALAMAT', 'AGAMA', 'STATUS', 
            'PEKERJAAN', 'KEWARGANEGARAAN', 'BERLAKU', 'HINGGA', 'GOLONGAN', 'DARAH', 'GOL',
            'RT', 'RW', 'KEL', 'DESA', 'KECAMATAN', 'PERKAWINAN', 'WIRASWASTA', 'PNS', 'SWASTA',
            'ISLAM', 'KRISTEN', 'KATOLIK', 'HINDU', 'BUDHA', 'WNI', 'SEUMUR', 'HIDUP', 'KAWIN',
            'LAKI', 'PEREMPUAN', 'MIJEN', 'JATISARI', 'PERUM', 'SEMARANG', 'DEMAK'];
        
        for (var i = 0; i < lines.length; i++) {
            var line = lines[i];
            var lineUpper = line.toUpperCase();
            
            // Cek apakah line mengandung kata NAMA (dengan typo tolerance)
            // N[aA][mMrR][aAeE] = Nama, Narna, Name, dll
            var isNamaLine = /N[AaUu][MmRr][AaEe]/i.test(line) && 
                            lineUpper.indexOf('KEWARGANEGARAAN') === -1;
            
            if (isNamaLine) {
                this.log('Potential NAMA line [' + i + ']: "' + line + '"');
                
                // Coba berbagai cara extract value
                var namaValue = null;
                
                // Method 1: Setelah : atau ;
                var m1 = line.match(/[:;]\s*(.+)/);
                if (m1) namaValue = m1[1].trim();
                
                // Method 2: Setelah kata Nama + spasi
                if (!namaValue) {
                    var m2 = line.match(/N[AaUu][MmRr][AaEe]\s+([A-Z][A-Z\s\.,']+)/i);
                    if (m2) namaValue = m2[1].trim();
                }
                
                // Method 3: Ambil semua KAPITAL setelah label
                if (!namaValue) {
                    var m3 = line.match(/[:\s]([A-Z][A-Z\s\.,']{3,})/);
                    if (m3) namaValue = m3[1].trim();
                }
                
                if (namaValue) {
                    this.log('Raw nama value: "' + namaValue + '"');
                    
                    // Potong di stop words
                    var stopWords = ['TEMPAT', 'TTL', 'TGL', 'JENIS', 'LAHIR', 'KELAMIN', 'GOL', 'ALAMAT', 'AGAMA'];
                    for (var j = 0; j < stopWords.length; j++) {
                        var idx = namaValue.toUpperCase().indexOf(stopWords[j]);
                        if (idx > 0) namaValue = namaValue.substring(0, idx);
                    }
                    
                    namaValue = namaValue.replace(/[\s:;,\.]+$/, '').trim();
                    
                    // Validasi
                    var namaUpper = namaValue.toUpperCase();
                    var isKeyword = false;
                    for (var k = 0; k < notNameKeywords.length; k++) {
                        if (namaUpper === notNameKeywords[k]) { isKeyword = true; break; }
                    }
                    
                    if (!isKeyword && namaValue.length >= 3 && /^[A-Za-z]/.test(namaValue)) {
                        result.nama = this.cleanName(namaValue);
                        this.log('NAMA extracted: "' + result.nama + '"');
                        break;
                    }
                }
            }
        }
        
        // Fallback NAMA: Cari baris dengan format "NAMA_ORANG" (huruf kapital 2-3 kata)
        if (!result.nama) {
            this.log('Trying NAMA fallback...');
            for (var i = 0; i < lines.length; i++) {
                var line = lines[i];
                
                // Cari pattern: beberapa kata kapital yang terlihat seperti nama orang
                // Contoh: "EDWIN OCKY PRAYOGO" atau ": EDWIN OCKY PRAYOGO"
                var namePattern = /[:;\s]([A-Z][A-Z]+(?:\s+[A-Z][A-Z]+){1,4})\s*$/;
                var match = line.match(namePattern);
                
                if (match) {
                    var potentialName = match[1].trim();
                    var nameUpper = potentialName.toUpperCase();
                    
                    // Cek bukan keyword
                    var isKeyword = false;
                    for (var k = 0; k < notNameKeywords.length; k++) {
                        if (nameUpper.indexOf(notNameKeywords[k]) !== -1) { 
                            isKeyword = true; 
                            break; 
                        }
                    }
                    
                    // Harus 2-4 kata, masing-masing 2+ huruf
                    var words = potentialName.split(/\s+/);
                    var validWordCount = words.length >= 2 && words.length <= 4;
                    var allWordsValid = words.every(function(w) { return w.length >= 2; });
                    
                    if (!isKeyword && validWordCount && allWordsValid && potentialName.length >= 5) {
                        this.log('NAMA fallback found: "' + potentialName + '"');
                        result.nama = this.cleanName(potentialName);
                        break;
                    }
                }
            }
        }

        // ========== 3. EXTRACT TEMPAT & TANGGAL LAHIR ==========
        for (var i = 0; i < lines.length; i++) {
            var line = lines[i];
            var lineUpper = line.toUpperCase();
            
            // Cek baris TTL dengan berbagai variasi
            var isTTLLine = lineUpper.indexOf('TEMPAT') !== -1 || 
                           lineUpper.indexOf('TTL') !== -1 ||
                           lineUpper.indexOf('TGL') !== -1 ||
                           lineUpper.indexOf('LAHIR') !== -1 ||
                           /T[EeAa][MmNn]P[AaOo]T/i.test(line);  // Typo tolerance
            
            if (isTTLLine) {
                this.log('TTL line [' + i + ']: "' + line + '"');
                
                // Cari tanggal dulu (lebih reliable)
                var dateMatch = line.match(/(\d{1,2})[-\/\.](\d{1,2})[-\/\.](\d{4})/);
                if (dateMatch && !result.tanggal_lahir) {
                    var day = dateMatch[1].padStart(2, '0');
                    var month = dateMatch[2].padStart(2, '0');
                    var year = dateMatch[3];
                    result.tanggal_lahir = year + '-' + month + '-' + day;
                    this.log('TANGGAL LAHIR: ' + result.tanggal_lahir);
                }
                
                // Cari tempat lahir (kota sebelum tanggal)
                if (!result.tempat_lahir) {
                    // Method 1: Setelah : dan sebelum tanggal/koma
                    var m1 = line.match(/[:;]\s*([A-Za-z]+)[,\s]+\d/);
                    if (m1) {
                        var city = m1[1].trim().toUpperCase();
                        var invalidCities = ['TEMPAT', 'TGL', 'TTL', 'LAHIR', 'TANGGAL', 'JENIS'];
                        if (invalidCities.indexOf(city) === -1 && city.length >= 3) {
                            result.tempat_lahir = this.cleanName(m1[1]);
                            this.log('TEMPAT LAHIR (m1): ' + result.tempat_lahir);
                        }
                    }
                    
                    // Method 2: Cari kata sebelum tanggal
                    if (!result.tempat_lahir) {
                        var m2 = line.match(/([A-Za-z]{3,})[,\s]+\d{1,2}[-\/\.]\d/);
                        if (m2) {
                            var city = m2[1].trim().toUpperCase();
                            var invalidCities = ['TEMPAT', 'TGL', 'TTL', 'LAHIR', 'TANGGAL', 'JENIS'];
                            if (invalidCities.indexOf(city) === -1) {
                                result.tempat_lahir = this.cleanName(m2[1]);
                                this.log('TEMPAT LAHIR (m2): ' + result.tempat_lahir);
                            }
                        }
                    }
                    
                    // Method 3: Cari value setelah : yang bukan tanggal
                    if (!result.tempat_lahir) {
                        var m3 = line.match(/[:;]\s*([A-Za-z]{3,})/);
                        if (m3) {
                            var city = m3[1].trim().toUpperCase();
                            var invalidCities = ['TEMPAT', 'TGL', 'TTL', 'LAHIR', 'TANGGAL', 'JENIS', 'LAKI', 'PEREMPUAN'];
                            if (invalidCities.indexOf(city) === -1) {
                                result.tempat_lahir = this.cleanName(m3[1]);
                                this.log('TEMPAT LAHIR (m3): ' + result.tempat_lahir);
                            }
                        }
                    }
                }
            }
        }
        
        // Fallback tanggal lahir
        if (!result.tanggal_lahir) {
            var dateMatch = text.match(/(\d{1,2})[-\/\.](\d{1,2})[-\/\.](\d{4})/);
            if (dateMatch) {
                var day = dateMatch[1].padStart(2, '0');
                var month = dateMatch[2].padStart(2, '0');
                result.tanggal_lahir = dateMatch[3] + '-' + month + '-' + day;
            }
        }

        // ========== 4. OTHER FIELDS ==========
        for (var i = 0; i < lines.length; i++) {
            var line = lines[i];
            var lineUpper = line.toUpperCase();
            
            // JENIS KELAMIN
            if ((lineUpper.indexOf('JENIS') !== -1 || lineUpper.indexOf('KELAMIN') !== -1) && !result.gender) {
                if (lineUpper.indexOf('LAKI') !== -1) result.gender = 'L';
                else if (lineUpper.indexOf('PEREMPUAN') !== -1 || lineUpper.indexOf('WANITA') !== -1) result.gender = 'P';
            }
            
            // AGAMA
            if (lineUpper.indexOf('AGAMA') !== -1 && !result.agama) {
                if (lineUpper.indexOf('ISLAM') !== -1) result.agama = 1;
                else if (lineUpper.indexOf('KRISTEN') !== -1) result.agama = 2;
                else if (lineUpper.indexOf('KATOLIK') !== -1) result.agama = 3;
                else if (lineUpper.indexOf('HINDU') !== -1) result.agama = 4;
                else if (lineUpper.indexOf('BUDHA') !== -1) result.agama = 5;
                else if (lineUpper.indexOf('KONGHUCU') !== -1) result.agama = 6;
            }
            
            // ALAMAT
            if (lineUpper.indexOf('ALAMAT') !== -1 && lineUpper.indexOf('RT') === -1 && !result.alamat) {
                var m = line.match(/[:;]\s*(.+)/);
                if (m && m[1].length >= 3) result.alamat = m[1].trim();
            }
            
            // RT/RW
            if (lineUpper.indexOf('RT') !== -1 && lineUpper.indexOf('RW') !== -1 && !result.rt_rw) {
                var m = line.match(/(\d{1,3})\s*[\/\\]\s*(\d{1,3})/);
                if (m) result.rt_rw = m[1].padStart(3, '0') + '/' + m[2].padStart(3, '0');
            }
            
            // KEL/DESA
            if ((lineUpper.indexOf('KEL') !== -1 || lineUpper.indexOf('DESA') !== -1) && !result.kel_desa) {
                var m = line.match(/[:;]\s*([A-Za-z]+)/);
                if (m) result.kel_desa = m[1].trim();
            }
            
            // KECAMATAN
            if ((lineUpper.indexOf('KECAMATAN') !== -1 || lineUpper.indexOf('KEC') !== -1) && !result.kecamatan) {
                var m = line.match(/[:;]\s*([A-Za-z]+)/);
                if (m) result.kecamatan = m[1].trim();
            }
            
            // STATUS PERKAWINAN
            if ((lineUpper.indexOf('STATUS') !== -1 || lineUpper.indexOf('PERKAWINAN') !== -1 || lineUpper.indexOf('KAWIN') !== -1) && !result.status_kawin) {
                if (lineUpper.indexOf('WARGA') === -1) {
                    if (lineUpper.indexOf('BELUM') !== -1) result.status_kawin = 'B';
                    else if (lineUpper.indexOf('CERAI HIDUP') !== -1 || lineUpper.indexOf('DUDA') !== -1) result.status_kawin = 'D';
                    else if (lineUpper.indexOf('CERAI MATI') !== -1 || lineUpper.indexOf('JANDA') !== -1) result.status_kawin = 'J';
                    else if (lineUpper.indexOf('KAWIN') !== -1) result.status_kawin = 'K';
                }
            }
        }

        // ========== FALLBACKS ==========
        if (!result.gender) {
            if (fullText.indexOf('LAKI-LAKI') !== -1 || fullText.indexOf('LAKI LAKI') !== -1) result.gender = 'L';
            else if (fullText.indexOf('PEREMPUAN') !== -1) result.gender = 'P';
        }
        
        if (!result.agama) {
            if (fullText.indexOf('ISLAM') !== -1) result.agama = 1;
            else if (fullText.indexOf('KRISTEN') !== -1) result.agama = 2;
            else if (fullText.indexOf('KATOLIK') !== -1) result.agama = 3;
            else if (fullText.indexOf('HINDU') !== -1) result.agama = 4;
            else if (fullText.indexOf('BUDHA') !== -1) result.agama = 5;
        }
        
        if (!result.status_kawin) {
            if (fullText.indexOf('BELUM KAWIN') !== -1) result.status_kawin = 'B';
            else if (fullText.indexOf('KAWIN') !== -1 && fullText.indexOf('BELUM') === -1) result.status_kawin = 'K';
        }
        
        // Gabungkan alamat
        var alamatParts = [];
        if (result.alamat) alamatParts.push(result.alamat);
        if (result.rt_rw) alamatParts.push('RT/RW ' + result.rt_rw);
        if (result.kel_desa) alamatParts.push('Kel. ' + result.kel_desa);
        if (result.kecamatan) alamatParts.push('Kec. ' + result.kecamatan);
        result.alamat_lengkap = alamatParts.length > 0 ? alamatParts.join(', ') : null;
        
        this.log('========== FINAL RESULT ==========', result);
        
        return result;
    }

    cleanName(name) {
        if (!name) return null;
        name = name.replace(/[^A-Za-z\s\.,']/g, '').replace(/\s+/g, ' ').trim();
        return name.split(' ').map(function(w) {
            return w.length > 0 ? w.charAt(0).toUpperCase() + w.slice(1).toLowerCase() : '';
        }).join(' ');
    }

    fillFormFields(data) {
        var mappings = {
            'nik': data.nik,
            'nama_lengkap': data.nama,
            'tempat_lahir': data.tempat_lahir,
            'tanggal_lahir': data.tanggal_lahir,
            'gender_id': data.gender,
            'agama_id': data.agama,
            'alamat': data.alamat_lengkap,
            'status_perkawinan_id': data.status_kawin
        };
        for (var id in mappings) {
            if (mappings[id]) {
                var el = document.getElementById(id);
                if (el) el.value = mappings[id];
            }
        }
    }

    // ============ CAMERA ============
    openCamera() {
        var self = this;
        var modal = document.createElement('div');
        modal.id = 'ktp-camera-modal';
        modal.innerHTML = '<div class="ktp-camera-overlay"><div class="ktp-camera-header"><span>Posisikan KTP dalam kotak</span><button type="button" class="ktp-camera-close" id="btn-close-camera">&times;</button></div><div class="ktp-camera-container"><video id="ktp-camera-video" autoplay playsinline></video><div class="ktp-camera-guide"><div class="ktp-guide-box"><div class="ktp-guide-corner tl"></div><div class="ktp-guide-corner tr"></div><div class="ktp-guide-corner bl"></div><div class="ktp-guide-corner br"></div><div class="ktp-guide-text">KTP</div></div></div><canvas id="ktp-camera-canvas" style="display:none;"></canvas></div><div class="ktp-camera-tips"><small>💡 Pastikan pencahayaan cukup & KTP tidak blur</small></div><div class="ktp-camera-actions"><button type="button" class="btn btn-light" id="btn-switch-camera"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 3h5v5M8 21H3v-5M21 3l-7 7M3 21l7-7"/></svg></button><button type="button" class="btn btn-primary btn-lg rounded-circle" id="btn-capture-ktp"><svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/></svg></button><div style="width:50px;"></div></div></div>';
        document.body.appendChild(modal);
        
        if (!document.getElementById('ktp-camera-styles')) {
            var styles = document.createElement('style');
            styles.id = 'ktp-camera-styles';
            styles.textContent = '#ktp-camera-modal{position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999}.ktp-camera-overlay{width:100%;height:100%;background:#000;display:flex;flex-direction:column}.ktp-camera-header{padding:15px;color:#fff;display:flex;justify-content:space-between;align-items:center;background:rgba(0,0,0,.7)}.ktp-camera-close{background:none;border:none;color:#fff;font-size:28px;cursor:pointer}.ktp-camera-container{flex:1;position:relative;overflow:hidden;display:flex;align-items:center;justify-content:center}#ktp-camera-video{width:100%;height:100%;object-fit:cover}.ktp-camera-guide{position:absolute;top:0;left:0;width:100%;height:100%;display:flex;align-items:center;justify-content:center;pointer-events:none}.ktp-guide-box{width:90%;max-width:420px;aspect-ratio:1.586;border:3px solid #0f0;border-radius:12px;position:relative;box-shadow:0 0 0 9999px rgba(0,0,0,.6)}.ktp-guide-corner{position:absolute;width:30px;height:30px;border:4px solid #0f0}.ktp-guide-corner.tl{top:-3px;left:-3px;border-right:none;border-bottom:none;border-radius:10px 0 0 0}.ktp-guide-corner.tr{top:-3px;right:-3px;border-left:none;border-bottom:none;border-radius:0 10px 0 0}.ktp-guide-corner.bl{bottom:-3px;left:-3px;border-right:none;border-top:none;border-radius:0 0 0 10px}.ktp-guide-corner.br{bottom:-3px;right:-3px;border-left:none;border-top:none;border-radius:0 0 10px 0}.ktp-guide-text{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);color:rgba(255,255,255,.4);font-size:28px;font-weight:700}.ktp-camera-tips{padding:10px;text-align:center;color:#ffc107;background:rgba(0,0,0,.7)}.ktp-camera-actions{padding:20px;display:flex;justify-content:space-around;align-items:center;background:rgba(0,0,0,.7)}.ktp-camera-actions .btn{width:50px;height:50px;display:flex;align-items:center;justify-content:center}.ktp-camera-actions #btn-capture-ktp{width:70px;height:70px}';
            document.head.appendChild(styles);
        }
        
        var video = document.getElementById('ktp-camera-video');
        var canvas = document.getElementById('ktp-camera-canvas');
        var facingMode = 'environment';
        
        function startCamera() {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: facingMode, width: { ideal: 1920 }, height: { ideal: 1080 } } })
                .then(function(stream) { self.stream = stream; video.srcObject = stream; })
                .catch(function(e) { Swal.fire({ text: 'Tidak dapat mengakses kamera.', icon: 'error' }); self.closeCamera(); });
        }
        startCamera();
        
        document.getElementById('btn-switch-camera').onclick = function() {
            facingMode = facingMode === 'environment' ? 'user' : 'environment';
            if (self.stream) self.stream.getTracks().forEach(function(t) { t.stop(); });
            startCamera();
        };
        
        document.getElementById('btn-capture-ktp').onclick = function() {
            var guideBox = document.querySelector('.ktp-guide-box');
            var videoRect = video.getBoundingClientRect();
            var guideRect = guideBox.getBoundingClientRect();
            var scaleX = video.videoWidth / videoRect.width;
            var scaleY = video.videoHeight / videoRect.height;
            var cropX = (guideRect.left - videoRect.left) * scaleX;
            var cropY = (guideRect.top - videoRect.top) * scaleY;
            var cropW = guideRect.width * scaleX;
            var cropH = guideRect.height * scaleY;
            canvas.width = cropW; canvas.height = cropH;
            canvas.getContext('2d').drawImage(video, cropX, cropY, cropW, cropH, 0, 0, cropW, cropH);
            canvas.toBlob(function(blob) { self.closeCamera(); self.processFromCamera(blob); }, 'image/jpeg', 0.95);
        };
        
        document.getElementById('btn-close-camera').onclick = function() { self.closeCamera(); };
    }
    
    closeCamera() {
        if (this.stream) { this.stream.getTracks().forEach(function(t) { t.stop(); }); this.stream = null; }
        var modal = document.getElementById('ktp-camera-modal');
        if (modal) modal.remove();
    }
    
    async processFromCamera(blob) {
        var preview = document.getElementById('ktp-preview');
        var wrapper = document.getElementById('ktp-preview-wrapper');
        if (preview && wrapper) { preview.src = URL.createObjectURL(blob); wrapper.style.display = 'block'; }
        await this.processKTPImage(blob);
    }

    showProcessingUI(show) {
        var pw = document.getElementById('ocr-progress-wrapper');
        var ua = document.getElementById('ktp-upload-area');
        if (pw) pw.style.display = show ? 'block' : 'none';
        if (ua) { ua.style.opacity = show ? '0.5' : '1'; ua.style.pointerEvents = show ? 'none' : 'auto'; }
    }

    previewImage(file) {
        var preview = document.getElementById('ktp-preview');
        var wrapper = document.getElementById('ktp-preview-wrapper');
        if (preview && wrapper) {
            var reader = new FileReader();
            reader.onload = function(e) { preview.src = e.target.result; wrapper.style.display = 'block'; };
            reader.readAsDataURL(file);
        }
    }

    resetUpload() {
        var input = document.getElementById('ktp-upload');
        var preview = document.getElementById('ktp-preview');
        var wrapper = document.getElementById('ktp-preview-wrapper');
        if (input) input.value = '';
        if (preview && wrapper) { preview.src = ''; wrapper.style.display = 'none'; }
        this.clearFormFields();
    }

    async terminate() { if (this.worker) { await this.worker.terminate(); this.worker = null; } }
}

// Initialize
var ktpOCR = new KTPOCRHandler();

document.addEventListener('DOMContentLoaded', function() {
    var ktpInput = document.getElementById('ktp-upload');
    var btnCamera = document.getElementById('btn-open-camera');
    var btnReset = document.getElementById('btn-reset-ktp');
    
    if (ktpInput) {
        ktpInput.onclick = function() { this.value = ''; };
        ktpInput.onchange = async function(e) {
            var file = e.target.files[0];
            if (!file) return;
            if (!file.type.startsWith('image/')) { Swal.fire({ text: 'Pilih file gambar', icon: 'warning' }); return; }
            if (file.size > 15 * 1024 * 1024) { Swal.fire({ text: 'Max 15MB', icon: 'warning' }); return; }
            ktpOCR.previewImage(file);
            await ktpOCR.processKTPImage(file);
        };
    }
    if (btnCamera) btnCamera.onclick = function() { ktpOCR.openCamera(); };
    if (btnReset) btnReset.onclick = function() { ktpOCR.resetUpload(); Swal.fire({ text: 'Form direset.', icon: 'info' }); };
});

window.onbeforeunload = function() { ktpOCR.closeCamera(); ktpOCR.terminate(); };