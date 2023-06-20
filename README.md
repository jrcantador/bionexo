
# Bionexo

Este projeto é utilizado para realizar testes técnicos.




## Autores

- [@jrcantador](https://github.com/jrcantador)


## Ferramentas

 - [Laravel](https://laravel.com/)
 - [Mysql](https://www.mysql.com/)
 - [web-driver](https://github.com/php-webdriver/php-webdriver)


## Documentação da API

####  Salva informações obitdas em uma tabela e salva no banco

```http
  post /api/informations
```

#### Preenche formulário e envia, retornando informações enviadas

```http
  post /api/informations/document
```

#### Faz download de um document

```http
  post /api/informations/dowload
```

#### Faz upload de um documento

```http
  post /api/informations/upload
```

#### obtem dados de um pdf e gera um csv

```http
  post /api/informations/pdf
```



## Instalação

Após clonar o projeto, executar o comando dentro da pasta docker

```bash
 docker-compose up -d
```
    
Após todos os containers estarem disponíeveis, será necessário alterar o arquivo adicionar o arquivo .env, adicionado as informações do banco de dados e link dos serviços que a API utiliza.


Em seguida, executar o comando para entra em um terminal dentro do container do PHP

```bash
 docker exec -ti setup-php bash
```

Em seguida, executar o seguinte comando instalar as dependencia do PHP

```bash
 composer install
```


Após terminar a instalação,  executar o seguinte comando para  gerar a chave

```bash
 php artisan key:generate
```
Essa chave será adicionada no arquivo .env automáticamente

Após terminar a instalação, executar o comando para rodar os scripts no banco de dados.

```bash
php artisan migrate
```


Por fim, executar comando abaixo para gerar um link a pasta pública

```bash
php artisan storage:link
```
