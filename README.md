=== BooPixel AI Chat Connect for n8n ===
Contributors: boopixel
Tags: chatbot, ai, n8n, automation, customer service, wordpress, webhook, integration
Requires at least: 5.0
Tested up to: 6.9
Requires PHP: 7.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Connect your WordPress site to n8n workflows for intelligent AI-powered customer service chat automation.

== Description ==

BooPixel AI Chat Connect for n8n is a modern, lightweight WordPress plugin that brings intelligent customer interaction directly to your website through seamless n8n integration.

Add a customizable chatbot popup that integrates seamlessly with n8n workflows, allowing you to automate customer support, respond to visitors in real-time, collect leads, and connect your chat interface to any external service or AI model.

Perfect for businesses, agencies, and developers who want to automate customer support, boost conversions, and create smarter interactions using visual, no-code automation with n8n.

= Key Features =

* **Fully Customizable Chat Interface**: Customize colors, fonts, icons, welcome messages, and chat name to match your brand
* **Direct n8n Webhook Integration**: Seamlessly connect to your n8n workflows via webhook URLs
* **AI-Ready Architecture**: Connect to ChatGPT, Claude, Gemini, Llama, and any AI model through n8n workflows
* **Lightweight & Fast**: Optimized code with minimal dependencies, ensuring fast page load times
* **Theme Compatible**: Works with all WordPress themes without conflicts
* **Custom Chat Icon**: Upload your own icon for the chat header
* **Multilingual Support**: English, Portuguese, and Spanish with automatic language detection
* **Session Management**: Automatic session tracking with persistent conversation history
* **Real-time Messaging**: Instant message sending and receiving
* **Statistics Dashboard**: Track interactions, view session data, and analyze chat performance
* **Export Functionality**: Export conversation sessions in JSON or CSV format
* **Responsive Design**: Mobile-friendly chat interface that works on all devices
* **Developer Friendly**: Clean, well-documented code following WordPress coding standards

= What You Can Automate =

* 24/7 AI-powered customer support bots
* Sales assistants that send quotes and product recommendations automatically
* Lead generation and qualification workflows
* Integration with external APIs, databases, CRM systems, and web services
* Personalized replies using AI models via n8n workflows
* Multi-step conversation flows with conditional logic
* Data collection and form automation

= Why Choose BooPixel AI Chat Connect for n8n =

* ✔ Seamless n8n integration - Connect to any n8n workflow instantly
* ✔ Modern UI with smooth animations and responsive design
* ✔ Perfect for AI-based chat flows and automation
* ✔ Zero coding required - Visual workflow design with n8n
* ✔ Ultra-flexible - Works with any tool or service connected to n8n
* ✔ Open source - Free to use, modify, and distribute under GPLv2
* ✔ Active development - Regular updates and improvements
* ✔ Well documented - Comprehensive documentation and code comments
* ✔ Ideal for agencies, freelancers, support teams, and e-commerce businesses

= How It Works =

== Frontend Chat Flow ==

1. **Plugin Activation**: Once activated, the chat popup automatically appears on your website (bottom-right corner, after 1 second delay)

2. **User Interaction**:
   * User clicks the chat popup to open the chat window
   * Welcome message is displayed (customizable)
   * User types a message and clicks "Send"

3. **Message Processing**:
   * JavaScript generates/retrieves session ID (stored in localStorage)
   * AJAX request sent to WordPress with message and session ID
   * WordPress validates and sanitizes the message
   * WordPress forwards message to your n8n webhook URL
   * n8n workflow processes the message (AI, database, API calls, etc.)
   * n8n returns response in JSON format
   * WordPress receives response and displays it in the chat
   * Conversation is logged in the database for statistics

4. **Response Display**:
   * Bot response appears in chat window with proper formatting
   * User can continue conversation
   * Session persists throughout the conversation
   * All messages are stored for analytics and export

== Setup Process ==

1. Install and activate BooPixel AI Chat Connect for n8n
2. Configure your n8n webhook URL in the plugin settings (Settings page)
3. Customize the chat appearance to match your brand (Customization page)
4. The chat widget automatically appears on your frontend
5. User messages are sent to your n8n workflow
6. Responses from your workflow are displayed in the chat
7. Monitor interactions and sessions in the Statistics and Sessions pages

= n8n Integration =

BooPixel AI Chat Connect for n8n sends messages to your n8n webhook in the following format:

`json
{
  "sessionId": "unique-session-id",
  "action": "sendMessage",
  "chatInput": "user message text"
}
`

Your n8n workflow should return a response in this format:

`json
{
  "output": "response message text"
}
`

== Example n8n Workflow ==

