# The Coffee Can

## 3402 Assignment 2 - Team 21 

### Team Members:

Belle

Joshua Gale

Thomas Napier

Caitlin Parker

Christopher Ryan

This repository contains the wp-content files for The Coffee Can website built using WordPress. See contents below for instructions on how to setup a local environment and begin working on the site. 

### Contents:

1. Development
    * Setup Local Environment

    * Sass setup (in phpstorm)

2. Deployment

    * Commit Theme to github

    * Merging with staging/production branches

    * Push Content to server

3. WordPress Admin Credentials
4. Site Links



## Development

### Setup Local Environment


1. Open command prompt or another terminal program (Git-Bash, PuTTY, etc)

2. In the terminal, enter: `git clone https://github.com/cp3402-students/env-cp3402-2019-team21.git projectname` (where `projectname` is the name of the folder that the repo will be cloned to)

3. Enter `cd projectname` 
This changes the current directory to the project folder

4. Enter `vagrant up`
You should be inside the folder with the VagrantFile (you can check with `ls -lah`). This boots the virtual machine and creates a new install of WordPress. It will typically take a while on first time setup. If the install fails, go here: [Troubleshooting](https://github.com/lindsaymarkward/WPDistillery#troubleshooting).

5. Search `192.168.33.10` in a browser and it should load a blank copy of wordpress. 

6. Login to the [Admin Panel](http://192.168.33.10/wp-admin/) with username: admin | password: admin

7. Go to `Plugins > Installed Plugins` then Activate `WP Sync DB`, `GitHub Updater` and `WP Sync DB Media Files` plugins

8. Go to `Tools > Migrate DB`. Click `pull` and paste in this key: 
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

### Sass setup (in phpstorm) 

Follow these steps to setup a file watcher in phpstorm (automatically compiles scss files into css file used for the site) 

1. Install Sass `https://github.com/sass/dart-sass/releases/tag/1.20.1` for your OS

2. Open the `public` folder in your project files in phpstorm

3. In phpstorm go to `file > settings > tools > File Watchers` 
4. Click the `+` on the left and select `SCSS`
    * File Type: `SCSS Style Sheet`

    * Scope: `Project Files`

    * For `Program` find where you downloaded Sass and select the `sass.bat` file `e.g. C:\Users\Username\Desktop\dart-sass-1.20.1-windows-x64\dart-sass\sass.bat` if it were on the desktop.

    * For arguments enter: `$FileName$:$FileParentDir$/css/$FileNameWithoutExtension$.css`

    * For paths to refresh enter: `../../css/$FileNameWithoutExtension$.css:../../css/$FileNameWithoutExtension$.css.map`
    * Click `OK` and make sure it the enabled checkbox is ticked in the `File Watchers` panel

Now if you make a change to `understrap/sass/theme/_theme.scss` the change should appear in `understrap/css/theme.css` and `theme.css.map`


## Deployment
### Commit Theme to github

1. Navigate to the wp-content folder using command line and the `cd` command

2. Use `git status` to find any unversioned files 

3. Use `git add (file path)` to add any files

4. Use `git commit -m "commit message"` with your own commit message  

5. Use `git push origin master` to push to github 

### Merging with staging/production branches
1. On github a2 repository go to `branches>new pull request` on the branch you wish to merge into

2. Ensure you are merging from 

  `compare: master` to `base: staging` for local to staging

  or

 `compare: staging` to `base: production` for staging to production

3. `Request` a review from a team member on the right side

4. Add a title and description and then `create pull request`

  The reviewer will then review the request and accept or deny the changes 


### Push Content to server

1. Go to the [Admin Panel](http://192.168.33.10/wp-admin/) while the local environment is running

2. Go to `Tools>Migrate DB`

3. Select `Push`

4. Enter `https://joshuag1.sgedu.site/staging
d4VQ6Ybifq5uPWEpvT6tVavuJRVY6IoR` into the connection info section

5. Press the `Migrate DB` button at the bottom of the page




## WordPress Admin Credentials



Staging
Username: team21
Password: admin

Production
Username: team21
Password: admin



## Site Links



Staging - http://joshuag1.sgedu.site/staging/

Production - http://joshuag1.sgedu.site/production/
