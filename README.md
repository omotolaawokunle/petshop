```markdown
# Petshop Project Setup Guide

This guide will help you set up the `petshop` project. It includes steps for setting up the project, seeding the database, and generating JWT token keys for encryption and decryption.

## Prerequisites

- PHP (>= 8.3)
- Composer
- MySQL
- Git

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/omotolaawokunle/petshop.git
   ```

2. Navigate to the project directory:

   ```bash
   cd petshop
   ```

3. Install PHP dependencies:

   ```bash
   composer install
   ```

4. Create a copy of the `.env.example` file and name it `.env`:

   ```bash
   cp .env.example .env
   ```

5. Generate an application key:

   ```bash
   php artisan key:generate
   ```

## Database Setup

1. Create a new MySQL database for the project.

2. Update the `.env` file with your database credentials:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   ```

3. Migrate the database tables:

   ```bash
   php artisan migrate
   ```

4. Seed the database with sample data:

   ```bash
   php artisan db:seed
   ```

## JWT Token Key Generation

1. Generate the private and public keys for JWT token encryption and decryption:

   ```bash
   ssh-keygen -t rsa -b 4096 -m PEM -f storage/private.key
   ssh-keygen -f storage/private.key -e -m PKCS8 > storage/public.key
   ```

   This will generate `private.key` and `public.key` in the `storage` directory.

## Running the Project

1. Serve the application:

   ```bash
   php artisan serve
   ```

2. Access the application in your web browser at `http://127.0.0.1:8000`.
3. Access the documentation at `http://127.0.0.1:8000/api/v1/docs`.

## Additional Information

- You can customize other settings in the `.env` file, such as mail configuration, cache, and more.

- For more information on the project and available features, refer to the project's documentation.

## Credits

- [Petshop Project](https://github.com/omotolaawokunle/petshop)

## License

This project is open-source and available under the [MIT License](LICENSE).
