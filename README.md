# Repositório GitHub - Integração de Consulta de CEP com Vue.js e Laravel

Este repositório contém um projeto que expande a funcionalidade original proposta e oferece uma experiência completa de consulta e gerenciamento de CEPs. O projeto utiliza Vue.js para a interface do usuário e Laravel para o backend.

## Funcionalidades

Além das funcionalidades básicas propostas, este projeto oferece:

- **Preenchimento Automático de Informações:** Ao digitar um CEP no formulário, as informações de endereço são automaticamente preenchidas a partir de dados existentes no banco de dados.

- **Busca e Cadastro Dinâmico:** Se um CEP não estiver cadastrado no banco de dados local, o sistema buscará automaticamente as informações referentes a esse CEP em uma API externa. As informações serão então cadastradas no banco de dados e exibidas na tela.

- **Edição e Armazenamento Local:** Ao editar os dados de um CEP, as alterações são automaticamente salvas no armazenamento local (LocalStorage) do navegador, permitindo uma experiência contínua mesmo após atualizações ou recarregamentos da página.

## Instruções de Instalação

Siga as etapas abaixo para configurar e executar o projeto localmente. Certifique-se de ter o [Node.js](https://nodejs.org/) e o [Composer](https://getcomposer.org/) instalados em sua máquina antes de começar.

### Instalação do Vue.js

1. Navegue até a pasta do projeto frontend: cd cep-client
2. Instale as dependências do Node.js: npm install
3. Instale as dependências do PHP: composer install
4. Crie o banco de dados `cep-api` no banco de dados configurado.
5. Execute as migrações para criar as tabelas do banco de dados: php artisan migrate:fresh
6. Inicie o servidor Laravel: php artisan serve
7. Inicie o projeto vue: npm run dev
8. O servidor estará disponível em `http://localhost:8000`.
