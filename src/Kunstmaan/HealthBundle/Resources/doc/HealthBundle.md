#  Using the Health bundle


## Installation

This bundle is compatible with all Symfony 3.* releases. More information about installing can be found in this line by line walkthrough of installing Symfony and all our bundles, please refer to the [Getting Started guide](http://bundles.kunstmaan.be/getting-started) and enjoy the full blown experience.

## Usage

This bundle allows you to collect information about your website.

### URL checker

#### 1. PagePart URL checker

The Url checker consist of link sources that return links that will be checked. Standard we have implemented the PagePartLinkSource.
This link source will extract all the links from all the pageparts that have a form with an url chooser form type.

These links will be passed to the urlchecker and will be checked for validity. 

If you want to add your own URL's that need to be checked, you must create a service that is tagged by "kunstmaan_health.link_source".

Those services will be called by the DeadLinkFinder helper.

#### 2. Doctrine URL checker

When you want to add custom fields of your doctrine entity to the url checker, you can created a tagged service by doing the following:

    kunstmaan_demo.link_source.office:
        class: Kunstmaan\HealthBundle\Helper\UrlChecker\Sources\DoctrineLinkSource
        arguments:
            - "@doctrine" 
            - KunstmaanDemoBundle:Office #The name of your custom entity
            - kunstmaan_demobundle_admin_office_edit #The edit route to your entity
            - [ customerUrlField ] #The fields that need to be checked
            - "@kunstmaan_health.url_extractor.default" 
            - [] #Extra criteria for the find query
            - Office #An optional extra string parameter that will be placed in the "Extra" Column in the url checker overview.
        tags:
            - { name: kunstmaan_health.doctrine_link_source }
            
