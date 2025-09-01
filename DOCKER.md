# Camagru - Setup Docker

## Démarrage rapide

1. Copier le fichier d'environnement :
```bash
cp .env.example .env
```

2. Modifier les variables dans `.env` selon vos besoins

3. Lancer les conteneurs :
```bash
docker-compose up -d
```

4. L'application sera accessible sur http://localhost:8080

## Configuration

### Extensions PHP incluses :
- **GD** : manipulation d'images
- **ImageMagick** : traitement avancé d'images
- **PDO MySQL** : base de données
- **ZIP** : archives

### Structure des volumes :
- `src/` : code source PHP monté
- `public/` : point d'entrée web
- `uploads/` : images utilisateurs
- `mysql-data` : données persistantes MySQL

### Sécurité :
- Dossiers sensibles protégés par Apache
- Variables sensibles dans `.env`
- Sessions sécurisées configurées
