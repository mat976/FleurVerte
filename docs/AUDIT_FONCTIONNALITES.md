# 🌸 FleurVerte — Audit des fonctionnalités

Document d'inventaire exhaustif des fonctionnalités du projet, avec leur état de fonctionnement.

**Légende :**
- ✅ **OK** — fonctionne correctement
- 🟡 **Partiel** — fonctionne mais avec limitations
- 🔴 **Cassé** — bug connu qui empêche le bon fonctionnement
- ❌ **Absent** — non implémenté

---

## 1. Authentification & Utilisateurs

| Fonctionnalité | État | Détails |
|---|---|---|
| Inscription (`/register`) | ✅ | `RegisterFormType`, avatar aléatoire, création Client auto |
| Connexion (`/login`) | ✅ | Bcrypt cost 12, CSRF activé |
| Déconnexion (`/logout`) | ✅ | |
| Suppression de compte | 🟡 | Session non invalidée après delete |

## 2. Catalogue & Recherche

| Fonctionnalité | État | Détails |
|---|---|---|
| Liste produits (`/product`) | ✅ | Affichage grille, images, prix |
| Détail produit (`/product/{id}`) | ✅ | Page complète avec tags, notes, commentaires |
| Recherche (`/search`) | ✅ | Par nom, description, tag |
| Tri (prix ASC/DESC, nom) | ✅ | `FleurService::search()` |
| Filtrage par tag | ✅ | |
| Statut stock (OK/faible/rupture) | ✅ | `getStockStatus()` |

## 3. Profils

| Fonctionnalité | État | Détails |
|---|---|---|
| Voir profil (`/profil`) | ✅ | |
| Éditer profil (`/profil/edit`) | ✅ | Username, email, avatar |
| Upload avatar | ✅ | Vich Uploader, validation 1MB, JPEG/PNG |
| Avatars prédéfinis | ✅ | 10 avatars |
| Toggle Client/Fleuriste | 🟡 | Fonctionne mais sans validation SIRET (à venir) |

## 4. Panier

| Fonctionnalité | État | Détails |
|---|---|---|
| Ajouter au panier | 🔴 | Bug `inversedBy: 'cartItems'` invalide dans `CartItem.php` |
| Voir panier | ✅ | |
| Supprimer article | ✅ | |
| Modifier quantité | ✅ | |
| Vider panier | ✅ | |
| Calcul total avec promo | ✅ | `getPrixEffectif()` |
| Compteur session | ✅ | `CartSessionSubscriber` |
| **Commande / Checkout** | ❌ | **Aucune entité Order, pas de paiement** |

## 5. 💬 Chat en direct (Messagerie)

| Fonctionnalité | État | Détails |
|---|---|---|
| Liste conversations (`/messages`) | ✅ | Tri par activité, aperçu dernier message |
| Affichage conversation | 🔴 | Bug `Criteria` non importé dans `Conversation.php::getDernierMessage()` |
| Nouveau message (form classique) | ✅ | POST `/messages/conversation/{id}` |
| **Envoi AJAX temps réel** | ✅ | POST `/messages/api/conversation/{id}/send` |
| **Polling live (2s)** | ✅ | GET `/messages/api/conversation/{id}/messages?lastMessageId=X` |
| Marquer comme lu | ✅ | Batch update DB via `markAsReadByConversationAndUser` |
| Badge messages non lus (nav) | ✅ | `MessageNotificationSubscriber` + cache 60s |
| Initier conversation | ✅ | `/messages/nouveau/{fleuristeId}` |
| Compteur non-lus par conv | ✅ | `countMessagesNonLus()` |
| Auto-scroll + animations | ✅ | JS inline dans `conversation.html.twig` |
| Protection d'accès | ✅ | `ConversationService::checkAccess()` |
| **WebSocket / Push réel** | ❌ | Polling uniquement, pas de Mercure |

**Verdict chat** : Fonctionnel en polling (2s), bug bloquant sur `getDernierMessage()` à corriger.

## 6. ⭐ Commentaires & Notes

