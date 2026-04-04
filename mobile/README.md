# Installs Bank — Mobile App

Flutter Android app for Installs Bank publishers.

## Setup

### 1. Firebase (Required)
1. Go to https://console.firebase.google.com
2. Create project **InstallsBank**
3. Add Android app — package name: `com.installsbank.app`
4. Download `google-services.json`
5. Place it at `mobile/android/app/google-services.json`
6. Enable **Cloud Messaging** in Firebase console

### 2. Install Flutter
Download from https://flutter.dev/docs/get-started/install
Run `flutter doctor` to verify.

### 3. Build APK

```bash
cd mobile
flutter pub get
flutter build apk --release
```

The APK will be at:
`mobile/build/app/outputs/flutter-apk/app-release.apk`

### Screens
- Login
- Dashboard (live stats, chart, country breakdown)
- Stats (daily, per-link, country)
- Payments (balance, address, withdrawal request, history)
- Support (tickets, chat)
- Profile (edit info, password, avatar)

### API Base
`https://installsbank.com/api`
