# easyadmin-tree-list-bundle

EasyAdmin 3.x / Symfony 5.x compatible bundle which overrides default EasyAdmin list templates, adding nested tree view on list for entities that use [Gedmo Tree extension](https://github.com/doctrine-extensions/DoctrineExtensions).

In order to achieve nested tree view, your entity must implement following:
- nested tree on entity
- root_id property 
- parent_id property
- lft (left) property
- rgt (right) property
- lvl (level) property

example: https://github.com/doctrine-extensions/DoctrineExtensions/blob/main/doc/tree.md

## Installation


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

## Configuration

As mentioned previously, your entity must implement Gedmo Tree.

Your EasyAdmin CRUD controller should override:
- `configureResponseParameters` - used to enable tree config for twig
- `createIndexQueryBuilder` - used to change sorting order needed to make a tree list
- `configureActions` - used to remove batch actions in front of tree navigation arrow

Example of `src/Controller/Admin/TermCrudController.php`

```
<?php

namespace App\Controller\Admin;

use App\Entity\Term;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;

// za custom order query
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;

// za ugasit akcije
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

class TermCrudController extends AbstractCrudController
{
    public function configureResponseParameters(KeyValueStore $responseParameters): KeyValueStore
    {
        $responseParameters->set('tree', true);
        // $responseParameters->setIfNotSet('bar.foo', '...');

        return $responseParameters;
    }
    
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::BATCH_DELETE);
    }    

    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = $this->get(EntityRepository::class)->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $qb->resetDQLPart('orderBy');
        $qb->addOrderBy($qb->getRootAlias().'.root', 'ASC');
        $qb->addOrderBy($qb->getRootAlias().'.lft', 'ASC');
        return $qb;
    }

    public static function getEntityFqcn(): string
    {
        return Term::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined() // bolje funka na tree-u
            ->setEntityLabelInSingular('Term')
            ->setEntityLabelInPlural('Term')
            ->setSearchFields(['id', 'name', 'slug', 'lft', 'lvl', 'rgt', 'position'])
            ->setPaginatorPageSize(5000);
    }

    public function configureFields(string $pageName): iterable
    {
        $name = TextField::new('name');
        $parent = AssociationField::new('parent');
        $parentCategories = AssociationField::new('parentCategories');
        $id = IntegerField::new('id', 'ID');
        $slug = TextField::new('slug');
        $lft = IntegerField::new('lft');
        $lvl = IntegerField::new('lvl');
        $rgt = IntegerField::new('rgt');
        $position = IntegerField::new('position');
        $root = AssociationField::new('root');
        $children = AssociationField::new('children');
        $childCategories = AssociationField::new('childCategories');
        $createdBy = AssociationField::new('createdBy');
        $updatedBy = AssociationField::new('updatedBy');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $root, $parent, $lft, $rgt, $lvl, $updatedBy, $createdBy];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $slug, $lft, $lvl, $rgt, $position, $root, $parent, $children, $parentCategories, $childCategories, $createdBy, $updatedBy];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$name, $parent, $parentCategories];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$name, $parent, $parentCategories];
        }
    }
}
```




























### local development

For local development if bundle is located in `~/dev/my_easyadmin_tree_bundle/nivas/easyadmin-tree-list-bundle`, manually add to your `composer.json` repo:

```
{
    "type": "project",
    "license": "proprietary",
    "repositories":[
		{
			"type": "path", 
			"url":  "~/dev/my_easyadmin_tree_bundle/nivas/easyadmin-tree-list-bundle"
		}
    ],    
...    
    "prefer-stable": true,
    "minimum-stability": "dev",
    "require": {
        "nivas/easyadmin-tree-list-bundle": "dev-main",
        ...
```

Then run `composer install`.

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