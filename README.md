# Help Plugin

A WordPress plugin that adds a custom admin dashboard page to your WordPress site.

## Description

Help Plugin is a WordPress plugin that extends the WordPress admin panel by adding a dedicated page in the sidebar menu. It provides an interface to view system information, perform custom actions, and manage plugin-specific settings.

## Features

- **Admin Dashboard Page**: Custom page in WordPress admin sidebar menu
- **System Information**: Display WordPress version, PHP version, and plugin version
- **Interactive Actions**: Built-in interactive buttons and functionality
- **Clean UI**: Modern and clean admin interface matching WordPress design standards
- **Fully Customizable**: Easy to extend with additional features
- **Unit Tests**: Complete test coverage with PHPUnit

## Installation

### Method 1: Via WordPress Admin (Recommended)

1. Download the `help-plugin.zip` file
2. Access your WordPress admin panel
3. Navigate to **Plugins > Add New**
4. Click **Upload Plugin**
5. Choose the `help-plugin.zip` file
6. Click **Install Now**
7. After installation completes, click **Activate Plugin**

### Method 2: Via FTP/SFTP

1. Extract the `help-plugin.zip` file contents
2. Connect to your server via FTP/SFTP client
3. Upload the `help-plugin` folder to `wp-content/plugins/` directory
4. Access your WordPress admin panel
5. Navigate to **Plugins**
6. Find **Help Plugin** in the list and click **Activate**

### Method 3: Via Command Line (WP-CLI)

```bash
wp plugin install help-plugin.zip --activate
```

## Usage

Once activated, you will see a new menu item **Help Plugin** in your WordPress admin sidebar menu (usually near the bottom).

### Accessing the Plugin Page

1. Log in to your WordPress admin panel
2. Click on **Help Plugin** in the sidebar menu
3. You will be redirected to the plugin's main page

### Available Features

The plugin page includes:

- **Welcome Section**: Overview and introduction to the plugin
- **System Information Table**: 
  - WordPress version
  - PHP version
  - Plugin version
- **Action Buttons**: Interactive elements for custom functionality

### Customization

You can extend the plugin by modifying:
- `help-plugin.php` - Main plugin logic
- `assets/css/admin-style.css` - Admin page styling
- `assets/js/admin-script.js` - Admin page JavaScript functionality

## Development

### Plugin Structure

```
help-plugin/
├── help-plugin.php          # Main plugin file
├── LICENSE                  # Proprietary license
├── assets/
│   ├── css/
│   │   └── admin-style.css  # Admin page styles
│   └── js/
│       └── admin-script.js  # Admin page scripts
├── tests/                   # Unit tests
│   ├── bootstrap.php        # Test bootstrap file
│   └── test-help-plugin.php # Plugin tests
├── build.sh                 # Build script to generate ZIP
├── composer.json            # PHP dependencies
├── package.json             # npm configuration
├── phpunit.xml              # PHPUnit configuration
└── README.md                # Documentation
```

### Building the Plugin

To generate the distribution ZIP file:

```bash
./build.sh
```

or using npm:

```bash
npm run build
```

This will create `help-plugin.zip` in the root directory, ready for distribution.

### Setting Up Development Environment

1. Clone or download the plugin repository
2. Install PHP dependencies:

```bash
composer install
```

### Running Tests

Install test dependencies:

```bash
composer install
```

Run all tests:

```bash
composer test
```

or directly:

```bash
./vendor/bin/phpunit
```

Run tests with code coverage:

```bash
composer test:coverage
```

Coverage report will be generated in the `coverage/` directory.

### Test Coverage

The plugin includes comprehensive unit tests covering:
- Singleton pattern implementation
- Menu registration
- Asset enqueueing
- Page rendering
- Constants definition

## Requirements

- **WordPress**: 5.0 or higher
- **PHP**: 7.4 or higher
- **MySQL**: 5.6 or higher (or MariaDB 10.0 or higher)

## Version

Current version: **1.0.0**

## Support

For support, feature requests, or bug reports, please contact the plugin author.

## Changelog

### 1.0.0 (Initial Release)
- Initial plugin release
- Admin dashboard page implementation
- System information display
- Interactive actions
- Unit test suite
- Build automation

## License

**Proprietary - Commercial License**

Copyright (c) 2024. All Rights Reserved.

This plugin is proprietary software. All rights reserved.

**This software is licensed, not sold.** You may not use, copy, modify, distribute, or create derivative works of this software without purchasing a valid commercial license.

### License Terms

- ✅ **Licensed use**: Authorized use on purchased domains/sites
- ❌ **Prohibited**: Redistribution, resale, or modification without permission
- ❌ **Prohibited**: Use without a valid license

For licensing information, pricing, and purchase:
- Contact the author directly
- Visit the license page: [https://example.com/license](https://example.com/license)

**Unauthorized use, reproduction, or distribution of this software is strictly prohibited and may result in legal action.**

See the [LICENSE](LICENSE) file for complete license terms.

## Author

**Your Name**

- Website: [https://example.com](https://example.com)
- Email: [your-email@example.com](mailto:your-email@example.com)

---

**Note**: This is commercial software. Make sure you have a valid license before using this plugin in production.
