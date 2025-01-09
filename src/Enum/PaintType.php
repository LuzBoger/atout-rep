<?php

namespace App\Enum;

enum PaintType: string
{
    // Peintures pour intérieur
    case ACRYLIC = 'acrylic'; // Peinture acrylique, très polyvalente pour intérieur et extérieur
    case LATEX = 'latex'; // Peinture latex, courante pour les murs intérieurs
    case MATTE = 'matte'; // Peinture mate, idéale pour un rendu sans brillance en intérieur
    case SATIN = 'satin'; // Peinture satinée, légèrement brillante, courante pour les murs et plafonds
    case SEMI_GLOSS = 'semi_gloss'; // Peinture semi-brillante, parfaite pour les cuisines et salles de bain
    case GLOSSY = 'glossy'; // Peinture brillante, souvent utilisée pour les boiseries et finitions intérieures

    // Peintures pour extérieur
    case OIL_BASED = 'oil_based'; // Peinture à base d'huile, résistante pour les surfaces extérieures
    case WATER_BASED = 'water_based'; // Peinture à base d'eau, utilisée pour les murs extérieurs
    case EPOXY = 'epoxy'; // Peinture résistante, idéale pour les sols extérieurs ou garages
    case TEXTURED = 'textured'; // Peinture texturée, parfaite pour les murs extérieurs rugueux

    // Peinture polyvalente (intérieur/extérieur)
    case PRIMER = 'primer'; // Sous-couche utilisée avant d'appliquer la peinture principale
}
