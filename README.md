# Help Plugin

Plugin WordPress com página no painel de controle administrativo.

## Descrição

Help Plugin é um plugin WordPress que adiciona uma página no menu do painel de controle, permitindo visualizar informações do sistema e realizar ações personalizadas.

## Instalação

### Método 1: Via Upload

1. Faça o download do arquivo `help-plugin.zip`
2. Acesse o painel do WordPress
3. Vá em **Plugins > Adicionar novo**
4. Clique em **Enviar plugin**
5. Selecione o arquivo `help-plugin.zip`
6. Clique em **Instalar agora**
7. Após a instalação, clique em **Ativar plugin**

### Método 2: Via FTP

1. Extraia o conteúdo do arquivo ZIP
2. Envie a pasta `help-plugin` para `wp-content/plugins/`
3. Acesse o painel do WordPress
4. Vá em **Plugins**
5. Ative o **Help Plugin**

## Uso

Após a ativação, você verá um novo item no menu lateral do WordPress chamado **Help Plugin**. Ao clicar, você terá acesso a:

- Página principal do plugin
- Informações do sistema (versão do WordPress, PHP e do plugin)
- Ações interativas

## Desenvolvimento

### Estrutura do Plugin

```
help-plugin/
├── help-plugin.php      # Arquivo principal do plugin
├── assets/
│   ├── css/
│   │   └── admin-style.css
│   └── js/
│       └── admin-script.js
├── build.sh             # Script para gerar ZIP
├── package.json         # Configurações npm
└── README.md
```

### Build

Para gerar o arquivo ZIP do plugin:

```bash
./build.sh
```

ou

```bash
npm run build
```

## Requisitos

- WordPress 5.0 ou superior
- PHP 7.4 ou superior

## Versão

1.0.0

## Licença

GPL v2 or later

## Autor

Seu Nome

