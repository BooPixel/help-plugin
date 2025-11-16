# Help Plugin

A WordPress plugin that provides a customer service chat widget with API integration, customization options, and statistics tracking.

## Description

Help Plugin is a WordPress plugin that adds a customer service chat widget to your website. It features a customizable chat interface that connects to an external API for conversation handling, includes comprehensive statistics tracking, and offers extensive customization options for colors, typography, and messaging.

## Features

- **Chat Widget**: Interactive chat popup and window on the frontend
- **API Integration**: Connects to external API for conversation handling
- **Customization Panel**: 
  - Customizable chat name and welcome message
  - Color customization (primary, secondary, background, text)
  - Typography options (font family and size)
- **Statistics Dashboard**: 
  - Track interactions (1 day, 7 days, 30 days)
  - Interactive charts with date range selection
  - Message content logging
- **Settings Management**: Configure API URL and chat settings
- **Responsive Design**: Mobile-friendly chat interface
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

## Configuration

### Setting Up the API

1. Navigate to **Help Plugin > Configurações** in WordPress admin
2. Enter your API webhook URL
3. Click **Salvar Configurações**

The API should accept POST requests with the following JSON format:
```json
{
  "sessionId": "unique-session-id",
  "action": "sendMessage",
  "chatInput": "user message text"
}
```

Expected response format:
```json
{
  "output": "API response message"
}
```

### Customizing the Chat

1. Navigate to **Help Plugin > Painel Principal** in WordPress admin
2. Customize the following options:
   - **Nome do Chat**: Display name in chat header
   - **Mensagem Inicial**: Welcome message shown when chat opens
   - **Cor Primária**: Primary gradient color (header, buttons)
   - **Cor Secundária**: Secondary gradient color
   - **Cor de Fundo do Chat**: Chat window background color
   - **Cor do Texto**: Text color for messages
   - **Fonte**: Font family selection
   - **Tamanho da Fonte**: Font size selection
3. Click **Salvar Customizações**

## Usage

### Frontend Chat

Once activated, a chat popup will appear on the frontend of your website (bottom-right corner). Users can:
- Click the popup to open the chat window
- Send messages that are processed by your configured API
- View responses from the API in real-time

### Statistics

View interaction statistics:
1. Navigate to **Help Plugin > Estatísticas** in WordPress admin
2. View quick summary (1 day, 7 days, 30 days)
3. Select a date range using the calendar picker
4. View interactive chart showing interactions over time

## Development

### Plugin Structure

```
help-plugin/
├── help-plugin.php              # Main plugin file
├── LICENSE                      # Proprietary license
├── assets/
│   ├── css/
│   │   ├── admin-style.css      # Admin page styles
│   │   └── chat-style.css       # Frontend chat styles
│   └── js/
│       ├── admin-script.js      # Admin page scripts
│       ├── chat-script.js       # Frontend chat functionality
│       └── statistics-script.js # Statistics page scripts
├── tests/                       # Unit tests
│   └── test-help-plugin.php     # Plugin tests
├── build.sh                     # Build script to generate ZIP
├── composer.json                # PHP dependencies
├── package.json                 # npm configuration
├── phpunit.xml                  # PHPUnit configuration
└── README.md                    # Documentation
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
- API integration
- Statistics tracking
- Customization settings
- Database interactions

## Database

The plugin creates a custom table `wp_help_plugin_interactions` to store:
- Session IDs
- Message content
- Interaction timestamps

This table is automatically created on first use.

## Requirements

- **WordPress**: 5.0 or higher
- **PHP**: 7.4 or higher
- **MySQL**: 5.6 or higher (or MariaDB 10.0 or higher)
- **jQuery**: Included with WordPress

## Version

Current version: **1.0.0**

## Support

For support, feature requests, or bug reports, please contact the plugin author.

## Changelog

### 1.0.0 (Initial Release)
- Initial plugin release
- Chat widget with popup and window
- API integration for conversation handling
- Customization panel (colors, typography, messages)
- Statistics dashboard with charts
- Interaction tracking and logging
- Settings management
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
