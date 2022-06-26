
# Setup Docker Para Projetos Laravel 9 com PHP 8


Suba os containers do projeto
~~~~
docker-compose up -d
~~~~


Acessar o container
~~~~
docker-compose exec app bash
~~~~


Instalar as dependências do projeto
~~~~
composer install
~~~~


Gerar a key do projeto Laravel
~~~~
php artisan key:generate
~~~~


Acesse o projeto
[http://localhost:8989](http://localhost:8989)
