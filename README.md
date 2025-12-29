# SportScout - Sports Managing Web Application

A comprehensive sports management web application with role-based access control. Manage sports leagues, seasons, teams, players, and track performance all in one place.

## Features

- **User Authentication** - Secure login system.
- **Role-Based Access Control** - Five distinct user roles with specific permissions.
- **Sports Management** - Organize and manage multiple sports and leagues.
- **Team Management** - Create and maintain team rosters and information.
- **Schedule History** - Track and view game results and performance data.
- **Player Tracking** - Comprehensive player profiles and statistics.
- **Position Management** - Organize and manage multiple positions.
- **Multi-Level Hierarchy** - Sports → Leagues → Seasons → Teams → Players
- **Responsive Interface** - Clean and intuitive user experience.

## User Roles & Permissions

### Administrator

- Full system access and control.
- Add, update, and delete sports.
- Add, update, and delete leagues.
- Add, update, and delete seasons.
- Add, update, and delete teams.
- Add, update, and delete players.
- Add, update, and delete users.
- Manage all system data.

### League Manager

- Manage their assigned leagues.
- Add, update, and delete seasons within their leagues.
- Add, update, and delete teams within their leagues.
- Add, update, and delete players within their leagues.

### Coach & Team Manager

- Manage their assigned teams.
- Add, update, and delete players within their teams.
- View team shedule history.
- Update team information.

### Fan

- View-only access.
- Browse team information.
- Access schedule history.
- View player profiles.

## Tech Stack

- **PHP** - Server-side Logic & Backend
- **MySQL** - Database Management
- **HTML5** - Structure & Content
- **CSS3** - Styling & Layout
- **JavaScript** - Client-Side Interactivity
- **jQuery** - DOM Manipulation & AJAX Requests

## Installation

### Prerequisites

- PHP 7.4 or higher.
- MySQL 5.7 or higher.
- Apache web server.
- MySQL server.

### Setup Instructions

1. Clone the repository:

```bash
git clone https://github.com/Balsha98/Repository-SportScout.git
```

2. Navigate to the project directory:

```bash
cd Repository-SportScout/sport-scout
```

3. Import the database:

```bash
# Import the SQL file into your MySQL database.
mysql -u root -p sport_scout < assets/sql/sportscout.sql
```

5. Set up your web server to point to the project directory.

6. Access the application:

```
http://localhost/local-repository-directory
```

## Getting Started

1. **Login**: Access the system with your credentials (admin approval).
2. **Dashboard**: Navigate to your role-specific dashboard.
3. **Management**: Add, edit, or view sports data based on your permissions.

## Project Structure

```
Repository-SportScout/
│
├── sport-scout/        # Main application directory.
│   │
│   ├── api/            # API endpoints for request processing.
│   │   ├── admin.php           # Admin view API.
│   │   ├── login.php           # Login view API.
│   │   ├── schedule.php        # Schedule view API.
│   │   └── team.php            # Team view API.
│   │
│   ├── assets/         # Application assets.
│   │   │
│   │   ├── classes/            # Helper PHP classes.
│   │   │
│   │   ├── css/                # Styling
│   │   │
│   │   ├── data/               # View-related data.
│   │   │
│   │   ├── javascript/
│   │   │   ├── data/           # JavaScript data files.
│   │   │   ├── helpers/        # Helper functions.
│   │   │   ├── libraries/      # Third-party libraries (jQuery).
│   │   │   └── views/          # View-specific scripts.
│   │   │
│   │   ├── media/              # Site visuals.
│   │   │
│   │   ├── sql/                # Database schema.
│   │   │
│   │   ├── template/           # Template PHP classes.
│   │   │
│   │   ├── validation/         # W3 validation proof.
│   │   │
│   │   └── views/              # Main application views.
│   │
│   ├── .htaccess               # Custom routing configuration.
│   ├── configuration.php       # Application configuration.
│   └── index.php               # Application entry point.
│
└── README.md           # Project documentation.
```

## Database Schema

The application uses a relational database with the following main tables:

- **users** - User accounts and authentication.
- **roles** - User role definitions.
- **sports** - Sports types (e.g., Basketball, Football).
- **leagues** - League information.
- **seasons** - Season information.
- **teams** - Team data.
- **schedule** - Game results and history.
- **players** - Player profiles.
- **positions** - Position information.

## Security Features

- Password hashing with PHP's `hash()` function.
- SQL injection prevention with prepared statements.
- Session management for user authentication.
- Role-based access control (RBAC).
- Input validation and sanitization.

## Let's Connect

If you enjoyed this project or have any questions, feel free to reach out!

[![Email](https://img.shields.io/badge/Email-D14836?style=for-the-badge&logo=gmail&logoColor=white)](mailto:balsa.bazovic@gmail.com)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-0077B5?style=for-the-badge&logo=linkedin&logoColor=white)](https://www.linkedin.com/in/balsha-bazovich)
[![GitHub](https://img.shields.io/badge/GitHub-100000?style=for-the-badge&logo=github&logoColor=white)](https://github.com/Balsha98)

⭐ If you found this project helpful, please consider giving it a star!

---

Made with PHP, HTML5, CSS3, JavaScript (jQuery), and ❤️!
