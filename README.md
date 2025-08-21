# Contact Manager

This is a simple contact management application built on the Laravel framework.

## Requirements

- Docker and Docker Compose

## Installation and Running the Local Environment

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/dzoganik/contact-manager.git
    cd contact-manager
    ```

2.  **Copy the environment file:**
    ```bash
    cp .env.example .env
    ```

3.  **Install Composer dependencies using a temporary Docker container:**
    This command uses Docker to run `composer install` without needing PHP or Composer installed on your local machine.
    ```bash
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php84-composer:latest \
        composer install --ignore-platform-reqs
    ```

4.  **Start Laravel Sail (Docker containers):**
    This command starts the application containers. We'll add a short delay to ensure the database service is ready before we continue.
    ```bash
    ./vendor/bin/sail up -d && sleep 20
    ```

5.  **Generate the application key:**
    ```bash
    ./vendor/bin/sail artisan key:generate
    ```

6.  **Run the database migrations:**
    ```bash
    ./vendor/bin/sail artisan migrate
    ```

7.  **Build frontend assets:**
    ```bash
    ./vendor/bin/sail npm install && ./vendor/bin/sail npm run build
    ```

8.  **Prepare the Search Index:**
    ```bash
    ./vendor/bin/sail artisan scout:sync-index-settings
    ```

The application should now be running at [http://localhost](http://localhost).
