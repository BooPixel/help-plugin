# BooPixel AI Chat Connect for n8n

[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.2%2B-purple)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPLv2-green)](https://www.gnu.org/licenses/gpl-2.0.html)

**BooPixel AI Chat Connect for n8n** é um plugin WordPress moderno que integra um chatbot inteligente com workflows n8n, permitindo automação de atendimento ao cliente, respostas em tempo real e integração com qualquer serviço externo ou modelo de IA.

## 🚀 Características Principais

- ✅ **Integração n8n** - Conecte-se a workflows n8n via webhook
- ✅ **Interface Customizável** - Cores, fontes, ícones e mensagens personalizáveis
- ✅ **Pronto para IA** - Conecte-se a ChatGPT, Claude, Gemini e mais via n8n
- ✅ **Multilíngue** - Suporte para Inglês, Português e Espanhol
- ✅ **Estatísticas** - Dashboard com gráficos e análises
- ✅ **Sessões** - Gerenciamento e exportação de conversas
- ✅ **Responsivo** - Funciona em todos os dispositivos
- ✅ **Leve e Rápido** - Código otimizado com dependências mínimas

## 📦 Instalação

### Instalação Automática

1. Acesse **Plugins > Adicionar Novo** no WordPress
2. Procure por "BooPixel AI Chat Connect for n8n"
3. Clique em **Instalar Agora**
4. Clique em **Ativar**

### Instalação Manual

1. Baixe o arquivo ZIP do plugin
2. Acesse **Plugins > Adicionar Novo > Enviar Plugin**
3. Selecione o arquivo ZIP e clique em **Instalar Agora**
4. Clique em **Ativar**

## ⚙️ Requisitos

- WordPress 5.0 ou superior
- PHP 7.2 ou superior (PHP 7.4+ recomendado)
- MySQL 5.6 ou superior
- Instância n8n ativa com workflows de webhook

## 🔧 Configuração Rápida

1. **Ative o plugin** no WordPress
2. **Configure o webhook n8n** em **BooPixel Chat > Configurações**
3. **Personalize a aparência** em **BooPixel Chat > Personalização**
4. O chat aparecerá automaticamente no frontend!

## 📚 Documentação

### 📖 Documentação Completa

Acesse a documentação completa com MkDocs:

```bash
# Instalar dependências
pip install -r requirements.txt

# Servir localmente
mkdocs serve
```

Acesse: `http://127.0.0.1:8000`

### 📋 Guias Disponíveis

- **[Guia SVN Completo](docs/guia-svn/index.md)** - Upload para WordPress.org
- **[Comandos Rápidos](docs/guia-svn/comandos-rapidos.md)** - Referência rápida
- **[Configurar Credenciais](docs/guia-svn/configurar-credenciais.md)** - Setup SVN
- **[Scripts de Automação](docs/scripts/upload-svn.md)** - Scripts automatizados
- **[FAQ](docs/recursos/faq.md)** - Perguntas frequentes

## 🛠️ Scripts Disponíveis

### Upload para SVN

```bash
./scripts/upload-to-svn.sh
```

Script automatizado que faz upload completo do plugin para WordPress.org.

### Preparar Arquivos

```bash
./scripts/prepare-svn.sh
```

Prepara arquivos antes do upload, criando uma pasta limpa.

## 🔗 Integração n8n

O plugin envia mensagens para seu webhook n8n no formato:

```json
{
  "sessionId": "unique-session-id",
  "action": "sendMessage",
  "chatInput": "user message text"
}
```

Seu workflow n8n deve retornar:

```json
{
  "output": "response message text"
}
```

## 📊 Recursos do Admin

- **Painel Principal** - Dashboard com informações e links rápidos
- **Personalização** - Painel visual para customizar a aparência
- **Configurações** - Configure webhook n8n e preferências
- **Estatísticas** - Gráficos interativos e análises
- **Sessões** - Visualize, pesquise e exporte conversas

## 🌍 Idiomas Suportados

- 🇺🇸 Inglês (English)
- 🇧🇷 Português (Português)
- 🇪🇸 Espanhol (Español)

## 📝 Changelog

### 1.0.0
- Lançamento inicial
- Integração completa com n8n
- Interface customizável
- Gerenciamento de sessões
- Dashboard de estatísticas
- Suporte multilíngue
- Funcionalidade de exportação
- Design responsivo

## 🤝 Contribuindo

Contribuições são bem-vindas! Por favor:

1. Faça um fork do repositório
2. Crie uma branch para sua feature (`git checkout -b feature/amazing-feature`)
3. Commit suas mudanças (`git commit -m 'Add amazing feature'`)
4. Push para a branch (`git push origin feature/amazing-feature`)
5. Abra um Pull Request

## 📄 Licença

Este plugin é licenciado sob a **GPLv2 ou posterior**.

```
Copyright (c) 2024 BooPixel

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
```

## 🔗 Links

- **Website:** https://boopixel.com
- **Suporte:** https://boopixel.com/support
- **WordPress.org:** https://wordpress.org/plugins/boopixel-ai-chat-for-n8n
- **Documentação:** Veja a pasta `docs/` ou execute `mkdocs serve`

## 👨‍💻 Desenvolvido por

**BooPixel**

---

⭐ Se este plugin foi útil, considere dar uma estrela no repositório!
