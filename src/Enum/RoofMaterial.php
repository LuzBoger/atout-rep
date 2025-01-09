<?php

namespace App\Enum;

enum RoofMaterial: string
{
    case ASPHALT_SHINGLES = 'asphalt_shingles'; // Tuiles en asphalte, très courantes et économiques
    case METAL = 'metal'; // Toiture en métal (aluminium, acier, zinc), durable et résistante
    case CLAY_TILES = 'clay_tiles'; // Tuiles en terre cuite, populaires dans les climats chauds
    case CONCRETE_TILES = 'concrete_tiles'; // Tuiles en béton, résistantes mais lourdes
    case WOOD_SHINGLES = 'wood_shingles'; // Tuiles ou bardeaux en bois, pour un aspect naturel
    case SLATE = 'slate'; // Toiture en ardoise, durable mais coûteuse
    case SYNTHETIC = 'synthetic'; // Matériaux synthétiques (imitation d'ardoise ou de bois)
    case GREEN_ROOF = 'green_roof'; // Toit végétalisé (toiture écologique)
    case TAR_AND_GRAVEL = 'tar_and_gravel'; // Toiture goudronnée avec gravier, souvent pour les toits plats
    case MEMBRANE = 'membrane'; // Membrane en PVC ou EPDM, courante pour les toits plats
}