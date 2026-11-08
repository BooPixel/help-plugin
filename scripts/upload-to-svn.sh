#!/bin/bash

# Script para fazer upload do plugin para o SVN do WordPress.org
# Execute este script APÓS fazer o checkout do repositório SVN

set -e  # Parar em caso de erro

echo "🚀 Script de Upload para SVN do WordPress.org"
echo "=============================================="
echo ""

# Verificar se estamos no diretório correto
if [ ! -f "boopixel-ai-chat-for-n8n.php" ]; then
    echo "❌ Erro: Execute este script a partir da raiz do projeto do plugin"
    exit 1
fi

# Verificar se SVN está instalado
if ! command -v svn &> /dev/null; then
    echo "❌ Erro: SVN não está instalado"
    echo "   Instale com: brew install svn (macOS) ou apt-get install subversion (Linux)"
    exit 1
fi

# Configurações
SVN_REPO="https://plugins.svn.wordpress.org/boopixel-ai-chat-for-n8n"
SVN_USERNAME="boopixel"
SVN_DIR="boopixel-svn"
VERSION="1.0.0"

echo "📋 Configurações:"
echo "   Repositório: $SVN_REPO"
echo "   Username: $SVN_USERNAME"
echo "   Versão: $VERSION"
echo ""

# Verificar se o diretório SVN existe
if [ ! -d "$SVN_DIR" ]; then
    echo "📥 Fazendo checkout do repositório SVN..."
    echo "   Username: $SVN_USERNAME"
    echo "   (Você será solicitado a inserir sua senha SVN)"
    echo ""
    svn checkout --username "$SVN_USERNAME" "$SVN_REPO" "$SVN_DIR"
    echo ""
else
    echo "✅ Diretório SVN já existe: $SVN_DIR"
    echo "   Atualizando..."
    cd "$SVN_DIR"
    svn update --username "$SVN_USERNAME"
    cd ..
    echo ""
fi

# Criar estrutura se não existir
echo "📁 Verificando estrutura do repositório..."
cd "$SVN_DIR"

if [ ! -d "trunk" ]; then
    echo "   Criando pasta trunk..."
    mkdir -p trunk
    svn add trunk
fi

if [ ! -d "tags" ]; then
    echo "   Criando pasta tags..."
    mkdir -p tags
    svn add tags
fi

if [ ! -d "assets" ]; then
    echo "   Criando pasta assets..."
    mkdir -p assets
    svn add assets
fi

cd ..

# Copiar arquivos para trunk
echo ""
echo "📦 Copiando arquivos para trunk/..."
cd "$SVN_DIR/trunk"

# Limpar trunk (exceto .svn)
find . -type f ! -path '*/.svn/*' -delete
find . -type d ! -path '*/.svn/*' ! -path '.' -exec rm -rf {} + 2>/dev/null || true

# Copiar arquivos principais
cp ../../boopixel-ai-chat-for-n8n.php .
cp ../../README.txt .
cp ../../LICENSE .

# Copiar pastas
cp -r ../../includes .
cp -r ../../languages .

# Copiar assets (CSS e JS)
mkdir -p assets
cp -r ../../assets/css assets/
cp -r ../../assets/js assets/

# Adicionar arquivos ao SVN
echo ""
echo "➕ Adicionando arquivos ao SVN..."
svn add --force .

cd ../..

# Copiar assets públicos para assets/
echo ""
echo "🖼️  Organizando assets públicos..."
cd "$SVN_DIR/assets"

# Criar pasta de screenshots se não existir
mkdir -p screenshots

# Copiar banner e ícone
if [ -f "../../banner-772x250.png" ]; then
    cp ../../banner-772x250.png .
    echo "   ✅ Banner copiado"
fi

if [ -f "../../icon-256x256.png" ]; then
    cp ../../icon-256x256.png .
    echo "   ✅ Ícone copiado"
fi

# Copiar screenshots
if [ -d "../../assets/screenshots" ]; then
    cp ../../assets/screenshots/*.png screenshots/ 2>/dev/null || true
    echo "   ✅ Screenshots copiados"
fi

# Adicionar assets ao SVN
svn add --force .

cd ../..

# Criar tag da versão
echo ""
echo "🏷️  Criando tag da versão $VERSION..."
cd "$SVN_DIR"

if [ -d "tags/$VERSION" ]; then
    echo "   ⚠️  Tag $VERSION já existe. Removendo..."
    svn remove --username "$SVN_USERNAME" "tags/$VERSION"
fi

svn copy --username "$SVN_USERNAME" trunk "tags/$VERSION"
echo "   ✅ Tag $VERSION criada"

cd ..

# Mostrar status
echo ""
echo "📊 Status do repositório:"
cd "$SVN_DIR"
svn status
cd ..

echo ""
echo "✅ Preparação concluída!"
echo ""
echo "📋 Próximos passos:"
echo ""
echo "1. Revise as mudanças:"
echo "   cd $SVN_DIR"
echo "   svn status"
echo ""
echo "2. Faça commit das mudanças:"
echo "   svn commit --username $SVN_USERNAME -m 'Initial release version $VERSION'"
echo "   (Você será solicitado a inserir sua senha SVN)"
echo ""
echo "3. Após o commit, seu plugin estará disponível em:"
echo "   https://wordpress.org/plugins/boopixel-ai-chat-for-n8n"
echo ""
echo "⚠️  IMPORTANTE:"
echo "   - Revise todos os arquivos antes de fazer commit"
echo "   - Certifique-se de que não há arquivos desnecessários"
echo "   - Verifique se os assets estão na pasta correta"
echo ""

