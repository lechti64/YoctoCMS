# Yocto
Un CMS simple, rapide et moderne.

[Site](http://yoctocms.com/) - [Forum](http://forum.yoctocms.com/) - [GitHub](https://github.com/remijean/YoctoCMS/)

## Sommaire

- [Chemin d'exécution](#chemin-dexécution)
- [Schéma de la base de données](#schéma-de-la-base-de-données)
- [Propriétés publiques](#propriétés-publiques)
- [Méthodes publiques](#méthodes-publiques)
- [Base de données](#base-de-données)
- [Template](#template-1)
- [Type de page d'exemple](#type-de-page-dexemple)
- [Débogage](#débogage)

## Chemin d'exécution

### 1. index.php

- Ouvre une session,
- Charge les autoloaders,
- Charge le gestionnaire d'erreurs,
- Génère la base de données par défaut,
- Récupère la configuration ([$_configuration](#données-de-configuration)),
- Récupère l'utilisateur courant ([$_user](#données-de-lutilisateur-courant)),
- Récupère la page courante ([$_page](#données-de-la-page-courante)),
- Récupère les données du type rattaché à la page courante ([$_type](#données-du-type-rattaché-à-la-page-courante)),
- Importe le routeur du type de la page courante.

### 2. type/[type]/router.php

- Crée une instance du contrôleur,
- Initialise les contrôleurs en fonction des routes.

### 3. type/[type]/Controller[type].php

- Actions rattachées au contrôleur (ex. : définition de la vue, du layout, des librairies, enregistrement de données...).

### 4. type/[type]/view/[vue].php

- Vue à afficher par le contrôleur.

## Schéma de la base de données

- `configuration` : Configuration du site.
- `group` : Groupes d'utilisateurs.
- `navigation` : Items du menu de navigation.
- `page` : Données des pages.
- `page-[type]` Données des types rattachés aux pages.
- `user` : Données des utilisateurs.

## Propriétés publiques

### Données de la page courante

```php
dump($this->_page);
```

### Données du type rattaché à la page courante

```php
dump($this->_type);
```

### Données de l'utilisateur courant

```php
dump($this->_user);
```

### Données de configuration

```php
dump($this->_configuration)
```

## Méthodes publiques

### Rechercher une clé dans les méthodes HTTP

#### Dans toutes les méthodes

```php
dump($this->get('nom-de-clé'));
```

L'ordre de recherche est le suivant : POST, GET puis COOKIE.

#### Dans une méthode spécifique

```php
dump($this->get('GET:nom-de-clé'));
```

##### Méthodes

- `COOKIE` : recherche dans $_COOKIE.
- `GET` : recherche dans $_GET.
- `POST` : recherche dans $_POST.

### Rendre un champ obligatoire

```php
dump($this->get('nom-de-clé', true));
```

La soumission du formulaire échoura si "nom-de-clé" est vide. De plus une notice sera ajoutée dans la vue au champ rattaché :

```
echo $this->tpl->input('nom-de-clé')
```

### Utliser le template

```php
$this->getTemplate()
```

[Voir la section dédiée](#template-1) pour plus d'informations sur l'utilisation du template.

### Configurer un layout

```php
$this->setLayout('nom-de-layout');
```

##### Layouts

- `main` : layout principal.
- `raw` : layout sans aucun HTML / CSS ajouté.

### Configurer une vue

```php
$this->setView('nom-de-vue');
```

### Configurer une librairie

```php
$this->setVendor(
    'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css',
    'sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T'
);
```

Importe une librairie depuis un CDN, le SRI n'est pas obligatoire.

### Configurer l'alerte de soumission

```php
$this->setAlert('Mon alert.', 'danger');
```

Il est possible d'utiliser `null` afin de cacher l'alerte après la soumission :

```php
$this->setAlert(null);
```

[Voir la section dédiée](#alerte) pour plus d'informations sur les types d'alerte.

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
dump($row);
```

#### Sélection multiple

```php
$rows = Database::instance('nom-de-table')->findAll();
foreach ($rows as $row) {
    dump($row);
}
```

### Insertion

```php
$row = Database::instance('nom-de-table');
$row->title = 'Un titre';
$row->position = 10;
$row->status = false;
$row->save();
```

### Mise à jour

```php
$row = Database::instance('nom-de-table')
    ->where('id', '=', 10)
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

### Bootstrap

[Voir la documentation de Bootstrap](https://getbootstrap.com/docs/4.3/getting-started/introduction/)

### Alerte

```php
echo $this->getTemplate()->alert('Une alerte.', 'success', [
    'dismissible' => true,
])
```

##### Types d'alerte

- `primary` : alerte bleu.
- `secondary` : alerte gris.
- `success` : alerte vert.
- `danger` : alerte rouge.
- `warning` : alerte orange.
- `info` :  alerte bleu clair.
- `light` : alerte gris clair.
- `dark` : alerte gris foncé.

##### Attributs spécifiques

- `class` : classe de l'alerte.
- `dismissible` : bouton pour dissimuler l'alerte.

##### Attributs génériques

- Aucun

### Bouton

```php
echo $this->getTemplate()->button('id-du-bouton', 'Valider', [
    'type' => 'submit',
])
```

##### Attributs spécifiques

- Aucun

##### Attributs génériques

- [Voir la documentation de Mozilla](https://developer.mozilla.org/fr/docs/Web/HTML/Element/Button#Attributs)

### Champ court

```php
echo $this->getTemplate()->input('id-du-champ', [
    'label' => 'Adresse email',
    'type' => 'email',
    'value' => 'email@yoctocms.com',
])
```

##### Attributs spécifiques

- `label` : Label au dessus du champ

##### Attributs génériques

- [Voir la documentation de Mozilla](https://developer.mozilla.org/fr/docs/Web/HTML/Element/Input#Attributs)

### Champ long

```php
echo $this->getTemplate()->textarea('id-du-champ', 'Un commentaire', [
    'label' => 'Commentaire',
])
```

##### Attributs spécifiques

- `label` : Label au dessus du champ

##### Attributs génériques

- [Voir la documentation de Mozilla](https://developer.mozilla.org/fr/docs/Web/HTML/Element/Textarea#Attributs)

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
$controller = new Yocto\ControllerExample($_configuration, $_page, $_type, $_user);;

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
            ->title = $this->get('title', true)
            ->save();
        $this->_type
            ->foo = $this->get('foo')
            ->bar = $this->get('bar')
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
    <?php echo $this->getTemplate()->input('title', [
        'label' => 'Titre de la page',
        'value' => $this->_page->title,
    ]); ?>
    <?php echo $this->getTemplate()->input('foo', [
        'label' => 'Titre de la page',
        'value' => $this->_type->foo,
    ]); ?>
    <?php echo $this->getTemplate()->input('bar', [
        'label' => 'Titre de la page',
        'value' => $this->_type->bar,
    ]); ?>
    <?php echo $this->getTemplate()->button('submit', 'Valider', [
        'type' => 'submit',
    ]); ?>
</form>
```

## Débogage

```php
debug($expression);
```

Retourne un var_dump() avec formatage.