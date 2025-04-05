# Étape 1 : Utiliser une image Nginx
FROM nginx:alpine

# Copier le fichier nginx.conf (si vous avez un fichier de configuration personnalisé)
COPY nginx.conf /etc/nginx/nginx.conf

# Copier le fichier index.php dans le répertoire racine de Nginx
COPY index.php /usr/share/nginx/html/

# Exposer le port 80
EXPOSE 80
