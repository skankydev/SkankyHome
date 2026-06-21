# SkankyHome — Contexte projet

Application de domotique : contrôle de LEDs RGB WS2812B via MQTT, modules ESP32, hébergé sur Raspberry Pi.
Basée sur le framework PHP MVC maison **SkankyDev**.

---

## Stack

- PHP 8.4 + MongoDB 2.1 (driver officiel)
- MQTT broker : `skankyhome.local` (port 1883, WebSocket 8083)
- Vue 3 + Vite + SCSS
- Point d'entrée web : `public/index.php`
- Point d'entrée CLI : `php craft`

---

## Tests

Suite **PHPUnit** dans `tests/`. Elle couvre **surtout le framework `SkankyDev`** (le cœur réutilisable) — la partie applicative `App` est très peu testée.
- `tests/SkankyTest/TestCase/` — tests unitaires (pas de dépendance externe)
- `tests/SkankyTest/Integration/` — tests d'intégration (nécessitent MongoDB)

Scripts Composer (à privilégier) :

```bash
composer test              # phpunit — toute la suite
composer coverage          # phpunit --coverage-text
composer coverage-pretty   # phpunit --coverage-html build/coverage
```

Ciblage fin si besoin :

```bash
php vendor/bin/phpunit tests/SkankyTest/TestCase/Model/...      # un fichier
php vendor/bin/phpunit --filter testMagicGetExistingProperty    # un test
```

> Validation : pour un changement dans `SkankyDev`, lancer `composer test` (PHPUnit attrape syntaxe **et** régressions). **Ne pas faire de `php -l`** — inutile : les tests couvrent la syntaxe du code testé, et pour le reste (templates, vues, `App`) les erreurs de syntaxe se voient tout de suite à l'exécution.

---

## Architecture SkankyDev

### Cycle de vie d'une requête

`public/index.php` → `Application::run()` :
1. `Config::initConf()` + handler d'exceptions global.
2. `Request::getInstance()` (superglobales → objet).
3. `Router::_findCurrentRoute($uri)` : route explicite (`routes/routes.php`) sinon fallback par convention.
4. `MiddlewareManager` exécute le pipeline (globaux + attributs `#[Middleware]`), terminé par :
5. `MasterFactory` instancie le controller et appelle l'action (Collections injectées par type, Documents résolus depuis les params d'URL).
6. L'action retourne une `Response` (`view()` / `redirect()` / `response()`) → `->send()`.

CLI : `craft` → `CliApplication` (même `Config::initConf`, auto-découverte des commandes).

### Routing

Routing par **convention** — pas besoin de déclarer les routes CRUD.

L'URI est parsée directement : `/controller/action/param1/param2`
→ `App\Controller\{Controller}Controller::{action}([param1, param2])`

Les routes explicites dans `routes/routes.php` ne servent que pour les exceptions (ex: `/` → HomeController).

Le fallback est dans `CurrentRoute::initFromUri()` — si aucune route définie ne matche, il parse l'URI.

### Singleton pattern

Les Collections utilisent un Singleton avec magic `__callStatic` qui préfixe les méthodes d'un underscore :

```php
ModuleCollection::_save($module);        // appelle ->save()
ModuleCollection::_findById($id);        // appelle ->findById()
ModuleCollection::_deleteOne($module);   // appelle ->deleteOne()
```

### Model binding

`MasterFactory` utilise la Reflection pour injecter automatiquement dans les controllers :
- Les Collections → injectées par type hint
- Les Documents → récupérés en base depuis les params de route et injectés

```php
public function show(Request $request, Module $module) {
    // $module est automatiquement résolu depuis l'ID dans l'URL
}
```

### MasterFactory (instanciation & injection)