| Fonctionnalité | État | Détails |
|---|---|---|
| Publier commentaire | ✅ | Formulaire sur page détail produit |
| Note 0-5 étoiles | ✅ | Validation `Range` |
| Validation longueur min (10 car) | ✅ | Contrainte `Length` |
| Afficher commentaires | ✅ | Liste avec avatar, date, note |
| Note moyenne produit | ✅ | `Fleur::getNoteMoyenne()` |
| Nombre de commentaires | ✅ | `Fleur::getNombreCommentaires()` |
| Supprimer (auteur ou admin) | 🟡 | Logique OK mais `ROLE_ADMIN` n'existe pas |
| Modifier commentaire | ❌ | Pas d'édition possible |
| Modération admin | ❌ | Pas d'interface admin |
| Réponse du fleuriste | ❌ | |

**Verdict commentaires** : Fonctionnel, manque modération admin + édition.

## 7. Tags

| Fonctionnalité | État | Détails |
|---|---|---|
| CRUD tags (fleuristes) | ✅ | Réservé `ROLE_FLEURISTE` |
| Couleur hexadécimale | ✅ | Validation Regex |
| Couleur texte auto (WCAG) | ✅ | `getTextColor()` |
| Associer tag à fleur | ✅ | ManyToMany |

## 8. Promotions

| Fonctionnalité | État | Détails |
|---|---|---|
| Pourcentage de promo | ✅ | 0-100% |
| Dates début/fin | ✅ | |
| Toggle activation | ✅ | |
| Prix promo calculé | ✅ | `getPrixPromo()`, `getPrixEffectif()` |
| Affichage badge promo | ✅ | |
| Affichage sur accueil | ✅ | Section dédiée |

## 9. Adresses

| Fonctionnalité | État | Détails |
|---|---|---|
| CRUD adresses | ✅ | |
| Adresse principale | ✅ | Flag `principale`, un seul par user |
| Protection suppression principale | ✅ | |
| Validation code postal (5 chiffres) | ✅ | |

## 10. Dashboard Fleuriste

| Fonctionnalité | État | Détails |
|---|---|---|
| Liste de mes fleurs | ✅ | |
| CRUD fleurs | ✅ | |
| Upload image fleur | ✅ | Vich Uploader |
| Réapprovisionnement stock | ✅ | `FleuristeService::reapprovisionner()` |
| Toggle promo sur fleur | ✅ | |
| Ajouter fleur du catalogue global | ✅ | `addFleurToCatalogue()` |
| Recherche fleuriste (publique) | 🔴 | Query retourne `User` au lieu de `Fleuriste` |
| Page vitrine fleuriste | ✅ | `/fleuriste/{id}` |
| Gestion images boutique | 🟡 | Entité existe mais pas de contrôleur dédié |

## 11. API REST

| Fonctionnalité | État | Détails |
|---|---|---|
| `GET /api/fleurs` | ✅ | Liste complète JSON |
| `GET /api/fleurs/{id}` | ✅ | Détail avec tags |
| `POST /api/login` | 🔴 | Token généré mais non stocké (inutilisable) |
| `GET /api/me` | 🟡 | Utilise la session, pas le token |

## 12. 🔐 Administration

| Fonctionnalité | État | Détails |
|---|---|---|
| Rôle `ROLE_ADMIN` | ❌ | **À ajouter** (pour valider SIRET fleuristes) |
| Panel admin | ❌ | |
| Validation fleuriste (SIRET) | ❌ | **À ajouter** |
| Modération commentaires | ❌ | |

## 13. Divers

| Fonctionnalité | État | Détails |
|---|---|---|
| Conseils culture | ✅ | Page statique |
| Page d'accueil | ✅ | Promos + dernières fleurs |
| Design responsive (Tailwind) | ✅ | |
| Alpine.js + Stimulus + Turbo | ✅ | |

---

## 📊 Résumé chiffré

| Catégorie | Nombre |
|---|---|
| Fonctionnalités ✅ OK | ~55 |
| Fonctionnalités 🟡 Partielles | 8 |
| Fonctionnalités 🔴 Cassées | 4 |
| Fonctionnalités ❌ Absentes | 8 |

## 🔥 Top priorité

1. **Bugs bloquants** : `Criteria` (chat), `inversedBy cartItems` (panier), conflit `security.yaml`
2. **SIRET + admin** : inscription fleuriste sérieuse
3. **Docker port 3010** : environnement reproductible
4. **Système de commande** : manque critique pour un e-commerce
