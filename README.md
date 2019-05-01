# The Coffee Can

## 3402 Assignment 2 - Team 21 

Team Members:
Belle
Joshua Gale
Thomas Napier
Caitlin Parker
Christopher Ryan

This repository contains the wp-content files for The Coffee Can website built using WordPress. See contents below for instructions on how to setup a local environment and begin working on the site. 

Contents:
1. Setup local environment (With WPDistillery) and setup this repository in the wp-content folder.
2. WordPress Admin Credentials
3. Site Links

--------------------------------------------------------------

## Setup Local Environment

--------------------------------------------------------------

1. Open command prompt or another terminal program (Git-Bash, PuTTY, etc)
2. In the terminal, enter: `git clone https://github.com/lindsaymarkward/WPDistillery projectname` (where `projectname` is the name of the folder that the repo will be cloned to)
3. Enter `cd projectname` 
This changes the current directory to the project folder
4. Enter `vagrant up`
You should be inside the folder with the VagrantFile (you can check with `ls -lah`). This boots the virtual machine and creates a new install of WordPress. It will typically take a while on first time setup. If the install fails, go here: [Troubleshooting](https://github.com/lindsaymarkward/WPDistillery#troubleshooting).
5. Enter `vagrant ssh` 
This will give you access to the virtual computer using the secure socket shell protocol and confirm that it is running. Note: at any time if you want to return to your local computer, enter `exit`.
6. Enter `cd /var/www/public/wp-content` 
This changes the current directory to the `wp-content` folder. This folder and all files within the `public` folder are shared between your local computer and the virtual machine.      
7. If you enter `ls -lah`, you will see a number of files/folders listed. Enter `rm -r *` This deletes all folders/files and if you type `ls -lah` again, nothing should appear apart from `. and ..` 
8. Enter `git clone https://github.com/cp3402-students/a2-cp3402-2019-team21 .` 
It is important to include the `.` at the end as this will clone the repository to the correct directory
9, Search `192.168.33.10` in a browser and it should load a blank copy of wordpress. 
10. Login to the [Admin Panel](http://192.168.33.10/wp-admin/) with username: admin | password: admin
11. Go to `Plugins > Installed Plugins` then Activate `WP Sync DB`, `GitHub Updater` and `WP Sync DB Media Files` plugins
12. Go to `Tools > Migrate DB`. Click `pull` and paste in this key: 
```
https://joshuag1.sgedu.site/staging
d4VQ6Ybifq5uPWEpvT6tVavuJRVY6IoR
```
All information should fill out automatically. At the bottom, click Migrate DB. It will ask you to login again. This time, you need to use the staging site credentials which are:
```
Username: team21
Password: admin
```
Now you should see all of the site content has been imported and you can make your changes here. 

--------------------------------------------------------------

## WordPress Admin Credentials

--------------------------------------------------------------

Staging
Username: team21
Password: admin

Production
Username: team21
Password: admin

--------------------------------------------------------------

## Site Links

--------------------------------------------------------------

Staging - http://joshuag1.sgedu.site/staging/

Production - http://joshuag1.sgedu.site/production/
