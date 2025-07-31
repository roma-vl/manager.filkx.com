#!/bin/bash
set -e

COLOR=$1
APP_DIR="/var/www/manager.filkx.com"
RELEASE_DIR="$APP_DIR/$COLOR/current"
DOCKER_COMPOSE_FILE="$RELEASE_DIR/docker-compose-production.yml"
WORKDIR_IN_CONTAINER="/var/www/html"

# ✅ Перевірки
if [[ "$COLOR" != "blue" && "$COLOR" != "green" ]]; then
  echo "❌ Некоректне середовище: $COLOR"
  exit 1
fi

if [ ! -d "$RELEASE_DIR" ]; then
  echo "❌ Папка релізу не знайдена: $RELEASE_DIR"
  exit 1
fi

echo "🚀 Деплой Symfony у $COLOR середовище"
cd "$RELEASE_DIR"

rm -f "$RELEASE_DIR/.env"
ln -sfn /var/www/manager.filkx.com/shared/.env "$RELEASE_DIR/.env"

# 🛑 Зупинка поточних контейнерів
echo "🛑 Зупинка контейнерів..."
docker-compose -f "$DOCKER_COMPOSE_FILE" down

# 🚀 Запуск контейнерів
echo "🚀 Запуск контейнерів..."
docker-compose -f "$DOCKER_COMPOSE_FILE" up -d --build

# ⏳ Очікування запуску CLI-контейнера
echo "⏳ Очікування запуску контейнера manager-php-cli..."
until docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T manager-php-cli true 2>/dev/null; do
  echo "⌛ Чекаємо... (manager-php-cli ще не готовий)"
  sleep 1
done

# 🔐 Права та структура
echo "🔐 Налаштування директорій кешу, логів, сховища..."
docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T -w "$WORKDIR_IN_CONTAINER" manager-php-cli sh -c "\
    mkdir -p var/cache var/log var/storage/default && \
    chown -R www-data:www-data var && \
    chmod -R 775 var && \
    ln -snf /app/var/storage/default /app/public/storage \
"


# ⚙️ Міграції
echo "⚙️ Doctrine міграції..."
docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T -w "$WORKDIR_IN_CONTAINER" manager-php-cli php bin/console doctrine:migrations:migrate --no-interaction

# 🧹 Кешування
echo "🧹 Очистка та кешування..."
docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T -w "$WORKDIR_IN_CONTAINER" manager-php-cli php bin/console cache:clear --env=prod
docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T -w "$WORKDIR_IN_CONTAINER" manager-php-cli php bin/console cache:warmup --env=prod


# 🔗 Перемикаємо current
ln -sfn "$APP_DIR/$COLOR/current" "$APP_DIR/current"

echo "✅ Деплой завершено. Активне середовище — $COLOR"
