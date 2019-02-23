# Yocto

Un CMS simple, rapide et moderne. (en développement)

[Site](http://yoctocms.com/) - [Forum](http://forum.yoctocms.com/) - [GitHub](https://github.com/remijean/YoctoCMS/)

## Sommaire

- [Propriétés](#propriétés)
- [Méthodes](#méthodes)
- [Base de données](#base-de-données)
- [Template](#template-1)
- [Type de page](#type-de-page)
- [Débogage](#débogage)

## Propriétés

### Données de la page

```php
$this->_page
```

### Données du type rattaché à la page

```php
$this->_type
```

### Données de l'utilisateur

```php
$this->_user
```

### Données de configuration

```php
$this->_configuration
```

## Méthodes

### Rechercher une clé dans les méthodes HTTP

#### Dans toutes les méthodes

```php
$this->get('nom-de-clé')
```

L'ordre de recherche est le suivant : POST, GET, COOKIE.

#### Dans une méthode spécifique

```php
$this->get('GET:nom-de-clé')
```

##### Méthodes

- `COOKIE` : recherche dans la variable "$_COOKIE".
- `GET` : recherche dans la variable "$_GET".
- `POST` : recherche dans la variable "$_POST".

### Rendre obligatoire un champ

```php
$this->get('nom-de-clé', true)
```

La soumission du formulaire échoura si la valeur de la clé "nom-de-clé" est vide. De plus la valeur "null" sera retournée et une notice ajoutée dans la vue.

La valeur "null" bloque l'enregistrement des données, pour plus d'informations voir les sections "[Insertion](#insertion)" ou "[Mise à jour](#mise-à-jour)".

### Configurer un layout

```php
$this->setLayout('nom-de-layout')
```

##### Layouts

- `main` : layout principal.
- `raw` : layout sans aucun HTML / CSS / JS.

### Configurer une vue

```php
$this->setView('nom-de-vue')
```

### Configurer une librairie

```php
$this->setVendor('lien', 'sri');
```

Importe une librairie depuis un CDN, le SRI n'est pas obligatoire, exemple de CDN : https://cdnjs.com/.

### Configurer l'alerte de soumission

```php
$this->setAlert('Mon alert.', 'danger');
```

##### Types d'alerte

- `success` : alerte vert _(valeur par défaut)_.
- `danger` : alerte rouge.
- `warning` : alerte orange.

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

#### Insertion dans une table

```php
$row = Database::instance('nom-de-table');
$row->title = 'Un titre';
$row->position = 10;
$row->status = false;
$row->save();
```

Lorsqu'une des valeurs est égale à "null", l'enregistrement de la ligne se bloque. Cela permet de gérer les colonnes obligatoires.

#### Insertion dans plusieurs tables

```php
$table1Row = Database::instance('table-1');
$table1Row->foo = 'foo';

$table2Row = Database::instance('table-2');
$table2Row->bar = 'bar';

Database::saveAll([$table1Row, $table2Row]);
```

Lorsqu'une des valeurs est égale à "null", l'enregistrement des lignes se bloque. Cela permet de gérer les colonnes obligatoires et d'éviter d'enregistrer qu'une partie des lignes.

### Mise à jour

#### Mise à jour dans une table

```php
$row = Database::instance('nom-de-table')
    ->where('id', '=', 10)
    ->find();
$row->status = true;
$row->save();
```

Lorsque l'une des valeurs est égale à "null", l'enregistrement de la ligne se bloque. Cela permet de gérer les colonnes obligatoires.

#### Mise à jour dans plusieurs tables

```php
$table1Row = Database::instance('table-1')
    ->where('id', '=', 10)
    ->find();
$table1Row->foo = 'foo';

$table2Row = Database::instance('table-2')
    ->where('id', '=', 20)
    ->find();
$table2Row->bar = 'bar';

Database::saveAll([$table1Row, $table2Row]);
```

Lorsqu'une des valeurs est égale à "null", l'enregistrement des lignes se bloque. Cela permet de gérer les colonnes obligatoires et d'éviter d'enregistrer qu'une partie des lignes.

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

##### Ordres de tri

- `ASC` : croissant
- `DESC` : décroissant

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

### Bootstrap

[Voir la documentation de Bootstrap](https://getbootstrap.com/docs/4.3/getting-started/introduction/)

### Bouton

```php
echo $this->getTemplate()->button('id-du-bouton', 'Valider', [
    'type' => 'submit',
]);
```

##### Attributs

- [Voir la documentation de Mozilla](https://developer.mozilla.org/fr/docs/Web/HTML/Element/Button#Attributs)

### Label

```php
echo $this->getTemplate()->label('id-du-champ-rattaché', 'Label du champ');
```

### Champ court

```php
echo $this->getTemplate()->input('id-du-champ', [
    'label' => 'Adresse email',
    'type' => 'email',
    'value' => 'email@yoctocms.com',
]);
```

##### Attributs

- [Voir la documentation de Mozilla](https://developer.mozilla.org/fr/docs/Web/HTML/Element/Input#Attributs)

### Champ long

```php
echo $this->getTemplate()->textarea('id-du-champ', 'Un commentaire', [
    'label' => 'Commentaire',
]);
```

##### Attributs

- [Voir la documentation de Mozilla](https://developer.mozilla.org/fr/docs/Web/HTML/Element/Textarea#Attributs)

## Type de page

### Fichiers de l'exemple

- type/example/view/index.php
- type/example/view/edit.php
- type/example/ControllerExample.php
- type/example/router.php

### Router (router.php)

```php
<?php

$controller = new Yocto\ControllerExample($_configuration, $_page, $_type, $_user);;
$router = new Yocto\Router($controller->get('action'));
$router->map('GET', '/', function () use ($controller) {
    $controller->index();
    return $controller;
});
$router->map('GET', '/edit', function () use ($controller) {
    $controller->edit();
    return $controller;
});
$router->map('POST', '/edit', function () use ($controller) {
    $controller->save();
    return $controller;
});
return $router->run();
```

### Contrôleur (ControllerExample.php)

```php
<?php

namespace Yocto;

class ControllerExample extends Controller
{

    public function edit()
    {
        // Affichage
        $this->setView('edit');
        $this->setLayout('main');
    }

    public function index()
    {
        // Affichage
        $this->setView('index');
        $this->setLayout('main');
    }

    public function save()
    {
        // Mise à jour de la page
        $pageRow = $this->_page;
        $pageRow->title = $this->get('title', true);
        // Mise à jour du type
        $typeRow = $this->_type;
        $typeRow->foo = $this->get('foo');
        $typeRow->bar = $this->get('bar');
        // Enregistrement
        Database::saveAll([$pageRow, $typeRow]);
        // Alerte
        $this->setAlert('Modifications enregistrées.');
        // Affichage
        $this->edit();
    }

}
```

### Vue d'accueil (view/index.php)

```html
<ul>
    <li>Foo : <?php echo $this->_type->foo; ?> de <?php echo $this->_page->title; ?></li>
    <li>Bar : <?php echo $this->_type->bar; ?> de <?php echo $this->_page->title; ?></li>
</ul>
```

### Vue d'édition (view/edit.php)

```html
<form method="post">
    <div class="form-group">
        <?php echo $this->getTemplate()->label('title', 'Titre de la page'); ?>
        <?php echo $this->getTemplate()->input('title', [
            'value' => $this->_page->title,
        ]); ?>
    </div>
    <div class="form-group">
        <?php echo $this->getTemplate()->label('foo', 'Foo'); ?>
        <?php echo $this->getTemplate()->input('foo', [
            'value' => $this->_type->foo,
        ]); ?>
    </div>
    <div class="form-group">
        <?php echo $this->getTemplate()->label('bar', 'Bar'); ?>
        <?php echo $this->getTemplate()->input('bar', [
            'value' => $this->_type->bar,
        ]); ?>
    </div>
    <?php echo $this->getTemplate()->button('submit', 'Valider', [
        'type' => 'submit',
    ]); ?>
</form>
```

### Javascript des vues

Créer un fichier view/nom-de-la-vue.js afin d'ajouter du Javascript à la vue.

## Débogage

```php
dump($expression);
```

Retourne un var_dump() avec formatage.