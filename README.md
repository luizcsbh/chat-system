
**Chat System - README**

  

Este projeto é um sistema de chat em tempo real, desenvolvido com uma arquitetura monolítica usando **Laravel**  no backend. O objetivo é criar uma aplicação escalável e eficiente que permita comunicação em tempo real entre os usuários.

  

**Tecnologias Utilizadas - Backend**

  

•  **PHP 8+**: Linguagem principal do backend.

•  **Laravel 10**: Framework PHP utilizado para desenvolvimento do backend.

•  **Redis**: Utilizado para cache e filas.

•  **MySQL**: Banco de dados relacional para persistência de dados.

•  **SQLite**: Banco de dados leve usado em ambiente de desenvolvimento ou testes.

•  **WebSockets**: Protocolo para comunicação em tempo real.

•  **Elasticsearch**: Utilizado para busca avançada.

•  **Docker**: Utilizado para containerização do ambiente de desenvolvimento.

•  **OAuth2 e 2FA**: Para autenticação segura.

  

**Pré-requisitos**

  

Antes de configurar o ambiente, certifique-se de ter as seguintes dependências instaladas na máquina:

  

•  **PHP 8+** com extensão php-redis.

•  **Composer** (gerenciador de dependências do PHP).

•  **MySQL** ou **SQLite**.

•  **Redis**.

•  **Elasticsearch**.

•  **Docker** (opcional para containerização).

•  **Git**.

  

**Configuração do Ambiente**

  

**Passos para Configuração em Outra Máquina**

  

1. **Clone o Repositório**

Primeiro, clone o repositório do projeto para a sua máquina local:
```git
git clone https://github.com/luizcsbh/chat-system.git
cd chat-system
```
2. **Instale as Dependências do PHP**

Use o Composer para instalar todas as dependências do Laravel:
```composer
composer install
```
3. **Configure o Arquivo** **.env**

Copie o arquivo .env.example para .env e edite as configurações de acordo com o seu ambiente:
``` .env
cp .env.example .env
```
Edite o arquivo .env e configure as seguintes variáveis:
```.env
APP_NAME=ChatSystem
APP_ENV=local
APP_KEY=base64:ChaveGeradaAleatoria
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=chat_system
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

ELASTIC_HOST=localhost
ELASTIC_PORT=9200

BROADCAST_DRIVER=redis
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```
4. **Gere a Chave da Aplicação**

Gere a chave de aplicação do Laravel:
```laravel
php artisan key:generate
```
5. **Configure o Banco de Dados**

Crie o banco de dados no MySQL e execute as migrações:
```laravel
php artisan migrate
```
Se estiver usando **SQLite**, crie o arquivo de banco de dados:
```
touch database/database.sqlite
```
Atualize o .env para usar SQLite:
```
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite
```
6. **Instale e Inicie o Redis**

•  **macOS:**
```
brew install redis
brew services start redis
```
•  **Ubuntu/Debian:**
```
sudo apt-get install redis-server
sudo service redis-server start
```
Verifique se o Redis está em execução:
```
redis-cli ping
```
7. **Instale e Configure o Elasticsearch**

Siga as instruções do Elasticsearch para instalar a versão correta para sua máquina:

•  **macOS:**
```
brew install elasticsearch-full
brew services start elasticsearch
```
•  **Ubuntu/Debian:**
```
wget https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-7.17.4-linux-x86_64.tar.gz
tar -xzf elasticsearch-7.17.4-linux-x86_64.tar.gz
cd elasticsearch-7.17.4
./bin/elasticsearch
```
Para verificar se o Elasticsearch está funcionando:
```
curl -X GET "localhost:9200/"
```
8. **Configure o WebSockets**

Instale a biblioteca de WebSockets:
```
composer require beyondcode/laravel-websockets
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"
```
Inicie o servidor de WebSockets:
```
php artisan websockets:serve
```
9. **Configure o Docker (Opcional)**

Se quiser usar Docker para o ambiente, crie um arquivo docker-compose.yml:
```yaml
version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    ports:
      - "8000:8000"
    depends_on:
      - redis
      - mysql
      - elasticsearch

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: chat_system
      MYSQL_ROOT_PASSWORD: sua_senha
    ports:
      - "3306:3306"

  redis:
    image: redis:alpine
    ports:
      - "6379:6379"

  elasticsearch:
    image: docker.elastic.co/elasticsearch/elasticsearch:7.17.4
    environment:
      - discovery.type=single-node
    ports:
      - "9200:9200"
  ```
  Para iniciar os contêineres, execute:
  ```
  docker-compose up -d
  ```
  10. **Inicie a Aplicação**

Inicie o servidor do Laravel:
```
php artisan serve
```
A aplicação estará disponível em:
```
http://localhost:8000
```
Para compilar a aplicação:
```
npm run build
```
Para acessar a documentação da API utilizando **Swagger**, siga os passos abaixo após iniciar o servidor do Laravel:

1.  Acesse o seguinte endereço:
```
http://localhost:8000/api/documentation
```
Certifique-se de que o servidor está em execução e que as rotas do Swagger foram configuradas corretamente no arquivo de rotas do Laravel (routes/api.php).

  

2.  Registre-se na aplicação e faça login.

3.  Após o login, vá para o perfil **API Tokens**, configure as permissões necessárias e gere um token para ser utilizado no Swagger.

  

Dessa forma, você poderá usar o Swagger para explorar e testar todos os endpoints da API com o token gerado.

