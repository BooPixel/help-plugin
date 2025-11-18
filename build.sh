#!/bin/bash

ZIP_NAME="boochat-connect.zip"

# Function to increment version (patch version)
increment_version() {
    local version=$1
    IFS='.' read -ra ADDR <<< "$version"
    local major=${ADDR[0]}
    local minor=${ADDR[1]}
    local patch=${ADDR[2]}
    patch=$((patch + 1))
    echo "$major.$minor.$patch"
}

# Get current version from boochat-connect.php
CURRENT_VERSION=$(grep "Version:" boochat-connect.php | head -1 | sed -E 's/.*Version: ([0-9]+\.[0-9]+\.[0-9]+).*/\1/')

if [ -z "$CURRENT_VERSION" ]; then
    CURRENT_VERSION="1.0.0"
fi

# Increment version
NEW_VERSION=$(increment_version "$CURRENT_VERSION")

echo "Updating version from $CURRENT_VERSION to $NEW_VERSION"

# Update version in boochat-connect.php (header and constant)
sed -i.bak "s/Version: $CURRENT_VERSION/Version: $NEW_VERSION/g" boochat-connect.php
sed -i.bak "s/define('BOOCHAT_CONNECT_VERSION', '$CURRENT_VERSION');/define('BOOCHAT_CONNECT_VERSION', '$NEW_VERSION');/g" boochat-connect.php

# Update version in package.json
sed -i.bak "s/\"version\": \"$CURRENT_VERSION\"/\"version\": \"$NEW_VERSION\"/g" package.json

# Update version in README.md
sed -i.bak "s/Current version: \*\*$CURRENT_VERSION\*\*/Current version: **$NEW_VERSION**/g" README.md

# Update version in README.txt
sed -i.bak "s/Stable tag: $CURRENT_VERSION/Stable tag: $NEW_VERSION/g" README.txt

# Remove backup files
rm -f boochat-connect.php.bak package.json.bak README.md.bak README.txt.bak

echo "Version updated to $NEW_VERSION"

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

# Create ZIP
[ -f "$ZIP_NAME" ] && rm "$ZIP_NAME"

zip -r "$ZIP_NAME" . \
    -x "*.git*" \
    -x "*.DS_Store*" \
    -x "*__MACOSX*" \
    -x "*.zip" \
    -x "build.sh" \
    -x ".gitignore" \
    -x "README.md" \
    -x "*.md" \
    -x "*.bak*" \
    -x "*backup*" \
    > /dev/null 2>&1

echo "Build complete: $ZIP_NAME (version $NEW_VERSION)"
