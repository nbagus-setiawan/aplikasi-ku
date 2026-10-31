#!/bin/bash
set -e

echo "=========================================="
echo "  Build APK & AAB SAP Al-Falah Putra     "
echo "=========================================="

# 1. Pastikan aset mobile tersinkronisasi
echo "[1/3] Menyinkronkan aset mobile ke Android..."
npx cap sync android

# 2. Build APK dan AAB via Gradle
echo "[2/3] Mem-build APK dan Android App Bundle (AAB)..."
cd android
./gradlew assembleDebug bundleDebug
cd ..

# 3. Salin hasil build ke folder output
mkdir -p dist-apk
cp android/app/build/outputs/apk/debug/app-debug.apk dist-apk/SAP-Al-Falah-Putra.apk
cp android/app/build/outputs/bundle/debug/app-debug.aab dist-apk/SAP-Al-Falah-Putra.aab

echo "=========================================="
echo "  Build Berhasil!                         "
echo "  - File APK: dist-apk/SAP-Al-Falah-Putra.apk"
echo "  - File AAB: dist-apk/SAP-Al-Falah-Putra.aab"
echo "=========================================="
