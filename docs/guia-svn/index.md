# Guide: Next Steps - Upload Plugin to WordPress.org via SVN

## Current Status

Your plugin has been **approved** by WordPress.org.

**Your repository information:**
- **SVN URL:** `https://plugins.svn.wordpress.org/boopixel-ai-chat-for-n8n`
- **Public URL:** `https://wordpress.org/plugins/boopixel-ai-chat-for-n8n`
- **SVN Username:** `boopixel` (case sensitive)
- **Access will be activated in:** 1 hour after approval

## Pre-Upload Checklist

### Verified Files

- `README.txt` - Correct WordPress format
- `banner-772x250.png` - Plugin banner
- `icon-256x256.png` - Plugin icon
- Screenshots in `assets/screenshots/`
- Main file: `boopixel-ai-chat-for-n8n.php`

## Steps for SVN Upload

### 1. Configure SVN Credentials

**IMPORTANT:**
- Your **SVN Username** is: `boopixel` (case sensitive - use exactly like this)
- **DO NOT use your email** as username
- Configure your SVN password at: https://profiles.wordpress.org/me/profile/edit/group/3/?screen=svn-password
- Generate your SVN password following: https://make.wordpress.org/meta/handbook/tutorials-guides/svn-access/

### 2. Prepare Code for Upload

#### Files that MUST be sent:
- All PHP files
- Complete `includes/` folder
- Complete `assets/` folder (CSS, JS, screenshots)
- Complete `languages/` folder
- `README.txt`
- `LICENSE`
- `banner-772x250.png`
- `icon-256x256.png`

#### Files that should NOT be sent:
- `boopixel-ai-chat-for-n8n.zip` (build file)
- `build.sh` and `build-1.0.0.sh` (build scripts)
- `package.json` (development dependencies)
- `composer.json` (if not needed in production)
- `phpunit.xml` (test files)
- `tests/` folder (if not needed)
- `.gitignore` and other version control files
- `README.md` (only README.txt is needed)

### 3. SVN Repository Structure

WordPress.org SVN works like this:

```
svn/
├── trunk/              # Development code
├── tags/               # Stable versions (1.0.0, 1.0.1, etc.)
│   └── 1.0.0/         # First version
└── assets/             # Banners, icons, screenshots
    ├── banner-772x250.png
    ├── icon-256x256.png
    └── screenshots/
        ├── 1-chat.png
        ├── 2-main-panel.png
        ├── 3-customization-panel.png
        └── 4-settings-page.png
```

### 4. Basic SVN Commands

#### First time (Checkout):
```bash
# Checkout repository (wait 1 hour after approval)
svn checkout https://plugins.svn.wordpress.org/boopixel-ai-chat-for-n8n/ boopixel-svn
cd boopixel-svn

# You will be prompted to enter:
# Username: boopixel
# Password: [your SVN password configured in profile]
```

#### Add files to trunk:
```bash
# Copy files from your project to trunk/
cp -r /path/to/your/project/* trunk/

# Add files to SVN
svn add trunk/*
svn add assets/*

# Commit
svn commit -m "Initial release version 1.0.0"
```

#### Create version 1.0.0 tag:
```bash
# Copy trunk to tag
svn copy trunk tags/1.0.0

# Commit tag
svn commit -m "Tag version 1.0.0"
```

### 5. Organize Assets (Banners, Icons, Screenshots)

Assets must be in the `assets/` folder at the SVN root:

```
assets/
├── banner-772x250.png
├── icon-256x256.png
└── screenshots/
    ├── 1-chat.png
    ├── 2-main-panel.png
    ├── 3-customization-panel.png
    └── 4-settings-page.png
```

**Important:** Screenshots must be in `assets/screenshots/`, not in `trunk/assets/screenshots/`.

### 6. Verify README.txt

Your `README.txt` is already correct, but make sure:
- The version in `Stable tag:` matches the version you're sending
- Referenced screenshots exist in `assets/screenshots/`

## Important Resources

### Official Documentation:
- **How to use SVN:** https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/
- **Plugin Guidelines:** https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/
- **README.txt:** https://developer.wordpress.org/plugins/wordpress-org/how-your-readme-txt-works/
- **Plugin Assets:** https://developer.wordpress.org/plugins/wordpress-org/plugin-assets/

### Notifications:
- **Make WordPress Plugins:** https://make.wordpress.org/plugins
- Add `plugins@wordpress.org` to your contacts

## Verification Tools

Before uploading, verify your code with:

1. **PHPCS + WPCS** (WordPress Coding Standards)
2. **Plugin Check** (WordPress plugin)

This helps ensure your plugin is secure and compatible.

## Important Reminders

1. **SVN is a release system**, not continuous development
2. Only commit **ready-to-use** versions
3. Each version must be created as a **tag** (1.0.0, 1.0.1, etc.)
4. Development code goes in `trunk/`
5. **Never** delete old tags (they are historical)

## Immediate Next Actions

1. **Wait 1 hour** for SVN access activation
2. **Configure SVN password** in your WordPress.org profile: https://profiles.wordpress.org/me/profile/edit/group/3/?screen=svn-password
3. **Read** all links from the approval email
4. **Prepare code** by running: `./scripts/prepare-svn.sh`
5. **Checkout** SVN repository (after 1 hour)
6. **Upload** code to `trunk/`
7. **Create tag** for version 1.0.0
8. **Organize assets** in the `assets/` folder

## Need Help?

- Read all links provided in the email first
- WordPress.org team cannot upload code for you
- They cannot give detailed instructions on specific SVN clients
- You need to master the SVN system

## Final Notes

- A successful approval **does not guarantee** the plugin is free from security issues
- Continue checking your code regularly
- If an issue arises, the plugin will be closed and you'll be notified to fix it
- This is standard procedure for all plugins

---

**Good luck with your plugin launch!**
