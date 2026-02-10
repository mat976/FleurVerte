# Résumé des entités et relations BDD

Ce document résume les principales entités Doctrine de l’application et leurs relations en base de données.

---

## Vue d’ensemble du modèle

- **User** : compte utilisateur (authentification, rôles, avatar), profil client et/ou fleuriste.
- **Client** : profil client rattaché à un `User`.
- **Fleuriste** : profil fleuriste rattaché à un `User`, avec un catalogue de `Fleur` et des `FleuristeImage`.
- **Fleur** : produit vendu (fleur), appartenant à un `Fleuriste`, avec des `Tag` et des `Commentaire`.
- **Tag** : étiquette pour catégoriser les `Fleur`.
- **Adresse** : adresses postales d’un `User`.
- **CartItem** : élément de panier (une `Fleur` pour un `User`).
- **Conversation** : discussion entre un `Client` et un `Fleuriste`.
- **Message** : message individuel dans une `Conversation`, entre deux `User`.
- **Commentaire** : avis + note d’un `User` sur une `Fleur`.
- **FleuristeImage** : images rattachées à un `Fleuriste`.
- **Rating** : entité technique minimale (id uniquement pour le moment).

---

## Détail par entité

### User

- **Table** : `user`
- **Rôle fonctionnel** :
  - Représente un compte utilisateur (email, mot de passe, rôles, username, avatar).
  - Peut être **client** et/ou **fleuriste** via des entités dédiées.
- **Relations principales** :
  - **OneToOne** `User` → `Client` (propriété `client` dans `User`, `user` dans `Client`).
  - **OneToOne** `User` → `Fleuriste` (propriété `fleuriste` dans `User`, `user` dans `Fleuriste`).
  - **OneToMany** `User` → `Adresse` (propriété `adresses` dans `User`, `user` dans `Adresse`).
  - **OneToMany** `User` → `Message` (messages envoyés, propriété `messagesEnvoyes` / champ `expediteur` dans `Message`).
  - **OneToMany** `User` → `Message` (messages reçus, propriété `messagesRecus` / champ `destinataire` dans `Message`).
  - **OneToMany** logique `User` → `CartItem` (via `CartItem.user`, même si le côté inverse n’apparaît pas dans l’extrait de `User`).

**Interprétation BDD** :
- La table `user` est centrale :
  - clé étrangère dans `client` (`client.user_id`).
  - clé étrangère dans `fleuriste` (`fleuriste.user_id`).
  - clé étrangère dans `adresse` (`adresse.user_id`).
  - clé étrangère dans `message` pour `expediteur_id` et `destinataire_id`.
  - clé étrangère dans `cart_item` (`cart_item.user_id`).

---

### Client

- **Table** : `client`
- **Rôle fonctionnel** : profil client rattaché à un `User`.
- **Champs principaux** :
  - `id`
  - `user` (OneToOne vers `User`)
- **Relations** :
  - **OneToOne (owning side)** : `Client.user` → `User` (avec cascade persist/remove).
  - Utilisé dans `Conversation` pour identifier le client participant.

**Interprétation BDD** :
- Table `client` avec une colonne `user_id` unique et not null, liée à `user.id`.

---

### Fleuriste

- **Table** : `fleuriste`
- **Rôle fonctionnel** :
  - Profil fleuriste (nom, description, coordonnées, état actif ou non).
  - Détient un catalogue de `Fleur` et des `FleuristeImage`.
- **Relations** :
  - **OneToOne (owning side)** : `Fleuriste.user` → `User` (champ non nul).
  - **OneToMany** : `Fleuriste.fleurs` ↔ `Fleur.fleuriste` (ManyToOne côté `Fleur`).
  - **OneToMany** : `Fleuriste.images` ↔ `FleuristeImage.fleuriste` (ManyToOne côté `FleuristeImage`).
  - **ManyToOne** depuis `Conversation.fleuriste`.

**Interprétation BDD** :
- Table `fleuriste` :
  - `user_id` (FK vers `user.id`).
- Table `fleur` :
  - `fleuriste_id` (FK vers `fleuriste.id`).
- Table `fleuriste_image` :
  - `fleuriste_id` (FK vers `fleuriste.id`).
- Table `conversation` :
  - `fleuriste_id` (FK vers `fleuriste.id`).

---

### Fleur

- **Table** : `fleur`
- **Rôle fonctionnel** :
  - Produit vendable : nom, description, prix, stock, épinglage, image, etc.
- **Relations** :
  - **ManyToOne** : `Fleur.fleuriste` → `Fleuriste` (fleur vendue par un fleuriste).
  - **ManyToMany** : `Fleur.tags` ↔ `Tag.fleurs`.
  - **OneToMany** : `Fleur.commentaires` ↔ `Commentaire.fleur`.
  - **ManyToOne** depuis `CartItem.fleur` (éléments de panier).

**Interprétation BDD** :
- Table `fleur` :
  - `fleuriste_id` (FK vers `fleuriste.id`, nullable).
- Table de jointure ManyToMany `fleur_tag` (ou équivalent) :
  - `fleur_id` (FK vers `fleur.id`).
  - `tag_id` (FK vers `tag.id`).
- Table `commentaire` :
  - `fleur_id` (FK vers `fleur.id`).
