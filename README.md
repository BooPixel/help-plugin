# BooChat Connect

AI Chatbot with n8n Automation - Connect your WordPress site to n8n workflows for intelligent customer service chat automation.

## Description

**BooChat Connect – AI Chatbot & n8n Automation** brings intelligent customer interaction directly to your WordPress website.

Add a modern, lightweight chatbot popup that integrates seamlessly with n8n, allowing you to automate workflows, respond to visitors in real time, collect leads, and connect your chat interface to any external service or AI model.

Perfect for businesses that want to automate customer support, boost conversions, and create smarter interactions using visual, no-code automation.

## Key Features

### Free Version
- ✅ **Fully customizable chat popup** (colors, icons, texts)
- ✅ **Direct n8n Webhook integration**
- ✅ **AI-ready**: Connect to ChatGPT, Claude, Gemini, Llama, and more (via n8n)
- ✅ **Lightweight, fast, and compatible** with all WordPress themes
- ✅ **Custom Chat Icon**: Upload your own icon for the chat header
- ✅ **Multilingual interface**: English, Portuguese, and Spanish
- ✅ **Settings Management**: Configure API URL and chat settings
- ✅ **Responsive Design**: Mobile-friendly chat interface
- ✅ **Session Management**: Automatic session tracking
- ✅ **Real-time Messaging**: Send and receive messages instantly

### PRO Version (Additional Features)
- 💎 **Advanced Statistics Dashboard**: 
  - Track interactions (1 day, 7 days, 30 days)
  - Interactive charts with date range selection
  - Custom date range filtering
- 💎 **Session Management**: 
  - View all user chat sessions
  - Complete conversation history
  - Session details and analytics
- 💎 **Export Options**: 
  - Export session messages as JSON
  - Export session messages as CSV
- 💎 **License Management**: 
  - License activation via key or Stripe checkout
  - License status tracking

### Technical Features
- **Clean Architecture**: Separated HTML templates and CSS files for better maintainability
- **WordPress Standards**: Full compliance with WordPress coding standards and security best practices
- **Comprehensive Unit Tests**: 60+ test cases covering core functionality
- **Complete Translations**: All strings translated in English, Portuguese, and Spanish (376 strings each)

## What You Can Automate

- **24/7 AI-powered customer support bots**
- **Sales assistants** that send quotes automatically
- **Integration with external APIs**, databases, and web services
- **Personalized replies** using AI via n8n workflows

## Why Choose BooChat Connect

- ✔ **Seamless n8n integration**
- ✔ **Modern UI and smooth experience**
- ✔ **Perfect for AI-based chat flows**
- ✔ **Zero coding needed**
- ✔ **Ultra-flexible** — works with any tool connected to n8n
- ✔ **Ideal for agencies, freelancers, support teams, and e-commerce**

## Installation

### Method 1: Via WordPress Admin (Recommended)

1. Download the `boochat-connect.zip` file
2. Access your WordPress admin panel
3. Navigate to **Plugins > Add New**
4. Click **Upload Plugin**
5. Choose the `boochat-connect.zip` file
6. Click **Install Now**
7. After installation completes, click **Activate Plugin**

### Method 2: Via FTP/SFTP

1. Extract the `boochat-connect.zip` file contents
2. Connect to your server via FTP/SFTP client
3. Upload the `boochat-connect` folder to `wp-content/plugins/` directory
4. Access your WordPress admin panel
5. Navigate to **Plugins**
6. Find **BooChat Connect** in the list and click **Activate**

### Method 3: Via Command Line (WP-CLI)

```bash
wp plugin install boochat-connect.zip --activate
```

## Configuration

### Setting Up n8n Webhook

1. Navigate to **BooChat Connect > Settings** in WordPress admin
2. Enter your n8n webhook URL (e.g., `https://your-n8n-instance.com/webhook/your-webhook-id`)
3. Select the plugin language (or leave as "Auto" to use WordPress language)
4. Click **Save Settings**

### n8n Webhook Format

BooChat Connect sends messages to your n8n webhook in the following JSON format:

**Request:**
```json
{
  "sessionId": "unique-session-id",
  "action": "sendMessage",
  "chatInput": "user message text"
}
```

**Expected Response:**
```json
{
  "output": "API response message"
}
```

### n8n Workflow Example

Your n8n workflow should:
1. Receive the webhook request
2. Extract `chatInput` from the request body
3. Process the message (connect to AI, database, API, etc.)
4. Return a JSON response with `output` field containing the reply

**Example n8n nodes:**
- Webhook (receive request)
- Function/Code (process message)
- HTTP Request (call AI API like OpenAI, Anthropic, etc.)
- Respond to Webhook (return response)

### Customizing the Chat

1. Navigate to **BooChat Connect > Main Panel** in WordPress admin
2. Customize the following options:
   - **Chat Name**: Display name in chat header (e.g., "Support", "Help", "Chat with us")
   - **Welcome Message**: Initial message shown when chat opens
   - **Primary Color**: Primary gradient color for header and buttons
   - **Secondary Color**: Secondary gradient color for gradient effect
   - **Chat Background Color**: Background color of the chat window
   - **Text Color**: Text color for messages
   - **Font Family**: Choose from Arial, Helvetica, Georgia, Times New Roman, Courier New, Verdana, Trebuchet MS, Open Sans, or Roboto
   - **Font Size**: Select from Small (12px), Normal (14px), Medium (16px), Large (18px), or Extra Large (20px)
3. Click **Save Customizations**

**Note**: User messages and bot responses use fixed colors (dark gray for user, light gray for bot) to ensure readability regardless of customization settings.

## Usage

### Frontend Chat Flow

1. **Plugin Activation**: Once activated, the chat popup automatically appears on your website (bottom-right corner, after 1 second delay)

