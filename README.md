# Implementando microsserviços com Kubernetes na prática

Clone este repositório:

```shell
$ git clone --recursive git@github.com:vcampitelli/workshop-kubernetes.git
```

## Slides

Você pode acessar os slides:

* Localmente abrindo o arquivo `docs/index.html` em seu navegador
* Remotamente através do site [viniciuscampitelli.com/workshop-kubernetes](https://viniciuscampitelli.com/workshop-kubernetes/)

## Ferramentas para acompanhamento

Instale as seguintes ferramentas para acompanhar o workshop:

1. Parte 1: Localmente
    * Docker: [docs.docker.com/get-docker](https://docs.docker.com/get-docker/)
    * minikube: [minikube.sigs.k8s.io](https://minikube.sigs.k8s.io/docs/start/)
    * Entre na pasta `scripts` e execute:
      ```shell
      $ minikube start --kubernetes-version=v1.26.3
      ```
2. Parte 2: Na AWS
    * `kubeadm`, `kubelet` e `kubectl` para gerenciar o _cluster_ em [kubernetes.io/docs/setup](https://kubernetes.io/docs/setup/production-environment/tools/kubeadm/install-kubeadm/)
    * `aws` para comunicação com os serviços da Amazon em [aws.amazon.com/cli](https://aws.amazon.com/cli)
    * `eksctl` para manuseio do _cluster_ em [eksctl.io](https://eksctl.io/introduction/#installation)
