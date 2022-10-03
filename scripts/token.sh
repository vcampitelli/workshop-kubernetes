#!/bin/sh
set -e
if [ -z "$URL_AUTH" ]; then
  URL_AUTH=$(./urls.sh | grep URL_AUTH | cut -d '=' -f 2)
fi
if [ -z "$URL_AUTH" ]; then
    echo "Erro ao buscar URL do serviço de Auth"
    exit 1
fi

TOKEN=$(curl -s -X POST -u "admin:admin" "${URL_AUTH}/auth" | jq -r '.access_token')
if [ -z "$URL_AUTH" ]; then
    echo "Erro ao buscar token"
    exit 1
fi

echo export TOKEN="${TOKEN}"

echo "# Para executar o comando acima automaticamente, execute:"
echo "# eval \$($0)"
