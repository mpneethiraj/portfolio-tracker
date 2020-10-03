## About Stock tracker

Stock tracker application developed using alpha vantage API and default laravel authentication has used. 


Note : I have worked only posstive flow, so while adding stock/ editing stock give me the proper ticket name 

## Installation
-- **[Clone the repository]**

 	git clone  git@github.com:mpneethiraj/portfolio-tracker.git

--**[Install all the dependencies using composer]**

	composer install

--**[Copy the example env file and make the required configuration changes in the .env file]**

	cp .env.example .env

--**[Generate a new application key]**

	php artisan key:generate

--**[Laravel Default Auth if required]**

    composer require laravel/ui "^1.0" --dev

--**[Run the database migrations (Set the database connection in .env before migrating)]**

	php artisan migrate

--**[Start the local development server]**

	php artisan serve
    
You can now access the server at http://localhost:8000

