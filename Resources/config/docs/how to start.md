# How to start
1. Generate map of your Entities
 ```
php app/console flexix:class-mapper:update-config-file
```
2. Add mapper.yml to your app/cofnig.yml file 
 ```
 - { resource: mapper.yml }
```
3. Create new App in application section on mapper.yml file

 3.1. You should see something like this:
```
flexix_mapper:
    applications: {  }
    entities:
        flexix_sample_entities:
            discount:
                alias: discount
                entity_class: Flexix\SampleEntitiesBundle\Entity\Discount
            measure_unit:
                alias: measure-unit
                entity_class: Flexix\SampleEntitiesBundle\Entity\MeasureUnit
           #...
            
```

 3.2. Add your routes name to mapper.yml
```
    applications:
        admin:
            bundles:
                - flexix_sample_entities
            path: address/of/your/app 
```
3. Create configuration for your application in service.yml file of your Symfony bundle

````
 parameters:    
    some_controller.config: 
        actions: 
        #for 'new' action
            new:
               templates:
                    widget: 'some_template.html.twig'
               services:#@todo
                    create:
                        name: 'some.service.name'
                        method: someMethod
               form: 
                   action: new 
                   form_type: 'Some\FormTypeClass'
               redirection: 
                    route_name: filter #route name
     
````

4. Add configuration service

```
services:
        some_service_name:
            class: Flexix\ConfigurationBundle\Util\Configuration
            arguments: [%some_controller.config%]
            tags:
                - { name: flexix_prototype_controller.controller_configuration, applicationPath: 'address/of/your/app', entity_alias: 'some-entity' }       

```
5. Add routing to app/routing.yml
```
flexix_prototype_controller:
    resource: "@FlexixPrototypeControllerBundle/Resources/config/routing.yml"
    prefix:   /
```
6. Yes, it's done, check your browswer!

Check 
```
   your_project/app_dev.php/adress/of/your/app/-/some-entity/new/
```

