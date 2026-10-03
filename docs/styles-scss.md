# Styles (SCSS)

Référence détaillée. Vue d'ensemble (build + principe « réutiliser l'existant ») dans [CLAUDE.md](../CLAUDE.md).

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

## Design tokens (`settings/variables.scss`)

- **Fonds** : `$bg-primary`, `$bg-secondary`, `$bg-tertiary`
- **Néon** : `$neon-red/orange/yellow/lime/green/cyan/blue/purple/magenta/pink` + map `$neon-colors` + `$neon-gradient` / `$neo-gradient`
- **Statuts** : `$success`, `$error`, `$warning`, `$favorie`, `$info`, `$disabled` + map `$tool-colors` (`primary/success/error/warning/favorie/info/disabled`)
- **Texte** : `$text-primary`, `$text-secondary`, `$text-muted` — **Bordure** : `$border-color`
- **Espacements** : `$p-xs` (4) `$p-s` (8) `$p-ms` (10) `$p-m` (16) … `$p-massive` (128) + map `$spaces`

## Conventions utiles

- **Réutiliser l'existant d'abord** : avant d'écrire du SCSS, composer les classes génériques (`elements/`, `tools/`, `scaffold/`). Ne créer un partial `component/` que si rien ne convient — ne pas re-styler ce qui a déjà une classe.
- **Badges de statut** : classe `.status-{nom}` (générée depuis `$tool-colors` dans `tools/neon.scss`) → texte coloré + neon glow. Les enums exposent `class()` (`status-warning`…) et `pretty()` pour les rendre.
- **Cards** : `.card` + variante colorée `.card-{tool-color}` (ex. `.card-success`).
