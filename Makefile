composer-install:
	docker compose -f docker-compose.composer.yml up composer-install --remove-orphans
composer-update:
	docker compose -f docker-compose.composer.yml up composer-update --remove-orphans
app-run:
	docker compose -f docker-compose.yml up -d --remove-orphans
app-stop:
	docker compose -f docker-compose.yml down
app-code-check:
	docker compose exec app composer code-check
proxy-run:
	ngrok http https://localhost:8000
