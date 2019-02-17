# Yocto
En développement.

## Chemin d'exécution

### 1. index.php

- Ouvre une session,
- Charge l'autoloader des classes,
- Récupère l'utilisateur courant,
- Récupère la page courante,
- Importe le routeur du type de la page courante.

### 2. type/[type]/router.php

- Crée une instance du contrôleur,
- Initialise les contrôleurs en fonction des routes.

### 3. type/[type]/Controller[type].php

- Actions rattachées au contrôleur (ex. : définition de la vue, du layout, des librairies, enregistrement de données...).

### 4. type/[type]/view/[vue].php

- Vue à afficher par le contrôleur.

## Base de données (src/Database.php)

### Création

Bientôt...

### Sélections

#### Sélection simple

```
$row = Database::instance('table")->find();

echo $row->colonne;
```

#### Sélection multiple

```
$rows = Database::instance('table")->findAll();

foreach ($rows as $row) {
    echo $row->colonne;
}
```

### Insertion

Bientôt...

### Mise à jour

Bientôt...

### Conditions

#### Where

```
$rows = Database::instance('table")
    ->where('colonne_1', '=', 'valeur_1')
    ->andWhere('colonne_2', '!=', 'valeur_2')
    ->orWhere('colonne_3', 'IN', [
        'valeur_3',
        'valeur_4',
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

```
$rows = Database::instance('table")
    ->limit(0, 10)
    ->findAll();
```

#### OrderBy

```
$rows = Database::instance('table")
    ->orderBy('colonne', 'ASC')
    ->findAll();
```

```
$rows = Database::instance('table")
    ->orderBy('colonne', 'DESC')
    ->findAll();
```

#### Conditions multiples

```
$rows = Database::instance('table")
    ->where('colonne_1', '=', 'valeur_1')
    ->andWhere('colonne_2', '!=', 'valeur_2')
    ->orWhere('colonne_3', 'IN', [
        'valeur_3',
        'valeur_4',
    ])
    ->limit(0, 10)
    ->orderBy('colonne_1', 'ASC')
    ->findAll();
```

### Enregistrement

Bientôt...

### Suppression

Bientôt...