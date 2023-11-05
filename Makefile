composer-install:
	docker compose -f docker-compose.composer.yml up composer-install --remove-orphans
composer-update:
	docker compose -f docker-compose.composer.yml up composer-update --remove-orphans
app-run:
	docker compose -f docker-compose.yml up -d --remove-orphans
app-run-prod:
	docker compose -f docker-compose.yml -f docker-compose.prod.yml up -d --remove-orphans
app-stop:
	docker compose -f docker-compose.yml down
app-code-check:
	docker compose exec app composer code-check
proxy-run:
	ngrok http https://localhost:8000 --host-header=rewrite
generate-jwt-keys:
	docker compose exec app composer generate-jwt-keys
ubuntu-docker-compose-install:
	mkdir -p ~/.docker/cli-plugins/
	curl -SL https://github.com/docker/compose/releases/download/v2.23.0/docker-compose-linux-x86_64 -o ~/.docker/cli-plugins/docker-compose
	chmod +x ~/.docker/cli-plugins/docker-compose
	docker compose version
