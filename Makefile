cli:
	docker run --rm -v ${PWD}/manager:/app --workdir=/app php:8.4-cli php bin/app.php
