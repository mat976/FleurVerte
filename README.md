# FleurVerte - Configuration MySQL avec Docker

Ce fichier explique comment utiliser Docker pour configurer rapidement une base de données MySQL correspondant à la configuration du projet.

## Prérequis

- Docker
- Docker Compose

## Démarrage de la base de données

Pour lancer la base de données MySQL :

```bash
docker-compose up -d
```

## Configuration de la base de données

La base de données est configurée avec les paramètres suivants :
- **Hôte** : localhost
- **Port** : 3306
- **Nom de la base de données** : fleurverte
- **Utilisateur** : root
- **Mot de passe** : (aucun)
- **Version** : MySQL 5.7

Ces paramètres correspondent exactement à la configuration dans le fichier .env :
```
DATABASE_URL="mysql://root:@127.0.0.1:3306/fleurverte?serverVersion=5.7&charset=utf8mb4"
```

## Commandes utiles

- Démarrer la base de données : `docker-compose up -d`
- Arrêter la base de données : `docker-compose down`
- Voir les logs : `docker-compose logs database`
- Accéder au shell MySQL : `docker exec -it fleurverte_mysql mysql -u root fleurverte`