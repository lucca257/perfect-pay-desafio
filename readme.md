# Como Configurar o Projeto com Docker Compose

Este projeto utiliza **Docker Compose** para facilitar a configuração do ambiente de desenvolvimento. Siga os passos abaixo para configurar e rodar o projeto corretamente.

## Demo do projeto
[screen-capture (3).webm](https://github.com/user-attachments/assets/5a9e20df-e0a5-4480-b3f9-aebdf83579ca)

## Pré-requisitos

Certifique-se de ter instalado em sua máquina:
- [Docker](https://www.docker.com/get-started)
- [Docker Compose](https://docs.docker.com/compose/install/)

## Passos para Configuração

1. **Copiar o arquivo `.env.example` para `.env`**

   ```sh
   cp .env.example .env
   ```
   lembre-se de setar as chaves do env do provedor de pagamento

2. **Subir os containers com Docker Compose**

   ```sh
   docker-compose up -d
   ```

3. **Instalar as dependências do projeto**

   ```sh
   docker-compose exec app composer install
   ```

4. **Rodar as migrations para configurar o banco de dados**

   ```sh
   docker-compose exec app php artisan migrate
   ```

5. **Rodar o sistema**
cadastre um usuário ao acessar o localhost e use o sistema

## Observações

- O projeto foi desenvolvido utilizando **SQLite**, pois todas as funcionalidades presentes atendem aos requisitos do sistema.
- As migrations foram feitas de maneira compatível com **MySQL**, permitindo uma possível migração futura caso necessário.


