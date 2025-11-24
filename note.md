# Aspetti di Cyber Security applicati al progetto

## Containerizzazione
Usiamo **Docker** per containerizzare l'applicazione web e il database MySQL. Questo ci permette di isolare i servizi tra loro e dal sistema host, riducendo la superficie di attacco.

## Utilizzo di Docker Secrets per la gestione dei segreti
Usiamo [**Docker Secrets**](https://docs.docker.com/compose/how-tos/use-secrets/#build-secrets) per gestire le password di root e dell'utente MySQL. Invece di definire le password direttamente nel file `docker-compose.yml` o in variabili d'ambiente, le password sono memorizzate in file separati all'interno della directory `secrets/`.
