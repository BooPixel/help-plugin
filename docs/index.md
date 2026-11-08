# BooPixel AI Chat for n8n - Documentation

Welcome to the complete documentation for managing and uploading the **BooPixel AI Chat Connect for n8n** plugin to WordPress.org.

## About the Plugin

**BooPixel AI Chat Connect for n8n** is a WordPress plugin that integrates an intelligent chatbot with n8n workflows, enabling customer service automation, real-time responses, and integration with any external service or AI model.

## Documentation

This documentation covers the entire process of:

- Uploading the plugin to WordPress.org via SVN
- Configuring credentials
- Using automated scripts
- Useful SVN commands
- Troubleshooting

## Quick Start

If you just received WordPress.org approval:

1. **[Configure SVN Credentials](guia-svn/configurar-credenciais.md)** - Set up your SVN password
2. **[Quick Commands](guia-svn/comandos-rapidos.md)** - Execute the upload quickly
3. **[Complete SVN Guide](guia-svn/index.md)** - Understand the entire process

## Repository Information

- **SVN URL:** `https://plugins.svn.wordpress.org/boopixel-ai-chat-for-n8n`
- **Public URL:** `https://wordpress.org/plugins/boopixel-ai-chat-for-n8n`
- **SVN Username:** `boopixel` (case sensitive)
- **Current Version:** 1.0.0

## Available Scripts

Scripts are located in the `scripts/` folder:

### `scripts/upload-to-svn.sh`
Automated script that:
- Performs SVN repository checkout
- Copies all necessary files
- Organizes assets correctly
- Creates version tags

**Usage:**
```bash
./scripts/upload-to-svn.sh
```

### `scripts/prepare-svn.sh`
Script to prepare files before upload, creating a clean folder.

**Usage:**
```bash
./scripts/prepare-svn.sh
```

## Navigation

Use the side menu to navigate the documentation:

- **SVN Guide** - Complete SVN tutorials
- **Scripts** - Automated scripts documentation
- **Resources** - Useful links and FAQ

## Current Status

Your plugin has been **approved** by WordPress.org.

Now you need to upload the code via SVN following the guides in this documentation.

## Important Links

- [WordPress Plugin Directory](https://wordpress.org/plugins/)
- [Plugin Guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/)
- [SVN Documentation](https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/)

## Project Structure

```
help-plugin/
├── scripts/                    # Automation scripts
│   ├── upload-to-svn.sh       # Upload to SVN
│   └── prepare-svn.sh         # Prepare files
├── docs/                       # MkDocs documentation
│   ├── index.md
│   ├── guia-svn/
│   ├── scripts/
│   └── recursos/
├── mkdocs.yml                  # MkDocs configuration
└── requirements.txt           # Python dependencies
```

## How to Use This Documentation

### View Locally

```bash
# Install dependencies
pip install -r requirements.txt

# Serve documentation
mkdocs serve
```

Access: `http://127.0.0.1:8000`

### Build for Production

```bash
mkdocs build
```

This will create the `site/` folder with static HTML files.

---

**Developed by BooPixel** | [Website](https://boopixel.com)
