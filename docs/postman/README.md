# How to
## Use dev environment:
1. load fixtures `php bin/console doctrine:fixtures:load` (it will purge all your db)
2. generate environment `php bin/console api:postman:environment:dev`
3. get your env file in `app/var/` folder
4. upload environment to the postman `File->import`

## Use collection
upload collection `File->import`
