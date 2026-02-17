# event-mangment-system-android-app
Event Management System built to streamline event planning, registration, and coordination. Features include user authentication, event creation, booking management, real-time updates, and admin dashboards. Designed for colleges and organizations using modern web and mobile technologies.
# 🚀 Event Management System

<div align="center">

![GitHub stars](https://img.shields.io/github/stars/chandrama12-ac/event-mangment-system-android-app?style=for-the-badge)](https://github.com/chandrama12-ac/event-mangment-system-android-app/stargazers)
[![GitHub forks](https://img.shields.io/github/forks/chandrama12-ac/event-mangment-system-android-app?style=for-the-badge)](https://github.com/chandrama12-ac/event-mangment-system-android-app/network)
[![GitHub issues](https://img.shields.io/github/issues/chandrama12-ac/event-mangment-system-android-app?style=for-the-badge)](https://github.com/chandrama12-ac/event-mangment-system-android-app/issues)
[![GitHub license](https://img.shields.io/github/license/chandrama12-ac/event-mangment-system-android-app?style=for-the-badge)](LICENSE)

**A comprehensive Event Management System built with Flutter for cross-platform mobile experiences and a PHP backend for robust API services.**

<!-- TODO: Add live demo link if available -->
<!-- TODO: Add documentation link if available -->

</div>

## 📖 Overview

This repository hosts a robust Event Management System designed to streamline event planning, registration, and coordination for colleges and organizations. It features a modern, cross-platform mobile application built with Flutter, complemented by a powerful PHP-based backend API. The system includes functionalities such as secure user authentication, intuitive event creation, efficient booking management, and real-time updates, all managed through dedicated user and administrator dashboards.

## ✨ Features

-   🎯 **Event Creation & Management**: Users can create, view, edit, and delete events.
-   🔐 **User Authentication**: Secure login, registration, and profile management for both regular users and administrators, powered by Firebase.
-   🎫 **Event Booking & Registration**: Seamless process for users to book and register for events.
-   🔔 **Real-time Updates**: Instant notifications and updates for event changes or new events.
-   📱 **Cross-Platform Mobile App**: A single codebase for Android (and potentially iOS, Web, Desktop) event management.
-   📊 **Admin Dashboards**: Dedicated interfaces for administrators to oversee events, users, and bookings.
-   🖼️ **Media Uploads**: Support for uploading event images and other relevant media.
-   ⚙️ **Robust Backend API**: A PHP-driven API handling all business logic and data persistence.

## 🖥️ Screenshots

<!-- TODO: Add actual screenshots of the mobile application -->
<!-- Example: -->
<!-- ![Screenshot of Event Listing](screenshots/event_list.png) -->
<!-- ![Screenshot of Event Detail](screenshots/event_detail.png) -->
<!-- ![Screenshot of Admin Dashboard](screenshots/admin_dashboard.png) -->

## 🛠️ Tech Stack

**Mobile Frontend:**
[![Flutter](https://img.shields.io/badge/Flutter-02569B?style=for-the-badge&logo=flutter&logoColor=white)](https://flutter.dev/)
[![Dart](https://img.shields.io/badge/Dart-0175C2?style=for-the-badge&logo=dart&logoColor=white)](https://dart.dev/)

**Backend API:**
[![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![Firebase](https://img.shields.io/badge/Firebase-FFCA28?style=for-the-badge&logo=firebase&logoColor=black)](https://firebase.google.com/)

**Database:**
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)

## 🚀 Quick Start

This project consists of two main parts: a Flutter mobile application and a PHP backend API. Both need to be set up independently.

### Prerequisites

**For Mobile Application:**
-   **Flutter SDK**: [Install Flutter](https://flutter.dev/docs/get-started/install) (version 3.x or higher recommended)
-   **Android Studio / Xcode**: For mobile development environment.
-   **Firebase Account**: A Google account to set up a Firebase project.
-   **Node.js & npm (optional)**: For Firebase CLI if you plan to use Firebase functions or deploy.

**For Backend API:**
-   **PHP**: (version 7.4 or higher recommended)
-   **Web Server**: Apache or Nginx
-   **MySQL / MariaDB**: Database server
-   **Composer (optional)**: If the backend uses PHP dependencies managed by Composer. (Not explicitly detected but good practice for PHP projects)

### Installation

#### 1. Clone the repository

```bash
git clone https://github.com/chandrama12-ac/event-mangment-system-android-app.git
cd event-mangment-system-android-app
```

#### 2. Mobile Application (Flutter) Setup

1.  **Navigate to the project root**:
    ```bash
    cd event-mangment-system-android-app
    ```
2.  **Install Flutter dependencies**:
    ```bash
    flutter pub get
    ```
3.  **Set up Firebase**:
    Follow the detailed instructions in `FIREBASE_SETUP.md` to connect your Flutter app to a Firebase project. This typically involves:
    *   Creating a Firebase project.
    *   Registering Android and iOS apps in Firebase.
    *   Downloading `google-services.json` (for Android) and `GoogleService-Info.plist` (for iOS) and placing them in the correct directories (`android/app/` and `ios/Runner/` respectively).
    *   Adding Firebase SDKs to your `pubspec.yaml` and running `flutter pub get`.

4.  **Run the Flutter application**:
    ```bash
    flutter run
    ```
    This will launch the app on a connected device or emulator.

#### 3. Backend API (PHP) Setup

1.  **Navigate to the backend directory**:
    ```bash
    cd backend
    ```
2.  **Configure Database Connection**:
    *   Locate the database connection file (e.g., `db_connect.php`, `config.php`, or similar in the `backend/` directory).
    *   Update the database credentials (host, username, password, database name) to match your local MySQL setup.
    *   *(Based on `test_connection.php`, the connection likely uses `mysqli_connect`.)*

3.  **Database Setup**:
    *   Create a new MySQL database (e.g., `event_management`).
    *   Import the SQL schema provided in the `database/` directory into your newly created database.
    *   *(Assuming `database/` contains `.sql` files for schema and initial data).*

4.  **Deploy to Web Server**:
    *   Place the entire `backend/` directory into your web server's document root (e.g., `/var/www/html/` for Apache or `/usr/share/nginx/html/` for Nginx).
    *   Ensure your web server is configured to process PHP files.
    *   The API endpoints will then be accessible at `http://localhost/backend/[endpoint]`, assuming your web server is running locally.

## 📁 Project Structure

```
event-mangment-system-android-app/
├── .gitignore
├── LICENSE
├── README.md
├── Campus Event Management System report final (2).pdf # Project Documentation
├── Event Management App - PHP Flutter (1).pptx      # Project Presentation
├── FIREBASE_SETUP.md                                # Firebase Setup Guide
├── Video Project 1.mp4                              # Project Demo Video
├── backend/                                         # PHP Backend API
│   ├── [PHP API files]
│   └── uploads/                                     # Directory for uploaded media
├── database/                                        # Database schema and initial data
│   └── [SQL files]
├── flutter_run_verbose.txt                          # Flutter run logs
├── ios/                                             # Flutter iOS specific project files
├── lib/                                             # Flutter application source code (Dart files)
│   ├── main.dart
│   └── [other Flutter modules/pages/widgets]
├── linux/                                           # Flutter Linux specific project files
├── logs/                                            # Application logs
├── macos/                                           # Flutter macOS specific project files
├── pubspec.lock                                     # Flutter dependency lock file
├── pubspec.yaml                                     # Flutter project configuration and dependencies
├── test/                                            # Flutter unit and widget tests
├── test_access.txt                                  # Access test file (purpose unknown without content)
├── test_connection.php                              # PHP database connection test script
├── web/                                             # Flutter web specific project files
└── windows/                                         # Flutter Windows specific project files
```

## ⚙️ Configuration

### Environment Variables (Flutter)
Firebase credentials are primarily managed through `google-services.json` (Android) and `GoogleService-Info.plist` (iOS), as described in `FIREBASE_SETUP.md`. No explicit `.env` file was detected for Flutter-specific variables.

### Configuration Files (PHP Backend)
The primary configuration for the PHP backend will be in its database connection file (e.g., `db_connect.php` or similar inside `backend/`).

| Variable (Example from PHP connection) | Description                 | Required |
|----------------------------------------|-----------------------------|----------|
| `DB_SERVER`                            | Database host address       | Yes      |
| `DB_USERNAME`                          | Database user name          | Yes      |
| `DB_PASSWORD`                          | Database password           | Yes      |
| `DB_NAME`                              | Name of the database        | Yes      |

## 🔧 Development

### Available Scripts (Flutter)

| Command              | Description                                        |
|----------------------|----------------------------------------------------|
| `flutter run`        | Runs the application on an attached device.        |
| `flutter run --release` | Builds and runs a release version of the app.     |
| `flutter build apk`  | Builds an Android APK.                             |
| `flutter build ios`  | Builds an iOS app bundle (requires Xcode).         |
| `flutter pub get`    | Installs all project dependencies.                 |
| `flutter analyze`    | Analyzes the project for potential issues.         |
| `flutter format .`   | Formats all Dart files in the project.             |
| `flutter test`       | Runs unit and widget tests.                        |

### Development Workflow

1.  Start your web server (Apache/Nginx) and MySQL database for the backend.
2.  Ensure the PHP backend is properly deployed and configured.
3.  Open the Flutter project in your IDE (VS Code, Android Studio, IntelliJ).
4.  Run `flutter run` to launch the mobile application in debug mode.
5.  Make changes to Flutter code, which will be hot-reloaded.
6.  For backend changes, modify PHP files and refresh the client application or re-test API endpoints directly.

## 🧪 Testing

### Mobile Application (Flutter)
To run the Flutter tests, use the following command:

```bash
flutter test
```

This will execute all tests located in the `test/` directory.

### Backend API (PHP)
Without a specific testing framework detected, testing for the PHP backend would typically involve:
-   Manually hitting API endpoints using tools like Postman or Insomnia.
-   Browser testing with the `test_connection.php` file to verify database connectivity.
-   Writing custom PHP scripts to test individual API functions.

## 🚀 Deployment

### Production Build (Mobile Application)
To create a production-ready build of the Flutter application:

```bash
# For Android
flutter build apk --release

# For iOS (requires Xcode)
flutter build ipa --release
```
The generated APK/IPA files can then be uploaded to Google Play Store or Apple App Store respectively.

### Deployment Options (Backend API)
The PHP backend can be deployed to any standard web hosting environment that supports PHP and MySQL.
-   Upload the `backend/` directory to your web server (e.g., via FTP/SFTP).
-   Ensure the database is set up and accessible from the server.
-   Configure your web server (Apache/Nginx) for the appropriate document root and PHP processing.

## 📚 API Reference (PHP Backend)

The backend provides a RESTful API to manage events, users, and bookings. While specific endpoints are not fully enumerated from the directory structure, common patterns would include:

### Authentication
User authentication is primarily handled by Firebase for the mobile app, with the PHP backend likely providing endpoints for user data management once authenticated.

### Endpoints (Inferred)
-   `GET /backend/events`: Retrieve a list of all events.
-   `GET /backend/events/{id}`: Retrieve details for a specific event.
-   `POST /backend/events`: Create a new event.
-   `PUT /backend/events/{id}`: Update an existing event.
-   `DELETE /backend/events/{id}`: Delete an event.
-   `GET /backend/users/{id}`: Retrieve user profile information.
-   `POST /backend/bookings`: Create a new booking for an event.
-   `GET /backend/bookings/{user_id}`: Retrieve bookings for a specific user.
-   `POST /backend/uploads`: Upload event images or other files.
-   `GET /backend/admin/dashboard`: Admin-specific data.

*(Actual endpoints would be derived from PHP files within the `backend/` directory.)*

## 🤝 Contributing

We welcome contributions to enhance this Event Management System! Please feel free to fork the repository, make your changes, and submit a pull request.

## 📄 License

This project is licensed under the [Apache License 2.0](LICENSE) - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

-   Built with [Flutter](https://flutter.dev/) for an amazing cross-platform experience.
-   Powered by [PHP](https://www.php.net/) for robust backend services.
-   Utilizes [MySQL](https://www.mysql.com/) for efficient data storage.
-   Integrates [Firebase](https://firebase.google.com/) for scalable authentication and real-time features.

## 📞 Support & Contact

-   🐛 Issues: [GitHub Issues](https://github.com/chandrama12-ac/event-mangment-system-android-app/issues)

---

<div align="center">

**⭐ Star this repo if you find it helpful!**

Made with ❤️ by [chandrama12-ac](https://github.com/chandrama12-ac)

</div>
