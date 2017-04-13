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

You should see something like this:
```
    entities:
        flexix_sample_entities:
            discount:
                alias: discount
                entity_class: Flexix\SampleEntitiesBundle\Entity\Discount
            measure_unit:
                alias: measure-unit
                entity_class: Flexix\SampleEntitiesBundle\Entity\MeasureUnit
            payment_frequency:
                alias: payment-frequency
                entity_class: Flexix\SampleEntitiesBundle\Entity\PaymentFrequency
```

Add to mapper your application name 
```
    applications:
        admin:
            bundles:
                - flexix_sample_entities
            name: adress/of/your/app
```
3. Create Configuration
4. Add confiuguration service
5. Yes, it's done, check your browswer!
