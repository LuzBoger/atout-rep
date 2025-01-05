# Atout-REP

Mise en place d'un site-web permettant le dépannage en ligne et l'achat de matériel

## Installation

git clone https://github.com/LuzBoger/atout-rep.git

- cd gramazon
- git checkout dev

puis tapper les commandes suivantes :
````bash
# Installation des composants de symfony
composer install
#Installation de la structure de la BDD
symfony console make:migration
symfony console doctrine:migrations:migrate
#Commande npm pour tailwind
npm install
npm run build
npm run watch
````
Commande pour charger les données :
````bash
symfony console doctrine:fixtures:load
````
Copyright © 2024 [Arthur Brouard et Pierre Caune]