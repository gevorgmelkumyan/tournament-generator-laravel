purge env:
	rm .env
	rm docker/.env

build:
	chmod +x update.env.sh
	bash update.env.sh
	docker compose -f docker/docker-compose.yml build

run:
	docker compose -f docker/docker-compose.yml up

stop:
	docker compose -f docker/docker-compose.yml stop

down:
	docker compose -f docker/docker-compose.yml down -v --rmi=all --remove-orphans

server:
	docker compose -f docker/docker-compose.yml exec server bash
