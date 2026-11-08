# Script: upload-to-svn.sh

Automated script to upload the complete plugin to WordPress.org SVN.

## Description

This script automates the entire plugin upload process, including:

- SVN repository checkout
- Copying all necessary files
- Organizing assets (banner, icon, screenshots)
- Creating version tags
- Preparing for commit

## How to Use

### Basic Execution

```bash
./scripts/upload-to-svn.sh
```

### Requirements

- SVN installed (`brew install subversion` on macOS)
- SVN credentials configured on WordPress.org
- Execute from the plugin project root

## Configuration

The script is configured with:

```bash
SVN_REPO="https://plugins.svn.wordpress.org/boopixel-ai-chat-for-n8n"
SVN_USERNAME="boopixel"
SVN_DIR="boopixel-svn"
VERSION="1.0.0"
```

## What the Script Does

### 1. Initial Checks

- Verifies if in correct directory
- Verifies if SVN is installed
- Verifies if SVN directory already exists

### 2. Repository Checkout/Update

- If directory doesn't exist, performs checkout
- If exists, updates with `svn update`

### 3. File Copying

**To `trunk/`:**
- Main plugin file
- README.txt
- LICENSE
- `includes/` folder
- `languages/` folder
- CSS and JS assets

**To `assets/`:**
- Banner (banner-772x250.png)
- Icon (icon-256x256.png)
- Screenshots

### 4. Tag Creation

- Creates version 1.0.0 tag by copying trunk

### 5. Final Status

- Shows repository status
- Lists all added files

## Credentials

The script automatically uses the username `boopixel`. When needed, you will be prompted to enter your SVN password.

## After Running the Script

After running the script, you need to commit:

```bash
cd boopixel-svn
svn status  # Review changes
svn commit --username boopixel -m "Initial release version 1.0.0"
```

## Important

- The script **does not commit automatically** (for security)
- Always review `svn status` before committing
- Make sure there are no unnecessary files

## Troubleshooting

### Error: "SVN is not installed"

```bash
brew install subversion
```

### Error: "Execute this script from the project root"

Make sure you're in the directory that contains `boopixel-ai-chat-for-n8n.php`

### Authentication Error

1. Verify you configured SVN password on WordPress.org
2. Use `--username boopixel` in commands
3. Verify username is correct (case sensitive)

## See Also

- [Configure Credentials](../guia-svn/configurar-credenciais.md)
- [Quick Commands](../guia-svn/comandos-rapidos.md)
- [View Files](../guia-svn/visualizar-arquivos.md)
