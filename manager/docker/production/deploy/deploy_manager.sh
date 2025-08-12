#!/bin/bash
set -e

COLOR=$1
APP_DIR="/var/www/manager.filkx.com"
RELEASE_DIR="$APP_DIR/$COLOR/current"
DOCKER_COMPOSE_FILE="$RELEASE_DIR/docker-compose-production.yml"
WORKDIR_IN_CONTAINER="/app"

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

# 🔐 Симлінк до .env
rm -f "$RELEASE_DIR/.env"
ln -sfn /var/www/manager.filkx.com/shared/.env "$RELEASE_DIR/.env"

# 📁 Симлінк до спільного сховища файлів
SHARED_STORAGE="$APP_DIR/shared/storage/default"
TARGET_STORAGE="$RELEASE_DIR/manager/var/storage/default"

echo "🔗 Перевірка та створення симлінку для сховища..."
mkdir -p "$SHARED_STORAGE"
mkdir -p "$(dirname "$TARGET_STORAGE")"

if [ ! -L "$TARGET_STORAGE" ]; then
  ln -sfn "$SHARED_STORAGE" "$TARGET_STORAGE"
  echo "✅ Симлінк створено: $TARGET_STORAGE → $SHARED_STORAGE"
else
  echo "ℹ️ Симлінк вже існує: $TARGET_STORAGE"
fi

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
    rm -rf public/storage && \
    ln -snf var/storage/default public/storage \
"

# ⚙️ Doctrine міграції
echo "⚙️ Doctrine міграції..."
docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T -w "$WORKDIR_IN_CONTAINER" manager-php-cli php bin/console doctrine:migrations:migrate --no-interaction

# 🧹 Кешування
echo "🧹 Очистка та кешування..."
docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T -w "$WORKDIR_IN_CONTAINER" manager-php-cli php bin/console cache:clear --env=prod
docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T -w "$WORKDIR_IN_CONTAINER" manager-php-cli php bin/console cache:warmup --env=prod

# 🔗 Перемикаємо current
ln -sfn "$APP_DIR/$COLOR/current" "$APP_DIR/current"

echo "✅ Деплой завершено. Активне середовище — $COLOR"
