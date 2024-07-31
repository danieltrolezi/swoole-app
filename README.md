# Base Swoole Application & Examples

Welcome to the base [Swoole](https://github.com/swoole/swoole-src) application repository. 
This project includes a fully functional HTTP server and WebSocket server, along with examples demonstrating how to use them effectively.

## Getting Started

Follow the steps below to set up and run the application.

### Requirements

- Docker: [Setting Up Docker on Ubuntu](https://github.com/danieltrolezi/laravel-app/wiki/01.-Setting-Up-Docker-on-Ubuntu)

### Running the Environment

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

### Servers

| Application | Type     | Address               |
|-------------|----------|-----------------------|
| HTML        | Nginx    | [http://localhost:80](http://localhost:80)     |
| API         | Swoole   | [http://localhost:8080](http://localhost:8080) |
| WebSocket   | Swoole   | [http://localhost:9090](http://localhost:9090) |

Explore the examples to see how to leverage Swoole for high-performance network programming. 
Happy coding!