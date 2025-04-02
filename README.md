# Gliding Club Booking System

A web-based booking system for gliding clubs, built with CodeIgniter 3.1.13. This application helps gliding clubs manage their aircraft, member bookings, and club operations efficiently.

## Features

- Member management and authentication
- Aircraft (glider) status tracking and maintenance scheduling
- Flying day management and scheduling
- Document library for club resources
- Qualification tracking for members
- Email notifications for bookings and updates
- Admin panel for club management
- Environment-based configuration

## Requirements

- PHP 8.1 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- mod_rewrite enabled (for Apache)

## Installation

1. **Clone the Repository**
   ```bash
   git clone [repository-url]
   cd GlidingClubBooking
   ```

2. **Configure Environment**
   - Copy `.env.example` to `.env`
   - Update the following variables in `.env`:
     ```
     DB_HOSTNAME=your_database_host
     DB_USERNAME=your_database_username
     DB_PASSWORD=your_database_password
     DB_DATABASE=your_database_name
     
     CLUB_NAME="Your Club Name"
     CLUB_SHORTNAME="ClubShortName"
     CLUB_EMAIL="club@example.com"
     CLUB_TIMEZONE="Your/Timezone"
     CLUB_LOGO_URL="assets/img/your_club_logo.png"
     
     SITE_TITLE="Your Club Booking System"
     PAGE_TITLE="Your Club Name"
     NEW_MEMBER_REGISTRATION_KEY="your-secure-key"
     BASE_URL="https://your-domain.com"
     ENCRYPTION_KEY="your-encryption-key"
     
     EMAIL_FROM="noreply@your-domain.com"
     EMAIL_SUMMARY_GROUP="admin@your-domain.com"
     
     PICKUP_LOCATION_1="Primary Location"
     PICKUP_LOCATION_1_LABEL="Primary"
     PICKUP_LOCATION_2="Secondary Location"
     PICKUP_LOCATION_2_LABEL="Secondary"
     ```

3. **Configure CodeIgniter**
   - Edit `application/config/config.php`:
     ```php
     $config['base_url'] = 'https://your-domain.com/';
     ```
   - Edit `application/config/database.php`:
     ```php
     $db['default'] = array(
         'hostname' => getenv('DB_HOSTNAME'),
         'username' => getenv('DB_USERNAME'),
         'password' => getenv('DB_PASSWORD'),
         'database' => getenv('DB_DATABASE'),
         'dbdriver' => 'mysqli',
         'dbprefix' => '',
         'pconnect' => FALSE,
         'db_debug' => TRUE,
         'cache_on' => FALSE,
         'cachedir' => '',
         'char_set' => 'utf8',
         'dbcollat' => 'utf8_general_ci',
         'swap_pre' => '',
         'autoinit' => TRUE,
         'stricton' => FALSE
     );
     ```

4. **Database Setup**
   - Create a new MySQL database
   - Import the database schema:
     ```bash
     mysql -u your_username -p your_database_name < bookings.sql
     ```

5. **File Permissions**
   - Ensure the following directories are writable:
     ```
     application/cache/
     application/logs/
     uploads/
     ```

6. **Composer Dependencies**
   - Install required packages:
     ```bash
     composer install
     ```

## Security Considerations

- Keep your `.env` file secure and never commit it to version control
- Use strong passwords for database and admin accounts
- Regularly update the `NEW_MEMBER_REGISTRATION_KEY` and `ENCRYPTION_KEY`
- Ensure proper file permissions are set
- Keep PHP and all dependencies up to date

## Support

For support or questions, please contact your system administrator or create an issue in the repository.

## License

[Your License Information]