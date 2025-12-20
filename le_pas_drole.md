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

## 📝 TODO

### Interface web
- [ ] ColorPicker - Sélecteur de couleur RGB
- [ ] Gestion de segments - Contrôle individuel des 3 bandeaux
- [ ] Planificateur d'effets - Programmation horaire des animations

### Backend PHP
- [ ] Correction des dates - Fix DateTime dans les vues générées (CrudMaker)
- [ ] Loop MQTT - Écoute permanente des messages MQTT
- [ ] MQTT Message handler - Traitement des messages entrants/sortants
- [ ] Tâches asynchrones - Système de queue pour les jobs

### ESP32
- [ ] Gestion des mises à jour - OTA (Over The Air) firmware update
- [ ] Gestion de segments - Support des 3 bandeaux LED indépendants

---

## 🎯 Fonctionnalités prévues

- Interface web de contrôle des LED
- Gestion de séquences/effets lumineux
- Communication temps réel via MQTT
- Animations RGB personnalisables
- Programmation horaire des effets
- Mise à jour OTA des ESP32

