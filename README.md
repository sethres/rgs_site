# RGS website

## Running without docker
1. Install composer (https://getcomposer.org/download/).
1. Navigate to site/api in a terminal.
1. Run `composer install`.

## Setting up for production or adding controllers
1. Navigate to site/api in a terminal.
1. Run `composer install -o` for a new install or `composer dump-autoload -o` for adding a controller

## Docker Setup
1. Build the docker image `docker-compose build`
1. Run the docker image `docker-compose up` or `docker-compose up -d` to run in detached mode.
1. The API is directly available locally here: http://localhost/api/

## API Info
The API is using slim framework 4.1 (http://www.slimframework.com/).
Upgrades can be made using composer.
Most changes to the API will be made in these files:
- app/settings.php - database connection info is here
- app/routes.php - to add new routes to get different data or modify existing routes
- src/application/Controllers/ProductController.php - to edit existing API calls or when adding a new route
- src/application/Models/ProductModel.php - to edit data returned for existing API calls or adding new data for a new route

## Frontend Info
The front end was written in Vue.js 2.6.12 and was built without npm/webpack to make it easier to pick up quickly and remove the need for a build process.
To upgrade Vue, search for 2.6.12 and replace that with the desired version.
Most changes to the frontend will be made in the component files in the components folder.
The rollbar configuration is in /products/index.php to configure when it sends the development/staging/production environment values.