#!/bin/bash
set -euo pipefail  # Mejor manejo de errores y variables no definidas

# Verificación mejorada de variables
if [[ -z "${DB_USER:-}" || -z "${DB_PASSWORD:-}" ]]; then
    echo >&2 "ERROR: Variables DB_USER o DB_PASSWORD no están definidas"
    exit 1
fi

# Verificación de variables base
if [[ -z "${DB_DATABASE:-}" || -z "${DB_ROOT_PASSWORD:-}" ]]; then
    echo >&2 "ERROR: Variables DB_DATABASE o DB_ROOT_PASSWORD no están definidas"
    exit 1
fi

# MySQL ya crea el usuario principal automáticamente desde las variables de entorno
# Pero podemos agregar permisos adicionales si es necesario

mysql -u root -p"${MYSQL_ROOT_PASSWORD}" <<-EOSQL
    -- Asegurar que el usuario tiene todos los permisos necesarios
    GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'%';
    
    -- Permisos adicionales que pueden ser útiles
    GRANT CREATE, ALTER, DROP, REFERENCES ON *.* TO '${DB_USER}'@'%';
    
    -- Aplicar los cambios
    FLUSH PRIVILEGES;
EOSQL

echo "Permisos adicionales para el usuario ${DB_USER} configurados exitosamente"

