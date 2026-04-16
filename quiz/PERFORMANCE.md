# Quiz Performance Notes

## Objetivo

Garantir cache agressivo para `quiz/assets/*`, manter HTML e JSON sem cache compartilhado e habilitar compressao no servidor.

## Apache (.htaccess)

```apacheconf
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/css application/javascript application/json image/svg+xml
</IfModule>

<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType image/svg+xml "access plus 1 year"
    ExpiresByType image/webp "access plus 1 year"
    ExpiresByType image/avif "access plus 1 year"
</IfModule>

<FilesMatch "\.(css|js|svg|webp|avif)$">
    Header set Cache-Control "public, max-age=31536000, immutable"
</FilesMatch>

<FilesMatch "^(index\.php|api\.php)$">
    Header set Cache-Control "no-store, no-cache, must-revalidate, max-age=0"
</FilesMatch>
```

## Nginx

```nginx
gzip on;
gzip_types text/css application/javascript application/json image/svg+xml;

location /quiz/assets/ {
    add_header Cache-Control "public, max-age=31536000, immutable";
}

location = /quiz/api.php {
    add_header Cache-Control "no-store, no-cache, must-revalidate, max-age=0";
}

location = /quiz/index.php {
    add_header Cache-Control "private, no-cache, must-revalidate, max-age=0";
}
```

## PHP OPcache

Config recomendada no ambiente PHP:

```ini
opcache.enable=1
opcache.enable_cli=0
opcache.validate_timestamps=1
opcache.revalidate_freq=2
opcache.memory_consumption=128
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
```

## Observacao

Os assets locais do quiz usam versionamento por `filemtime`, entao mudancas em `quiz/assets/` podem ficar com cache longo sem risco de servir conteudo antigo.
