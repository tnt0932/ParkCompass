
# Park Compass

Park Compass (http://parkcompass.com) lets users discover parks by location and park facility search.
It makes use of Google Maps, MYSQL, PHP, SASS, GRUNT, JSON & jQuery.
Parks data provided by the open data projects of the cities of Vancouver & North Vancouver.

### Getting set up
**Note:** *This was a student project - so it needs lots of love before it's truly "production ready" :)
Also, just a heads up that these setup instructions may not be comprehensive. Let me know if you find anything missing.*

#####Install the following on your local machine:
* [Node & NPM](http://nodejs.org/)
* Ruby (already installed on mac)
* [SASS](http://sass-lang.com/install)
* [Compass](http://compass-style.org/install/)

#####Clone repo
```
git clone git@github.com:tnt0932/ParkCompass.git
```

#####Set up mysql database
* current park database located at `resources>parkcompass_*.sql`

#####Set up config files
* **rename** `db_config_demo.php` to `db_config.php` and fill with local database info.
* **copy** `db_config.php` into **dist** folder and populate with production database info

#####Set up local variables
* rename `project_vars_demo.php` to `project_vars.php` and populate
(enter bit.ly account info to enable link shortening)


#####Install project dependencies
Navigate into the Park Compass repo folder and enter the following to install the package.json dependencies
```
sudo npm install
```
##### Set up virtual host
* [Mac instructions](http://coolestguidesontheplanet.com/set-virtual-hosts-apache-mac-osx-10-10-yosemite/)

#####Run grunt watch task to compile sass on save
```
grunt
```   

#####Run grunt dist task to package everything up into the dist folder
```
grunt dist
```

