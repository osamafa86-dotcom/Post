<!-- <p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>
<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p> -->


<h2 align="center">Devlo CMS Dashboard</h2>
<br>
<h2>📋 Table of Contents</h2>

- <a href="#-system-requirements">System Requirements</a>
- <a href="#-installation">Installation</a>
- <a href="#-features">Features</a>
- <a href="#-key-capabilities">Key Capabilities</a>
- <a href="#-environment-configuration">Environment Configuration</a>

<p>A comprehensive Content Management System built with Laravel, designed specifically for news websites and digital publishing platforms. Devlo CMS provides a robust dashboard to efficiently manage, create, and publish content while organizing authors, resources, publishers, and community engagement.</p>
<br>
<h2 style="color: #e74c3c; border-bottom: 2px solid #e74c3c; padding-bottom: 10px;">🚀 Features</h2>
<ul>
    <li><strong style="color: #2c3e50;">Content Management:</strong> Create, edit, schedule, and publish news articles and blog posts</li>
    <li><strong style="color: #2c3e50;">Multi-author Support:</strong> Manage multiple authors with role-based permissions</li>
    <li><strong style="color: #2c3e50;">Media Library:</strong> Organize and manage images, videos, and documents</li>
    <li><strong style="color: #2c3e50;">Draft System:</strong> Save articles as drafts and publish when ready</li>
    <li><strong style="color: #2c3e50;">Category & Tag Management:</strong> Organize content with hierarchical categories and tags</li>
    <li><strong style="color: #2c3e50;">Publisher Management:</strong> Handle multiple publishing sources and outlets</li>
    <li><strong style="color: #2c3e50;">Community Engagement:</strong> Manage comments, user interactions, and feedback</li>
    <li><strong style="color: #2c3e50;">Resource Organization:</strong> Centralized management of sources, references, and assets</li>
    <li><strong style="color: #2c3e50;">SEO Optimization:</strong> Built-in SEO tools for better search engine visibility</li>
    <li><strong style="color: #2c3e50;">Analytics Integration:</strong> Track article performance and reader engagement</li>
</ul>

<h2 style="color: #2ecc71; border-bottom: 2px solid #2ecc71; padding-bottom: 10px; margin-top: 40px;">🎯 Key
Capabilities</h2>

<h3 style="color: #9b59b6;">For Content Teams:</h3>
<ul>
    <li><strong>Text Editor:</strong> Rich text editing with media embedding</li>
    <li><strong>Collaborative Workflows:</strong> Multi-user editing and approval processes</li>
    <li><strong>Content Scheduling:</strong> Plan and automate publication dates</li>
    <li><strong>Version Control:</strong> Track changes and revert to previous versions</li>
</ul>

<h3 style="color: #e67e22;">For Administrators:</h3>
<ul>
    <li><strong>User Management:</strong> Complete control over authors and contributors</li>
    <li><strong>Role-based Access:</strong> Fine-grained permissions system</li>
    <li><strong>Content Moderation:</strong> Review and approve submissions</li>
    <li><strong>Performance Analytics:</strong> Monitor website traffic and content performance</li>
</ul>

<h3 style="color: #3498db;">For Publishers:</h3>
<ul>
    <li><strong>Multi-platform Publishing:</strong> Publish to web, mobile, and social media</li>
    <li><strong>RSS Feed Management:</strong> Automate content syndication</li>
    <li><strong>API Ready:</strong> RESTful API for third-party integrations</li>
    <li><strong>Customizable Templates:</strong> Flexible layout and design options</li>
</ul>

## 🛠️ System Requirements

Make sure your server meets the following requirements before installation:cite[5]:

- **PHP** 8.3 or higher
- **Composer** 2.0 or higher
- **MySQL** 5.7+ or **MariaDB** 10.3+
- **Node.js** 18.x or higher & **NPM**
- **Laravel** 10.x or higher

## ⚡ Installation

Follow these steps to get Devlo CMS running on your local development environment:cite[5]:cite[10]:

### 1. Clone the Repository

```bash
git clone https://github.com/yourusername/devlo-cms.git
cd devlo-cms
```

### 2. Composer Installation

```bash
composer install
```

### 3. npm dependencies install

```bash
npm install
```

### 4. npm running

```bash
npm run build
```

## 5. Environment Configuration

