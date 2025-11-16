#!/bin/bash

ZIP_NAME="boochat-connect.zip"

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
    > /dev/null 2>&1
