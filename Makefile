composer-install:
	docker compose -f docker-compose.composer.yml up composer-install --remove-orphans
composer-update:
	docker compose -f docker-compose.composer.yml up composer-update --remove-orphans
app-run-local:
	docker network ls|grep reverse_proxy_network > /dev/null || docker network create reverse_proxy_network
	docker compose --env-file ./.env.dev -f docker-compose.reverse-proxy.yml -f docker-compose.yml -f docker-compose.local.yml up -d --remove-orphans
app-run-prod:
	docker network ls|grep reverse_proxy_network > /dev/null || docker network create reverse_proxy_network
	docker compose  --env-file ./.env.prod -f docker-compose.reverse-proxy.yml -f docker-compose.yml -f docker-compose.prod.yml up -d --remove-orphans
app-build-local:
	docker network ls|grep reverse_proxy_network > /dev/null || docker network create reverse_proxy_network
	docker compose --env-file ./.env.dev -f docker-compose.reverse-proxy.yml -f docker-compose.yml -f docker-compose.local.yml build --force-rm --no-cache
app-build-prod:
	docker network ls|grep reverse_proxy_network > /dev/null || docker network create reverse_proxy_network
	docker compose  --env-file ./.env.prod -f docker-compose.reverse-proxy.yml -f docker-compose.yml -f docker-compose.prod.yml build --force-rm --no-cache
app-stop:
	docker compose -f docker-compose.reverse-proxy.yml -f docker-compose.yml -f docker-compose.local.yml -f docker-compose.prod.yml down
app-code-check:
	docker compose exec php composer code-check
reverse-proxy-run:
	docker network ls|grep reverse_proxy_network > /dev/null || docker network create reverse_proxy_network
	docker compose -f docker-compose.reverse-proxy.yml up -d --remove-orphans
proxy-run:
	ngrok http https://localhost:8000 --host-header=rewrite
generate-jwt-keys:
	docker compose exec php composer generate-jwt-keys
generate-reverse-proxy-user:
	htpasswd -nB $(ARGS)
ubuntu-docker-compose-install:
	sudo apt update
	sudo apt install apt-transport-https ca-certificates curl software-properties-common
	curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
	echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
	sudo apt update
	apt-cache policy docker-ce
	sudo apt install docker-ce
	sudo systemctl status docker
	sudo usermod -aG docker ${USER}
	su - ${USER}
	groups
	mkdir -p ~/.docker/cli-plugins/
	curl -SL https://github.com/docker/compose/releases/download/v2.23.0/docker-compose-linux-x86_64 -o ~/.docker/cli-plugins/docker-compose
	chmod +x ~/.docker/cli-plugins/docker-compose
	docker compose version

grep-logs-from-server:
	scp -r root@194.33.180.50:/var/www/api/tmp/log ./tmp/log/server
