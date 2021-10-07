# projet blog batman

## instalation

### cloner le projet

```
git clone https://github.com/Hypnololz/blogBatman.git
```

### changer les connection base de données dans .env

### deplacer le terminal dans le dossier cloné

```
cd batmanBlog
```
### taper les commandes suivantes :

```
composer install
symfony console doctrine:database:create
symfony console make:migration
symfony console doctrine:migrations:migrate
```
