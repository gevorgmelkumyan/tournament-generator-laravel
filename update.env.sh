#!/bin/bash

ROOT_ENV_FILE=".env"
DOCKER_ENV_FILE="docker/.env"

if [ ! -f "$DOCKER_ENV_FILE" ]; then
    echo "docker/.env is missing, copying from docker/.env.example..."
    cp docker/.env.example docker/.env
fi

if [ ! -f "$ROOT_ENV_FILE" ]; then
    echo ".env is missing, copying from .env.example..."
    cp .env.example .env

    # Load environment variables from .env to this script's session
    set -a
    [ -f .env ] && . ./.env
    [ -f docker/.env ] && . ./docker/.env
    set +a

    if [ -z "$APP_URL" ]; then
        APP_URL="http://localhost:$SERVER_PORT"
    fi

    # Export all the necessary variables fetched from both .envs to the root .env
    {
        printf "\n"
        echo "APP_URL=$APP_URL"
        printf "\n"
        echo "DB_HOST=mysql"
        echo "DB_PORT=3306"
        echo "DB_DATABASE=$DB_DATABASE"
        echo "DB_USERNAME=$DB_USERNAME"
        echo "DB_PASSWORD=$DB_PASSWORD"
        printf "\n"
        echo "REDIS_HOST=redis"
        echo "REDIS_PORT=$REDIS_PORT"
    } >> .env
fi
