# WebData
## Description
  Ce fichier permet d'obtenir un document XML compatible avec la DTD info.dtd, à partir d'un programme XQuery.
Le dossier est composé de plusieurs fichiers :
  - xquery.xq : programme principal
  - exec.sh : script Shell permettant de lancer le programme
  - covid-tp.xml : 
  - covid-tp.dtd : 
  - info.dtd : 
  - Saxon9he.jar : logiciel Saxon d'exécution de programmes XQuery.

## Exécution
Notre programme utilise Saxon9 disponible sur internet à partir du site : https://www.saxonica.com/.

Le fichier **xquery.xq** permet de générer le document XML de sortie permettant de répondre aux exigences du distanciel.
Il s'exécute à l'aide de la commande (dans le dossier courant) : "java -cp saxon9he.jar net.sf.saxon.Query -q:xquery.xq"

Pour simplifier l'execution nous avons écrit un fichier shell qui s'execute avec la commande [: exec.sh ] dans le repertoire courant
