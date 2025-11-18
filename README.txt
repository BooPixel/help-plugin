=== BooChat Connect ===
Contributors: boopixel
Tags: chatbot, ai, n8n, automation, customer service
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.2
Stable tag: 1.0.288
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Connect your WordPress site to n8n workflows for intelligent AI-powered customer service chat automation.

== Description ==

BooChat Connect – AI Chatbot & n8n Automation brings intelligent customer interaction directly to your WordPress website.

Add a modern, lightweight chatbot popup that integrates seamlessly with n8n, allowing you to automate workflows, respond to visitors in real time, collect leads, and connect your chat interface to any external service or AI model.

Perfect for businesses that want to automate customer support, boost conversions, and create smarter interactions using visual, no-code automation.

= Key Features =

== Free Version ==

* **Fully customizable chat popup** (colors, icons, texts)
* **Direct n8n Webhook integration**
* **AI-ready**: connect to ChatGPT, Claude, Gemini, Llama, and more (via n8n)
* **Lightweight, fast, and compatible** with all WordPress themes
* **Custom Chat Icon**: Upload your own icon for the chat header
* **Multilingual interface**: English, Portuguese, and Spanish
* **Settings Management**: Configure API URL and chat settings
* **Responsive Design**: Mobile-friendly chat interface
* **Session Management**: Automatic session tracking
* **Real-time Messaging**: Send and receive messages instantly

== PRO Version (Additional Features) ==

* **Advanced Statistics Dashboard**: Track interactions (1 day, 7 days, 30 days) with interactive charts and date range selection
* **Session Management**: View all user chat sessions with complete conversation history
* **Export Options**: Export session messages as JSON or CSV
* **License Management**: License activation via key or Stripe checkout

= What You Can Automate =

* 24/7 AI-powered customer support bots
* Sales assistants that send quotes automatically
* Integration with external APIs, databases, and web services
* Personalized replies using AI via n8n workflows

= Why Choose BooChat Connect =

* ✔ Seamless n8n integration
* ✔ Modern UI and smooth experience
* ✔ Perfect for AI-based chat flows
* ✔ Zero coding needed
* ✔ Ultra-flexible — works with any tool connected to n8n
* ✔ Ideal for agencies, freelancers, support teams, and e-commerce

= How It Works =

== Frontend Chat Flow ==

1. **Plugin Activation**: Once activated, the chat popup automatically appears on your website (bottom-right corner, after 1 second delay)

2. **User Interaction**:
   * User clicks the chat popup to open the chat window
   * Welcome message is displayed
   * User types a message and clicks "Send"

3. **Message Processing**:
   * JavaScript generates/retrieves session ID (stored in localStorage)
   * AJAX request sent to WordPress with message and session ID
   * WordPress forwards message to your n8n webhook URL
   * n8n workflow processes the message (AI, database, API calls, etc.)
   * n8n returns response
   * WordPress receives response and displays it in the chat
   * If PRO: Message is saved to database for statistics

4. **Response Display**:
   * Bot response appears in chat window
   * User can continue conversation
   * Session persists throughout the conversation

== Setup Process ==

1. Install and activate BooChat Connect
2. Configure your n8n webhook URL in the plugin settings
3. Customize the chat appearance to match your brand
4. The chat widget automatically appears on your frontend
5. User messages are sent to your n8n workflow
6. Responses from your workflow are displayed in the chat

= n8n Integration =

BooChat Connect sends messages to your n8n webhook in the following format:

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

= Customization Options =

* Chat name and welcome message
* Primary and secondary gradient colors
* Chat background color
* Text color
* Font family and size
* Language selection (auto-detects from WordPress)

= Statistics & Analytics (PRO Only) =

Track your chat performance with:
* Quick summary boxes (24 hours, 7 days, 30 days)
* Interactive charts with date range selection
* Custom date range filtering
* Message content logging
* Session-based interaction tracking
* Export capabilities (JSON/CSV)

= Session Management (PRO Only) =

View and manage user sessions:
* View all chat sessions with session ID, timestamps, and message counts
* Complete conversation history per session
* Export sessions as JSON or CSV for analysis
* Session details and analytics

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Navigate to Plugins > Add New
3. Search for "BooChat Connect"
4. Click "Install Now"
5. Click "Activate"

== Frequently Asked Questions ==

= Do I need n8n to use this plugin? =

Yes, BooChat Connect requires an n8n webhook URL to function. You'll need to set up an n8n workflow that handles the chat messages and returns responses.

= Can I customize the chat appearance? =

Yes! The plugin includes extensive customization options for colors, fonts, chat name, and welcome message. All settings are available in the plugin's main panel.

= Does it work on mobile devices? =

Yes, the chat widget is fully responsive and works on all devices including mobile phones and tablets.

= What languages are supported? =

The plugin automatically detects your WordPress language and supports English, Portuguese, and Spanish. You can also manually select a language in the settings.

= How are chat sessions tracked? =

Each user gets a unique session ID that persists throughout their conversation. This allows for conversation continuity and accurate statistics tracking.

= Can I see chat statistics? =

Yes! The PRO version includes a comprehensive statistics dashboard with quick summaries and interactive charts showing chat interactions over time. The free version includes basic chat functionality without statistics tracking.

== Screenshots ==

1. Chat widget on frontend
2. Main panel dashboard with information and quick links
3. Customization panel for chat appearance settings
4. Settings page with n8n webhook configuration
5. Statistics dashboard with charts

== Upgrade Notice ==

= 1.0.45 =
This version includes code refactoring, improved admin panel design, and enhanced performance. Update recommended for all users.

= 1.0.0 =
Initial release of BooChat Connect. Install and configure your n8n webhook to get started.

== Support ==

For support, please visit: https://boopixel.com/support

== Credits ==

Developed by BooPixel
Website: https://boopixel.com

