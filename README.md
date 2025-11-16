# Help Plugin

WordPress plugin with admin dashboard page.

## Description

Help Plugin is a WordPress plugin that adds a page to the admin menu, allowing you to view system information and perform custom actions.

## Installation

### Method 1: Via Upload

1. Download the `help-plugin.zip` file
2. Access WordPress admin panel
3. Go to **Plugins > Add New**
4. Click **Upload Plugin**
5. Select the `help-plugin.zip` file
6. Click **Install Now**
7. After installation, click **Activate Plugin**

### Method 2: Via FTP

1. Extract the ZIP file contents
2. Upload the `help-plugin` folder to `wp-content/plugins/`
3. Access WordPress admin panel
4. Go to **Plugins**
5. Activate **Help Plugin**

## Usage

After activation, you will see a new item in the WordPress sidebar menu called **Help Plugin**. Clicking on it will give you access to:

- Main plugin page
- System information (WordPress version, PHP version, and plugin version)
- Interactive actions

## Development

### Plugin Structure

```
help-plugin/
├── help-plugin.php      # Main plugin file
├── assets/
│   ├── css/
│   │   └── admin-style.css
│   └── js/
│       └── admin-script.js
├── tests/               # Unit tests
│   ├── bootstrap.php
│   └── test-help-plugin.php
├── build.sh             # Build script to generate ZIP
├── composer.json        # PHP dependencies
├── package.json         # npm configuration
├── phpunit.xml          # PHPUnit configuration
└── README.md
```

### Build

To generate the plugin ZIP file:

```bash
./build.sh
```

or

```bash
npm run build
```

### Testing

Install dependencies:

```bash
composer install
```

Run tests:

```bash
composer test
```

or

```bash
./vendor/bin/phpunit
```

Run tests with coverage:

```bash
composer test:coverage
```

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher

## Version

1.0.0

## License

GPL v2 or later

## Author

Your Name
