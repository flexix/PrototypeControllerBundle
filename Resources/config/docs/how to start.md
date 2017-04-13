# How to Start
1. Generate map of your Entities
 ```
php app/console flexix:class-mapper:update-config-file
```
2. Add mapper.yml to your app/cofnig.yml file 
 ```
 - { resource: mapper.yml }
```
3. Create new App in application section on mapper.yml file
```
    applications:
        admin:
            bundles:
                - bundle_name
            name: adress/of/your/app
```
3. Create Configuration
4. Add confiuguration service
5. Yes, it's done, check your browswer!
