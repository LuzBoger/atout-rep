<?php

namespace App\Enum;

enum StatusCart: string
{
    case EMPTY = 'empty'; // Le panier est vide
    case ACTIVE = 'active'; // Le panier contient des articles
    case CHECKOUT = 'checkout'; // Le panier est en cours de validation
    case PAID = 'paid'; // Le panier a été payé
}
