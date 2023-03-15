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

