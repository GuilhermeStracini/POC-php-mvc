# PoC PHP MVC Framework with Custom Router, IoC/DI Container (PSR-compliant)

This project is a **Proof of Concept (PoC)** for a custom-built **PHP MVC framework** that adheres to several PHP-FIG standards, including PSR-1, PSR-2/PSR-12, PSR-4, and PSR-11. It demonstrates how to build a lightweight MVC structure with custom routing, dependency injection (IoC/DI), session management, and static-asset bundling, without relying on a full-blown framework.

## Table of Contents

- [PoC PHP MVC Framework with Custom Router, IoC/DI Container (PSR-compliant)](#poc-php-mvc-framework-with-custom-router-iocdi-container-psr-compliant)
  - [Table of Contents](#table-of-contents)
  - [Introduction](#introduction)
  - [PSRs in Use](#psrs-in-use)
  - [Directory Structure](#directory-structure)
  - [Architecture Overview](#architecture-overview)
    - [`Application`](#application)
    - [`Router`](#router)
    - [`DIContainer`](#dicontainer)
    - [`BaseController` / `ApiBaseController`](#basecontroller--apibasecontroller)
    - [`BundleManager` and `SessionManager`](#bundlemanager-and-sessionmanager)
  - [Registered Routes](#registered-routes)
    - [HTML (SSR) Routes](#html-ssr-routes)
    - [API Routes](#api-routes)
  - [Example Code](#example-code)
    - [`app/Controllers/HomeController.php`](#appcontrollershomecontrollerphp)
    - [`app/Config/Registration.php`](#appconfigregistrationphp)
    - [`public/index.php`](#publicindexphp)
  - [Requirements](#requirements)
  - [Getting Started](#getting-started)
  - [Environment Variables](#environment-variables)
  - [Running the Project with Docker](#running-the-project-with-docker)
    - [Prerequisites](#prerequisites)
    - [Running with Apache](#running-with-apache)
    - [Running with NGINX and PHP-FPM](#running-with-nginx-and-php-fpm)
  - [Deploy to Vercel](#deploy-to-vercel)
    - [Steps to Deploy](#steps-to-deploy)
    - [Vercel Configuration](#vercel-configuration)
  - [Continuous Integration](#continuous-integration)
  - [Tests](#tests)
  - [Known Limitations](#known-limitations)
  - [License](#license)

## Introduction

In this PoC, we build a small MVC application from scratch using **PSR-compliant components**. This includes a custom **router** (with base-path support, dynamic `{param}` segments, static-file serving, and trailing-slash redirects), a basic **dependency injection container** (constructor autowiring via Reflection), an organized **controller/view structure** with layouts and sections, and a couple of RESTful JSON API controllers.

The goal is to demonstrate **clean, maintainable architecture** by implementing industry-standard interfaces and coding practices, using Composer for PSR-4 autoloading.

## PSRs in Use

The project complies with the following PSRs:

- **[PSR-1](https://www.php-fig.org/psr/psr-1/)**: Basic coding standards, ensuring a consistent code style.
- **[PSR-2](https://www.php-fig.org/psr/psr-2/)**: Coding style guide (**deprecated - superseded by PSR-12**).
- **[PSR-4](https://www.php-fig.org/psr/psr-4/)**: Autoloading standard using Composer, mapping namespaces to file directories.
- **[PSR-11](https://www.php-fig.org/psr/psr-11/)**: Container interface for dependency injection (`DIContainer` implements `Psr\Container\ContainerInterface`).
- **[PSR-12](https://www.php-fig.org/psr/psr-12/)**: Extended coding style guide, enforced via PHP CS Fixer (see [Continuous Integration](#continuous-integration)).

## Directory Structure

```bash
poc-php-mvc/
├── app/
│   ├── Config/
│   │   ├── BundleRegistration.php     # Registers static asset bundles (CSS/JS)
│   │   └── Registration.php           # Registers services (DI) and routes
│   ├── Controllers/
│   │   ├── AboutController.php        # About page
│   │   ├── ApiController.php          # GET /api/v1 -> dumps $_SERVER as JSON
│   │   ├── AuthController.php         # Login/logout against an in-memory user list
│   │   ├── ContactController.php      # Contact form (show + submit)
│   │   ├── HomeController.php         # Home, docs, sandbox, sections pages
│   │   ├── UsersApiController.php     # REST API for users (index/show/create/update/destroy)
│   │   └── UsersController.php        # Server-rendered user list/detail pages
│   ├── Models/
│   │   ├── ContactModel.php           # Contact form data + validation
│   │   └── UserModel.php              # In-memory user data
│   └── Views/
│       ├── About/index.php
│       ├── Auth/login.php
│       ├── Contact/index.php
│       ├── Home/{index,docs,sandbox,sections}.php
│       ├── Shared/layout.php          # Shared layout wrapping all views
│       └── Users/{index,show}.php
├── nginx/
│   ├── Dockerfile                     # PHP-FPM image (php:8.3-fpm) used by the *-prod compose file
│   └── nginx.conf                     # Proxies .php requests to php-fpm:9000
├── public/
│   ├── assets/{scripts.js,styles.css}
│   └── index.php                      # Front controller / application entry point
├── src/
│   ├── Container/DIContainer.php      # PSR-11 DI container with constructor autowiring
│   ├── Controller/
│   │   ├── ApiBaseController.php      # Base for JSON API controllers (view() -> json())
│   │   └── BaseController.php         # Base for HTML controllers (view/layout/sections)
│   ├── Core/
│   │   ├── Application.php            # Bootstraps container + router, run()/error handling
│   │   ├── BundleManager.php          # Static registry of named asset bundles
│   │   ├── HttpException.php          # Exception carrying an HTTP status code
│   │   └── SessionManager.php         # Thin wrapper around PHP sessions
│   └── Router/Router.php              # Route matching, dispatch, API auto-registration
├── tests/
│   ├── phpunit.xml                    # PHPUnit configuration (unit + integration suites)
│   ├── Integration/{FullAppTest,HomeControllerTest}.php
│   └── Unit/{DIContainerTest,RouterTest}.php
├── Dockerfile / Dockerfile-dev         # Apache images (php:8.5-apache)
├── docker-compose*.yml                 # See "Running the Project with Docker"
├── vercel.json                         # Vercel deployment config
├── composer.json
└── README.md
```

## Architecture Overview

### `Application`

`src/Core/Application.php` is the composition root. Its constructor takes an optional `$basePath` (default `''`) and `$publicDirBasePath` (default `'public/'`), and builds a `DIContainer` and a `Router` wired to that container and those paths. `run()` dispatches the current request (`$_SERVER['REQUEST_METHOD']`/`REQUEST_URI`) through the router, echoes the response, and centralizes error handling:

- An `HttpException` sets the matching HTTP status code and echoes its message (used for 404s, for example).
- Any other `\Exception` returns HTTP 500. When `getenv('APP_ENV') === 'production'`, the response body is a generic `500 Internal Server Error`; otherwise it includes the message, file, line, and stack trace — see [Environment Variables](#environment-variables).

### `Router`

`src/Router/Router.php` supports:

- `add(string $method, string $path, callable $handler)` — register a route; `$path` may contain `{param}` placeholders.
- `registerApiController($controller, string $prefix = '/api/v1')` — reflects over a controller's own public methods and auto-registers RESTful routes using a fixed verb map (`index`/`list`→`GET`, `show`/`get`→`GET /{id}`, `create`/`post`→`POST`, `update`/`put`→`PUT /{id}`, `delete`→`DELETE /{id}`, etc.). Only methods whose **name** matches one of those verbs are registered — see the note on `UsersApiController::destroy()` in [Known Limitations](#known-limitations).
- `dispatch(string $method, string $uri)` — strips the configured base path, serves a static file directly if one exists under `publicDirBasePath`, 301-redirects bare GET requests to a trailing-slash form, matches the remaining routes by regex, and throws `HttpException` (404) if nothing matches.
- `hasRoute(string $method, string $uri): bool` — check whether a route is registered without dispatching it.

### `DIContainer`

`src/Container/DIContainer.php` implements `Psr\Container\ContainerInterface`:

- `set(string $id, callable $service)` — register a factory closure for a service id.
- `get(string $id)` — resolve a service; if the registered value is callable it's invoked with the container itself, otherwise the container attempts to autowire the class's constructor via `ReflectionClass` (recursively resolving typed parameters).
- `has(string $id): bool`.

### `BaseController` / `ApiBaseController`

`src/Controller/BaseController.php` is the base for HTML controllers. Its constructor takes a `$viewsPath` and derives a view search order of `{viewsPath}/{ControllerName}/`, `{viewsPath}/Shared/`, then `{viewsPath}/` (the `Controller` suffix is stripped from the calling class name). Key protected methods:

- `view(string $viewName, array $data = [], ?string $layout = 'layout'): void` — renders a view (optionally wrapped in a layout) by including the PHP template and buffering its output; throws if no matching file is found in the search paths.
- `partialView(string $viewName, array $data = []): void` — same as `view()` but without a layout.
- `redirect(string $url): void`, `json($data): void` — send a `Location` header, or a JSON response.
- `addGlobal(string $key, $value): void` — make a variable available to every subsequent view.
- `startSection($name)` / `endSection()` / `renderSection($name)` — output-buffering "sections" for use inside layouts.
- `renderBundles($bundleName)` — emits `<link>`/`<script>` tags for a bundle registered via `BundleManager`.

`src/Controller/ApiBaseController.php` extends `BaseController` but overrides `view()` to always call `json($data)` regardless of the view name/layout arguments — i.e. any controller extending it is JSON-only by construction. `ApiController` uses this base directly; `UsersApiController` extends it too but implements its own `jsonResponse()` helper for status codes instead of using the inherited `json()`.

### `BundleManager` and `SessionManager`

`src/Core/BundleManager.php` is a static registry: `register($bundleName, $assets)`, `getBundle($bundleName)`, `getAllBundles()` — used by `BundleRegistration` and `renderBundles()`.

`src/Core/SessionManager.php` is a static, thin wrapper over native PHP sessions: `start()`, `set()`, `get()`, `has()`, `remove()`, `destroy()`, `regenerate()`. Used by `AuthController` (login/logout) and `UsersController` (gating `/users` behind a logged-in session).

## Registered Routes

All routes below are registered in `app/Config/Registration.php`. Remember that `public/index.php` constructs `new Application('/poc-php-mvc')`, so as shipped every path is actually served under the `/poc-php-mvc` prefix (e.g. `/poc-php-mvc/about`) — see [Getting Started](#getting-started) if you want to run it at the domain root instead.

### HTML (SSR) Routes

| Method | Path              | Controller::method                  |
| ------ | ----------------- | ------------------------------------ |
| GET    | `/`                | `HomeController::index`             |
| GET    | `/docs`            | `HomeController::docs`               |
| GET    | `/sandbox`         | `HomeController::sandbox`            |
| GET    | `/sections`        | `HomeController::sections`           |
| GET    | `/login`           | `AuthController::login`              |
| POST   | `/login`           | `AuthController::login`              |
| GET    | `/logout`          | `AuthController::logout`             |
| GET    | `/about`           | `AboutController::index`             |
| GET    | `/contact`         | `ContactController::showForm`        |
| POST   | `/contact/submit`  | `ContactController::handleFormSubmission` |
| GET    | `/users`           | `UsersController::index` (requires a logged-in session) |
| GET    | `/users/{id}`      | `UsersController::show` (requires a logged-in session)  |
| GET    | `/api/v1`          | `ApiController::index`               |

### API Routes

Auto-registered via `$router->registerApiController(UsersApiController::class)`:

| Method | Path                 | Controller::method          |
| ------ | -------------------- | ---------------------------- |
| GET    | `/api/v1/users`       | `UsersApiController::index`  |
| GET    | `/api/v1/users/{id}`  | `UsersApiController::show`    |
| POST   | `/api/v1/users`       | `UsersApiController::create`  |
| PUT    | `/api/v1/users/{id}`  | `UsersApiController::update`  |

> `UsersApiController::destroy()` exists but is **not** reachable — see [Known Limitations](#known-limitations).

## Example Code

### `app/Controllers/HomeController.php`

```php
<?php

namespace GuiBranco\PocMvc\App\Controllers;

use GuiBranco\PocMvc\Src\Controller\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        return $this->view('index', ['title' => 'Home Page']);
    }

    public function docs()
    {
        return $this->view('docs', ['title' => 'Documentation']);
    }

    public function sandbox()
    {
        return $this->view('sandbox', ['title' => 'Sandbox']);
    }

    public function sections()
    {
        return $this->view('sections', ['title' => 'Sections']);
    }
}
```

### `app/Config/Registration.php`

```php
<?php

namespace GuiBranco\PocMvc\App\Config;

use GuiBranco\PocMvc\App\Controllers\HomeController;
use GuiBranco\PocMvc\Src\Core\Application;

class Registration
{
    protected $router;
    protected $container;

    public function __construct(Application $app)
    {
        $this->router = $app->getRouter();
        $this->container = $app->getContainer();
    }

    public function addServices(): void
    {
        $viewsPath = __DIR__ . '/../Views';
        $this->container->set(HomeController::class, fn ($c) => new HomeController($viewsPath));
        // ... other controllers registered the same way
    }

    public function registerRoutes(): void
    {
        $this->router->add('GET', '/', [$this->container->get(HomeController::class), 'index']);
        // ... remaining routes, see the full table above
    }
}
```

### `public/index.php`

The **front controller** wires everything together and dispatches the request:

```php
<?php

use GuiBranco\PocMvc\App\Config\BundleRegistration;
use GuiBranco\PocMvc\App\Config\Registration;
use GuiBranco\PocMvc\Src\Core\Application;

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Application('/poc-php-mvc'); // base path the app is mounted under
$registration = new Registration($app);
$registration->addServices();          // register controllers in the DI container
$registration->registerRoutes();       // register HTML routes
$registration->registerApiControllers(); // auto-register the users REST API
$bundleRegistration = new BundleRegistration();
$bundleRegistration->registerBundles(); // register CSS/JS asset bundles
$app->run();                           // dispatch the request and print the response
```

## Requirements

- **PHP 8.3+** — the exact version isn't pinned in `composer.json`; different environments in this repo target different minor versions (CI runs PHP 8.4, the Apache Docker images use PHP 8.5, the NGINX/PHP-FPM image uses PHP 8.3). Anything **8.3 or later** should work; if you hit an incompatibility, check which environment introduced it.
- **Composer** (dependency management).
- The `sockets` PHP extension (installed automatically in CI; enable it locally if you see related errors).

## Getting Started

1. **Clone the repository**:

   ```bash
   git clone https://github.com/GuilhermeStracini/poc-php-mvc.git
   cd poc-php-mvc
   ```

2. **Install dependencies**:

   ```bash
   composer install
   ```

3. **Run the application** with PHP's built-in server:

   ```bash
   php -S localhost:8000 -t public
   ```

4. **Access the application.** `public/index.php` mounts the app under the `/poc-php-mvc` base path by default, so browse to:
   - [http://localhost:8000/poc-php-mvc/](http://localhost:8000/poc-php-mvc/) — Home page
   - [http://localhost:8000/poc-php-mvc/about/](http://localhost:8000/poc-php-mvc/about/) — About page
   - [http://localhost:8000/poc-php-mvc/api/v1/users/](http://localhost:8000/poc-php-mvc/api/v1/users/) — Users JSON API

   Note the trailing slash: any bare GET request without one is 301-redirected to the trailing-slash form by the router.

   If you'd rather serve the app at the domain root (no `/poc-php-mvc` prefix), change the `Application` constructor call in `public/index.php` to `new Application()` (empty base path).

5. **Log in** to access `/users` and `/users/{id}`: `AuthController` validates against an in-memory user list (`john` / `password123`, `jane` / `securePass`) — this is a PoC, replace it with real authentication before using this anywhere real.

## Environment Variables

The only environment variable the code reads directly is:

- `APP_ENV` — checked in `Application::run()`. Set it to `production` to hide stack traces and return a generic `500 Internal Server Error` body on uncaught exceptions; any other value (or unset) shows the full error message, file, line, and trace, which is convenient for local development but should **not** be used in production.

There is no `.env` file loader in this project (no `vlucas/phpdotenv` or similar dependency) — set environment variables through your shell, your web server configuration, or your Docker Compose file's `environment:` block.

## Running the Project with Docker

Five Compose files are provided, split by web server and by dev/prod intent:

| File | Web server | Image | Notes |
| --- | --- | --- | --- |
| `docker-compose.yml` | Apache | `Dockerfile` (PHP 8.5) | Baseline, no volume mount |
| `docker-compose-apache-prod.yml` | Apache | `Dockerfile` (PHP 8.5) | Same as above |
| `docker-compose-apache-dev.yml` | Apache | `Dockerfile-dev` (PHP 8.5) | Mounts the repo as a volume for live editing |
| `docker-compose-nginx-prod.yml` | NGINX + PHP-FPM | `nginx/Dockerfile` (PHP 8.3-FPM) | |
| `docker-compose-nginx-dev.yml` | NGINX + PHP-FPM | `nginx/Dockerfile-dev` | ⚠️ this file does not currently exist in `nginx/` — see [Known Limitations](#known-limitations) |

### Prerequisites

Ensure you have Docker and Docker Compose installed on your machine.

### Running with Apache

```bash
docker compose -f docker-compose-apache-dev.yml up --build
```

Then access the application at [http://localhost:8080](http://localhost:8080) (remember the `/poc-php-mvc` base path noted in [Getting Started](#getting-started)). Stop the containers with:

```bash
docker compose -f docker-compose-apache-dev.yml down
```

### Running with NGINX and PHP-FPM

```bash
docker compose -f docker-compose-nginx-prod.yml up --build
```

Then access the application at [http://localhost:8080](http://localhost:8080). Stop the containers with:

```bash
docker compose -f docker-compose-nginx-prod.yml down
```

## Deploy to Vercel

You can deploy this project to Vercel with one click. Vercel is a good fit for hosting PHP projects with minimal configuration.

[![Deploy to Vercel](https://vercel.com/button)](https://vercel.com/import/project?template=https://github.com/GuilhermeStracini/poc-php-mvc)

### Steps to Deploy

1. Click the **"Deploy to Vercel"** button above, or go to [Vercel Import](https://vercel.com/import).
2. Import your GitHub repository by linking your GitHub account.
3. Configure your project settings:
   - Leave the **Root Directory** as the project root.
   - Set the **Output Directory** to `public/`, since that's where `index.php` lives.
4. Deploy — the app will be live in a few seconds.

### Vercel Configuration

`vercel.json` routes every request to the front controller:

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

## Continuous Integration

GitHub Actions workflows in `.github/workflows/`:

- **`build.yml`** — on pull requests: installs dependencies with PHP 8.4 and runs the PHPUnit suite with coverage.
- **`deep-source.yml`** — on push to `main` and on pull requests: runs the same PHPUnit suite (coverage over `app`, `src`, `tests`) and uploads a test-coverage report to [DeepSource](https://deepsource.com/).
- **`deploy.yml`** — on push to `main`: computes a semantic version with GitVersion, builds a production-ready `poc-php-mvc/` bundle (production `.htaccess`, `composer install --no-dev`), FTP-uploads it, and cuts a GitHub Release.
- **`php-lint.yml`** — lints PHP syntax on push/PR.
- **`json-yaml-lint.yml`** — validates JSON/YAML files.
- **`infisical-secrets-check.yml`** — scans for leaked secrets.
- **`size-label.yml`** — labels pull requests by diff size.

Code style is enforced via PHP CS Fixer (see `.deepsource.toml`).

## Tests

Unit and integration tests validate the core framework and application behavior. Run the full suite with:

```bash
vendor/bin/phpunit --configuration tests/phpunit.xml
```

Tests are split into two suites, defined in `tests/phpunit.xml`:

- `tests/Unit` — `DIContainerTest`, `RouterTest`: exercise the DI container and router in isolation.
- `tests/Integration` — `FullAppTest`, `HomeControllerTest`: exercise a wired-up `Router` + `DIContainer` + real controller/view together.

Run a single suite with `--testsuite`:

```bash
vendor/bin/phpunit --configuration tests/phpunit.xml --testsuite unit
vendor/bin/phpunit --configuration tests/phpunit.xml --testsuite integration
```

## Known Limitations

This is a PoC, and a few rough edges are worth knowing about rather than papering over:

- **`UsersApiController::destroy()` is dead code.** `Router::registerApiController()` only auto-registers methods whose name matches its fixed verb map, and that map has no entry for `destroy` (only `delete`). As shipped, there is no route to trigger it — `DELETE /api/v1/users/{id}` returns 404.
- **`docker-compose-nginx-dev.yml` references `nginx/Dockerfile-dev`, which doesn't exist** in the `nginx/` directory (only `nginx/Dockerfile` is present). Use `docker-compose-nginx-prod.yml`, or add the missing Dockerfile, if you need the NGINX dev workflow.
- **`BundleRegistration` registers `/public/assets/style.css`** (singular), but the file on disk is `public/assets/styles.css` (plural) — the stylesheet bundle won't resolve to a real asset until one side is corrected.
- **No single pinned PHP version.** `composer.json` has no `"php"` constraint; CI, the Apache Docker images, and the NGINX/PHP-FPM Docker image each target a different PHP 8.x minor version (8.4, 8.5, and 8.3 respectively).
- **Authentication is a hardcoded, in-memory user list** in `AuthController` — fine for demoing the framework, not something to reuse as-is.

## License

MIT — see [LICENSE](./LICENSE).
