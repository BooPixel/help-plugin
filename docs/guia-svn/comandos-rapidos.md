# Quick Commands - Upload to WordPress.org

## Your Plugin Information

- **SVN URL:** `https://plugins.svn.wordpress.org/boopixel-ai-chat-for-n8n`
- **Public URL:** `https://wordpress.org/plugins/boopixel-ai-chat-for-n8n`
- **Username:** `boopixel` (case sensitive)
- **Version:** 1.0.0

## Wait 1 hour

SVN access will be activated 1 hour after approval. After that, you can proceed.

## Step 1: Configure SVN Password

1. Visit: https://profiles.wordpress.org/me/profile/edit/group/3/?screen=svn-password
2. Generate your SVN password following: https://make.wordpress.org/meta/handbook/tutorials-guides/svn-access/
3. Note your password (you'll need it)

## Step 2: Prepare and Upload

### Option A: Use the Automated Script (Recommended)

```bash
# Execute the upload script
./scripts/upload-to-svn.sh
```

The script will:
- Perform SVN repository checkout
- Copy all necessary files
- Organize assets correctly
- Create version tag 1.0.0

Then, review and commit:

```bash
cd boopixel-svn
svn status  # Review changes
svn commit -m "Initial release version 1.0.0"
```

### Option B: Manual Commands

```bash
# 1. Checkout
svn checkout https://plugins.svn.wordpress.org/boopixel-ai-chat-for-n8n/ boopixel-svn
cd boopixel-svn

# 2. Copy files to trunk
# (Manually copy your project files to trunk/)

# 3. Add files
svn add trunk/*
svn add assets/*

# 4. Create tag
svn copy trunk tags/1.0.0

# 5. Commit
svn commit -m "Initial release version 1.0.0"
```

## Structure that will be created

```
boopixel-svn/
├── trunk/                    # Your main code
│   ├── boopixel-ai-chat-for-n8n.php
│   ├── README.txt
│   ├── LICENSE
│   ├── includes/
│   ├── languages/
│   └── assets/
│       ├── css/
│       └── js/
├── tags/
│   └── 1.0.0/               # Version 1.0.0 (copy of trunk)
└── assets/                   # Public assets
    ├── banner-772x250.png
    ├── icon-256x256.png
    └── screenshots/
        ├── 1-chat.png
        ├── 2-main-panel.png
        ├── 3-customization-panel.png
        └── 4-settings-page.png
```

## Important

1. **Username:** Use exactly `boopixel` (case sensitive)
2. **Password:** Use the SVN password you configured in your WordPress.org profile (NOT your email)
3. **Assets:** Banners, icons, and screenshots go in `assets/`, not in `trunk/assets/`
4. **Tag:** Always create a tag for each version (1.0.0, 1.0.1, etc.)

## Verify before committing

```bash
cd boopixel-svn
svn status  # See all changes
```

Make sure:
- There are no `.zip`, `build.sh`, `package.json` files, etc.
- Assets are in `assets/` (not in `trunk/assets/`)
- Tag 1.0.0 was created

## Useful Links

- **How to use SVN:** https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/
- **Plugin Assets:** https://developer.wordpress.org/plugins/wordpress-org/plugin-assets/
- **Plugin Guidelines:** https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/

## After Commit

Your plugin will be available at:
**https://wordpress.org/plugins/boopixel-ai-chat-for-n8n**

It may take up to 72 hours to appear in search results.
