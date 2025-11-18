# Translation Files

This directory contains translation files following WordPress I18n standards.

## Files

- `boochat-connect.pot` - Template file (Portable Object Template)
- `boochat-connect-en_US.po` - English translations
- `boochat-connect-pt_BR.po` - Portuguese (Brazil) translations
- `boochat-connect-es_ES.po` - Spanish translations

## Compiling .mo files

To compile `.po` files to `.mo` (binary) files, use the `msgfmt` tool:

```bash
msgfmt -o boochat-connect-en_US.mo boochat-connect-en_US.po
msgfmt -o boochat-connect-pt_BR.mo boochat-connect-pt_BR.po
msgfmt -o boochat-connect-es_ES.mo boochat-connect-es_ES.po
```

Or compile all at once:

```bash
for file in *.po; do
    msgfmt -o "${file%.po}.mo" "$file"
done
```

## WordPress I18n Standards

This plugin now follows WordPress I18n standards:
- Uses `load_plugin_textdomain()` to load translations
- Uses `esc_html__()`, `__()`, `_e()`, etc. for translations
- Text Domain: `boochat-connect`
- Translation files follow WordPress naming conventions

## Updating Translations

1. Edit the `.po` files directly
2. Compile to `.mo` files using `msgfmt`
3. The plugin will automatically load the correct translation based on WordPress locale or plugin settings

