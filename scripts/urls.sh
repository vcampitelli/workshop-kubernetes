#!/bin/bash

AUTH=$(minikube service auth-service --url 2>/dev/null)
if [ "$AUTH" = "" ]; then
    echo "Serviço Auth ainda não está pronto"
    exit 1
fi
echo "export URL_AUTH=${AUTH}"

POSTS=$(minikube service posts-service --url 2>/dev/null)
if [ "$POSTS" = "" ]; then
    echo "Serviço Posts ainda não está pronto"
    exit 1
fi
echo "export URL_POSTS=${POSTS}"

COMMENTS=$(minikube service comments-service --url 2>/dev/null)
if [ "$COMMENTS" = "" ]; then
    echo "Serviço Comments ainda não está pronto"
    exit 1
fi
echo "export URL_COMMENTS=${COMMENTS}"

COMPOSITION=$(minikube service composition-service --url 2>/dev/null)
if [ "$COMPOSITION" = "" ]; then
    echo "Serviço Composition ainda não está pronto"
    exit 1
fi
echo "export URL_COMPOSITION=${COMPOSITION}"

GATEWAY=$(minikube service -n istio-system istio-ingressgateway list 2>/dev/null | grep "http2/80" | awk '{print $6}')
if [ "$GATEWAY" != "" ]; then
    echo "export URL_GATEWAY=${GATEWAY}"
fi

echo "# Para executar os comandos acima automaticamente, execute:"
echo "# eval \$($0)"
