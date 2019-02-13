# Yocto
En développement.

## Cheminement lors de l'exécution

### 1. index.php
- Ouvre une session,
- Charge l'autoloader des classes,
- Crée une instance de la base de données,
- Récupère l'id de la page courante,
- Importe le routeur du type rattaché à la page courante.

### 2. type/[TYPE]/router.php
- Crée une instance du contrôleur,
- Initialise les contrôleurs en fonction des routes.

### 3. type/[TYPE]/Controller[TYPE].php
- Actions rattachées au contrôleur (ex. : définition de la vue, du layout, des librairies, enregistrement de données...).

### 4. type/[TYPE]/view/[VUE].php
- Vue à afficher par le contrôleur.