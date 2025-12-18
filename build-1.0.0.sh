#!/bin/bash

ZIP_NAME="boopixel-ai-chat-for-n8n.zip"
VERSION="1.0.0"

echo "Building plugin version $VERSION (no version increment)"

# Compile .mo files from .po files if msgfmt is available
if command -v msgfmt &> /dev/null; then
    echo "Compiling translation files..."
    cd languages
    for po_file in *.po; do
        if [ -f "$po_file" ]; then
            mo_file="${po_file%.po}.mo"
            msgfmt -o "$mo_file" "$po_file" 2>/dev/null && echo "  Compiled: $mo_file" || echo "  Warning: Failed to compile $po_file"
        fi
    done
    cd ..
else
    echo "Warning: msgfmt not found. .mo files will not be compiled."
    echo "Install gettext package to compile translation files."
fi

# Remove old ZIP if exists
[ -f "$ZIP_NAME" ] && rm "$ZIP_NAME"

# Create ZIP excluding development files
zip -r "$ZIP_NAME" . \
    -x "*.git*" \
    -x "*.DS_Store*" \
    -x "*__MACOSX*" \
    -x "*.zip" \
    -x "build.sh" \
    -x "build-1.0.0.sh" \
    -x ".gitignore" \
    -x "README.md" \
    -x "*.md" \
    -x "*.bak*" \
    -x "*backup*" \
    -x "tests/*" \
    -x "phpunit.xml" \
    -x "composer.json" \
    -x "package.json" \
    -x "node_modules/*" \
    > /dev/null 2>&1

echo ""
echo "✅ Build complete: $ZIP_NAME (version $VERSION)"
echo "📦 Ready for upload to WordPress.org"

