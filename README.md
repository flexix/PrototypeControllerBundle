
# Flexix\PrototypeControllerBundle installation description

>by Mariusz Piela <mariusz.piela@tmsolution.pl>


---


### Installation

To install the bundle, add: 

```
//composer require

"flexix/prototype-controller-bundle": "dev-master"
```

to your project's `composer.json` file. Later, enable your bundle in the app's kernel:


```
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Flexix\PrototypeControllerBundle\FlexixPrototypeControllerBundle()
    );
}
```



## Creating cofniguration for Controller

1. Creaete Configuration
```
 parameters:    
    some_controller.config: 
        #for all actions
        base:
            allowed: true # you can put array with values xhttp or subrequest
            models:
                 get:
                        name: 'some.service.name'
                        method: someMethod
   
        actions: 
        #for 'new' action
            new:
               templates:
                    widget: 'some_templarte.html.twig'
               models:
                    create:
                        name: 'some.service.name'
                        method: someMethod
               form: 
                   action: new 
                   form_type: 'Some\FormTypeClass'
               redirection: 
                    route_name: filter #route name
        #for 'list' action            
            list:
                allowed: #only xhttp and subrequest possible
                        - xhttp
                        - subrequest
                templates:
                    widget: 'some_templarte.html.twig' 
                models:
                    list:
                        name: 'some.service.name'
                        method: someMethod
                form: 
                   form_type: 'Some\FormTypeClass'
                   action: list
                   method: GET
                adapter: 'some.adapter.service'
        #and so on ....         
            typeahead:
                allowed: 
                        - xhttp
                templates:
                    widget: 'blank.html.twig' 
                models:
                    list:
                        name: 'flexix_menu.typeahead'
                        method: find
  
     
                form: 
                   form_type: 'Flexix\MenuBundle\Form\SearchMenuItemType'
                   action: list
                   method: GET                                  
            filter:
                templates:
                    widget: 'menuitem\filter.html.twig' 
     
                form: 
                   form_type: 'Flexix\MenuBundle\Form\SearchMenuItemType'
                   method: GET
                   action: test   
            get:
                templates:
                    widget: 'menuitem\show.html.twig'
                template_var: menuItem    
            edit:
                templates:
                    widget: 'menuitem\edit.html.twig' 
                form: 
                   form_type: 'Flexix\MenuBundle\Form\MenuItemType' 
                   action: edit
                models:
                    update:
                        name: 'flexix_menu.model'
                        method: update
                        result_parameter: menuItem
                redirection:
                    route_name: filter       
            delete:
                models:
                    delete:
                        name: 'flexix_menu.model'
                        method: delete
                redirection:
                    route_name: filter
   
      
``` 

2. Create service

```       
services:
        some_service_name:
            class: Flexix\ConfigurationBundle\Util\Configuration
            arguments: [%some_controller.config%]
            tags:
                - { name: flexix_prototype_controller.controller_configuration, applicationPath: 'some/path', entity_alias: 'entity-alias' }       
 ```      
       
       
       
       
       
