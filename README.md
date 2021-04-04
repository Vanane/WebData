# WebData
## Description
  Ce fichier permet d'obtenir un document XML compatible avec la DTD info.dtd, à partir d'un programme XQuery.
Le dossier est composé de plusieurs fichiers :
  - xquery.xq : programme principal
  - exec.sh : script Shell permettant de lancer le programme
  - covid-tp.xml : fichier XML source
  - covid-tp.dtd : DTD du fichier XML source
  - info.dtd : DTD attendue du fichier de sortie
  - Saxon9he.jar : logiciel Saxon d'exécution de programmes XQuery. Nous utilisons une copie fournie par le professeur.

## Utilisation
  Le programme est fourni en dossier ZIP. Il suffit de décompresser d'archive, puis se placer avec un terminal dans le dossier contenant le programme.
Notre programme utilise Saxon9 disponible sur internet à partir du site : https://www.saxonica.com/.
Le fichier **xquery.xq** permet de générer le document XML de sortie permettant de répondre aux exigences du distanciel.
Il s'exécute à l'aide de la commande (dans le dossier courant) : `java -cp saxon9he.jar net.sf.saxon.Query -q:xquery.xq`

Pour simplifier l'exécution nous avons écrit un fichier shell qui s'exécute avec la commande : `bash exec.sh` dans le répertoire courant
