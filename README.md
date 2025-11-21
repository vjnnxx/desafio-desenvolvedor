## Configurações do ambiente

## Subindo a imagem do app

```docker-compose up -d app```

## Buildando e iniciando os outros containers

```docker-compose up -d --build```

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

### api/upload [POST] - Upload de Arquivo

Parâmetros: Arquivo nos formatos .csv, xls ou xlsx.

####  Respostas 

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

### api/search [GET] - Histórico de upload de arquivo

Parâmetros: Nome do arquivo ou data de envio. (Data deve ser enviada no formato YYYY-MM-DD)

Exemplo: 
```
{
    name: "InstrumentsConsolidatedFile_20240823",
    date: "2025-11-20",
}
```


#### Respostas

* 200

```
{
    filename: "InstrumentsConsolidatedFile_20240823",
    upload_date: "2025-11-21",
}
   
```

* 404

```
{
    message: "Nenhum arquivo encontrado!",   
}
```


### api/search-content [GET] - Buscar conteúdo do arquivo


Parâmetros: RptDt ou TckrSymb

Exemplo:

```
{
    RptDt: "2024-08-23",
    TckrSymb: "AMZO34",
}
```

#### Respostas 

* 200 

```
{
    "current_page": 1,
    "data": [
        {
            "RptDt": "2024-08-23",
            "TckrSymb": "AMZO34",
            "MktNm": "EQUITY-CASH",
            "SctyCtgyNm": "BDR",
            "ISIN": "BRAMZOBDR002",
            "CrpnNm": "AMAZON.COM, INC"
        }
    ],
    "first_page_url": "http://localhost:8000/api/search-content?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "http://localhost:8000/api/search-content?page=1",
    "links": [
        {
            "url": null,
            "label": "&laquo; Previous",
            "active": false
        },
        {
            "url": "http://localhost:8000/api/search-content?page=1",
            "label": "1",
            "active": true
        },
        {
            "url": null,
            "label": "Next &raquo;",
            "active": false
        }
    ],
    "next_page_url": null,
    "path": "http://localhost:8000/api/search-content",
    "per_page": 1000,
    "prev_page_url": null,
    "to": 1,
    "total": 1
}
```

* 404

```
{
    message: "Nenhum resultado encontrado!",   
}
```






