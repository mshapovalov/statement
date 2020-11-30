# Installation
- [Install docker](https://docs.docker.com/engine/install/)
- [Install docker-compose](https://docs.docker.com/compose/install/)
- Create .env file from .env.dist
- Create `config.php` file from `config.dist.php`
- run `docker-compose up -d`
- run `docker-compose exec php composer install`
- go to the url `http://loclahost:<DOCKER_HTTP_PORT>/?author=<your_github_login>&month=<month>&secret=<secret_from_config.php>`
