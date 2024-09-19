## :rocket: Base Swoole Application

Welcome to the base [Swoole](https://github.com/swoole/swoole-src) application repository. 

This repository contains a basic application built using the Swoole extension in PHP 8.3. It includes an HttpServer and a WebSocketServer. 

The application demonstrates how to implement a simple router, manage routes, and handle WebSocket connections and Coroutines efficiently.

### Getting Started

Follow the steps below to set up and run the application.

#### Requirements

- Docker: [Setting Up Docker on Ubuntu](https://github.com/danieltrolezi/laravel-app/wiki/01.-Setting-Up-Docker-on-Ubuntu)

#### Running the Environment

1. **Build Images:**
```sh
docker compose build
```

2. **Run the Containers:**
```sh
docker compose up -d
```

3. **Access the Examples:**
```
http://localhost/examples
```

Explore the examples to see how to leverage Swoole for high-performance network programming. 

#### Servers

| Application | Type     | Address                                        |
|-------------|----------|------------------------------------------------|
| Interface   | Nginx/JS | [http://localhost:80](http://localhost:80)     |
| API         | Swoole   | [http://localhost:8080](http://localhost:8080) |
| WebSocket   | Swoole   | [http://localhost:9090](http://localhost:9090) |

### Application Reference

| Description                    | Location                           |
|--------------------------------|------------------------------------|
| Docker's Entrypoint            | `/docker/entrypoint.sh`            |
| Server's Starting point        | `/docker/supervisord/*`            |
| Application's Bootstrap        | `/bootstrap/app.php`               |
| HTTP Server                    | `/app/Servers/HttpServer.php`      |
| Websocket Server               | `/app/Servers/WebSocketServer.php` |
| Router                         | `/app/Router.php`                  |
| Examples                       | `/public/examples/*`               |
