
# PHP_Laravel12_GitScrum

## Overview

GitScrum is an open-source **Scrum & Agile project management tool** built on **Laravel**. It helps teams manage **projects, sprints, and issues** efficiently using Agile methodologies. The application supports **OAuth authentication** and integrates with **GitHub, GitLab, and Bitbucket**, making it ideal for modern development workflows.

GitScrum is suitable for:

* Agile & Scrum teams
* Software development teams
* Project managers
* Open-source contributors

---

## Features

* Scrum & Agile project management
* Project, Sprint, and Issue tracking
* OAuth authentication (GitHub, GitLab, Bitbucket)
* Multi-language support
* Team collaboration
* Sprint progress visualization
* Git repository integration
* Laravel-based modular architecture
* Optional Docker support

---

---

## STEP 1: System Requirements

Ensure your system meets the following requirements:

* PHP: >= 7.1 (Recommended: PHP 7.4)
* Composer
* Web Server: Apache or Nginx
* Database: MySQL or MariaDB
* Git
* Docker (Optional)

### Check Installed Versions

```bash
php -v
composer -v
git --version
```

---

## STEP 2: Get the Project Source Code

### Option A: Install via Composer (Recommended)

```bash
composer create-project gitscrum-community-edition/laravel-gitscrum --stability=stable --keep-vcs
```

---

### Option B: Clone from GitHub

```bash
git clone https://github.com/GitScrum-Community/laravel-gitscrum.git
cd laravel-gitscrum
composer update
composer run-script post-root-package-install
```

---

## STEP 3: Environment Configuration (.env)

### Set Application URL

Open `.env` and update:

```env
APP_URL=http://localhost/gitscrum
```

⚠️ Protocol (http or https) is mandatory

---

### Set Application Language

Supported languages:

```
en | zh | zh_cn | ru | de | es | pt | it | id | fr | hu
```

Example:

```env
APP_LANG=en
```

---

## STEP 4: Database Setup

### 1️⃣ Create Database

Create a MySQL database manually:

```sql
CREATE DATABASE gitscrum;
```

---

### 2️⃣ Update Database Credentials in .env

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gitscrum
DB_USERNAME=root
DB_PASSWORD=
```

---

### 3️⃣ Run Migrations & Seeder

```bash
php artisan migrate
php artisan db:seed --class=SettingSeeder
```

✔ Database tables created
✔ Default settings inserted

---

## STEP 5: Generate Application Key

```bash
php artisan key:generate
```

---

## STEP 6: GitHub OAuth Configuration

Create GitHub OAuth Application:

* Application Name: gitscrum
* Homepage URL: Same as APP_URL
* Authorization Callback URL:

```
http://APP_URL/auth/provider/github/callback
```

Add to `.env`:

```env
GITHUB_CLIENT_ID=your_client_id
GITHUB_CLIENT_SECRET=your_client_secret
```

---

## STEP 7: GitLab OAuth Configuration (Optional)

Create GitLab Application:

* Name: gitscrum
* Redirect URI:

```
http://APP_URL/auth/provider/gitlab/callback
```

* Scopes:

```
api, read_user
```

Add to `.env`:

```env
GITLAB_KEY=your_application_id
GITLAB_SECRET=your_secret
GITLAB_INSTANCE_URI=https://gitlab.com/
```

---

## STEP 8: Bitbucket OAuth Configuration (Optional)

Create Bitbucket OAuth Consumer:

* Name: gitscrum
* Callback URL:

```
http://APP_URL/auth/provider/bitbucket/callback
```

* URL:

```
http://APP_URL
```

* Permissions:

```
repositories (write)
issues (write)
```

* ❌ Uncheck "This is a private consumer"

Add to `.env`:

```env
BITBUCKET_CLIENT_ID=your_key
BITBUCKET_CLIENT_SECRET=your_secret
```

---

## STEP 9: Proxy Configuration (Optional)

If your server uses a proxy, configure the following in `.env`:

```env
PROXY_PORT=
PROXY_METHOD=
PROXY_SERVER=
PROXY_USER=
PROXY_PASS=
```

---

## STEP 10: Run the Application

### ▶ Using Laravel Built-in Server

```bash
php artisan serve
```

Open in browser:

```
http://127.0.0.1:8000
```

---

### ▶ Using Apache / Nginx

Set your document root to:

```
/laravel-gitscrum/public
```

---

## STEP 11: Login & Verify

✔ Application loads successfully
✔ Login/Register using GitHub, GitLab, or Bitbucket
✔ Create projects, sprints, and issues

---

## GitScrum Screenshots

<img width="975" height="845" alt="image" src="https://github.com/user-attachments/assets/8192a510-747a-4ea3-af0b-3aa003258a08" />

<img width="975" height="539" alt="image" src="https://github.com/user-attachments/assets/2a830bf6-5ece-4284-8678-5cdcee0b7920" />

<img width="975" height="520" alt="image" src="https://github.com/user-attachments/assets/720d03f5-96bd-4d0e-8a50-b518214af9d9" />

<img width="975" height="611" alt="image" src="https://github.com/user-attachments/assets/df787826-09c7-4ab7-8244-2940bdc3725c" />




