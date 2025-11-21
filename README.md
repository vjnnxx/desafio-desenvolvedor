## Configurações do ambiente

## Subindo a imagem do app

```docker-compose up -d app```

## Buildando e iniciando os outros containers

```docker-compose up -d --build # buildando os containers```

## Instalando as dependências do laravel dentro do container da aplicação

```docker-compose exec app composer install --no-dev```

## Ajustar permissões

```docker-compose exec app chmod -R 775 storage bootstrap/cache```
```docker-compose exec app chown -R www-data:www-data storage bootstrap/cache```

## Gerar chave da aplicação
```docker-compose exec app php artisan key:generate```

## Rodar Migrations 

```docker-compose exec app php artisan migrate```

## Observações

É necessário copiar o arquivo .env.example e inserir as configurações do ambiente como banco de dados e etc.

Após realizar os passos a aplciação deve rodar em: ```http://localhost:8000```

# Utilização da API

## Endpoints

### api/upload [POST]

Parâmetros: Arquivo nos formatos .csv, xls ou xlsx.

- Respostas

* 200 

    ```
    {
        message: "Arquivo enviado!"
    }
    ```

* 403

    ```
    {
        message: "Não é possível realizar o download, arquivo já enviado."
    }
    ```

* 500

    ```
    {
        message: "Erro ao processar arquivo."
    }
    ```    

