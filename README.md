# Transaction APP

## Ambiente

Necessário ter docker e docker-compose instalado.

Execute o comando:

```shell
docker-composer up -d
```

Após iniciado containers, para executar os testes:

````shell
docker-compose exec php ./vendor/bin/phpunit
````

## POC

### Objetivo:
   * Fazer o processo de transferência sincrono, com o cobertura de testes nos casos de sucesso e falha.

#### Não faz parte do objetivo:
   * Gestão eficiente de cadastro de usuários, apenas validações básicas.
   * Gestão de autorização/autenticação


### Rotas

```text
user
	POST / [data]

transaction
	POST / [data]
```

#### User [data]
```json
{
    "name": "string",
    "email": "email",
    "document": "cpf/cnpj (only numbers)",
    "wallet": "float"
}
```

#### Transaction [data]
```json
{
    "value": "float",
    "payer": "integer",
    "payee": "integer"
}
```

### API

#### Response:
```json
{
    "success": true
}
```

### Cenários

#### Transação com suceso:
* Descrementa valor da origem
* Adiciona valor no destino
* Envia notificação de tranferência

#### Transação com falha:
* Dados insuficientes
* Saldo insuficiaente
* Sistema de autorização negar processo


## TODO

* Melhorar respostas de erros, com mensagens informando o erro ocorrido.
* Tornar processo assincrono para melhorar a escalabilidade.
* Adicionar "retry" no processo de autorização para casos de falha de comunicação
* Adicionar "retry" no processo de envio de mensagens para casos de falha de comunicação
* Executar testes em base separada para testes