Conteneur DI maison, appelé en statique (Singleton) : `MasterFactory::_make()`, `MasterFactory::_call()`.
- `_make(class, args)` : instancie en résolvant le constructeur. Gère les Singleton (`getInstance()`), les classes sans constructeur, et la DI récursive.
- `_call(objet, méthode, params)` : appelle une méthode en résolvant ses paramètres.
- **Résolution d'un paramètre** : sous-classe de `MasterDocument` → `find()` depuis l'ID de route (clé nommée ou positionnelle) ; clé nommée présente → telle quelle ; type natif/sans type → valeur fournie ou défaut ; autre classe → `_make()` récursif.

### Middleware

Pipeline exécuté par `MiddlewareManager` (dans `Application::run`) : middlewares globaux + ceux des attributs, dans l'ordre, se terminant par l'appel au controller.

- **Contrat** : `MiddlewareInterface::handle(Request $request, callable $next): mixed`. On appelle `$next($request)` pour continuer, ou on **retourne une `Response`** pour court-circuiter (auth refusée, CSRF, etc.).
- **Globaux** : déclarés dans la config `middlewares` (clé => alias), résolus via la map `class.middlewares`. Ordre = ordre d'exécution (ex. `Session` avant `Csrf`).
- **Ciblés** : attribut `#[Middleware(Alias::class, ...args)]` sur le controller (toutes les actions) ou sur une action (celle-là seulement). Les args vont au constructeur du middleware (via `MasterFactory`).
- **Sur une route explicite** : `Router::_add('/x', [...])->setMiddlewares(['Alias', ...])` (peu utilisé ici, le routing est surtout par convention).
- Exemples livrés : `SessionMiddleware` (session), `CsrfMiddleware` (CSRF, global), `PostOnly` (n'autorise que POST — opt-in via `#[Middleware('PostOnly')]` sur les actions de mutation : store/update/delete/setStatus).

Côté front, un lien qui doit déclencher un POST (ex. delete) utilise `data-method="post"` (+ `data-confirm="…"`) : un handler global dans `app.js` construit et soumet un `<form method=post>` avec le token CSRF. → un `delete` se sécurise avec **`#[Middleware('PostOnly')]` côté action + `data-method="post"` côté lien**.

```php
#[Middleware(AuthMiddleware::class)]                 // tout le controller
class AdminController extends MasterController {
    #[Middleware(PermissionMiddleware::class, 'edit')] // cette action
    public function edit(...) {}
}
```

### Vues

- Templates PHP dans `src_front/view/`
- Layout : `$this->setLayout('layout.default')`
- Rendu : `return view('dossier.fichier', ['var' => $val])`
- URL helper : `$this->url(['action' => 'show', 'params' => [$id]])`
- Échappement XSS : `e($valeur)` — à utiliser systématiquement sur les données utilisateur

### FormBuilder

```php
$this->add('name', 'text', ['label' => 'Nom', 'rules' => ['required']]);
$this->submit('<i class="icon-save"></i> SAVE');
```

Types de champs disponibles : `text`, `textarea`, `number`, `checkbox`, `radio`, `select`, `file`, `icon`, `hidden`

### Validation

Règles dans les Forms, vérifiées via `$form->validate($input)`.
En cas d'échec : `redirect()->withErrors($form->getErrors())->withInput($input)`

Règles dispo (config `class.rules`) : `required`, `email`, `numeric`, `min`, `max`, `min_length`, `max_length`, `regex`, `confirmed`, `same`, `hex_color`. Syntaxe : `'rules' => ['required', 'min_length:3', 'max:255']` (params après `:`). Fail-fast : 1ʳᵉ règle qui casse par champ.

### CSRF

`CsrfMiddleware` (global, après `Session`) valide les requêtes **POST/PUT/PATCH/DELETE** : token `_token` (forms) ou header `X-CSRF-Token` (AJAX), comparé au token de session (1 par session, via `csrf_token()`). Échec → 419 JSON (AJAX) ou redirect + flash.
- **Forms** : `FormBuilder` injecte `_token` automatiquement (rien à faire).
- **AJAX** : `app.js` wrappe `fetch` pour ajouter `X-CSRF-Token` (lu du `<meta name="csrf-token">`) → automatique pour tout `fetch` same-origin.
- Les mutations sensibles doivent être en POST + `#[Middleware('PostOnly')]` (cf. section Middleware). Le CrudMaker génère déjà `PostOnly` sur `store`/`update`/`delete` et le lien delete en `data-method="post"`.

### Réponses

Une action retourne une `Response` (jamais d'`echo`). Trois fabriques (`function.php`) :
- `view('dossier.fichier', $data)` → vue HTML (ou JSON si `Accept: application/json`).
- `redirect(['action' => ...])` → 302 + `Location`. Chaînable : `->withFlash()`, `->withErrors()`, `->withInput()`.
- `response($data)` → **réponse de données** : toujours du JSON (pas de vue), pour l'AJAX. `->status(419)`, etc.

`Response::build()` rend du JSON si le client le demande **ou** s'il n'y a pas de vue ; sinon la `HtmlView`. `send()` construit le body des 2xx et 4xx/5xx (pas des redirections 3xx).

### Parts

Bouts de vue réutilisables (façon View Cells) : `$this->part('part.markdown', ['content' => $x])`
- rend `src_front/view/part/markdown.php` ;
- si une classe `App\View\Part\PartMarkdownPart` existe (convention : segments du nom capitalisés + `Part`), son `data($options)` est fusionné aux variables avant rendu.
Exemples : `part.table` (table data-driven, vue pure), `part.markdown` / `part.breadcrumb` (avec classe).

### Config

`Config::initConf()` fusionne, dans l'ordre : `src/SkankyDev/Config/default.config.php` ← config de chaque module (`src/{Module}/Config/config.php`) ← `config/master.config.php`. Accès : `Config::get('chemin.pointe')`. Y vivent `middlewares`, `class.middlewares`, `class.fields`, `class.rules`, `Module`, `paginator`, `icons`…

### Gestion des erreurs

`ExceptionHandler` est le handler global (+ shutdown pour les fatales). Debug (`Config::get('debug')`) → page détaillée (classe, fichier, trace) ; prod → page générique. Le code de l'exception sert de statut (ex. `throw new ...Exception('...', 404)`).

### Helpers globaux (`function.php`)

`view()`, `redirect()`, `response()`, `url()`, `asset()` ; `e()` (échappement HTML), `json()` ; `csrf_field()` / `csrf_token()` ; `flash()`, `old()`, `error()` ; `debug()`.

---

## Modèles MongoDB

### Structure type

```
src/App/Model/
├── Document/Module.php       ← MasterDocument (propriétés typées)
└── ModuleCollection.php      ← MasterCollection + Singleton
```

**Documents** : classes avec propriétés publiques typées PHP, étendent `MasterDocument`.
Pour bénéficier de `created_at` / `updated_at` automatiques, le document fait `use TimedTrait` (opt-in).

**Collections** : déclarent `$collectionName` (nom MongoDB) et `$documentClass`.

### Types & enums (conversion par Reflection)

`MasterDocument` convertit les propriétés **selon leur type déclaré** (plus de regex sur le nom) :
- `ObjectId` ← string (fill) ; stocké tel quel ; → string en JSON. FK = toujours typées `ObjectId` (cf. convention plus bas).
- `BackedEnum` ← string via `tryFrom()` (fill / relecture Mongo) ; → `->value` en base et en JSON.
- `DateTime` ← string ISO (fill, parse l'input navigateur) ; → `UTCDateTime` en base.
- Une chaîne vide / valeur invalide laisse le **défaut** de la propriété (pas de crash) — donc une prop enum/ObjectId doit avoir un défaut si requise (ex. `public TaskStatus $status = TaskStatus::TODO;`).

**Convention status (enum)** : un backed enum string dans `App\Model\Enum\` avec `label()` (libellé FR) et `options()` (`value => label`, pour un `select`). Les enums de statut exposent en plus `class()` (→ `status-xxx`, cf. SCSS) et `pretty()` (badge HTML).
- Form : `$this->add('status','select',['options' => TaskStatus::options(), ...])`.
- Vue : `$x->status->pretty()` (badge) ou `e($x->status->label())`.

### Behaviors

Un behavior se compose de **deux éléments liés par convention** :
- un **trait** dans `Model/Document/Traits/` (`FooTrait`) qui déclare les propriétés gérées par le behavior — typées, donc pas de `#[\AllowDynamicProperties]` ;
- une **classe** dans `Model/Behavior/` (`FooBehavior` étend `MasterBehavior`) qui implémente les hooks (`beforeInsert`, `afterUpdate`, etc.).

Le **document est la source de vérité** : il active un behavior simplement en utilisant son trait (`use TimedTrait`). `MasterCollection::loadBehaviors()` inspecte les traits du `$documentClass` et résout la classe par convention : `...\Model\Document\Traits\FooTrait` → `...\Model\Behavior\FooBehavior` (même namespace racine, donc marche aussi dans `App\`). Plusieurs behaviors = plusieurs traits, ils se composent ; un conflit de propriété entre deux traits = erreur fatale explicite (garde-fou).

> **Convention à respecter** : les clés étrangères (`module_id`, `scenario_id`, etc.) doivent être stockées en `ObjectId`, pas en string. MongoDB ne force pas les relations mais les aggregations `$lookup` sont plus propres avec des ObjectId des deux côtés.

### CRUD Collection

```php
$collection->find([filtre]);
$collection->findById($id);
$collection->paginate([], Request::_paginateInfo());
Collection::_save($document);    // insert ou update selon présence de _id
Collection::_deleteOne($doc);
```

---

## Système MQTT

### Publier

```php
MqttSender::publish($topic, $message, $qos = 0);
// $message peut être array (sérialisé JSON) ou string
```

### Queue de jobs

Jobs stockés en MongoDB via `MongoDB\BSON\Persistable` — l'objet est sérialisé/désérialisé automatiquement sans passer par le constructeur.

Structure d'un job en base :
- `payload` : instance du Job (aussi Persistable) avec les données métier
- `status` : pending / processing / completed / failed
- `attempts` / `max_attempts` : retry automatique (max 3)
- `started_at`, `completed_at`, `created_at`, `updated_at`

Le worker tourne en CLI : `php craft queue:work`

### Écoute MQTT

```bash
php craft mqtt-loop
```

Subscribe à `skankyhome/info/#`, pousse chaque message en queue comme `MqttMessageJob`.
Dispatch dynamique : message `{'cmd': 'hello'}` → appelle `helloCmd()` sur le job.

---

## CLI (craft)

Point d'entrée : `php craft <signature> [args]`. `CliApplication` auto-découvre les commandes dans le dossier `Command/` de chaque module (+ SkankyDev). `php craft help` les liste.

Créer une commande : classe dans `src/App/Command/` étendant `MasterCommand`, avec les propriétés statiques `$signature` + `$help`, et la méthode `run(array $arg)`. Helpers CLI (trait `CliMessage`) : `info`/`success`/`warning`/`error`/`text`, `ask`, `choice`, `valide`.

Commandes livrées : `crud-maker`, `queue:work`, `mqtt-loop`.

---

## Génération CRUD

```bash
php craft crud-maker NomDocument
```

Génère interactivement : Document, Collection, Controller, Form, et les 4 vues (index, create, edit, show).

Templates dans `src_front/template/` — utilisent `%?php` / `?%` / `%?=` à la place des balises PHP pour éviter l'exécution pendant la génération. Le CrudMaker remplace ces pseudo-balises après le rendu.

---

## Frontend Vue 3

Composants montés sur des IDs dans les vues PHP :

```js
// app.js
createApp(ScenarioMaker).mount('#ScenarioMaker')
createApp(LiveMode).mount('#LiveMode')
createApp(IconPicker).mount('#AppForm')
```

**LiveMode.vue** : client MQTT WebSocket direct depuis le navigateur (`ws://skankyhome.local:8083/mqtt`), contrôle temps réel des LEDs.

**ScenarioMaker.vue** : éditeur d'animations LED (curseurs draggables, steps, segments, couleurs, effets). Sauvegarde via AJAX.

**ColorPicker.vue** : canvas interactif, fait maison — pas de lib externe.

---

## Styles (SCSS)

Source dans `src_front/scss/`, compilé par **Vite** vers `public/dist/styles.css` (avec `app.js`).
Toute modif SCSS nécessite un build pour être visible :

```bash
npm run dev      # ou: npm run watch — build watch (dev)
npm run build    # ou: npm run prod  — build production
```

Point d'entrée : `main.scss` qui `@use` tous les partials. Organisation :
- `settings/` — `variables` (tokens), `mixins`, `functions`, `animations`
- `tools/` — utilitaires : `space` (classes `.p-s`, `.p-m`…), `layout` (`.grid-layout`, `.grid-half`), `neon`, `scrollable`, `accordion`, `tooltips`
- `scaffold/` — `page`, `burger`, `home`
- `elements/` — briques UI : `card`, `button`, `form`, `table`, `liste`, `title`, `breadcrumb`, `flash`, `link`, `diviser`, `image`
- `component/` — un fichier par feature/composant (`scenario-maker`, `color-picker`, `persona-chat`…)

**Ajouter un style de feature** : créer un partial dans `component/` (ou `elements/`), l'ajouter dans `main.scss` (`@use 'component/...'`), puis rebuild.

### Design tokens (`settings/variables.scss`)

- **Fonds** : `$bg-primary`, `$bg-secondary`, `$bg-tertiary`
- **Néon** : `$neon-red/orange/yellow/lime/green/cyan/blue/purple/magenta/pink` + map `$neon-colors` + `$neon-gradient` / `$neo-gradient`
- **Statuts** : `$success`, `$error`, `$warning`, `$favorie`, `$info`, `$disabled` + map `$tool-colors` (`primary/success/error/warning/favorie/info/disabled`)
- **Texte** : `$text-primary`, `$text-secondary`, `$text-muted` — **Bordure** : `$border-color`
- **Espacements** : `$p-xs` (4) `$p-s` (8) `$p-ms` (10) `$p-m` (16) … `$p-massive` (128) + map `$spaces`

### Conventions utiles

- **Réutiliser l'existant d'abord** : avant d'écrire du SCSS, composer les classes génériques (`elements/`, `tools/`, `scaffold/`). Ne créer un partial `component/` que si rien ne convient — ne pas re-styler ce qui a déjà une classe.
- **Badges de statut** : classe `.status-{nom}` (générée depuis `$tool-colors` dans `tools/neon.scss`) → texte coloré + neon glow. Les enums exposent `class()` (`status-warning`…) et `pretty()` pour les rendre.
- **Cards** : `.card` + variante colorée `.card-{tool-color}` (ex. `.card-success`).

---

## Conventions LED / ESP32

Les effets LED ont toujours **3 couleurs** dans le payload, même si l'effet n'en utilise qu'une ou deux. Les couleurs inutilisées sont des strings vides `""`. C'est volontaire — l'ESP32 attend toujours un tableau de 3.

Structure d'un scénario en base / MQTT :
```json
{
  "line_0": {
    "steps": [{
      "duration": 5,
      "cursors": [0, 15, 30, 59],
      "segments": [{
        "first": 0, "last": 14,
        "effect": 44,
        "colors": ["#ff0000", "", ""],
        "speed": 1000,
        "reverse": false
      }]
    }]
  }
}
```

---

## Ce qui est en cours / incomplet

ScenarioMaker est fonctionnel (quelques évolutions possibles, rien de marquant). LiveMode est abandonné.
