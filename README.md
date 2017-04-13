


# Sorry, it's not complete yet! 
# We are still working on it (So hard)

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



### Creating configuration for Controller

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
       
       
       
       
       
