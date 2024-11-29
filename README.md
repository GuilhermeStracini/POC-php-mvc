# PoC PHP MVC Framework with Custom Router, IoC/DI Container (PSR-compliant)

This project is a **Proof of Concept (PoC)** for a custom-built **PHP MVC framework** that adheres to several PHP-FIG standards, including PSR-1, PSR-2, PSR-4, and PSR-11. It demonstrates how to build a lightweight MVC structure with custom routing and dependency injection (IoC/DI) while following best practices for modern PHP development.

## Table of Contents

- [PoC PHP MVC Framework with Custom Router, IoC/DI Container (PSR-compliant)](#poc-php-mvc-framework-with-custom-router-iocdi-container-psr-compliant)
  - [Table of Contents](#table-of-contents)
  - [Introduction](#introduction)
  - [PSRs in Use](#psrs-in-use)
  - [Directory Structure](#directory-structure)
  - [What We Will Build](#what-we-will-build)
    - [Key Features:](#key-features)
    - [API Documentation](#api-documentation)
    - [Example Code](#example-code)
      - [`app/Controllers/HomeController.php`](#appcontrollershomecontrollerphp)
      - [`public/index.php`](#publicindexphp)
      - [`app/Config/Registration.php`](#appconfigregistrationphp)
  - [Requirements](#requirements)
  - [Getting Started](#getting-started)
  - [Expected Outcome](#expected-outcome)
  - [Deploy to Vercel](#deploy-to-vercel)
    - [Steps to Deploy](#steps-to-deploy)
    - [Vercel Configuration](#vercel-configuration)
      - [`vercel.json`](#verceljson)
  - [Running the Project with Docker](#running-the-project-with-docker)
    - [Prerequisites](#prerequisites)
    - [Running with Apache](#running-with-apache)
    - [Running with NGINX and PHP-FPM](#running-with-nginx-and-php-fpm)
  - [Tests](#tests)

## Introduction

In this PoC, we will build a small MVC application from scratch using **PSR-compliant components**. This includes a custom **router**, a basic **dependency injection container**, and an organized **controller/view structure**.

The goal of this PoC is to demonstrate the importance of **clean, maintainable architecture** by implementing industry-standard interfaces and coding practices. We will also use Composer to handle autoloading, complying with the PSR-4 standard.

## PSRs in Use

The project complies with the following PSRs:

- **[PSR-1](https://www.php-fig.org/psr/psr-1/)**: Basic coding standards, ensuring a consistent code style.
- **[PSR-2](https://www.php-fig.org/psr/psr-2/)**: Coding style guide, which helps maintain a consistent structure across files. (**deprecated - view PSR-12**)
- **[PSR-4](https://www.php-fig.org/psr/psr-4/)**: Autoloading standard using Composer, mapping namespaces to file directories.
- **[PSR-11](https://www.php-fig.org/psr/psr-11/)**: Container interface for dependency injection, enabling a service-based architecture.
  **[PSR-12](https://www.php-fig.org/psr/psr-12/)**: Coding style guide, which helps maintain a consistent structure across files.

## Directory Structure

The project follows a clear structure that separates concerns into Controllers, Views, and configuration:

```bash
/project-root
│
├───app
│   ├───Config
│   │       BundleRegistration.php       # Handles the registration of static bundles (CSS, JS, etc.)
│   │       Registration.php             # Registers application services and controllers
│   │       
│   ├───Controllers                      # Contains all application controllers
│   │       AboutController.php          # Controller for the About page
│   │       ApiController.php            # Handles general API endpoints
│   │       AuthController.php           # Handles authentication─related actions
│   │       ContactController.php        # Manages the Contact form
│   │       HomeController.php           # Handles the home and sandbox pages
│   │       UsersApiController.php       # API Controller for user─related endpoints
│   │       UsersController.php          # Controller for user management
│   │       
│   ├───Models                           # Contains the application's models
│   │       ContactModel.php             # Model representing the contact form data
│   │       UserModel.php                # Model representing a user
│   │       
│   └───Views                            # Contains the views rendered in response to requests
│       ├───About
│       │       index.php                # About page view
│       │       
│       ├───Auth
│       │       login.php                # Login page view
│       │       
│       ├───Contact
│       │       index.php                # Contact form view
│       │       
│       ├───Home
│       │       docs.php                 # API documentation view
│       │       index.php                # Home page view
│       │       sandbox.php              # Sandbox view for testing API requests
│       │       sections.php             # View demonstrating the use of sections in layouts
│       │       
│       ├───Shared
│       │       layout.php               # Shared layout file for consistent structure
│       │       
│       └───Users
│               index.php                # View listing all users
│               show.php                 # View showing user details
│               
├───nginx                                # NGINX configuration for running the application
│       Dockerfile                       # Dockerfile to build NGINX environment
│       nginx.conf                       # NGINX configuration file
│       
├───public                               # Publicly accessible directory (web root)
│   │   index.php                        # Application entry point
│   │   
│   └───assets                           # Static assets
│           scripts.js                   # Custom JS for the application
│           styles.css                   # Custom styles for the application
│           
├───src                                  # Core application logic
│   ├───Container
│   │       DIContainer.php              # Dependency Injection Container
│   │       
│   ├───Controller
│   │       ApiBaseController.php        # Base controller for API endpoints
│   │       BaseController.php           # Main base controller for all standard controllers
│   │       
│   ├───Core
│   │       Application.php              # Main Application class that bootstraps the framework
│   │       BundleManager.php            # Manages static asset bundles (CSS, JS)
│   │       SessionManager.php           # Manages session functionality
│   │       
│   └───Router
│           Router.php                   # Router class for handling routes
│           
├───tests                                # Unit and integration tests for the framework
│   │   phpunit.xml                      # PHPUnit configuration file
│   │   
│   ├───Integration
│   │       FullAppTest.php              # Integration test covering full application behavior
│   │       HomeControllerTest.php       # Integration test for HomeController
│   │       
│   └───Unit
│           DIContainerTest.php          # Unit test for DI Container
│           RouterTest.php               # Unit test for Router
│           
├───vendor                               # Composer dependencies
├──composer.json                         # Composer configuration file
└──README.md                             # Project documentation

```

## What We Will Build

We will build a **minimalistic PHP framework** with the following components:

1. **Routing**: A custom router that maps incoming HTTP requests (GET, POST, etc.) to controllers and actions.
2. **Dependency Injection**: A basic IoC container (compliant with PSR-11) that resolves controller dependencies.
3. **MVC Pattern**: We will adhere to the MVC (Model-View-Controller) architecture, separating logic into Controllers and Views.
4. **PSR-4 Autoloading**: The project structure is set up for PSR-4 compliant autoloading using Composer.

### Key Features:

1. **Routing**: 
   - Custom router that supports dynamic routes with parameters.
   - Automatic route registration using attributes.
   - Handles static files and supports trailing slashes.

2. **Dependency Injection (DI)**:
   - Custom DI container to manage class dependencies.
   - Services and controllers are registered through the `Registration.php` file.

3. **Static Bundling**:
   - Bundles CSS, JS, and other assets using the `BundleManager` class.

4. **Session Management**:
   - Custom session handling using the `SessionManager`.

### API Documentation

The project also includes a simple REST API for user management, located at `/api/v1/users/`. You can find detailed documentation for this API in the [API Docs](./app/Views/Home/docs.php) view.

### Example Code

The example below is a simple **HomeController** that renders a view:

#### `app/Controllers/HomeController.php`

```php
<?php

namespace GuiBranco\PocMvc\App\Controllers;

use GuiBranco\PocMvc\Src\Controller\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        return $this->view('home', ['title' => 'Home Page']);
    }

    public function about()
    {
        return $this->view('home', ['title' => 'About Us']);
    }
}
```

The **front controller** dispatches incoming HTTP requests:

#### `public/index.php`

```php
<?php

use GuiBranco\PocMvc\App\Config\BundleRegistration;
use GuiBranco\PocMvc\App\Config\Registration;
use GuiBranco\PocMvc\Src\Core\Application;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Application(); // The core/main application class.
$registration = new Registration($app); // The user-defined registration class. Register routes, add services (DI/IoC), register API controllers.
$registration->addServices();
$registration->registerRoutes();
$registration->registerApiControllers();
$bundleRegistration = new BundleRegistration(); // The user-defined bundle registration. Use this to register assets in bundles to be rendered in the views.
$bundleRegistration->registerBundles();
$app->run(); // Run the application. Accept requests.

```

Finally, the **IoC container** and **Router registration** resolves the controller dependencies and the routes:

#### `app/Config/Registration.php`

```php
<?php

use GuiBranco\PocMvc\App\Controllers\HomeController;
use GuiBranco\PocMvc\Src\Container\DIContainer;
use GuiBranco\PocMvc\Src\Router\Router;

$container = new DIContainer();
$container->set(HomeController::class, function() { return new HomeController(); });

$router = new Router();
$router->add('GET', '/', [HomeController::class, 'index']);
$router->add('GET', '/about', [HomeController::class, 'about']);
```

## Requirements

- PHP 8.3 or higher (it can work with 7.4 or higher, but not tested).
- Composer (for dependency management)

## Getting Started

1. **Clone the Repository**:

   ```bash
   git clone https://github.com/GuilhermeStracini/poc-php-mvc.git
   cd poc-php-mvc
   ```

2. **Install Dependencies**:
   Run the following command to install the required dependencies via Composer:

   ```bash
   composer install
   ```

3. **Run the Application**:
   Start the built-in PHP server:

   ```bash
   php -S localhost:8000 -t public
   ```

4. **Access the Application**:
   Open your browser and navigate to:
   - [http://localhost:8000](http://localhost:8000) to view the home page.
   - [http://localhost:8000/about](http://localhost:8000/about) to view the about page.

## Expected Outcome

Once the application is running:

- Visiting `/` will display the **Home Page**.
- Visiting `/about` will display the **About Us** page.
- Both pages will use the **MVC pattern**, with the controller handling logic and views rendering the HTML.
- The application will follow **PSR-1**, **PSR-2/PSR-12**, **PSR-4**, and **PSR-11**, ensuring a scalable and maintainable codebase.

## Deploy to Vercel

You can deploy this project to Vercel with just one click! Vercel is a great platform for hosting PHP projects with zero configuration. Follow the steps below to deploy this example to Vercel.

[![Deploy to Vercel](https://vercel.com/button)](https://vercel.com/import/project?template=https://github.com/GuilhermeStracini/poc-php-mvc)

### Steps to Deploy

1. **Click the "Deploy to Vercel" button** above or go to [Vercel Import](https://vercel.com/import).
2. **Import your GitHub repository** by linking your GitHub account.
3. **Configure your project settings**:
    - Ensure the `Root Directory` is set to the project root (leave this blank).
    - Set the **Output Directory** to `public/`, since this is where the `index.php` resides.
4. **Deploy your project** and your PHP MVC framework will be live in a few seconds.

### Vercel Configuration

To ensure that Vercel handles your PHP app properly, you need to add a `vercel.json` configuration file in your project’s root directory:

#### `vercel.json`

```json
{
  "version": 2,
  "builds": [
    {
      "src": "public/index.php",
      "use": "@vercel/php"
    }
  ],
  "routes": [
    {
      "src": "/(.*)",
      "dest": "public/index.php"
    }
  ]
}
```

## Running the Project with Docker

This project can be run using Docker, and we provide two variants for running the PHP 8.3 environment:

- **Apache**
- **NGINX** with PHP-FPM

### Prerequisites

Ensure you have Docker and Docker Compose installed on your machine.

### Running with Apache

To run the project with PHP 8.3 and Apache, follow these steps:

1. **Build and run the containers**:

   ```bash
   docker compose up --build
   ```

2. **Access the application**:
   After the container is up and running, you can access the application in your browser at [http://localhost:8080](http://localhost:8080).

3. **Stopping the containers**:
   To stop the running containers, use:

   ```bash
   docker compose down
   ```

### Running with NGINX and PHP-FPM

To run the project with PHP 8.3, NGINX, and PHP-FPM, follow these steps:

1. **Ensure you have the `nginx.conf` file in the `nginx` directory**:
   The NGINX configuration is required for routing PHP requests to PHP-FPM. The file should be located at `nginx/nginx.conf`.

2. **Build and run the containers**:

   ```bash
   docker compose -f docker-compose-nginx.yml up --build
   ```

3. **Access the application**:
   Once the containers are up and running, access the application in your browser at [http://localhost:8080](http://localhost:8080).

4. **Stopping the containers**:
   To stop the running containers, use:

   ```bash
   docker compose -f docker-compose-nginx.yml down
   ```

## Tests

Unit and integration tests are provided to validate the core functionality. Run the tests with PHPUnit:

```bash
vendor/bin/phpunit tests
```

Tests are organized into two directories:

- `tests/Unit`: Contains unit tests for individual components.
- `tests/Integration`: Contains integration tests for full application functionality.
