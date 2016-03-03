# Symfony2RAD
PHP Symfony2 Service Oriented Architecture for rapid corporate website development. 
Designed to ease Java Spring MVC developer migrating to PHP.

###### Symfony 2.6
###### Twig
###### Doctrine DBAL

##### SOA based corporate website RAD
##### Using only Doctrine DBAL for database connection, No ORM, native raw SQL
##### Thin Controller, thick service
##### Transaction control in service
##### Typical Spring configuration (DAO injected to Service, Services injected to controller)
##### View, Controller, Model (Service + DAO), VO (Value Object), Validator
##### Custom Validator for each entity
##### Form created in View layer, controller doesn't need to know about Form (and should not know)
##### Directory restructured for shared hosting (custom .htaccess)

