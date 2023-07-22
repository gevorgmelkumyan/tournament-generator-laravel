# Tournament Generator API

## Getting Started

To set up the project on your local machine, follow these steps:

1. Clone the repository:

```bash
git clone https://github.com/gevorgmelkumyan/tournament-generator-laravel.git
```

2. Run the Makefile `build` command:

```bash
make build
```

3. Start the Docker environment using the Makefile `run` command:

```bash
make run
```

## Makefile Commands

The Makefile provides several useful commands for interacting with the Docker environment:

- `make purge env`: Remove both `.env`s
- `make build`: Build the Docker containers
- `make run`: Start the Docker containers
- `make stop`: Stop the Docker containers
- `make down`: Remove the Docker containers, volumes, and images
- `make server`: Access the server container's bash shell
- `make mysql`: Access the mysql container's bash shell
- `make test`: Run tests
