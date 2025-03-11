# WiseTeam Setup Guide

## Prerequisites
Before setting up the project, ensure the target machine has the necessary dependencies installed:

- **PHP** (>=8.1, depending on your Symfony version)
- **Composer** (latest version)
- **Database** (MySQL)
- **Web Server** (Apache/Nginx)

## Installation Steps

### 1. Clone the Repository
Copy the WiseTeam project to the new machine using Git:

```bash
git clone https://github.com/KarolisGitHubas/WiseTeam.git
cd WiseTeam
```

### 2. Configure Database Credentials
Edit the `.env` (line 29) and `.env.test` (line 6) files to match your database credentials.

### 3. Install Dependencies
Run the following command to install all required dependencies:

```bash
composer2 install
```

### 4. Set Up the Database
Run database migrations and load initial data:

```bash
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

### 5. Access the Project
Once everything is set up, you can access your project at:

```
localhost/WiseTeam/public/
```

### 6. Test User Credentials
Use the following credentials to log in:

- **Username:** WiseTeam  
- **Password:** WiseTeam1

---

Your WiseTeam project is now set up and ready to use!

