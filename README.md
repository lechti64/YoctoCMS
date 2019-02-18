# Yocto
Un CMS simple, rapide et moderne.

[Site](http://yoctocms.com/) - [Forum](http://forum.yoctocms.com/) - [GitHub](https://github.com/remijean/YoctoCMS/)

## Sommaire

- [Chemin d'exécution](#chemin-dexécution)
- [Schéma de la base de données](#schéma-de-la-base-de-données)
- [Propriétés publiques](#propriétés-publiques)
- [Base de données](#base-de-données)
- [Template](#template-1)
- [Type de page d'exemple](#type-de-page-dexemple)

## Chemin d'exécution

### 1. index.php

- Ouvre une session,
- Charge l'autoloader des classes,
- Récupère l'utilisateur courant ([$_user](#données-de-lutilisateur-courant)),
- Récupère la page courante ([$_page](#données-de-la-page-courante)),
- Importe le routeur du type de la page courante.

### 2. type/[type]/router.php

- Crée une instance du contrôleur,
- Initialise les contrôleurs en fonction des routes.

### 3. type/[type]/Controller[type].php

- Récupère les données du type rattaché à la page courante ([$_type](#données-du-type-rattaché-à-la-page-courante)),
- Récupère la classe Template ([$template](#template)),
- Autres actions rattachées au contrôleur (ex. : définition de la vue, du layout, des librairies, enregistrement de données...).

### 4. type/[type]/view/[vue].php

- Vue à afficher par le contrôleur.

## Schéma de la base de données

- `configuration` : Configuration du site
- `group` : Groupes d'utilisateurs
- `navigation-item` : Items du menu de navigation
- `page` : Données des pages
- `page-[type]` Données des types rattachés aux pages
- `user` : Données des utilisateurs

## Propriétés publiques

### Données de la page courante

```php
print_t($this->_page);
```

### Données du type rattaché à la page courante

```php
print_t($this->_type);
```

### Données de l'utilisateur courant

```php
print_t($this->_user);
```

### Template

```php
$this->template
```

[Voir la section dédiée](#template-1) pour plus d'informations sur l'utilisation du template.


## Base de données

### Création

```php
Database::create('nom-de-table', [
    'title' => 'string',
    'position' => 'integer',
    'status' => 'boolean',
]);
```

##### Types de colonne

- `boolean` : booléen (true ou false).
- `float` : nombre à virgule.
- `id` : alphanumérique en minuscule et trait d'union "-".
- `integer` : entier.
- `string` : chaîne de caractères.

### Sélections

#### Sélection simple

```php
$row = Database::instance('nom-de-table')->find();
print_r($row);
```

#### Sélection multiple

```php
$rows = Database::instance('nom-de-table')->findAll();
foreach ($rows as $row) {
    print_r($row);
}
```

### Insertion

```php
$row = Database::instance('nom-de-table');
$row->id = 'nouvelle-ligne';
$row->title = 'Un titre';
$row->position = 10;
$row->status = false;
$row->save();
```

### Mise à jour

```php
$row = Database::instance('nom-de-table')
    ->where('id', '=', 'ligne-existante')
    ->find();
$row->status = true;
$row->save();
```

### Conditions

#### Where

```php
$rows = Database::instance('nom-de-table")
    ->where('status', '=', false)
    ->andWhere('position', '>', 10)
    ->orWhere('title', 'IN', [
        'Un titre',
        'Un autre titre',
    ])
    ->findAll();
```

##### Opérateurs de comparaison

- `=` : égale.
- `!=` : pas égale.
- `>` : supérieur à.
- `>=` : supérieur ou égale à.
- `<` : inférieur à.
- `<=` : inférieur ou égale à.
- `IN` : égale à l'une des valeurs du tableau, l'argument $value doit être un tableau.
- `NOT IN` : pas égale à l'une des valeurs du tableau, l'argument $value doit être un tableau.
- `LIKE` : recherche dans un modèle, l'argument $value doit être une chaine contenant le caractère joker "%". Exemples :
    - `%foobar` : recherche les lignes qui se termine par "foobar".
    - `foobar%` : recherche les lignes qui commence par "foobar".
    - `%foobar%` : recherche les lignes qui utilisent le mot "foobar".
    - `foo%bar` : recherche les lignes qui commence par "foo" et qui se terminent par "bar".

#### Limit

```php
$rows = Database::instance('nom-de-table")
    ->limit(0, 10)
    ->findAll();
```

#### OrderBy

```php
$rows = Database::instance('nom-de-table")
    ->orderBy('position', 'ASC')
    ->findAll();
```

```php
$rows = Database::instance('nom-de-table")
    ->orderBy('position', 'DESC')
    ->findAll();
```

#### Conditions multiples

```php
$rows = Database::instance('nom-de-table")
    ->where('status', '=', false)
    ->andWhere('position', '>', 10)
    ->orWhere('title', 'IN', [
       'Un titre',
       'Un autre titre',
    ])
    ->limit(0, 10)
    ->orderBy('position', 'ASC')
    ->findAll();
```

### Suppression

#### Suppression simple

```php
Database::instance('nom-de-table")
    ->where('status', '=', false)
    ->find()
    ->delete();
```

#### Suppression multiple

```php
Database::instance('nom-de-table")
    ->where('status', '=', false)
    ->findAll()
    ->delete();
```

#### Suppression de table

```php
Database::instance('nom-de-table")->delete();
```

## Template

### Champ simple

```php
echo $this->template->input('id-du-champ', [
    'label' => 'Adresse email',
    'type' => 'email',
])
```

##### Attributs spécifiques

- `label` : Label au dessus du champ

##### Attributs génériques

- [Voir la documentation Mozilla](https://developer.mozilla.org/fr/docs/Web/HTML/Element/Input#Attributs)

### Bouton

```php
echo $this->template->button('id-du-bouton', 'Valider', [
    'type' => 'submit',
])
```

##### Attributs spécifiques

- Aucun

##### Attributs génériques

- [Voir la documentation Mozilla](https://developer.mozilla.org/fr/docs/Web/HTML/Element/Button#Attributs)

## Type de page d'exemple

Liste des fichiers de l'exemple :

- type/example/view/index.php
- type/example/view/edit.php
- type/example/ControllerExample.php
- type/example/router.php

### Le router : type/example/router.php

```php
<?php

// Crée une instance du contrôleur
$controller = new Yocto\ControllerExample($_page, $_user);

// Initialise les contrôleurs en fonction des routes
$router = new Yocto\Router($controller->get('action'));
$router->map('GET', '/', function() use ($controller) {
    $controller->index();
    return $controller;
});
$router->map('GET', '/edit', function() use ($controller) {
    $controller->edit();
    return $controller;
});
$router->map('POST', '/edit', function() use ($controller) {
    $controller->save();
    return $controller;
});
return $router->run();
```

### Le contrôleur : type/example/ControllerExample.php

```php
<?php

namespace Yocto;

class ControllerExample extends Controller {

    /**
     * MÉTHODES PUBLIQUES
     */

    public function edit() {
        $this->setView('edit');
        $this->setLayout('main');
    }

    public function index() {
        $this->setView('index');
        $this->setLayout('main');
    }

    public function save() {
        $this->_page
            ->title = $this->get('POST:title')
            ->save();
        $this->_type
            ->foo = $this->get('POST:foo')
            ->bar = $this->get('POST:bar')
            ->save();
        $this->setView('index');
        $this->setLayout('main');
    }

}
```

### La vue d'accueil : type/example/view/index.php

```html
<ul>
    <li>Foo : <?php echo $this->_type->foo; ?> de <?php echo $this->_page->title; ?></li>
    <li>Bar : <?php echo $this->_type->bar; ?> de <?php echo $this->_page->title; ?></li>
</ul>
```

### La vue d'édition : type/example/view/edit.php

```html
<form method="post">
    <?php echo $this->template->input('title', [
        'label' => 'Titre de la page',
        'value' => $this->_page->title,
    ]); ?>
    <?php echo $this->template->input('foo', [
        'label' => 'Titre de la page',
        'value' => $this->_type->foo,
    ]); ?>
    <?php echo $this->template->input('bar', [
        'label' => 'Titre de la page',
        'value' => $this->_type->bar,
    ]); ?>
    <?php echo $this->template->button('submit', 'Valider', [
        'type' => 'submit',
    ]); ?>
</form>
```