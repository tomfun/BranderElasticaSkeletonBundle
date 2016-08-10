# BranderElasticaSkeletonBundle

 * **[example of elastica usage](src/queries.md)**
 * [example of elastica post query 1](src/example_query1.json)
 * [example of elastica post query 2](src/example_query2.json)
 * [example of elastica post query 3](src/example_query3.json)
 
## Configurations:

```yaml
# app/config/config.yml
doctrine_cache:
  aliases:
    brander.bundle.elasticaskeleton.cache_storage: my_apc_cache # or other

  providers:
    my_apc_cache:
      type: apc # or other cacher
      namespace: my_apc_cache_ns
#     namespace: "%kernel.root_dir%/%kernel.environment%/%assets_version%"
```

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
            new \Brander\Bundle\ElasticaSkeletonBundle\BranderElasticaSkeletonBundle(),
        );
        // ...
    }
    // ...
}
```
This just [doctrine cache bundle configuration](https://symfony.com/doc/current/bundles/DoctrineCacheBundle/usage.html)
BranderElasticaSkeletonBundle require some cache adapter by alias 
*brander.bundle.elasticaskeleton.cache_storage*