- Table `cart_item` :
  - `fleur_id` (FK vers `fleur.id`).

---

### Tag

- **Table** : `tag`
- **Rôle fonctionnel** : tag/catégorie visuelle (nom + couleur) pour les `Fleur`.
- **Relations** :
  - **ManyToMany (inverse side)** : `Tag.fleurs` ↔ `Fleur.tags`.

**Interprétation BDD** :
- Table `tag` simple.
- Table de jointure ManyToMany avec `fleur` (nom donnée par Doctrine, typiquement `fleur_tag`).

---

### Commentaire

- **Table** : `commentaire`
- **Rôle fonctionnel** :
  - Commentaire + note d’un `User` sur une `Fleur`.
- **Relations** :
  - **ManyToOne** : `Commentaire.user` → `User`.
  - **ManyToOne** : `Commentaire.fleur` → `Fleur` (inverse de `Fleur.commentaires`).

**Interprétation BDD** :
- Table `commentaire` :
  - `user_id` (FK vers `user.id`).
  - `fleur_id` (FK vers `fleur.id`).

---

### Adresse

- **Table** : `adresse`
- **Rôle fonctionnel** :
  - Adresse postale d’un utilisateur (rue, code postal, ville, complément, flag principale).
- **Relations** :
  - **ManyToOne** : `Adresse.user` → `User`.
  - Inverse côté `User` : `User.adresses` (OneToMany).

**Interprétation BDD** :
- Table `adresse` :
  - `user_id` (FK vers `user.id`, non nul).

---

### CartItem

- **Table** : `cart_item`
- **Rôle fonctionnel** :
  - Élément du panier : une `Fleur` avec une `quantity` pour un `User` donné.
- **Relations** :
  - **ManyToOne** : `CartItem.user` → `User`.
  - **ManyToOne** : `CartItem.fleur` → `Fleur`.

**Interprétation BDD** :
- Table `cart_item` :
  - `user_id` (FK vers `user.id`, non nul).
  - `fleur_id` (FK vers `fleur.id`, non nul).

---

### Conversation

- **Table** : `conversation`
- **Rôle fonctionnel** :
  - Conversation entre un `Client` et un `Fleuriste`.
  - Contient un ensemble ordonné de `Message`.
- **Relations** :
  - **ManyToOne** : `Conversation.client` → `Client`.
  - **ManyToOne** : `Conversation.fleuriste` → `Fleuriste`.
  - **OneToMany** : `Conversation.messages` ↔ `Message.conversation`.

**Interprétation BDD** :
- Table `conversation` :
  - `client_id` (FK vers `client.id`).
  - `fleuriste_id` (FK vers `fleuriste.id`).
- Table `message` :
  - `conversation_id` (FK vers `conversation.id`).

---

### Message

- **Table** : `message`
- **Rôle fonctionnel** :
  - Message textuel dans une `Conversation`.
  - A un expéditeur et un destinataire (`User`), une date d’envoi et un statut de lecture.
- **Relations** :
  - **ManyToOne** : `Message.expediteur` → `User`.
  - **ManyToOne** : `Message.destinataire` → `User`.
  - **ManyToOne** : `Message.conversation` → `Conversation`.

**Interprétation BDD** :
- Table `message` :
  - `expediteur_id` (FK vers `user.id`).
  - `destinataire_id` (FK vers `user.id`).
  - `conversation_id` (FK vers `conversation.id`).

---

### FleuristeImage

- **Table** : `fleuriste_image`
- **Rôle fonctionnel** :
  - Image liée à un `Fleuriste` (nom de fichier, légende, métadonnées VichUploader).
- **Relations** :
  - **ManyToOne** : `FleuristeImage.fleuriste` → `Fleuriste`.
  - Inverse côté `Fleuriste` : `Fleuriste.images` (OneToMany).

**Interprétation BDD** :
- Table `fleuriste_image` :
  - `fleuriste_id` (FK vers `fleuriste.id`).

---

### Rating

- **Table** : `rating`
- **Rôle fonctionnel** :
  - Entité minimale pour l’instant (uniquement `id`).
  - A priori prête à être étendue pour gérer d’autres notations.
- **Relations** :
  - Aucune relation définie actuellement dans le code fourni.

---

## Résumé des principales associations

- **Utilisateur → Profils**
  - `User` 1 — 0..1 `Client`
  - `User` 1 — 0..1 `Fleuriste`

- **Utilisateur → Données associées**
  - `User` 1 — * `Adresse`
  - `User` 1 — * `Message` (envoyés et reçus)
  - `User` 1 — * `CartItem`

- **Fleuriste → Catalogue / Média**
  - `Fleuriste` 1 — * `Fleur`
  - `Fleuriste` 1 — * `FleuristeImage`

- **Fleur → Meta / Avis**
  - `Fleur` * — * `Tag`
  - `Fleur` 1 — * `Commentaire`
  - `Fleur` 1 — * `CartItem`

- **Messagerie**
  - `Conversation` 1 — * `Message`
  - `Conversation` * — 1 `Client`
  - `Conversation` * — 1 `Fleuriste`
  - `Message` * — 1 `User` (expéditeur)
  - `Message` * — 1 `User` (destinataire)

Ce fichier peut servir de base pour la documentation technique (schéma UML ou MCD) ou pour préparer des migrations/évolutions du modèle.
