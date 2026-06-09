# Refonte Admin - Support IT

## Phase 1 : Layout & Sidebar Admin (Style Support)
- [ ] Créer le layout admin (admin/layouts/app.blade.php) avec sidebar collapsible
- [ ] Créer la sidebar admin avec tous les menus (Heroicons)
- [ ] Ajouter le header admin moderne
- [ ] Intégrer le dark mode et la command palette
- [ ] Configurer les notifications en temps réel (polling)

## Phase 2 : Dashboard Admin Moderne
- [ ] Refaire le controller Admin Dashboard avec toutes les stats
- [ ] Refaire la vue dashboard admin (8 KPI + 3 graphiques)
- [ ] Ajouter les graphiques (Chart.js)
- [ ] Ajouter les derniers tickets et conversations
- [ ] Ajouter l'état du système

## Phase 3 : Page Tickets Admin
- [ ] Refaire le controller Admin TicketController (filtres + tri + export)
- [ ] Refaire la vue tickets avec filtres avancés
- [ ] Ajouter pagination et tri
- [ ] Ajouter export CSV/Excel

## Phase 4 : Page Conversations Admin (style WhatsApp/Zendesk)
- [ ] Controller Admin ConversationController
- [ ] Vue avec liste utilisateurs → conversations → chat
- [ ] Fonctionnalités : répondre, voir messages IA, voir tickets liés

## Phase 5 : Base de Connaissance Admin
- [ ] Supprimer l'ancienne page "Ajouter solution"
- [ ] Refaire la page unique avec modal (CRUD)
- [ ] Controller et vues

## Phase 6 : Page Notifications Admin
- [ ] Refaire la page avec tous les types de notifications
- [ ] Temps réel

## Phase 7 : Page Monitoring Système
- [ ] Créer la page monitoring
- [ ] Service pour checker les statuts (Laravel, MySQL, n8n, Jira, Gmail, OpenAI)
- [ ] Vue moderne avec cartes de statut

## Phase 8 : Page Paramètres Admin
- [ ] Refaire le controller SettingsController
- [ ] Sections : Compte, Sécurité, Apparence, Intégrations
- [ ] Modifier les valeurs via DB (settings table) sans toucher .env

## Phase 9 : Routes & Finalisation
- [ ] Mettre à jour les routes web.php
- [ ] Tester la cohérence
