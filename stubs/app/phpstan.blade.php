includes:
    - ./vendor/larastan/larastan/extension.neon

parameters:
    level: 5
    paths:
        - app
        - config
        - database
        - routes
    excludePaths:
        - ./*/*/FileToBeExcluded.php
    checkMissingIterableValueType: false
    checkGenericClassInNonGenericObjectType: false
    treatPhpDocTypesAsCertain: false
