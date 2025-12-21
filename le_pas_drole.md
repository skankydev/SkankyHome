# 🏠 SkankyHome - Suivi du projet

## 📋 Architecture

### Backend - Framework SkankyDev
- ✅ Request / Response - Gestion des requêtes HTTP
- ✅ Routing - Système de routes avec paramètres
- ✅ Middlewares - Pipeline de middlewares
- ✅ Dependency Injection - MasterFactory
- ✅ FormBuilder - Construction de formulaires
- ✅ Validation - Validator + Rules
- ✅ Session / Flash - Gestion de session
- ✅ HtmlView - Rendu de vues
- ✅ MongoDB - MasterDocument + MasterCollection
- ✅ Behaviors - TimedBehavior (created_at/updated_at)
- ✅ Model binding - Injection automatique dans les controllers
- ✅ CLI - Génération CRUD interactive

### Infrastructure
- ✅ Raspberry Pi 4 - Ubuntu Server
- ✅ Apache + PHP 8.2 + extension MongoDB
- ✅ MongoDB local
- ✅ EMQX (broker MQTT)
- ✅ Composer
- ✅ Repository GitHub

### Stack technique
- **Backend** : PHP (SkankyDev) + MongoDB
- **Frontend** : Vue.js + SCSS (Vite)
- **Communication** : MQTT (EMQX)
- **Hardware** : ESP32 + LED WS2812B

---

# 📝 TODO - SkankyHome

## 🎨 Interface ScenarioMaker

### Boutons & Actions

- [ ] Bouton "Dupliquer step"
- [ ] Boutons pour réorganiser les steps (↑ monter / ↓ descendre)
- [ ] Bouton "Supprimer step" (déjà dans le HTML, à connecter)
- [ ] Bouton "Ajouter step" (déjà dans le HTML, à connecter)

### Améliorations visuelles (optionnel)
- [ ] Preview des couleurs directement sur la barre LED
- [ ] Animation lors du drag des curseurs
- [ ] Indicateur du segment actuellement édité
- [ ] Presets de segments ("Split en 2", "Split en 3", etc.)

---

## 🔧 Backend PHP

### SkankyDev Framework
- [ ] **Fix dates**


### MQTT Integration
- [x] Loop MQTT - Script PHP qui écoute en permanence les messages MQTT
- [x] MQTT Message handler - Traiter les messages entrants/sortants
- [x] Système de queue pour tâches asynchrones (jobs)

### Gestion horaires
- [ ] Interface pour définir les plages autorisées
  - Formulaire pour ajouter des créneaux (ex: 08:00-12:00)
  - Validation des horaires (pas de chevauchement)
- [ ] Backend pour stocker les plages dans MongoDB
- [ ] Logique pour activer/désactiver les LEDs selon l'heure

---

## 🤖 ESP32

### Réception MQTT
- [ ] Coder la réception des segments depuis MQTT
- [ ] Parser le JSON reçu
- [ ] Appliquer les segments avec `setSegment()`
- [ ] Gérer les 3 couleurs par segment (si l'effet le supporte)

### Gestion des lignes
- [ ] Support des 3 bandeaux indépendants
- [ ] Router les commandes vers la bonne ligne
- [ ] Tester avec plusieurs lignes simultanément

### Mises à jour
- [x] Mise à jour OTA (Over The Air)
- [x] Interface web pour uploader le firmware
- [x] Gestion des versions


---

## 📱 Interface Live Mode

### Améliorations
- [ ] Afficher l'état de connexion MQTT
- [ ] Liste des scénarios enregistrés
- [ ] Bouton "Play scenario" pour lancer un scénario
- [ ] Stop/Pause des animations
- [ ] Brightness global

---

## 📚 Documentation

### README
- [ ] README
- [ ] Screenshots de l'interface
- [ ] Guide d'installation
- [ ] Architecture du projet

### Code
- [ ] Commenter les parties complexes (drag curseurs, split segments)
- [ ] Documentation API MQTT (topics, formats JSON)

---

## 🧪 Tests & Debug

### À tester
- [ ] Scénario complet end-to-end (Interface → MQTT → ESP32 → LEDs)
- [x] Save/Load de scénarios complexes
- [ ] Gestion des erreurs MQTT (déconnexion, reconnexion)


### Performance
- [ ] Optimiser le nombre d'appels `splitSegment()`
- [ ] Throttle/debounce sur le drag des curseurs
- [ ] Lazy loading des effets dans la liste

---

## 🎯 Nice to Have (Bonus)

- [ ] Mode sombre/clair
- [ ] Export/Import de scénarios (JSON)
- [ ] Partage de scénarios entre utilisateurs
- [ ] Timeline pour prévisualiser le scénario dans le temps
- [ ] Historique des modifications (undo/redo)
- [ ] Présets de scénarios (Noël, Anniversaire, etc.)

---

## 📊 Priorités suggérées

### 🔥 Urgent (pour que ça marche bout en bout)
1. Finir les boutons manquants du ScenarioMaker
2. Coder la réception MQTT côté ESP32
3. Tester le flow complet

### ⚡ Important (pour la stabilité)
4. Fix dates dans CrudMaker
5. Loop MQTT backend PHP
6. Gestion des erreurs et reconnexions

### 🌟 Améliorations (quand tout marche)
7. Config horaires
8. Mises à jour OTA
9. Documentation complète
10. Nice to have

---

**Dernière mise à jour :** 21 décembre 2025
---

## 🎯 Fonctionnalités prévues

- Interface web de contrôle des LED
- Gestion de séquences/effets lumineux
- Communication temps réel via MQTT
- Animations RGB personnalisables
- Programmation horaire des effets
- Mise à jour OTA des ESP32

