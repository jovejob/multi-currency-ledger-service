# Ledger CQRS Project

This project is a Symfony application configured with Docker and PostgreSQL for the database. Below are the steps to set up the application and run migrations.

## Setup Instructions

### 1. **Clone the Repository**

If you haven't cloned the repository yet, do so by running:

```bash
git clone <repository_url>
cd <project_folder>
```

### 2. **Configure Database Connection**

Ensure that the Symfony application is connecting to the PostgreSQL database running in the Docker container.

In your Symfony project, edit the `.env` or `.env.local` file and update the `DATABASE_URL` to point to the PostgreSQL service defined in `docker-compose.yml`:

```env
DATABASE_URL="postgresql://postgres:mysecretpassword@symfony_db:5432/ledger_db?serverVersion=12&charset=utf8"
```

- **`postgres`** is the username for PostgreSQL.
- **`mysecretpassword`** is the password for the PostgreSQL user.
- **`symfony_db`** is the name of the PostgreSQL service from `docker-compose.yml`.
- **`ledger_db`** is the name of the PostgreSQL database.

### 3. **Build the Docker Containers**

Rebuild the Docker containers to apply the environment changes:

```bash
docker-compose down --volumes
docker-compose up --build
```

- The `--volumes` flag ensures that any existing volumes are removed and reset, giving you a fresh start.

### 4. **Check Database Tables**

To ensure the database is set up correctly, you can connect to the PostgreSQL container and list the tables:

```bash
docker-compose exec symfony_db psql -U postgres
```

Then, within the `psql` prompt, run:

```sql
\dt
```

This will list the tables in the database. If no tables are present, proceed to run the migrations.

### 5. **Run Migrations**

Once the database is set up, run the migrations to create the necessary tables:

```bash
docker-compose exec symfony_app bash
php bin/console doctrine:migrations:migrate
```

This will apply any pending migrations and create the required tables in the `ledger_db` database.

### 6. **Access the Symfony Application**

Once the containers are running, you can access the Symfony application at:

```bash
http://localhost:8000
```

This will load the Symfony application running inside the Docker container.

You can now proceed to test the API or interact with the application as needed.

---

Running Tests

To run the tests and automatically load the fixtures before executing the tests, use the following Composer command:

```bash
composer test-with-fixtures
```

This command will load the test fixtures and then run your PHPUnit tests.

---

## Troubleshooting

### **Error: `connection to server at "localhost" (127.0.0.1), port 5432 failed`**

If you encounter an error like:

```
An exception occurred in the driver: SQLSTATE[08006] [7] connection to server at "localhost" (127.0.0.1), port 5432 failed: Connection refused
```

This means the application is trying to connect to `localhost` inside the container. To fix this, make sure that the `DATABASE_URL` in `.env` or `.env.local` is set to:

```env
DATABASE_URL="postgresql://postgres:mysecretpassword@symfony_db:5432/ledger_db?serverVersion=12&charset=utf8"
```

### **Unable to Access Database**

If you're unable to connect to the PostgreSQL database or access tables, verify that the `symfony_db` container is running:

```bash
docker ps
```

Ensure the database service is up and the PostgreSQL container is mapped to the correct port.

---

## Docker Commands Recap

Here are the important Docker commands used in this process:

1. **Build and start containers:**

```bash
docker-compose up --build
```

2. **Stop and remove containers, networks, and volumes:**

```bash
docker-compose down --volumes
```

3. **Access the Symfony container:**

```bash
docker-compose exec symfony_app bash
```

4. **Access the PostgreSQL container:**

```bash
docker-compose exec symfony_db psql -U postgres
```

5. **Run migrations in Symfony:**

```bash
php bin/console doctrine:migrations:migrate
```

---

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
