KunstmaanCookieBundle
=====================

The Kunstmaan Cookie Bundle provides a cookie bar; 
detailed pop-up window and a similar page 
explaining each type of cookie used on the website.

All provide the ability to accept or decline certain cookies.

# Importing the cookie bundle

## Show cookie on page
Add following block to the main layout of your website

```
{# Kuma Cookie Bar #}
{% block kumacookiebar %}
    <kuma-cookie-bar></kuma-cookie-bar>
{% endblock %}
```

## CSS: First Method
Apply all CSS by importing the legal.scss file 
into the vendors file of your project

```
@import "vendor/kunstmaan/bundles-cms/src/Kunstmaan/CookieBundle/Resources/ui/scss/legal";
``` 

## CSS: Second method
Import the Kunstmaan Cookie Bundle variables and imports to be overridden. 
Copy the files at the following path to your project folder.

vendor/kunstmaan/bundles-cms/src/Kunstmaan/CookieBundle/Resources/ui/scss/config/legal-variables.scss
vendor/kunstmaan/bundles-cms/src/Kunstmaan/CookieBundle/Resources/ui/scss/config/legal-imports.scss

Alter variables and comment imports to fit the project's styling.

# Commands

## Generate fixtures
 
```
php bin/console d:f:l --fixtures src/Will/WebsiteBundle/DataFixtures/ORM/LegalGenerator/DefaultFixtures.php --append
``` 

## Copy Cookiebar Resources to your project

```
php bin/console kuma:generate:legal --prefix will_ --demosite
``` 

