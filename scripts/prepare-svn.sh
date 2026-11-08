#!/bin/bash

# Script para preparar arquivos para upload no SVN do WordPress.org
# Este script cria uma pasta 'svn-ready' com apenas os arquivos necessários

echo "🚀 Preparando arquivos para SVN do WordPress.org..."

# Nome da pasta de saída
OUTPUT_DIR="svn-ready"
PLUGIN_NAME="boopixel-ai-chat-for-n8n"

# Limpar pasta anterior se existir
if [ -d "$OUTPUT_DIR" ]; then
    echo "📁 Removendo pasta anterior..."
    rm -rf "$OUTPUT_DIR"
fi

# Criar estrutura de pastas
echo "📁 Criando estrutura de pastas..."
mkdir -p "$OUTPUT_DIR/trunk"
mkdir -p "$OUTPUT_DIR/assets/screenshots"
mkdir -p "$OUTPUT_DIR/tags/1.0.0"

# Copiar arquivos principais para trunk
echo "📦 Copiando arquivos principais..."
cp boopixel-ai-chat-for-n8n.php "$OUTPUT_DIR/trunk/"
cp README.txt "$OUTPUT_DIR/trunk/"
cp LICENSE "$OUTPUT_DIR/trunk/"

# Copiar pastas necessárias
echo "📁 Copiando pastas necessárias..."
cp -r includes "$OUTPUT_DIR/trunk/"
cp -r languages "$OUTPUT_DIR/trunk/"

# Copiar assets (CSS e JS) para trunk
echo "🎨 Copiando assets (CSS/JS)..."
mkdir -p "$OUTPUT_DIR/trunk/assets"
cp -r assets/css "$OUTPUT_DIR/trunk/assets/"
cp -r assets/js "$OUTPUT_DIR/trunk/assets/"

# Copiar assets públicos (banner, ícone, screenshots) para assets/
echo "🖼️  Copiando assets públicos..."
if [ -f "banner-772x250.png" ]; then
    cp banner-772x250.png "$OUTPUT_DIR/assets/"
fi

if [ -f "icon-256x256.png" ]; then
    cp icon-256x256.png "$OUTPUT_DIR/assets/"
fi

if [ -d "assets/screenshots" ]; then
    cp -r assets/screenshots/* "$OUTPUT_DIR/assets/screenshots/"
fi

# Copiar tudo para tags/1.0.0 também (mesma estrutura do trunk)
echo "🏷️  Criando tag 1.0.0..."
cp -r "$OUTPUT_DIR/trunk"/* "$OUTPUT_DIR/tags/1.0.0/"

# Criar arquivo .svnignore (se necessário)
cat > "$OUTPUT_DIR/.svnignore" << EOF
# Arquivos ignorados pelo SVN
.DS_Store
*.zip
.gitignore
README.md
package.json
composer.json
phpunit.xml
build.sh
build-*.sh
tests/
EOF

echo ""
echo "✅ Preparação concluída!"
echo ""
echo "📂 Estrutura criada em: $OUTPUT_DIR/"
echo ""
echo "📋 Estrutura do SVN:"
echo "   $OUTPUT_DIR/"
echo "   ├── trunk/              # Código principal"
echo "   ├── tags/"
echo "   │   └── 1.0.0/         # Versão 1.0.0"
echo "   └── assets/             # Banners, ícones, screenshots"
echo ""
echo "🚀 Próximos passos:"
echo "   1. Aguarde o email com credenciais SVN"
echo "   2. Faça checkout do repositório SVN"
echo "   3. Copie os arquivos de $OUTPUT_DIR/ para o repositório SVN"
echo "   4. Faça commit das mudanças"
echo ""
echo "📖 Leia o arquivo GUIA-SVN.md para instruções detalhadas"

