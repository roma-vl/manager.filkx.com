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

# 🔐 Права на var
docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T -w "$WORKDIR_IN_CONTAINER" php chown -R www-data:www-data var
docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T -w "$WORKDIR_IN_CONTAINER" php chmod -R 775 var

# ⚙️ Міграції
echo "⚙️ Doctrine міграції..."
docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T -w "$WORKDIR_IN_CONTAINER" php bin/console doctrine:migrations:migrate --no-interaction

# 🧹 Кешування
echo "🧹 Очистка та кешування..."
docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T -w "$WORKDIR_IN_CONTAINER" php bin/console cache:clear
docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T -w "$WORKDIR_IN_CONTAINER" php bin/console cache:warmup

# 🔗 Перемикаємо current
ln -sfn "$APP_DIR/$COLOR/current" "$APP_DIR/current"

echo "✅ Деплой завершено. Активне середовище — $COLOR"
