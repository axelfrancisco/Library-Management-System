# NEU Library Visitor Log

This project is a PHP + MySQL library system updated for a visitor log workflow with Google Sign-In, role-based access control, visitor analytics, and block management.

## Live app[
https://librarymanagement.42web.io

## Repository submission
Submit this GitHub repository link to your professor.

## Main features
- Google Sign-In ready login flow
- Role-based access control for regular user and admin
- Same Google account can hold both roles safely
- Visitor onboarding after sign-in
- Welcome message for regular users: `Welcome to NEU Library!`
- Admin dashboard with statistics cards and filters
- Visitor log history with login timestamps
- User blocking with reason tracking

## Important setup before this works live
Google authentication cannot work until you add your own Google OAuth / Google Identity Services credentials.

### 1) Create Google credentials
Create a Google Web Client ID in Google Cloud Console and add your local and deployed domains to the allowed JavaScript origins.

### 2) Update `src/config/google.php`
Set your Google client ID there.

### 3) Update the database
Import `src/database/lib.sql` into MySQL.

### 4) Configure the database connection
Edit `src/config/db.php`.

## Professor access
The SQL file seeds `jcesperanza@neu.edu.ph` with both `user` and `admin` roles so the account can switch safely after Google sign-in.

## Notes
- The login page keeps the existing video background.
- Blocking only prevents regular-user access. Admin access still depends on assigned roles.
- If you want production-ready deployment, use a host that supports PHP + MySQL.
