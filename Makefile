.PHONY: up down status logs health backup test

up:
	docker compose up -d --build

down:
	docker compose down

status:
	docker compose ps

logs:
	docker compose logs --tail=100 app db

health:
	./ops/healthcheck.sh

backup:
	./ops/backup.sh

test:
	node tests/structure_test.mjs
	./tests/http_smoke.sh

