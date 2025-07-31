#!/bin/bash
set -e

COLOR=$1
APP_DIR="/var/www/manager.filkx.com"
RELEASE_DIR="$APP_DIR/$COLOR/current"
DOCKER_COMPOSE_FILE="$RELEASE_DIR/docker-compose-production.yml"
WORKDIR_IN_CONTAINER="/app"


if [ ! -d "$RELEASE_DIR" ]; then
  echo "❌ Папка релізу не знайдена: $RELEASE_DIR"
  exit 1
fi

echo "🚀 Запуск контейнерів..."
docker-compose -f "$DOCKER_COMPOSE_FILE" up -d

echo "⏳ Очікування запуску контейнера manager-php-cli..."
until docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T manager-php-cli true 2>/dev/null; do
  echo "⌛ Чекаємо... (manager-php-cli ще не готовий)"
  sleep 1
done

echo "🧹 Очистка та кешування..."
docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T -w "$WORKDIR_IN_CONTAINER" manager-php-cli php bin/console cache:clear --env=prod
docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T -w "$WORKDIR_IN_CONTAINER" manager-php-cli php bin/console cache:warmup --env=prod
