<img src="https://github.com/user-attachments/assets/826276a0-f9ee-4dc1-8bcc-d1db61efeb32" style="width: 240px;" />

# Gym Diary - Backend

Welcome to the Gym Diary backend repository! This project serves as the backend API for the Gym Diary service, a platform connecting trainers and trainees for personalized fitness sessions anytime, anywhere.  
日本語版READMEは[こちら](https://github.com/yuki00yossi/gymdiary-web/blob/main/README-ja.md)
## Overview

The backend of Gym Diary is responsible for handling the core functionalities such as user authentication, trainer-trainee matching, session scheduling, and progress tracking. Built with scalability and security in mind, this API serves as the backbone of the Gym Diary ecosystem.

This project uses [Laravel Sail](https://laravel.com/docs/11.x/sail) for local development, providing a Docker-based environment that simplifies the setup process.

## Features

- User authentication (sanctum-based)
- Trainer and trainee profile management
- Matching system for trainers and trainees
- Scheduling and session management
- Progress tracking for trainees
- Support for multiple fitness goals

## Installation

To set up the project locally for development or learning purposes using Laravel Sail, follow these steps:

1. Clone the repository:
    ```bash
    git clone https://github.com/yourusername/gym-diary-backend.git
    ```
2. Navigate into the project directory:
    ```bash
    cd gym-diary-backend
    ```
3. Install dependencies:
    ```bash
    composer install
    ```
4. Set up the environment variables (refer to `.env.example`):
    ```bash
    cp .env.example .env
    ```
5. Install Laravel Sail:
    ```bash
    php artisan sail:install
    ```
6. Start the development environment:
    ```bash
    ./vendor/bin/sail up
    ```

7. Run migrations:
    ```bash
    ./vendor/bin/sail artisan migrate
    ```

8. The development server will be available at `http://localhost`.

## Usage

Once the development environment is up and running, you can interact with the API at `http://localhost`. API route documentation will be provided soon.

## License

This project is licensed under a custom license. While you are free to fork and use this repository for **personal learning purposes**, any form of **commercial use or distribution is strictly prohibited** without prior written consent.

### Allowed:
- Forking for personal learning or development purposes.
- Contributions to the project.

### Not Allowed:
- Commercial use, such as integrating the code into a paid service or application.
- Redistributing the project or modified versions for commercial purposes.

For any licensing inquiries, please contact us directly.

## Contribution

Contributions are more than welcome! If you have ideas, improvements, or bug fixes, feel free to open an issue or submit a pull request. Let's build something great together!

1. Fork this repository.
2. Create your feature branch: `git checkout -b feature/your-feature`.
3. Commit your changes: `git commit -m 'Add your feature'`.
4. Push to the branch: `git push origin feature/your-feature`.
5. Open a pull request.

We appreciate all contributions, big or small, to help enhance the Gym Diary backend.

## Contact

For any inquiries or questions, feel free to reach out to us at yoshioka@studio-babe.jp
