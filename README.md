# Museum Management System

A comprehensive web-based platform for managing museum operations, including artwork collections, events, ticket bookings, visitor feedback, and an integrated boutique. Built with PHP and styled with Tailwind CSS.

## 🌟 Features

### For Visitors
- **Event & Exhibition Browsing:** Discover upcoming events and explore the museum's artwork collections.
- **Online Ticket Booking:** Easily purchase tickets for various events with secure checkout.
- **Boutique Shop:** Browse and purchase museum-related articles and souvenirs.
- **Reviews & Feedback:** Leave ratings and comments on artworks, events, and purchased articles.

### For Administrators & Employees
- **Admin Dashboard:** Centralized view of overall museum statistics and operations.
- **Employee Management:** Add, edit, and manage museum staff accounts.
- **Collection & Artwork Management:** Catalog artworks with details like title, artist, creation date, and materials.
- **Event Management:** Schedule and manage exhibitions and events with capacity tracking.
- **Visitor Management:** Track visitor registrations, ticket sales, and user feedback.
- **Store Inventory:** Manage boutique items, stock, and pricing.

## 🛠 Tech Stack

- **Backend:** PHP (Native) with PDO for secure database interactions.
- **Database:** MySQL / MariaDB (Schema dump provided).
- **Frontend:** HTML5, PHP, and Tailwind CSS.
- **Styling:** Tailwind CSS integrated via configuration.

## 📂 Project Structure

- `/front`: Contains all application logic, UI views, forms, and assets.
  - `*-dashboard.php`: Management dashboards for different entities (Admin, Events, Artworks, Visitors).
  - `*-form.php` / `*-list.php`: CRUD operations interfaces for various models.
  - `login.php`, `register.php`: User authentication and authorization pages.
  - `tailwind.config.js`: Configuration for Tailwind CSS.
  - `/styles.css`: Compiled CSS and custom styles.
- `/vendor`: Composer dependencies.
- `db.sql`: Database schema export for quick setup and deployment.

## 🚀 Getting Started

### Prerequisites
- PHP 8.0 or higher.
- MySQL / MariaDB server.
- Composer (for managing any additional PHP dependencies).
- A local web server like XAMPP, WAMP, or Laravel Valet.

### Installation

1. **Clone the repository:**
   ```bash
   git clone <https://github.com/chvr4f/Museum-php.git>
   cd museum-management
   ```

2. **Database Setup:**
   - Create a new MySQL database named `g27` (or your preferred name).
   - Import the provided `db.sql` file into your database to create the required tables (`achat`, `article`, `avis`, `billets`, `employe`, `evenement`, `oeuvres`, `utilisateur`, `visiteur`).
   
3. **Configuration:**
   - Open `/front/config.php` and update the database credentials to match your local environment:
     ```php
     <?php
     $host = '127.0.0.1'; // Update with your host IP
     $dbname = 'g27'; // Update with your database name
     $username = 'root'; // Update with your DB username
     $password = ''; // Update with your DB password
     
     try {
         $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
         $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     } catch (PDOException $e) {
         die("Database connection failed: " . $e->getMessage());
     }
     ?>
     ```

4. **Start the Web Server:**
   - If using XAMPP/WAMP, place the project folder in the `htdocs` or `www` directory and access it via `http://localhost/museum-management/front/login.php`.
   - Alternatively, you can run PHP's built-in development server directly from the `/front` directory:
     ```bash
     cd front
     php -S localhost:8000
     ```
   - Then open `http://localhost:8000/login.php` in your browser.

## 🎨 Styling (Tailwind CSS)
If you need to make modifications to the styling and recompile Tailwind CSS, run the following commands (assuming Node.js is installed):
```bash
cd front
npx tailwindcss -i ./styles.css -o ./styles.css --watch
```

## 📄 Documentation
A presentation outlining the project's conception and development is available in the root directory: `Conception et développement d’un site web (1).pptx`.

## 📝 License
This project is for educational purposes as part of a web development conception module.