```bash
# Copy the environment file
cp .env.example .env

# Generate a secure application key
php artisan key:generate
```

## 6.Database Setup

```bash
# Run database migrations
php artisan migrate

# Seed the database with initial data
php artisan db:seed

# Create a symbolic link for public storage
php artisan storage:link
```

## 7.env file

<h3> Basic Application Settings </h3>    
<h3>Application Configuration</h3>
<ul>
  <li>APP_NAME="Devlo CMS"</li>
  <li>APP_ENV=local</li>
  <li>APP_KEY= # This is generated by 'php artisan key:generate'</li>
  <li>APP_DEBUG=true</li>
  <li>APP_URL=http://localhost:8000</li>
 
</ul>

<h3>App Languages</h3>
<ul>
  <li>WEBSITE_LOCALE=ar</li>
  <li>DASHBOARD_LOCALE=en</li>
 <li>APP_ACTIVE_LANGUAGES_SYSTEM=true</li>
</ul>

<h3>Posts Views</h3>
<ul>
<li>VIEW_EXPIRATION_HOURS=4</li>
<li>AVAILABLE_DATE_AFTER=24</li>
</ul>

<h3>Database Configuration</h3>
<ul>
  <li>DB_CONNECTION=mysql</li>
  <li>DB_HOST=127.0.0.1</li>
  <li>DB_PORT=3306</li>
  <li>DB_DATABASE=devlo_cms</li>
  <li>DB_USERNAME=your_db_username</li>
  <li>DB_PASSWORD=your_db_password</li>
</ul>

<h3>Mail Configuration</h3>
<ul>
  <li>MAIL_MAILER=smtp</li>
  <li>MAIL_HOST=mailpit</li>
  <li>MAIL_PORT=1025</li>
  <li>MAIL_USERNAME=null</li>
  <li>MAIL_PASSWORD=null</li>
  <li>MAIL_ENCRYPTION=null</li>
  <li>MAIL_FROM_ADDRESS="[email protected]"</li>
  <li>MAIL_FROM_NAME="${APP_NAME}"</li>
</ul>

<h3>Cache, Session & Queue</h3>
<ul>
  <li>CACHE_DRIVER=file</li>
  <li>SESSION_DRIVER=file</li>
  <li>SESSION_LIFETIME=120</li>
  <li>QUEUE_CONNECTION=sync</li>
</ul>

<h3>Services</h3>
<ul>
  <li>AWS_ACCESS_KEY_ID=</li>
  <li>AWS_SECRET_ACCESS_KEY=</li>
  <li>PUSHER_APP_ID=</li>
  <li>PUSHER_APP_KEY=</li>
  <li>PUSHER_APP_SECRET=</li>
</ul>

<h2>🛠 Our Special Environment Variables Usage</h2>

<h3>🌐 APP_ACTIVE_LANGUAGES_SYSTEM</h3>
<ul>
<li><strong>Type</strong>: <code>Boolean</code></li>
<li><strong>Description</strong>: If set to <code>true</code>, it will create the data in database in languages specified by the system to enable multi-language system functionality.</li>
</ul>

<h3>👁️ VIEW_EXPIRATION_HOURS</h3>
<ul>
<li><strong>Type</strong>: <code>Integer</code></li>
<li><strong>Description</strong>: Sets the hours duration for last view from the same user before counting his view as a new view for posts. This ensures accurate post views counting by preventing duplicate views from the same user within the specified timeframe.</li>
</ul>

<h3>📅 AVAILABLE_DATE_AFTER</h3>
<ul>
<li><strong>Type</strong>: <code>Date/Time</code></li>
<li><strong>Description</strong>: Controls the display of latest posts according to their available publish date. Enables live posts updates and ensures timely content delivery to users.</li>
</ul>

<h3>🌍 WEBSITE_LOCALE</h3>
<ul>
<li><strong>Type</strong>: <code>String</code></li>
<li><strong>Description</strong>: Sets the theme language for website visitors. Determines the default language interface that users see when visiting your site.</li>
</ul>

<h3>⚙️ DASHBOARD_LOCALE</h3>
<ul>
<li><strong>Type</strong>: <code>String</code></li>
<li><strong>Description</strong>: Sets the language for your admin dashboard interface and also affects the database seeder language. Separate from website locale to allow different languages for admin and frontend.</li>
</ul>