2. **User Interaction**:
   - User clicks the chat popup to open the chat window
   - Welcome message is displayed
   - User types a message and clicks "Send"

3. **Message Processing**:
   - JavaScript generates/retrieves session ID (stored in localStorage)
   - AJAX request sent to WordPress with message and session ID
   - WordPress forwards message to your n8n webhook URL
   - n8n workflow processes the message (AI, database, API calls, etc.)
   - n8n returns response
   - WordPress receives response and displays it in the chat
   - If PRO: Message is saved to database for statistics

4. **Response Display**:
   - Bot response appears in chat window
   - User can continue conversation
   - Session persists throughout the conversation

### Statistics (PRO Only)

View interaction statistics:
1. Navigate to **BooChat Connect > Statistics** in WordPress admin
2. View quick summary boxes (1 day, 7 days, 30 days)
3. Select a date range using the calendar picker
4. View interactive chart showing interactions over time
5. Analyze chat performance and user engagement

### Session Management (PRO Only)

View and manage user sessions:
1. Navigate to **BooChat Connect > Sessions** in WordPress admin
2. View all chat sessions with:
   - Session ID
   - First and last interaction timestamps
   - Message count per session
   - User vs Bot message counts
3. Click "View" to see complete conversation history
4. Export sessions as JSON or CSV for analysis

## Development

### Plugin Structure

```
boochat-connect/
├── boochat-connect.php          # Main plugin file
├── LICENSE                      # GPLv2 license
├── README.txt                   # WordPress.org format documentation
├── banner-772x250.png           # Plugin banner (772x250px)
├── icon-256x256.png             # Plugin icon (256x256px)
├── includes/                    # Plugin core classes
│   ├── views/                   # HTML templates (separated from PHP logic)
│   │   ├── admin-main.php       # Main admin dashboard template
│   │   ├── admin-customization.php # Customization page template
│   │   ├── admin-settings.php   # Settings page template
│   │   ├── admin-statistics.php # Statistics dashboard template
│   │   └── frontend-chat.php    # Frontend chat widget template
│   ├── class-boochat-connect-admin.php
│   ├── class-boochat-connect-ajax.php
│   ├── class-boochat-connect-api.php
│   ├── class-boochat-connect-database.php
│   ├── class-boochat-connect-frontend.php
│   ├── class-boochat-connect-settings.php
│   ├── class-boochat-connect-statistics.php
│   └── helpers.php
├── assets/
│   ├── css/
│   │   ├── admin-style.css      # Base admin styles (always loaded)
│   │   ├── admin-main.css       # Main admin page styles
│   │   ├── admin-statistics.css # Statistics page styles
│   │   └── chat-style.css       # Frontend chat styles
│   ├── js/
│   │   ├── admin-script.js      # Admin page scripts
│   │   ├── chat-script.js       # Frontend chat functionality
│   │   └── statistics-script.js # Statistics page scripts
│   └── screenshots/             # Plugin screenshots (1280x720px)
│       ├── 1-chat-widget.png
│       ├── 2-customization-panel.png
│       ├── 3-settings-page.png
│       └── 4-statistics-dashboard.png
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

This will create `boochat-connect.zip` in the root directory, ready for distribution.

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
- Asset enqueueing (CSS and JS)
- Page rendering with template separation
- Template file existence
- CSS file existence
- API integration
- Statistics tracking
- Customization settings
- Database interactions
- Helper methods (load_view, verify_request, send_error)
- Frontend chat widget rendering

## Database

The plugin creates a custom table `wp_boochat_connect_interactions` to store:
- Session IDs
- Message content
- Interaction timestamps

This table is automatically created on first use.

## Requirements

- **WordPress**: 5.0 or higher
- **PHP**: 7.2 or higher (7.4+ recommended)
- **MySQL**: 5.6 or higher (or MariaDB 10.0 or higher)
- **jQuery**: Included with WordPress
- **n8n**: An n8n instance with a webhook endpoint (cloud or self-hosted)

## Compatibility

- ✅ All WordPress themes
- ✅ WordPress Multisite
- ✅ Gutenberg and Classic Editor
- ✅ Popular caching plugins (W3 Total Cache, WP Super Cache, WP Rocket)
- ✅ Mobile responsive
- ✅ All modern browsers

## Support

For support, feature requests, or bug reports:

- **Website**: [https://boopixel.com](https://boopixel.com)
- **Support**: [https://boopixel.com/support](https://boopixel.com/support)
- **Plugin Page**: [https://boopixel.com/boochat-connect](https://boopixel.com/boochat-connect)

## Changelog

### 1.0.0 (Initial Release)
- Initial plugin release
- **n8n Webhook Integration**: Direct integration with n8n workflows
- **AI-Ready**: Connect to ChatGPT, Claude, Gemini, Llama via n8n
- **Chat Widget**: Modern, lightweight popup with customizable appearance
- **Customization Panel**: Full control over colors, fonts, messages, and branding
- **Statistics Dashboard**: Track interactions with detailed analytics and charts
- **Multi-language Support**: Automatic WordPress language detection (English, Portuguese, Spanish)
- **Session Management**: Automatic session tracking for conversation continuity
- **Message Logging**: Complete conversation history stored in WordPress database
- **Settings Management**: Easy configuration of n8n webhook URL and language
- **Responsive Design**: Mobile-friendly interface
- **Unit Test Suite**: Comprehensive test coverage
- **Build Automation**: Automated ZIP generation for distribution

## License

**GNU General Public License v2 or later**

Copyright (c) 2024 BooPixel

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

See the [LICENSE](LICENSE) file for complete license terms.

## Author

**BooPixel**

- Website: [https://boopixel.com](https://boopixel.com)
- Support: [https://boopixel.com/support](https://boopixel.com/support)

---
