# easyadmin-tree-list-bundle

symfony 5 bundle starter kit - this bundle overrides another bundle templates


## local development

ako se razvija bundle u direktoriju `/Users/neven/dev/my_easyadmin_tree_bundle/nivas/easyadmin-tree-list-bundle`, kad se oce testno instalirati u neki projektu u `composer.json` od projekta u koji se testira razvoj bundla dodati:

```
{
    "type": "project",
    "license": "proprietary",
    "repositories":[
		{
			"type": "path", 
			"url":  "/Users/neven/dev/my_easyadmin_tree_bundle/nivas/easyadmin-tree-list-bundle"
		}
    ],    

    "prefer-stable": true,
    "minimum-stability": "dev",
    "require": {
        "nivas/easyadmin-tree-list-bundle": "dev-main",
        ...
```

pokrenuti ovo kako bi se symlinkao u vendor od projekta iz razvojog direktorija

```
composer require nivas/easyadmin-tree-list-bundle
```

after installation your `bundles.php` will contain new bundle:

```
<?php

return [
...
    Nivas\Bundle\EasyAdminTreeListBundle\EasyAdminTreeListBundle::class => ['all' => true],
];
```

## Drama sa dohvacanjem entity-a u twig templateima u easyadmin bundleu

In EasyAdminu v2 you could access value of entity property by typing `entity.id`. not anymore. this was replaced by EasyAdmin DTD.

Isto tako prije se moglo lagano doci do propertya koji bi mu se u yamlu stavio na entity tipa:

easy_admin:
  entities:
    Organization:
      class: App\Entity\Organization
      label: 'Organization'
      tree: true
      form:
        fields:
          - name
```

Now this is done by overriding configureResponseParameters of your admin CrudController:

```
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;

class OrganizationCrudController extends AbstractCrudController
{
    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        $responseParameters->set('tree', true);
        // $responseParameters->setIfNotSet('bar.foo', '...');

        return $responseParameters;
    }
```

In template canb e checked:

```
{% if tree  is defined %}
tree je true - {{ tree }}
{% endif %}
```





# Tests

```
<hr>

{# 

<hr>
{{  dump(((entities|last).fields.getByProperty('lvl').value)) }}
{{  dump((entity.fields.getByProperty('lvl').value)) }}

{{ (entities|first).fields.getByProperty('lvl') }}  <-- ovo je dohvatilo
{{ (entities|first).fields.get('01GWEVQB8J0AVMKARH62NRE5E8') }} <-- ovdje ovaj get ocekuje fieldUniqueId i on je uvijek drugaciji random
<hr>

{{ entity.getFqcn }}
{{ entity.getName }}
{{ entity.getInstance }}
{{ entity.getPrimaryKeyName }}
{{ entity.getPrimaryKeyValue }}
{{ dump(entity.getAllPropertyNames) }}

#}

{# ne radi ?! #}
{% if entity.getFqcn == 'App\Entity\Organization' %}
{% endif %}

{{ entity.fields.get('ID') }}  <-- zasto ovo ne radi :()
{{ entity.fields.get('Id') }}  <-- zasto ovo ne radi :()
{{ entity.fields.get('id') }}  <-- zasto ovo ne radi :()
{{ entity.fields.get('lvl') }}  <-- zasto ovo ne radi :()
xxx
{# {{  (entity.fields.get('ID')) }} #}
{% for field in entity.fields %}

{{ field.property }} /  
{{ field.formattedValue }}  /   
{{ field.label|raw }}<br/>

{% endfor %}
```