# CodeIgniter Kickstart App
Common CodeIgniter configuration for production-ready apps. 
Why? Because after deploying many CodeIgniter apps into production, I've found that I setup and configure each app very similarly.

## Summary of Changes
1. Change /application/ and /system/ folders to include the current CI version
2. Create /application/config/development|production/ config files
3. Create /application/config/version.php config
4. Create /application/Home.php controller
5. Create /application/views/common/header.php & footer.php view templates
6. Create /application/views/home/home_index.php view template
7. Create /application/core/APP_Controller.php
8. Define default Home controller in /application/config/routes.php
9. Create .htaccess file to remove /index.php from URL
