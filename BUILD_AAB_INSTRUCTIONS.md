# Panduan Membangun Android App Bundle (.aab) - Bengkel GMJ

Dokumen ini berisi petunjuk lengkap untuk mengompilasi folder proyek `android/` di dalam repositori ini menjadi **Android App Bundle (.aab)** yang siap diunggah ke Google Play Console.

---

## 📋 Prasyarat

1. **Android Studio** (Versi Hedgehog / Iguana / Jellyfish atau yang terbaru) atau **JDK 17+ & Android SDK**.
2. **Aplikasi Laravel Sudah Di-host / Memiliki URL Server** (HTTPS sangat disarankan untuk rilis produksi di Play Store).

---

## 🚀 Langkah-Langkah Pembuatan (.aab)

### Langkah 1: Atur URL Server Aplikasi

Buka file [strings.xml](file:///c:/Games/Bengkel-GMJ/android/app/src/main/res/values/strings.xml) yang berada di lokasi:
`android/app/src/main/res/values/strings.xml`

Ubah nilai `<string name="web_url">` sesuai lingkungan yang Anda gunakan:

- **Untuk Produksi (Play Store):**
  ```xml
  <string name="web_url">https://bengkel-gmj.com</string>
  ```
- **Untuk Pengujian Emulator Android Studio:**
  ```xml
  <string name="web_url">http://10.0.2.2:8000</string>
  ```
- **Untuk Pengujian di HP Fisik lewat Wi-Fi Lokal:**
  ```xml
  <string name="web_url">http://192.168.1.50:8000</string>
  ```

---

### Langkah 2: Mengompilasi .aab Menggunakan Android Studio (Metode Paling Mudah)

1. Buka aplikasi **Android Studio**.
2. Pilih **Open** (atau **File > Open**) lalu pilih direktori folder `android` di dalam proyek ini (`Bengkel-GMJ/android`).
3. Tunggu hingga proses **Gradle Sync** selesai (Android Studio akan mengunduh dependensi Gradle yang diperlukan).
4. Klik menu bagian atas: **Build > Generate Signed Bundle / APK...**
5. Pilih **Android App Bundle** lalu klik **Next**.
6. **Key store path**:
   - Jika sudah punya Keystore, pilih file `.jks` Anda.
   - Jika belum, klik **Create new...** untuk membuat file kunci penandatanganan baru (simpan file `.jks` dan kata sandi dengan aman!).
7. Isi nama Alias dan Password Keystore, lalu klik **Next**.
8. Pilih **release** sebagai Build Variant, lalu klik **Create**.
9. File `.aab` siap diunggah dan berada di lokasi:
   `android/app/release/app-release.aab`

---

### Langkah 3: Mengompilasi .aab Melalui Terminal Command Line

Jika Anda menggunakan Command Line / Terminal yang sudah terpasang JDK & Android SDK:

1. Buka Terminal di folder `android`:
   ```bash
   cd android
   ```
2. Jalankan perintah kompilasi release bundle:
   - **Windows:**
     ```cmd
     gradlew.bat bundleRelease
     ```
   - **Linux / macOS:**
     ```bash
     ./gradlew bundleRelease
     ```
3. File `.aab` hasil build dapat ditemukan pada direktori:
   `android/app/build/outputs/bundle/release/app-release.aab`

---

### 💡 Tips: Membuat File APK Debug untuk Pengujian di HP Fisik

Sebelum mengunggah ke Google Play Store, Anda bisa mencoba aplikasi di HP Android dengan membuat file APK Debug:

1. Di dalam folder `android`, jalankan perintah:
   ```cmd
   gradlew.bat assembleDebug
   ```
2. File APK debug akan tersimpan di:
   `android/app/build/outputs/apk/debug/app-debug.apk`
3. Kirim file `app-debug.apk` tersebut ke HP Anda dan install untuk mencoba fitur Absensi (Kamera), Kasir, Struk Stempel/Cetak, dan Laporan.

---

## 🛠 Features & Capabilities Terintegrasi

- **Native WebView dengan JavaScript & DOM Storage Enabled**: Menjamin tampilan Blade Laravel berjalan persis seperti di browser.
- **Support Kamera & Galeri (WebChromeClient)**: Pengguna dapat mengambil foto langsung dari HP untuk fitur Absensi.
- **Pull to Refresh**: Geser layar ke bawah untuk menyegarkan halaman.
- **Handling Navigasi Tombol Back**: Menekan tombol kembali di HP akan mengarahkan pengguna ke halaman web sebelumnya (tidak langsung keluar aplikasi).
- **Download & Struk Handler**: Mendukung unduh/cetak file PDF dari fitur Kasir & Laporan.
- **Offline Error Handling**: Tampilan ramah pengguna saat koneksi internet terputus.