1. Create a webhook node in n8n
2. Add your AI model node (OpenAI, Anthropic, etc.) or any processing node
3. Configure the webhook to receive POST requests
4. Map the `chatInput` field to your AI model input
5. Return the AI response in the `output` field
6. Copy the webhook URL to the plugin settings

= Customization Options =

* **Chat Identity**: Custom chat name and welcome message
* **Colors**: Primary and secondary gradient colors for header and buttons
* **Background**: Chat background color customization
* **Typography**: Text color, font family, and font size
* **Icon**: Upload custom chat icon (supports common image formats)
* **Language**: Manual language selection or auto-detection from WordPress locale

= Admin Features =

* **Main Panel**: Dashboard with quick links and plugin information
* **Customization**: Visual customization panel for chat appearance
* **Settings**: Configure n8n webhook URL and language preferences
* **Statistics**: Interactive charts and analytics for chat interactions
* **Sessions**: View, search, and export conversation sessions

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Navigate to Plugins > Add New
3. Search for "BooPixel AI Chat Connect for n8n"
4. Click "Install Now"
5. Click "Activate"

= Manual Installation =

1. Download the plugin ZIP file
2. Log in to your WordPress admin panel
3. Navigate to Plugins > Add New
4. Click "Upload Plugin"
5. Choose the ZIP file and click "Install Now"
6. Click "Activate"

= Requirements =

* WordPress 5.0 or higher
* PHP 7.2 or higher (PHP 7.4+ recommended)
* MySQL 5.6 or higher
* An active n8n instance with webhook workflows

== Frequently Asked Questions ==

= Do I need n8n to use this plugin? =

Yes, BooPixel AI Chat Connect for n8n requires an n8n webhook URL to function. You'll need to set up an n8n workflow that handles the chat messages and returns responses. n8n can be self-hosted or used via n8n.cloud.

= Can I customize the chat appearance? =

Yes! The plugin includes extensive customization options for colors, fonts, chat name, welcome message, and icon. All settings are available in the plugin's Customization page.

= Does it work on mobile devices? =

Yes, the chat widget is fully responsive and works on all devices including mobile phones and tablets. The interface adapts to different screen sizes automatically.

= What languages are supported? =

The plugin automatically detects your WordPress language and supports English, Portuguese, and Spanish. You can also manually select a language in the Settings page.

= How are chat sessions tracked? =

Each user gets a unique session ID that persists throughout their conversation. This allows for conversation continuity and ensures that each conversation maintains context. Sessions are stored in the database and can be viewed in the Sessions admin page.

= Can I export conversation data? =

Yes, you can export individual conversation sessions in JSON or CSV format from the Sessions admin page. This is useful for analysis, backup, or integration with other systems.

= Is this plugin free? =

Yes, BooPixel AI Chat Connect for n8n is free and open source, released under the GPLv2 license. You can use, modify, and distribute it freely.

= How do I contribute to this project? =

Contributions are welcome! You can contribute by:
* Reporting bugs and issues
* Suggesting new features
* Submitting pull requests
* Improving documentation
* Translating the plugin to other languages

Please visit the project repository for contribution guidelines.

= Where can I get support? =

For support, documentation, and updates, please visit:
* Support: https://boopixel.com/support
* Documentation: https://boopixel.com/docs
* GitHub Repository: [Add your repository URL]

== Screenshots ==

1. Chat widget on frontend - Modern, responsive chat interface
2. Main panel dashboard - Overview with quick links and information
3. Customization panel - Visual customization for chat appearance
4. Settings page - n8n webhook configuration and language settings
5. Statistics dashboard - Interactive charts and analytics
6. Sessions management - View and export conversation sessions

== Changelog ==

= 1.0.0 =
* Initial release
* Full n8n webhook integration
* Customizable chat interface
* Session management and tracking
* Statistics and analytics dashboard
* Multilingual support (EN, PT, ES)
* Export functionality (JSON/CSV)
* Responsive design
* WordPress coding standards compliance

== Development ==

= Contributing =

We welcome contributions! Please follow these guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

= Code Standards =

* Follow WordPress Coding Standards
* Use proper prefixes for all functions, classes, and constants
* Include PHPDoc comments for all functions and classes
* Write unit tests for new features
* Ensure all code is properly sanitized and escaped

= Testing =

The plugin includes PHPUnit tests. To run tests:

```bash
composer install
vendor/bin/phpunit
```

== Support ==

For support, please visit: https://boopixel.com/support
Email: support@boopixel.com

== Credits ==

Developed by BooPixel
Website: https://boopixel.com

Special thanks to:
* n8n team for the amazing automation platform
* WordPress community for the excellent platform
* All contributors and users of this plugin

== License ==

This plugin is licensed under the GPLv2 or later.

Copyright (c) 2024 BooPixel

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
