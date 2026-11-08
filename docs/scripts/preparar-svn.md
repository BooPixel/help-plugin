# Script: prepare-svn.sh

Script to prepare files before uploading to SVN, creating a clean folder with only necessary files.

## Description

This script creates an `svn-ready/` folder with the correct SVN structure, containing only files that should be sent to WordPress.org.

## How to Use

```bash
./scripts/prepare-svn.sh
```

## Created Structure

The script creates the following structure:

```
svn-ready/
├── trunk/              # Main code
├── tags/
│   └── 1.0.0/         # Version 1.0.0
└── assets/             # Public assets
    └── screenshots/
```

## Included Files

### In trunk/

- `boopixel-ai-chat-for-n8n.php`
- `README.txt`
- `LICENSE`
- `includes/` (complete)
- `languages/` (complete)
- `assets/css/` and `assets/js/`

### In assets/

- `banner-772x250.png`
- `icon-256x256.png`
- `screenshots/` (all images)

## Excluded Files

The script automatically excludes:

- `.zip` files
- Build scripts (`build.sh`, `build-*.sh`)
- `package.json`
- `composer.json`
- `phpunit.xml`
- `tests/` folder
- `.gitignore`
- `README.md` (only README.txt is needed)

## When to Use

Use this script when:

- You want to review files before upload
- You need a clean copy for backup
- You want to verify structure before uploading

## Next Steps

After running the script:

1. Review the `svn-ready/` folder
2. Use the `scripts/upload-to-svn.sh` script to do the actual upload
3. Or manually copy files to the SVN repository

## Tip

You can use this folder as a reference to verify all necessary files are present before uploading.

## See Also

- [Upload Script](upload-svn.md)
- [Complete SVN Guide](../guia-svn/index.md)
