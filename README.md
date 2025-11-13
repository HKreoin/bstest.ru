## Требования

-   Docker и Docker Compose

## Установка и запуск

1. Клонируйте репозиторий:

```bash
git clone <repository-url>
cd bstest.ru
```

2. Скопируйте `.env.example` в `.env` (если нужно):

```bash
cp .env.example .env
```

3. Запустите Docker контейнеры:

```bash
docker compose up -d
```

4. Установите зависимости:

```bash
docker compose exec app composer install
```

5. Сгенерируйте ключ приложения:

```bash
docker compose exec app php artisan key:generate
```

6. Выполните миграции:

```bash
docker compose exec app php artisan migrate
```
