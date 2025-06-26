# FleurVerte - Configuration Docker

Ce projet est configuré pour fonctionner avec Docker, ce qui facilite la mise en place de l'environnement de développement.

## Prérequis

- Docker
- Docker Compose

## Installation

1. Clonez le dépôt :
   ```bash
   git clone <url-du-repo>
   cd FleurVerte
   ```

2. Lancez les conteneurs Docker :
   ```bash
   docker-compose up -d
   ```

3. Accédez à l'application :
   - L'application web sera disponible à l'adresse : http://localhost:8080
   - La base de données MySQL est accessible sur le port 3306

## Configuration de la base de données

La base de données est configurée avec les paramètres suivants :
- **Hôte** : database (dans le réseau Docker) ou localhost:3306 (depuis votre machine)
- **Nom de la base de données** : fleurverte
- **Utilisateur** : root
- **Mot de passe** : (aucun)

## Commandes utiles

- Démarrer les conteneurs : `docker-compose up -d`
- Arrêter les conteneurs : `docker-compose down`
- Voir les logs : `docker-compose logs`
- Accéder au shell du conteneur web : `docker exec -it fleurverte_web bash`
- Accéder au shell MySQL : `docker exec -it fleurverte_mysql mysql -u root fleurverte`

## Structure du projet

Le projet est configuré avec :
- Un conteneur **web** basé sur PHP 8.1 avec Apache
- Un conteneur **database** avec MySQL 5.7

## Migrations de base de données

Pour exécuter les migrations Symfony :

```bash
docker exec -it fleurverte_web php bin/console doctrine:migrations:migrate
```