#!/bin/bash
SERVICES=$(minikube kubectl -- get services)

AUTH=$(awk '/auth-service/ {print $4}' <<< "$SERVICES")
if [ "$AUTH" = "" ] || [ "$AUTH" = "<pending>" ]; then
    echo "Serviço Auth ainda não está pronto"
    exit 1
fi
echo "export IP_AUTH=${AUTH}:3000"

COMMENTS=$(awk '/comments-service/ {print $4}' <<< "$SERVICES")
if [ "$COMMENTS" = "" ] || [ "$COMMENTS" = "<pending>" ]; then
    echo "Serviço Comments ainda não está pronto"
    exit 1
fi
echo "export IP_COMMENTS=${COMMENTS}:3000"

POSTS=$(awk '/posts-service/ {print $4}' <<< "$SERVICES")
if [ "$POSTS" = "" ] || [ "$POSTS" = "<pending>" ]; then
    echo "Serviço Posts ainda não está pronto"
    exit 1
fi
echo "export IP_POSTS=${POSTS}:3000"

USERS=$(awk '/users-service/ {print $4}' <<< "$SERVICES")
if [ "$USERS" = "" ] || [ "$USERS" = "<pending>" ]; then
    echo "Serviço Users ainda não está pronto"
    exit 1
fi
echo "export IP_USERS=${USERS}:3000"

echo "# Para executar os comandos acima automaticamente, execute:"
echo "# eval \$($0)"
