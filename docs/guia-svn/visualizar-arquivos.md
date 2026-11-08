# How to View Files in SVN - Command Guide

## Repository Structure

Your SVN repository is at: `boopixel-svn/`

Structure:
```
boopixel-svn/
├── trunk/          # Main plugin code
├── tags/           # Versions (1.0.0, 1.0.1, etc.)
└── assets/         # Banner, icon, screenshots
```

## Commands to View Files

### 1. View folder structure

```bash
cd boopixel-svn
ls -la
```

### 2. View files in trunk (main code)

```bash
# List main folders and files
ls -la trunk/

# View all files recursively
find trunk -type f

# View only files (without folders)
ls -R trunk/
```

### 3. View files in assets (banner, icon, screenshots)

```bash
# See what's in assets/
ls -la assets/

# View screenshots
ls -la assets/screenshots/
```

### 4. View files in tag 1.0.0

```bash
# View tag structure
ls -la tags/1.0.0/

# View all tag files
find tags/1.0.0 -type f
```

### 5. View SVN status (added/modified files)

```bash
cd boopixel-svn
svn status
```

**Legend:**
- `A` = Added (will be committed)
- `M` = Modified
- `?` = Not tracked by SVN
- `D` = Deleted

### 6. View detailed file information

```bash
# View information for a specific file
svn info trunk/boopixel-ai-chat-for-n8n.php

# View differences (if there are modifications)
svn diff trunk/boopixel-ai-chat-for-n8n.php
```

### 7. View commit history

```bash
# View commit log
svn log

# View log for a specific file
svn log trunk/boopixel-ai-chat-for-n8n.php
```

## Useful Combined Commands

### View complete structure in organized way

```bash
cd boopixel-svn
echo "=== TRUNK ===" && find trunk -type f | head -10
echo ""
echo "=== ASSETS ===" && ls -la assets/
echo ""
echo "=== TAGS ===" && ls -la tags/
```

### Count files

```bash
# Count files in trunk
find trunk -type f | wc -l

# Count files in tag
find tags/1.0.0 -type f | wc -l
```

### View only PHP files

```bash
find trunk -name "*.php"
```

### View only CSS files

```bash
find trunk/assets/css -name "*.css"
```

## Most Used Quick Commands

```bash
# 1. Enter SVN directory
cd boopixel-svn

# 2. View main structure
ls -la

# 3. View files in trunk
ls -R trunk/

# 4. View status (what will be committed)
svn status

# 5. View assets
ls -la assets/
```

## Tip: Using Finder (macOS)

You can also open the directory in Finder:

```bash
open boopixel-svn
```

This will open the folder in Finder for visual navigation.

## Current Structure Summary

Based on your repository:

**trunk/** contains:
- `boopixel-ai-chat-for-n8n.php` (main file)
- `README.txt`
- `LICENSE`
- `includes/` (PHP classes)
- `languages/` (translations)
- `assets/css/` and `assets/js/` (CSS and JavaScript)

**assets/** contains:
- `banner-772x250.png`
- `icon-256x256.png`
- `screenshots/` (4 images)

**tags/1.0.0/** contains:
- Complete copy of trunk (version 1.0.0)
