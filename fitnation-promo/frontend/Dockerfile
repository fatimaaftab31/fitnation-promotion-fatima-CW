FROM nginx:alpine

# Set working directory
WORKDIR /usr/share/nginx/html

# Copy HTML/PHP files
COPY index.php index.php

# Copy custom Nginx config
COPY nginx.conf /etc/nginx/nginx.conf

# Expose a  port 
EXPOSE 8082

# Start Nginx
CMD ["nginx", "-g", "daemon off;"]
