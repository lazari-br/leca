# Leca Moda Fitness - E-commerce

Este projeto é um e-commerce para a Leca Moda Fitness, desenvolvido com Laravel e Docker.

## Requisitos

- Docker e Docker Compose
- Git

## Configuração Inicial

1. Clone o repositório:
```bash
git clone [URL_DO_REPOSITORIO]
cd leca-ecommerce
```

2. Crie um arquivo `.env` a partir do exemplo:
```bash
cp .env.example .env
```

3. Configure as variáveis de ambiente no arquivo `.env`:
```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=leca_db
DB_USERNAME=leca_user
DB_PASSWORD=secure_password
```

4. Inicie os contêineres Docker:
```bash
docker-compose up -d
```

5. Acesse o contêiner da aplicação:
```bash
docker-compose exec app bash
```

6. Instale as dependências do Laravel:
```bash
composer install
```

7. Gere a chave da aplicação:
```bash
php artisan key:generate
```

8. Execute as migrações e os seeders:
```bash
php artisan migrate --seed
```

9. Crie o link simbólico para o storage:
```bash
php artisan storage:link
```

10. Opcional: Importe as imagens dos produtos para a pasta `storage/app/public/products/`

## Acesso à Aplicação

- Site: http://localhost:8000
- phpMyAdmin: http://localhost:8080

## Estrutura do Projeto

- **app/Models/**: Modelos de dados (Product, Category, etc.)
- **app/Http/Controllers/**: Controladores da aplicação
- **resources/views/**: Templates Blade
- **database/migrations/**: Migrações do banco de dados
- **database/seeders/**: Seeders para popular o banco de dados

## Customização

### Alterando o Número do WhatsApp

Edite o arquivo `resources/views/products/show.blade.php` e altere o número no link do WhatsApp:

```php
<a href="https://wa.me/5500000000000?text=Olá! Tenho interesse no produto {{ $product->name }}%20(Código: {{ $product->code }})"
```

## Adicionando Imagens de Produtos

1. Coloque as imagens na pasta `storage/app/public/products/`
2. O nome do arquivo deve corresponder ao slug do produto com extensão `.jpg` (ex: `conjunto-short-com-bolso-e-regata-nadador.jpg`)

## Desenvolvimento Futuro

O sistema está preparado para implementações futuras como:

- Sistema de checkout completo
- Gerenciamento de estoque
- Painel administrativo
- Sistema de cupons de desconto
- Integração com gateways de pagamento

## Suporte

Para suporte ou dúvidas sobre o projeto, entre em contato através do e-mail: [seu-email@exemplo.com]
