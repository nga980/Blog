# Project Readme

## Overview

This project is a Laravel-based web application with an admin dashboard and post management features. It uses Tailwind CSS for styling and DataTables for interactive tables.

## Features

- User and post management with CRUD operations.
- Admin dashboard showing user and post growth charts.
- Export posts list to Excel using client-side DataTables Buttons.
- Responsive UI with Tailwind CSS and Bootstrap.
- Authentication and authorization handled by Laravel.

## Setup Instructions

1. Clone the repository.
2. Run `composer install` to install PHP dependencies.
3. Run `npm install` to install frontend dependencies.
4. Configure `.env` file with database and other settings.
5. Run `php artisan migrate` to set up the database.
6. Run `npm run dev` to build frontend assets.
7. Run `php artisan serve` to start the development server.

## Usage

- Access the admin dashboard at `/admin/dashboard`.
- Manage posts at `/admin/posts`.
- Use the "Thêm bài viết" button to create new posts.
- Export the posts list to Excel using the export button on the posts list page.

## Notes

- The project uses Tailwind CSS with a custom font stack including 'Figtree'.
- DataTables is used with Buttons extension for exporting data.
- Chart.js is used for displaying growth charts on the dashboard.

## Testing

- Manual testing is recommended for UI components.
- Backend tests can be run using `php artisan test`.

## How to Push Project to GitHub

1. Initialize git repository (if not already):
   ```
   git init
   ```

2. Add all files to staging:
   ```
   git add .
   ```

3. Commit changes:
   ```
   git commit -m "Initial commit"
   ```

4. Create a new repository on GitHub.

5. Add remote origin (replace URL with your repo URL):
   ```
   git remote add origin https://github.com/your-username/your-repo.git
   ```

6. Push to GitHub:
   ```
   git push -u origin main
   ```

7. For subsequent changes, use:
   ```
   git add .
   git commit -m "Your message"
   git push
   ```

## Contact

For any issues or contributions, please contact the project maintainer.
