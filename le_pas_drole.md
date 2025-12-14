# 🏠 SkankyHome 

## 🎯 SkankyDev - Le Framework

### Core du framework

✅ Request / Response - Gestion des requêtes HTTP
✅ Routing - Système de routes avec paramètres
✅ Middlewares - Pipeline de middlewares
✅ MasterFactory avec DI - Injection de dépendances
✅ FormBuilder - Construction de formulaires (TextField, Email, etc.)
✅ Système de validation - Validator + Rules
✅ Session / Flash - Gestion de session avec messages flash
✅ HtmlView - Rendu de vues

### Couche MongoDB

✅ MasterDocument - Modèle de base avec Persistable
✅ MasterCollection - find/insert/update/delete/aggregate/paginate
✅ Behaviors - TimedBehavior pour created_at/updated_at
✅ Model binding automatique - Tu peux faire show(Sequence $sequence) dans tes controllers et ça charge automatiquement l'objet depuis l'ID en paramètre

### Outils CLI

✅ Génération CRUD interactive - Commande CLI pour créer des controllers/models/vues complets

## 🏠 SkankyHome - Le Projet Domotique

Contexte : Application de contrôle de LED pour tes étagères Lego, tournant sur Raspberry Pi 4.

### Stack technique :

Backend : PHP (SkankyDev framework) + MongoDB
Frontend : Vue.js + SCSS (compilé avec Vite)
Communication : MQTT via EMQX
Hardware : ESP32 + LED WS2812B

### Ce qui est configuré :

✅ Installation Ubuntu Server sur Raspberry Pi
✅ Configuration Apache + PHP 8.2 + extension MongoDB
✅ Installation MongoDB en local
✅ Installation EMQX (broker MQTT)
✅ Installation Composer
✅ Création du repo GitHub

### Fonctionnalités prévues :

Interface web de contrôle des LED
Gestion de séquences/effets lumineux
Communication en temps réel via MQTT vers les ESP32
Animations RGB pour faire briller tout ça