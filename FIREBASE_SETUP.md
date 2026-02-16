# Firebase Google Sign-In Setup Guide

## 1. Create a Firebase Project
1. Go to [Firebase Console](https://console.firebase.google.com/).
2. Click **Add project** and follow the steps.

## 2. Add Android App
1. In the Firebase console, click the **Android** icon.
2. Enter your package name (check `android/app/build.gradle`, usually `com.example.event_mangment`).
3. **Register App**.
4. **Download `google-services.json`** and place it in `android/app/`.

## 3. Add SHA-1 Key (Required for Google Sign-In)
1. Open your terminal in the project folder.
2. Run:
   ```sh
   cd android
   ./gradlew signingReport
   ```
   (On Windows: `gradlew signingReport`)
3. Copy the **SHA-1** fingerprint from the output.
4. Go to Firebase Console > Project Settings > General > Your Apps > Android.
5. Click **Add fingerprint** and paste the SHA-1 key.

## 4. Enable Google Sign-In
1. Go to **Authentication** > **Sign-in method**.
2. Click **Add new provider** > **Google**.
3. Enable it and save.

## 5. Web Configuration
1. In Firebase Console, go to Project Settings.
2. Under "General", scroll to "Your apps".
3. Select "Web" (</>) icon to add a web app.
4. Copy the `const firebaseConfig` object if needed, but for Google Sign-In on Web, you specifically need the **OAuth 2.0 Client ID**.
5. Go to [Google Cloud Console](https://console.cloud.google.com/apis/credentials).
6. Select your project.
7. Create credentials > OAuth client ID > Web application.
8. Add `http://localhost` and your local IP (e.g., `http://localhost:port`) to **Authorized JavaScript origins**.
9. Use this **Client ID** in your `index.html` meta tag (see below).

## 6. Update `web/index.html`
Add this meta tag inside `<head>`:
```html
<meta name="google-signin-client_id" content="YOUR_WEB_CLIENT_ID.apps.googleusercontent.com">
```

## 7. Update `android/build.gradle`
Ensure you have the Google services classpath:
```gradle
dependencies {
    classpath 'com.google.gms:google-services:4.4.0'
}
```

## 8. Update `android/app/build.gradle`
Add the plugin at the bottom:
```gradle
apply plugin: 'com.google.gms.google-services'
```
