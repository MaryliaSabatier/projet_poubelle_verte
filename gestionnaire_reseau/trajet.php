<?php
error_reporting(E_ALL);

$stops = array(
    // Rue Croix-Baragnon
    "La Défense" => array("lat" => 48.8913, "lng" => 2.2376),
    "Esplanade de la Défense" => array("lat" => 48.8881, "lng" => 2.2500),
    "Pont de Neuilly" => array("lat" => 48.8841, "lng" => 2.2591),
    "Les Sablons" => array("lat" => 48.8805, "lng" => 2.2698),
    "Porte Maillot" => array("lat" => 48.8779, "lng" => 2.2825),
    "Argentine" => array("lat" => 48.8747, "lng" => 2.2941),
    "Charles de Gaulle-Étoile" => array("lat" => 48.8738, "lng" => 2.2950),
    "George V" => array("lat" => 48.8722, "lng" => 2.3000),
    "Franklin D. Roosevelt" => array("lat" => 48.8696, "lng" => 2.3087),
    "Champs-Élysées-Clemenceau" => array("lat" => 48.8675, "lng" => 2.3130),
    "Concorde" => array("lat" => 48.8656, "lng" => 2.3211),
    "Tuileries" => array("lat" => 48.8636, "lng" => 2.3283),
    "Palais Royal-Musée du Louvre" => array("lat" => 48.8625, "lng" => 2.3359),
    "Louvre-Rivoli" => array("lat" => 48.8606, "lng" => 2.3410),
    "Châtelet" => array("lat" => 48.8584, "lng" => 2.3470),
    "Hôtel de Ville" => array("lat" => 48.8566, "lng" => 2.3522),
    "Saint-Paul" => array("lat" => 48.8554, "lng" => 2.3620),
    "Bastille" => array("lat" => 48.8530, "lng" => 2.3690),
    "Gare de Lyon" => array("lat" => 48.8443, "lng" => 2.3730),
    "Reuilly-Diderot" => array("lat" => 48.8489, "lng" => 2.3850),
    "Nation" => array("lat" => 48.8482, "lng" => 2.3958),
    "Porte de Vincennes" => array("lat" => 48.8470, "lng" => 2.4145),
    "Saint-Mandé" => array("lat" => 48.8441, "lng" => 2.4156),
    "Bérault" => array("lat" => 48.8427, "lng" => 2.4235),
    "Château de Vincennes" => array("lat" => 48.8443, "lng" => 2.4394),

    // Rue des Arts
    "Porte Dauphine" => array("lat" => 48.8715, "lng" => 2.2746),
    "Victor Hugo" => array("lat" => 48.8684, "lng" => 2.2881),
    "Ternes" => array("lat" => 48.8781, "lng" => 2.2989),
    "Courcelles" => array("lat" => 48.8807, "lng" => 2.3084),
    "Monceau" => array("lat" => 48.8816, "lng" => 2.3124),
    "Villiers" => array("lat" => 48.8833, "lng" => 2.3168),
    "Rome" => array("lat" => 48.8823, "lng" => 2.3225),
    "Place de Clichy" => array("lat" => 48.8837, "lng" => 2.3276),
    "Blanche" => array("lat" => 48.8832, "lng" => 2.3319),
    "Pigalle" => array("lat" => 48.8820, "lng" => 2.3370),
    "Anvers" => array("lat" => 48.8820, "lng" => 2.3441),
    "Barbès-Rochechouart" => array("lat" => 48.8833, "lng" => 2.3499),
    "La Chapelle" => array("lat" => 48.8848, "lng" => 2.3591),
    "Stalingrad" => array("lat" => 48.8841, "lng" => 2.3682),
    "Jaurès" => array("lat" => 48.8820, "lng" => 2.3705),
    "Colonel Fabien" => array("lat" => 48.8756, "lng" => 2.3706),
    "Belleville" => array("lat" => 48.8722, "lng" => 2.3765),
    "Couronnes" => array("lat" => 48.8697, "lng" => 2.3799),
    "Ménilmontant" => array("lat" => 48.8669, "lng" => 2.3816),
    "Père Lachaise" => array("lat" => 48.8613, "lng" => 2.3862),
    "Philippe Auguste" => array("lat" => 48.8592, "lng" => 2.3886),
    "Alexandre Dumas" => array("lat" => 48.8573, "lng" => 2.3923),
    "Avron" => array("lat" => 48.8528, "lng" => 2.4000),

    // Rue Pargaminières
    "Pont de Levallois-Bécon" => array("lat" => 48.8933, "lng" => 2.2740),
    "Anatole France" => array("lat" => 48.8899, "lng" => 2.2826),
    "Louise Michel" => array("lat" => 48.8880, "lng" => 2.2885),
    "Porte de Champerret" => array("lat" => 48.8851, "lng" => 2.2959),
    "Pereire" => array("lat" => 48.8858, "lng" => 2.2988),
    "Wagram" => array("lat" => 48.8856, "lng" => 2.3054),
    "Malesherbes" => array("lat" => 48.8828, "lng" => 2.3086),
    "Europe" => array("lat" => 48.8789, "lng" => 2.3252),
    "Saint-Lazare" => array("lat" => 48.8756, "lng" => 2.3259),
    "Havre-Caumartin" => array("lat" => 48.8738, "lng" => 2.3288),
    "Opéra" => array("lat" => 48.8719, "lng" => 2.3325),
    "Quatre-Septembre" => array("lat" => 48.8693, "lng" => 2.3366),
    "Bourse" => array("lat" => 48.8685, "lng" => 2.3396),
    "Sentier" => array("lat" => 48.8674, "lng" => 2.3461),
    "Réaumur-Sébastopol" => array("lat" => 48.8672, "lng" => 2.3524),
    "Arts et Métiers" => array("lat" => 48.8658, "lng" => 2.3557),
    "Temple" => array("lat" => 48.8645, "lng" => 2.3616),
    "République" => array("lat" => 48.8673, "lng" => 2.3630),
    "Parmentier" => array("lat" => 48.8643, "lng" => 2.3740),
    "Rue Saint-Maur" => array("lat" => 48.8630, "lng" => 2.3795),
    "Gambetta" => array("lat" => 48.8630, "lng" => 2.3988),
    "Porte de Bagnolet" => array("lat" => 48.8647, "lng" => 2.4070),
    "Gallieni" => array("lat" => 48.8665, "lng" => 2.4170),

    // Rue Saint-Rome
    "Gambetta" => array("lat" => 48.8630, "lng" => 2.3988),
    "Pelleport" => array("lat" => 48.8703, "lng" => 2.3988),
    "Saint-Fargeau" => array("lat" => 48.8725, "lng" => 2.4025),
    "Porte des Lilas" => array("lat" => 48.8750, "lng" => 2.4072),

    // Rue Saint-Antoine du T
    "Porte de Clignancourt" => array("lat" => 48.8976, "lng" => 2.3441),
    "Simplon" => array("lat" => 48.8952, "lng" => 2.3466),
    "Marcadet-Poissonniers" => array("lat" => 48.8927, "lng" => 2.3494),
    "Château Rouge" => array("lat" => 48.8906, "lng" => 2.3499),
    "Gare du Nord" => array("lat" => 48.8809, "lng" => 2.3553),
    "Gare de l'Est" => array("lat" => 48.8760, "lng" => 2.3580),
    "Château d'Eau" => array("lat" => 48.8723, "lng" => 2.3570),
    "Strasbourg-Saint-Denis" => array("lat" => 48.8696, "lng" => 2.3545),
    "Réaumur-Sébastopol" => array("lat" => 48.8672, "lng" => 2.3524),
    "Étienne Marcel" => array("lat" => 48.8662, "lng" => 2.3491),
    "Les Halles" => array("lat" => 48.8610, "lng" => 2.3460),
    "Châtelet" => array("lat" => 48.8584, "lng" => 2.3470),
    "Cité" => array("lat" => 48.8554, "lng" => 2.3450),
    "Saint-Michel-Notre-Dame" => array("lat" => 48.8530, "lng" => 2.3441),
    "Odéon" => array("lat" => 48.8534, "lng" => 2.3384),
    "Saint-Germain-des-Prés" => array("lat" => 48.8543, "lng" => 2.3333),
    "Saint-Sulpice" => array("lat" => 48.8515, "lng" => 2.3322),
    "Saint-Placide" => array("lat" => 48.8479, "lng" => 2.3250),
    "Montparnasse-Bienvenüe" => array("lat" => 48.8421, "lng" => 2.3215),
    "Vavin" => array("lat" => 48.8411, "lng" => 2.3285),
    "Raspail" => array("lat" => 48.8410, "lng" => 2.3328),
    "Denfert-Rochereau" => array("lat" => 48.8338, "lng" => 2.3320),
    "Mouton-Duvernet" => array("lat" => 48.8311, "lng" => 2.3301),
    "Alésia" => array("lat" => 48.8286, "lng" => 2.3258),
    "Porte d'Orléans" => array("lat" => 48.8239, "lng" => 2.3250),

    // Rue de la Fonderie
    "Bobigny-Pablo Picasso" => array("lat" => 48.9085, "lng" => 2.4433),
    "Bobigny-Pantin-Raymond Queneau" => array("lat" => 48.8949, "lng" => 2.4141),
    "Église de Pantin" => array("lat" => 48.8900, "lng" => 2.4098),
    "Hoche" => array("lat" => 48.8858, "lng" => 2.4015),
    "Porte de Pantin" => array("lat" => 48.8851, "lng" => 2.3937),
    "Ourcq" => array("lat" => 48.8867, "lng" => 2.3839),
    "Laumière" => array("lat" => 48.8847, "lng" => 2.3767),
    "Jaurès" => array("lat" => 48.8820, "lng" => 2.3705),
    "Stalingrad" => array("lat" => 48.8841, "lng" => 2.3682),
    "Gare du Nord" => array("lat" => 48.8809, "lng" => 2.3553),
    "Gare de l'Est" => array("lat" => 48.8760, "lng" => 2.3580),
    "Jacques Bonsergent" => array("lat" => 48.8701, "lng" => 2.3612),
    "République" => array("lat" => 48.8673, "lng" => 2.3630),
    "Oberkampf" => array("lat" => 48.8641, "lng" => 2.3705),
    "Richard-Lenoir" => array("lat" => 48.8600, "lng" => 2.3719),
    "Bréguet-Sabin" => array("lat" => 48.8571, "lng" => 2.3711),
    "Bastille" => array("lat" => 48.8530, "lng" => 2.3690),
    "Quai de la Rapée" => array("lat" => 48.8440, "lng" => 2.3690),
    "Gare d'Austerlitz" => array("lat" => 48.8422, "lng" => 2.3652),
    "Saint-Marcel" => array("lat" => 48.8390, "lng" => 2.3580),
    "Campo-Formio" => array("lat" => 48.8359, "lng" => 2.3553),
    "Place d'Italie" => array("lat" => 48.8319, "lng" => 2.3551),

    // Rue Peyrolières
    "Charles de Gaulle-Étoile" => array("lat" => 48.8738, "lng" => 2.2950),
    "Kléber" => array("lat" => 48.8695, "lng" => 2.2934),
    "Boissière" => array("lat" => 48.8661, "lng" => 2.2934),
    "Trocadéro" => array("lat" => 48.8631, "lng" => 2.2889),
    "Passy" => array("lat" => 48.8575, "lng" => 2.2770),
    "Champ de Mars-Tour Eiffel" => array("lat" => 48.8553, "lng" => 2.2930),
    "Dupleix" => array("lat" => 48.8496, "lng" => 2.2936),
    "La Motte-Picquet-Grenelle" => array("lat" => 48.8481, "lng" => 2.2985),
    "Cambronne" => array("lat" => 48.8461, "lng" => 2.3038),
    "Sèvres-Lecourbe" => array("lat" => 48.8443, "lng" => 2.3123),
    "Pasteur" => array("lat" => 48.8420, "lng" => 2.3157),
    "Montparnasse-Bienvenüe" => array("lat" => 48.8421, "lng" => 2.3215),
    "Edgar Quinet" => array("lat" => 48.8410, "lng" => 2.3224),
    "Raspail" => array("lat" => 48.8410, "lng" => 2.3328),
    "Denfert-Rochereau" => array("lat" => 48.8338, "lng" => 2.3320),
    "Saint-Jacques" => array("lat" => 48.8337, "lng" => 2.3360),
    "Glacière" => array("lat" => 48.8320, "lng" => 2.3442),
    "Corvisart" => array("lat" => 48.8305, "lng" => 2.3505),
    "Place d'Italie" => array("lat" => 48.8319, "lng" => 2.3551),
    "Nationale" => array("lat" => 48.8290, "lng" => 2.3620),
    "Chevaleret" => array("lat" => 48.8300, "lng" => 2.3700),
    "Quai de la Gare" => array("lat" => 48.8350, "lng" => 2.3760),
    "Bercy" => array("lat" => 48.8390, "lng" => 2.3800),
    "Dugommier" => array("lat" => 48.8360, "lng" => 2.3860),
    "Daumesnil" => array("lat" => 48.8360, "lng" => 2.3930),
    "Bel-Air" => array("lat" => 48.8410, "lng" => 2.4000),
    "Picpus" => array("lat" => 48.8430, "lng" => 2.4050),
    "Nation" => array("lat" => 48.8482, "lng" => 2.3958),

    // Rue Genty-Magre
    "La Courneuve-8 Mai 1945" => array("lat" => 48.9280, "lng" => 2.3974),
    "Fort d'Aubervilliers" => array("lat" => 48.9120, "lng" => 2.4030),
    "Aubervilliers-Pantin-Quatre Chemins" => array("lat" => 48.9000, "lng" => 2.3930),
    "Porte de la Villette" => array("lat" => 48.8980, "lng" => 2.3830),
    "Corentin Cariou" => array("lat" => 48.8940, "lng" => 2.3760),
    "Crimée" => array("lat" => 48.8910, "lng" => 2.3710),
    "Riquet" => array("lat" => 48.8880, "lng" => 2.3670),
    "Stalingrad" => array("lat" => 48.8841, "lng" => 2.3682),
    "Louis Blanc" => array("lat" => 48.8820, "lng" => 2.3620),
    "Château-Landon" => array("lat" => 48.8790, "lng" => 2.3610),
    "Gare de l'Est" => array("lat" => 48.8760, "lng" => 2.3580),
    "Poissonnière" => array("lat" => 48.8790, "lng" => 2.3480),
    "Cadet" => array("lat" => 48.8760, "lng" => 2.3440),
    "Le Peletier" => array("lat" => 48.8750, "lng" => 2.3380),
    "Chaussée d'Antin-La Fayette" => array("lat" => 48.8730, "lng" => 2.3320),
    "Opéra" => array("lat" => 48.8719, "lng" => 2.3325),
    "Pyramides" => array("lat" => 48.8650, "lng" => 2.3350),
    "Palais Royal-Musée du Louvre" => array("lat" => 48.8625, "lng" => 2.3359),
    "Pont Neuf" => array("lat" => 48.8580, "lng" => 2.3430),
    "Châtelet" => array("lat" => 48.8584, "lng" => 2.3470),
    "Pont Marie" => array("lat" => 48.8520, "lng" => 2.3550),
    "Sully-Morland" => array("lat" => 48.8510, "lng" => 2.3620),
    "Jussieu" => array("lat" => 48.8470, "lng" => 2.3550),
    "Place Monge" => array("lat" => 48.8420, "lng" => 2.3540),
    "Censier-Daubenton" => array("lat" => 48.8400, "lng" => 2.3510),
    "Les Gobelins" => array("lat" => 48.8360, "lng" => 2.3560),
    "Place d'Italie" => array("lat" => 48.8319, "lng" => 2.3551),
    "Tolbiac" => array("lat" => 48.8270, "lng" => 2.3600),
    "Maison Blanche" => array("lat" => 48.8240, "lng" => 2.3610),
    "Porte d'Italie" => array("lat" => 48.8190, "lng" => 2.3630),
    "Porte de Choisy" => array("lat" => 48.8170, "lng" => 2.3700),
    "Porte d'Ivry" => array("lat" => 48.8150, "lng" => 2.3770),
    "Pierre et Marie Curie" => array("lat" => 48.8160, "lng" => 2.3850),
    "Mairie d'Ivry" => array("lat" => 48.8130, "lng" => 2.3880),

    // Rue d'Alsace-Lorraine
    "Louis Blanc" => array("lat" => 48.8820, "lng" => 2.3620),
    "Jaurès" => array("lat" => 48.8820, "lng" => 2.3705),
    "Bolivar" => array("lat" => 48.8800, "lng" => 2.3760),
    "Buttes Chaumont" => array("lat" => 48.8810, "lng" => 2.3810),
    "Botzaris" => array("lat" => 48.8800, "lng" => 2.3860),
    "Place des Fêtes" => array("lat" => 48.8780, "lng" => 2.3930),
    "Pré Saint-Gervais" => array("lat" => 48.8830, "lng" => 2.4010),

    // Rue Peyras
    "Balard" => array("lat" => 48.8380, "lng" => 2.2750),
    "Lourmel" => array("lat" => 48.8400, "lng" => 2.2820),
    "Boucicaut" => array("lat" => 48.8410, "lng" => 2.2880),
    "Félix Faure" => array("lat" => 48.8420, "lng" => 2.2930),
    "Commerce" => array("lat" => 48.8440, "lng" => 2.2970),
    "École Militaire" => array("lat" => 48.8551, "lng" => 2.3073),
    "La Tour-Maubourg" => array("lat" => 48.8570, "lng" => 2.3121),
    "Invalides" => array("lat" => 48.8604, "lng" => 2.3131),
    "Madeleine" => array("lat" => 48.8695, "lng" => 2.3259),
    "Richelieu-Drouot" => array("lat" => 48.8725, "lng" => 2.3401),
    "Grands Boulevards" => array("lat" => 48.8713, "lng" => 2.3442),
    "Bonne Nouvelle" => array("lat" => 48.8700, "lng" => 2.3490),
    "Filles du Calvaire" => array("lat" => 48.8624, "lng" => 2.3652),
    "Saint-Sébastien-Froissart" => array("lat" => 48.8610, "lng" => 2.3686),
    "Chemin Vert" => array("lat" => 48.8578, "lng" => 2.3719),
    "Ledru-Rollin" => array("lat" => 48.8528, "lng" => 2.3771),
    "Faidherbe-Chaligny" => array("lat" => 48.8501, "lng" => 2.3828),
    "Montgallet" => array("lat" => 48.8466, "lng" => 2.3892),
    "Michel Bizot" => array("lat" => 48.8418, "lng" => 2.3965),
    "Porte Dorée" => array("lat" => 48.8401, "lng" => 2.4018),
    "Porte de Charenton" => array("lat" => 48.8346, "lng" => 2.4075),
    "Liberté" => array("lat" => 48.8217, "lng" => 2.4097),
    "Charenton-Écoles" => array("lat" => 48.8181, "lng" => 2.4135),
    "École Vétérinaire de Maisons-Alfort" => array("lat" => 48.8134, "lng" => 2.4206),
    "Maisons-Alfort-Stade" => array("lat" => 48.8078, "lng" => 2.4297),
    "Maisons-Alfort-Les Juilliottes" => array("lat" => 48.8015, "lng" => 2.4392),
    "Créteil-L'Échat" => array("lat" => 48.7925, "lng" => 2.4479),
    "Créteil-Université" => array("lat" => 48.7873, "lng" => 2.4557),
    "Créteil-Préfecture" => array("lat" => 48.7800, "lng" => 2.4606),
    "La Motte-Picquet-Grenelle" => array("lat" => 48.8481, "lng" => 2.2985),
    "Concorde" => array("lat" => 48.8656, "lng" => 2.3211),
    "Opéra" => array("lat" => 48.8719, "lng" => 2.3325),
    "Strasbourg-Saint-Denis" => array("lat" => 48.8696, "lng" => 2.3545),
    "République" => array("lat" => 48.8673, "lng" => 2.3630),
    "Bastille" => array("lat" => 48.8530, "lng" => 2.3690),
    "Reuilly-Diderot" => array("lat" => 48.8489, "lng" => 2.3850),
    "Daumesnil" => array("lat" => 48.8360, "lng" => 2.3930),
    "Place d'Italie" => array("lat" => 48.8319, "lng" => 2.3551),

    // Rue du Taur
    "Pont de Sèvres" => array("lat" => 48.8299, "lng" => 2.2335),
    "Billancourt" => array("lat" => 48.8330, "lng" => 2.2404),
    "Marcel Sembat" => array("lat" => 48.8346, "lng" => 2.2478),
    "Porte de Saint-Cloud" => array("lat" => 48.8366, "lng" => 2.2568),
    "Exelmans" => array("lat" => 48.8386, "lng" => 2.2639),
    "Michel-Ange-Molitor" => array("lat" => 48.8443, "lng" => 2.2621),
    "Michel-Ange-Auteuil" => array("lat" => 48.8461, "lng" => 2.2644),
    "Jasmin" => array("lat" => 48.8485, "lng" => 2.2682),
    "Ranelagh" => array("lat" => 48.8525, "lng" => 2.2698),
    "Rue de la Pompe" => array("lat" => 48.8631, "lng" => 2.2793),
    "Iéna" => array("lat" => 48.8640, "lng" => 2.2930),
    "Alma-Marceau" => array("lat" => 48.8644, "lng" => 2.3001),
    "Saint-Philippe du Roule" => array("lat" => 48.8700, "lng" => 2.3098),
    "Miromesnil" => array("lat" => 48.8717, "lng" => 2.3161),
    "Saint-Augustin" => array("lat" => 48.8744, "lng" => 2.3203),
    "Richelieu-Drouot" => array("lat" => 48.8725, "lng" => 2.3401),
    "Grands Boulevards" => array("lat" => 48.8713, "lng" => 2.3442),
    "Bonne Nouvelle" => array("lat" => 48.8700, "lng" => 2.3490),
    "Saint-Ambroise" => array("lat" => 48.8615, "lng" => 2.3766),
    "Voltaire" => array("lat" => 48.8577, "lng" => 2.3808),
    "Charonne" => array("lat" => 48.8537, "lng" => 2.3870),
    "Rue des Boulets" => array("lat" => 48.8513, "lng" => 2.3898),
    "Buzenval" => array("lat" => 48.8500, "lng" => 2.3980),
    "Maraîchers" => array("lat" => 48.8510, "lng" => 2.4028),
    "Porte de Montreuil" => array("lat" => 48.8516, "lng" => 2.4093),
    "Robespierre" => array("lat" => 48.8550, "lng" => 2.4187),
    "Croix de Chavaux" => array("lat" => 48.8615, "lng" => 2.4324),
    "Mairie de Montreuil" => array("lat" => 48.8648, "lng" => 2.4413),

    // Allée Jean Jaurès
    "Boulogne-Pont de Saint-Cloud" => array("lat" => 48.8403, "lng" => 2.2290),
    "Boulogne-Jean Jaurès" => array("lat" => 48.8428, "lng" => 2.2398),
    "Porte d'Auteuil" => array("lat" => 48.8471, "lng" => 2.2603),
    "Église d'Auteuil" => array("lat" => 48.8477, "lng" => 2.2684),
    "Javel-André Citroën" => array("lat" => 48.8440, "lng" => 2.2775),
    "Charles Michels" => array("lat" => 48.8481, "lng" => 2.2894),
    "Avenue Émile Zola" => array("lat" => 48.8488, "lng" => 2.2964),
    "Ségur" => array("lat" => 48.8480, "lng" => 2.3080),
    "Duroc" => array("lat" => 48.8464, "lng" => 2.3160),
    "Vaneau" => array("lat" => 48.8476, "lng" => 2.3221),
    "Sèvres-Babylone" => array("lat" => 48.8511, "lng" => 2.3253),
    "Mabillon" => array("lat" => 48.8524, "lng" => 2.3331),
    "Cluny-La Sorbonne" => array("lat" => 48.8525, "lng" => 2.3445),
    "Maubert-Mutualité" => array("lat" => 48.8505, "lng" => 2.3479),
    "Cardinal Lemoine" => array("lat" => 48.8477, "lng" => 2.3527),

    // Rue du May
    "Rambuteau" => array("lat" => 48.8616, "lng" => 2.3522),
    "Goncourt" => array("lat" => 48.8690, "lng" => 2.3706),
    "Pyrénées" => array("lat" => 48.8727, "lng" => 2.3839),
    "Jourdain" => array("lat" => 48.8755, "lng" => 2.3891),
    "Télégraphe" => array("lat" => 48.8777, "lng" => 2.3988),
    "Mairie des Lilas" => array("lat" => 48.8804, "lng" => 2.4187),

    // Rue des Filatiers
    "Porte de la Chapelle" => array("lat" => 48.8982, "lng" => 2.3611),
    "Marx Dormoy" => array("lat" => 48.8927, "lng" => 2.3591),
    "Jules Joffrin" => array("lat" => 48.8920, "lng" => 2.3449),
    "Lamarck-Caulaincourt" => array("lat" => 48.8875, "lng" => 2.3382),
    "Abbesses" => array("lat" => 48.8841, "lng" => 2.3380),
    "Saint-Georges" => array("lat" => 48.8797, "lng" => 2.3375),
    "Notre-Dame-de-Lorette" => array("lat" => 48.8768, "lng" => 2.3387),
    "Trinité-d'Estienne d'Orves" => array("lat" => 48.8761, "lng" => 2.3326),
    "Falguière" => array("lat" => 48.8428, "lng" => 2.3171),
    "Volontaires" => array("lat" => 48.8415, "lng" => 2.3129),
    "Vaugirard" => array("lat" => 48.8395, "lng" => 2.3076),
    "Convention" => array("lat" => 48.8380, "lng" => 2.2980),
    "Corentin Celton" => array("lat" => 48.8277, "lng" => 2.2778),
    "Mairie d'Issy" => array("lat" => 48.8240, "lng" => 2.2730),

    // Rue Mage
    "Saint-Denis-Université" => array("lat" => 48.9444, "lng" => 2.3608),
    "Saint-Denis-Porte de Paris" => array("lat" => 48.9304, "lng" => 2.3580),
    "Carrefour Pleyel" => array("lat" => 48.9214, "lng" => 2.3377),
    "Mairie de Saint-Ouen" => array("lat" => 48.9118, "lng" => 2.3339),
    "Garibaldi" => array("lat" => 48.9083, "lng" => 2.3285),
    "Porte de Saint-Ouen" => array("lat" => 48.8998, "lng" => 2.3265),
    "Guy Môquet" => array("lat" => 48.8924, "lng" => 2.3262),
    "La Fourche" => array("lat" => 48.8867, "lng" => 2.3267),
    "Liège" => array("lat" => 48.8787, "lng" => 2.3274),
    "Champs-Élysées-Clemenceau" => array("lat" => 48.8675, "lng" => 2.3130),
    "Invalides" => array("lat" => 48.8626, "lng" => 2.3131),
    "Varenne" => array("lat" => 48.8559, "lng" => 2.3152),
    "Saint-François-Xavier" => array("lat" => 48.8508, "lng" => 2.3158),
    "Gaîté" => array("lat" => 48.8390, "lng" => 2.3213),
    "Pernety" => array("lat" => 48.8356, "lng" => 2.3168),
    "Plaisance" => array("lat" => 48.8322, "lng" => 2.3140),
    "Malakoff-Plateau de Vanves" => array("lat" => 48.8229, "lng" => 2.2949),
    "Malakoff-Rue Étienne Dolet" => array("lat" => 48.8195, "lng" => 2.3020),
    "Châtillon-Montrouge" => array("lat" => 48.8170, "lng" => 2.3073),

    // Rue d'Espinasse
    "Pyramides" => array("lat" => 48.8662, "lng" => 2.3324),
    "Cour Saint-Émilion" => array("lat" => 48.8334, "lng" => 2.3870),
    "Olympiades" => array("lat" => 48.8267, "lng" => 2.3685),

    // Rue des Gestes
    "Gare Saint-Denis" => array("lat" => 48.9187, "lng" => 2.3467),
    "Théâtre Gérard Philipe" => array("lat" => 48.9350, "lng" => 2.3560),
    "Marché de Saint-Denis" => array("lat" => 48.9365, "lng" => 2.3575),
    "Cimetière de Saint-Denis" => array("lat" => 48.9375, "lng" => 2.3520),
    "Hôpital Delafontaine" => array("lat" => 48.9400, "lng" => 2.3540),
    "Cosmonautes" => array("lat" => 48.9415, "lng" => 2.3620),
    "La Courneuve-Six Routes" => array("lat" => 48.9240, "lng" => 2.3990),
    "Hôtel de Ville de La Courneuve" => array("lat" => 48.9230, "lng" => 2.3960),
    "Stade Géo André" => array("lat" => 48.9210, "lng" => 2.3930),
    "Danton" => array("lat" => 48.9190, "lng" => 2.3900),
    "Maurice Lachâtre" => array("lat" => 48.9170, "lng" => 2.3870),
    "Drancy-Avenir" => array("lat" => 48.9160, "lng" => 2.3800),
    "Hôpital Avicenne" => array("lat" => 48.9080, "lng" => 2.4220),
    "Gaston Roulaud" => array("lat" => 48.9070, "lng" => 2.4190),
    "Escadrille Normandie-Niémen" => array("lat" => 48.9050, "lng" => 2.4160),
    "La Ferme" => array("lat" => 48.9030, "lng" => 2.4130),
    "Libération" => array("lat" => 48.9010, "lng" => 2.4100),
    "Hôtel de Ville de Bobigny" => array("lat" => 48.8990, "lng" => 2.4070),
    "Jean Rostand" => array("lat" => 48.8970, "lng" => 2.4040),
    "Auguste Delaune" => array("lat" => 48.8950, "lng" => 2.4010),
    "Pont de Bondy" => array("lat" => 48.8910, "lng" => 2.3970),
    "Petit Noisy" => array("lat" => 48.8890, "lng" => 2.3940),
    "Noisy-le-Sec" => array("lat" => 48.8860, "lng" => 2.3900),

    // Rue Quai de la Daurade
    "Puteaux" => array("lat" => 48.8842, "lng" => 2.2386),
    "Belvédère" => array("lat" => 48.8810, "lng" => 2.2290),
    "Suresnes-Longchamp" => array("lat" => 48.8720, "lng" => 2.2250),
    "Les Coteaux" => array("lat" => 48.8640, "lng" => 2.2200),
    "Les Milons" => array("lat" => 48.8580, "lng" => 2.2150),
    "Parc de Saint-Cloud" => array("lat" => 48.8450, "lng" => 2.2190),
    "Musée de Sèvres" => array("lat" => 48.8250, "lng" => 2.2210),
    "Brimborion" => array("lat" => 48.8220, "lng" => 2.2230),
    "Meudon-sur-Seine" => array("lat" => 48.8200, "lng" => 2.2290),
    "Les Moulineaux" => array("lat" => 48.8230, "lng" => 2.2520),
    "Jacques-Henri Lartigue" => array("lat" => 48.8250, "lng" => 2.2560),
    "Issy-Val de Seine" => array("lat" => 48.8259, "lng" => 2.2631),

    // Rue Bédelières
    "Pont du Garigliano" => array("lat" => 48.8383, "lng" => 2.2681),
    "Desnouettes" => array("lat" => 48.8350, "lng" => 2.2850),
    "Georges Brassens" => array("lat" => 48.8330, "lng" => 2.3010),
    "Brancion" => array("lat" => 48.8320, "lng" => 2.3050),
    "Didot" => array("lat" => 48.8300, "lng" => 2.3130),
    "Jean Moulin" => array("lat" => 48.8280, "lng" => 2.3200),
    "Montsouris" => array("lat" => 48.8210, "lng" => 2.3380),
    "Cité universitaire" => array("lat" => 48.8200, "lng" => 2.3390),
    "Stade Charléty" => array("lat" => 48.8190, "lng" => 2.3450),
    "Poterne des Peupliers" => array("lat" => 48.8170, "lng" => 2.3500),
    "Montempoivre" => array("lat" => 48.8360, "lng" => 2.4150),

    // Rue Merlane
    "Auber" => array("lat" => 48.8724, "lng" => 2.3281),
    "Châtelet-Les Halles" => array("lat" => 48.8609, "lng" => 2.3470),
    "Gare de Lyon" => array("lat" => 48.8443, "lng" => 2.3730),
    "Nation" => array("lat" => 48.8482, "lng" => 2.3958),

    // Rue Vélane
    "Saint-Michel-Notre-Dame" => array("lat" => 48.8530, "lng" => 2.3441),
    "Luxembourg" => array("lat" => 48.8462, "lng" => 2.3371),
    "Port-Royal" => array("lat" => 48.8413, "lng" => 2.3398),
    "Cité Universitaire" => array("lat" => 48.8200, "lng" => 2.3387),

    // Rue Étroite
    "Porte de Clichy" => array("lat" => 48.8926, "lng" => 2.3166),
    "Pereire" => array("lat" => 48.8858, "lng" => 2.2988),
    "Porte Maillot" => array("lat" => 48.8779, "lng" => 2.2825),
    "Avenue Foch" => array("lat" => 48.8715, "lng" => 2.2769),
    "Avenue Henri Martin" => array("lat" => 48.8640, "lng" => 2.2760),
    "Avenue du Président Kennedy" => array("lat" => 48.8563, "lng" => 2.2805),
    "Pont de l'Alma" => array("lat" => 48.8620, "lng" => 2.3000),
    "Musée d'Orsay" => array("lat" => 48.8600, "lng" => 2.3266),
    "Gare d'Austerlitz" => array("lat" => 48.8422, "lng" => 2.3652),
    "Bibliothèque François Mitterrand" => array("lat" => 48.8335, "lng" => 2.3767),

    // Rue des Tourneurs
    // Les arrêts sont déjà définis

    // Rue de la Trinité
    // Les arrêts sont déjà définis
);

// Définir les rues avec leurs arrêts respectifs
$streets = array(
    "Rue Croix-Baragnon" => array(
        "La Défense",
        "Esplanade de la Défense",
        "Pont de Neuilly",
        "Les Sablons",
        "Porte Maillot",
        "Argentine",
        "Charles de Gaulle-Étoile",
        "George V",
        "Franklin D. Roosevelt",
        "Champs-Élysées-Clemenceau",
        "Concorde",
        "Tuileries",
        "Palais Royal-Musée du Louvre",
        "Louvre-Rivoli",
        "Châtelet",
        "Hôtel de Ville",
        "Saint-Paul",
        "Bastille",
        "Gare de Lyon",
        "Reuilly-Diderot",
        "Nation",
        "Porte de Vincennes",
        "Saint-Mandé",
        "Bérault",
        "Château de Vincennes"
    ),
    "Rue des Arts" => array(
        "Porte Dauphine",
        "Victor Hugo",
        "Charles de Gaulle-Étoile",
        "Ternes",
        "Courcelles",
        "Monceau",
        "Villiers",
        "Rome",
        "Place de Clichy",
        "Blanche",
        "Pigalle",
        "Anvers",
        "Barbès-Rochechouart",
        "La Chapelle",
        "Stalingrad",
        "Jaurès",
        "Colonel Fabien",
        "Belleville",
        "Couronnes",
        "Ménilmontant",
        "Père Lachaise",
        "Philippe Auguste",
        "Alexandre Dumas",
        "Avron",
        "Nation"
    ),
    "Rue Pargaminières" => array(
        "Pont de Levallois-Bécon",
        "Anatole France",
        "Louise Michel",
        "Porte de Champerret",
        "Pereire",
        "Wagram",
        "Malesherbes",
        "Villiers",
        "Europe",
        "Saint-Lazare",
        "Havre-Caumartin",
        "Opéra",
        "Quatre-Septembre",
        "Bourse",
        "Sentier",
        "Réaumur-Sébastopol",
        "Arts et Métiers",
        "Temple",
        "République",
        "Parmentier",
        "Rue Saint-Maur",
        "Père Lachaise",
        "Gambetta",
        "Porte de Bagnolet",
        "Gallieni"
    ),
    "Rue Saint-Rome" => array(
        "Gambetta",
        "Pelleport",
        "Saint-Fargeau",
        "Porte des Lilas"
    ),
    "Rue Saint-Antoine du T" => array(
        "Porte de Clignancourt",
        "Simplon",
        "Marcadet-Poissonniers",
        "Château Rouge",
        "Barbès-Rochechouart",
        "Gare du Nord",
        "Gare de l'Est",
        "Château d'Eau",
        "Strasbourg-Saint-Denis",
        "Réaumur-Sébastopol",
        "Étienne Marcel",
        "Les Halles",
        "Châtelet",
        "Cité",
        "Saint-Michel-Notre-Dame",
        "Odéon",
        "Saint-Germain-des-Prés",
        "Saint-Sulpice",
        "Saint-Placide",
        "Montparnasse-Bienvenüe",
        "Vavin",
        "Raspail",
        "Denfert-Rochereau",
        "Mouton-Duvernet",
        "Alésia",
        "Porte d'Orléans"
    ),
    "Rue de la Fonderie" => array(
        "Bobigny-Pablo Picasso",
        "Bobigny-Pantin-Raymond Queneau",
        "Église de Pantin",
        "Hoche",
        "Porte de Pantin",
        "Ourcq",
        "Laumière",
        "Jaurès",
        "Stalingrad",
        "Gare du Nord",
        "Gare de l'Est",
        "Jacques Bonsergent",
        "République",
        "Oberkampf",
        "Richard-Lenoir",
        "Bréguet-Sabin",
        "Bastille",
        "Quai de la Rapée",
        "Gare d'Austerlitz",
        "Saint-Marcel",
        "Campo-Formio",
        "Place d'Italie"
    ),
    "Rue Peyrolières" => array(
        "Charles de Gaulle-Étoile",
        "Kléber",
        "Boissière",
        "Trocadéro",
        "Passy",
        "Champ de Mars-Tour Eiffel",
        "Dupleix",
        "La Motte-Picquet-Grenelle",
        "Cambronne",
        "Sèvres-Lecourbe",
        "Pasteur",
        "Montparnasse-Bienvenüe",
        "Edgar Quinet",
        "Raspail",
        "Denfert-Rochereau",
        "Saint-Jacques",
        "Glacière",
        "Corvisart",
        "Place d'Italie",
        "Nationale",
        "Chevaleret",
        "Quai de la Gare",
        "Bercy",
        "Dugommier",
        "Daumesnil",
        "Bel-Air",
        "Picpus",
        "Nation"
    ),
    "Rue Genty-Magre" => array(
        "La Courneuve-8 Mai 1945",
        "Fort d'Aubervilliers",
        "Aubervilliers-Pantin-Quatre Chemins",
        "Porte de la Villette",
        "Corentin Cariou",
        "Crimée",
        "Riquet",
        "Stalingrad",
        "Louis Blanc",
        "Château-Landon",
        "Gare de l'Est",
        "Poissonnière",
        "Cadet",
        "Le Peletier",
        "Chaussée d'Antin-La Fayette",
        "Opéra",
        "Pyramides",
        "Palais Royal-Musée du Louvre",
        "Pont Neuf",
        "Châtelet",
        "Pont Marie",
        "Sully-Morland",
        "Jussieu",
        "Place Monge",
        "Censier-Daubenton",
        "Les Gobelins",
        "Place d'Italie",
        "Tolbiac",
        "Maison Blanche",
        "Porte d'Italie",
        "Porte de Choisy",
        "Porte d'Ivry",
        "Pierre et Marie Curie",
        "Mairie d'Ivry"
    ),
    "Rue d'Alsace-Lorraine" => array(
        "Louis Blanc",
        "Jaurès",
        "Bolivar",
        "Buttes Chaumont",
        "Botzaris",
        "Place des Fêtes",
        "Pré Saint-Gervais"
    ),
    "Rue Peyras" => array(
        "Balard",
        "Lourmel",
        "Boucicaut",
        "Félix Faure",
        "Commerce",
        "La Motte-Picquet-Grenelle",
        "École Militaire",
        "La Tour-Maubourg",
        "Invalides",
        "Concorde",
        "Madeleine",
        "Opéra",
        "Richelieu-Drouot",
        "Grands Boulevards",
        "Bonne Nouvelle",
        "Strasbourg-Saint-Denis",
        "République",
        "Filles du Calvaire",
        "Saint-Sébastien-Froissart",
        "Chemin Vert",
        "Bastille",
        "Ledru-Rollin",
        "Faidherbe-Chaligny",
        "Reuilly-Diderot",
        "Montgallet",
        "Daumesnil",
        "Michel Bizot",
        "Porte Dorée",
        "Porte de Charenton",
        "Liberté",
        "Charenton-Écoles",
        "École Vétérinaire de Maisons-Alfort",
        "Maisons-Alfort-Stade",
        "Maisons-Alfort-Les Juilliottes",
        "Créteil-L'Échat",
        "Créteil-Université",
        "Créteil-Préfecture"
    ),
    "Rue du Taur" => array(
        "Pont de Sèvres",
        "Billancourt",
        "Marcel Sembat",
        "Porte de Saint-Cloud",
        "Exelmans",
        "Michel-Ange-Molitor",
        "Michel-Ange-Auteuil",
        "Jasmin",
        "Ranelagh",
        "La Muette",
        "Rue de la Pompe",
        "Trocadéro",
        "Iéna",
        "Alma-Marceau",
        "Franklin D. Roosevelt",
        "Saint-Philippe du Roule",
        "Miromesnil",
        "Saint-Augustin",
        "Havre-Caumartin",
        "Chaussée d'Antin-La Fayette",
        "Richelieu-Drouot",
        "Grands Boulevards",
        "Bonne Nouvelle",
        "Strasbourg-Saint-Denis",
        "République",
        "Oberkampf",
        "Saint-Ambroise",
        "Voltaire",
        "Charonne",
        "Rue des Boulets",
        "Nation",
        "Buzenval",
        "Maraîchers",
        "Porte de Montreuil",
        "Robespierre",
        "Croix de Chavaux",
        "Mairie de Montreuil"
    ),
    "Allée Jean Jaurès" => array(
        "Boulogne-Pont de Saint-Cloud",
        "Boulogne-Jean Jaurès",
        "Porte d'Auteuil",
        "Michel-Ange-Auteuil",
        "Église d'Auteuil",
        "Javel-André Citroën",
        "Charles Michels",
        "Avenue Émile Zola",
        "La Motte-Picquet-Grenelle",
        "Ségur",
        "Duroc",
        "Vaneau",
        "Sèvres-Babylone",
        "Mabillon",
        "Odéon",
        "Cluny-La Sorbonne",
        "Maubert-Mutualité",
        "Cardinal Lemoine",
        "Jussieu",
        "Gare d'Austerlitz"
    ),
    "Rue du May" => array(
        "Châtelet",
        "Hôtel de Ville",
        "Rambuteau",
        "Arts et Métiers",
        "République",
        "Goncourt",
        "Belleville",
        "Pyrénées",
        "Jourdain",
        "Place des Fêtes",
        "Télégraphe",
        "Porte des Lilas",
        "Mairie des Lilas"
    ),
    "Rue des Filatiers" => array(
        "Porte de la Chapelle",
        "Marx Dormoy",
        "Marcadet-Poissonniers",
        "Jules Joffrin",
        "Lamarck-Caulaincourt",
        "Abbesses",
        "Pigalle",
        "Saint-Georges",
        "Notre-Dame-de-Lorette",
        "Trinité-d'Estienne d'Orves",
        "Saint-Lazare",
        "Madeleine",
        "Concorde",
        "Assemblée nationale",
        "Solférino",
        "Rue du Bac",
        "Sèvres-Babylone",
        "Rennes",
        "Notre-Dame-des-Champs",
        "Montparnasse-Bienvenüe",
        "Falguière",
        "Pasteur",
        "Volontaires",
        "Vaugirard",
        "Convention",
        "Porte de Versailles",
        "Corentin Celton",
        "Mairie d'Issy"
    ),
    "Rue Mage" => array(
        "Saint-Denis-Université",
        "Basilique de Saint-Denis",
        "Saint-Denis-Porte de Paris",
        "Carrefour Pleyel",
        "Mairie de Saint-Ouen",
        "Garibaldi",
        "Porte de Saint-Ouen",
        "Guy Môquet",
        "La Fourche",
        "Place de Clichy",
        "Liège",
        "Saint-Lazare",
        "Miromesnil",
        "Champs-Élysées-Clemenceau",
        "Invalides",
        "Varenne",
        "Saint-François-Xavier",
        "Duroc",
        "Montparnasse-Bienvenüe",
        "Gaîté",
        "Pernety",
        "Plaisance",
        "Porte de Vanves",
        "Malakoff-Plateau de Vanves",
        "Malakoff-Rue Étienne Dolet",
        "Châtillon-Montrouge"
    ),
    "Rue d'Espinasse" => array(
        "Saint-Lazare",
        "Madeleine",
        "Pyramides",
        "Châtelet",
        "Gare de Lyon",
        "Bercy",
        "Cour Saint-Émilion",
        "Bibliothèque François Mitterrand",
        "Olympiades"
    ),
    "Rue des Gestes" => array(
        "Gare Saint-Denis",
        "Théâtre Gérard Philipe",
        "Marché de Saint-Denis",
        "Basilique de Saint-Denis",
        "Cimetière de Saint-Denis",
        "Hôpital Delafontaine",
        "Cosmonautes",
        "La Courneuve-Six Routes",
        "Hôtel de Ville de La Courneuve",
        "Stade Géo André",
        "Danton",
        "La Courneuve-8 Mai 1945",
        "Maurice Lachâtre",
        "Drancy-Avenir",
        "Hôpital Avicenne",
        "Gaston Roulaud",
        "Escadrille Normandie-Niémen",
        "La Ferme",
        "Libération",
        "Hôtel de Ville de Bobigny",
        "Bobigny-Pablo Picasso",
        "Jean Rostand",
        "Auguste Delaune",
        "Pont de Bondy",
        "Petit Noisy",
        "Noisy-le-Sec"
    ),
    "Rue Quai de la Daurade" => array(
        "La Défense",
        "Puteaux",
        "Belvédère",
        "Suresnes-Longchamp",
        "Les Coteaux",
        "Les Milons",
        "Parc de Saint-Cloud",
        "Musée de Sèvres",
        "Brimborion",
        "Meudon-sur-Seine",
        "Les Moulineaux",
        "Jacques-Henri Lartigue",
        "Issy-Val de Seine",
        "Balard",
        "Porte de Versailles"
    ),
    "Rue Bédelières" => array(
        "Pont du Garigliano",
        "Balard",
        "Desnouettes",
        "Porte de Versailles",
        "Georges Brassens",
        "Brancion",
        "Porte de Vanves",
        "Didot",
        "Jean Moulin",
        "Porte d'Orléans",
        "Montsouris",
        "Cité universitaire",
        "Stade Charléty",
        "Poterne des Peupliers",
        "Porte d'Italie",
        "Porte de Choisy",
        "Porte d'Ivry",
        "Bibliothèque François Mitterrand",
        "Porte de Charenton",
        "Porte Dorée",
        "Montempoivre",
        "Porte de Vincennes"
    ),
    "Rue Merlane" => array(
        "La Défense",
        "Charles de Gaulle-Étoile",
        "Auber",
        "Châtelet-Les Halles",
        "Gare de Lyon",
        "Nation"
    ),
    "Rue Vélane" => array(
        "Gare du Nord",
        "Châtelet-Les Halles",
        "Saint-Michel-Notre-Dame",
        "Luxembourg",
        "Port-Royal",
        "Denfert-Rochereau",
        "Cité Universitaire"
    ),
    "Rue Étroite" => array(
        "Porte de Clichy",
        "Pereire",
        "Porte Maillot",
        "Avenue Foch",
        "Avenue Henri Martin",
        "La Muette",
        "Avenue du Président Kennedy",
        "Champ de Mars-Tour Eiffel",
        "Pont de l'Alma",
        "Invalides",
        "Musée d'Orsay",
        "Saint-Michel-Notre-Dame",
        "Gare d'Austerlitz",
        "Bibliothèque François Mitterrand"
    ),
    "Rue des Tourneurs" => array(
        "Gare du Nord",
        "Châtelet-Les Halles",
        "Gare de Lyon"
    ),
    "Rue de la Trinité" => array(
        "Saint-Lazare",
        "Gare du Nord"
    )
);



// Connexion à la base de données
$dsn = "mysql:host=localhost;dbname=poubelle_verte;charset=utf8";
$username = "root";
$password = "";



try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Récupérer les vélos disponibles
$sqlVelo = "SELECT id, numero FROM velos WHERE etat = 'en_cours_utilisation'";
$velosDisponibles = $pdo->query($sqlVelo)->fetchAll();

// Récupérer les utilisateurs
$sqlUtilisateur = "SELECT id, nom, prenom FROM utilisateurs WHERE disponibilite = 'en tournée'";
$utilisateursDisponibles = $pdo->query($sqlUtilisateur)->fetchAll();

// Nombre d'agents = nombre de vélos disponibles ou nombre d'utilisateurs disponibles, le plus petit
$numAgents = min(count($velosDisponibles), count($utilisateursDisponibles));

// Autres paramètres
$groupSize = 4; // Nombre d'arrêts avant de revenir à "Porte d'Ivry"
$startStop = "Porte d'Ivry"; // Point de départ
$stopsToVisit = array_keys($stops);
$visitedStops = [];

// Retirer le point de départ des arrêts à visiter
if (($key = array_search($startStop, $stopsToVisit)) !== false) {
    unset($stopsToVisit[$key]);
}

// Générer tous les itinéraires possibles
$allRoutes = [];
while (!empty($stopsToVisit)) {
    $route = [$startStop];
    $newStops = [];

    foreach ($stopsToVisit as $stop) {
        if (count($newStops) < $groupSize) {
            $newStops[] = $stop;
            $visitedStops[] = $stop;
        } else {
            break;
        }
    }

    // Retirer les arrêts visités de la liste des arrêts à visiter
    foreach ($newStops as $stop) {
        if (($key = array_search($stop, $stopsToVisit)) !== false) {
            unset($stopsToVisit[$key]);
        }
    }

    // Ajouter les nouveaux arrêts à la route
    $route = array_merge($route, $newStops);

    // Retourner au point de départ
    $route[] = $startStop;

    // Ajouter la route à la liste des itinéraires
    $allRoutes[] = $route;
}

// Distribuer les itinéraires entre les agents
$itineraries = [];
for ($i = 0; $i < $numAgents; $i++) {
    $itineraries[$i] = [];
}

if ($numAgents <= 0) {
    die("Erreur : le nombre d'agents doit être supérieur à zéro.");
}

$routeIndex = 0;
foreach ($allRoutes as $route) {
    $agentIndex = $routeIndex % $numAgents;
    $itineraries[$agentIndex][] = $route;
    $routeIndex++;
}

// Récupérer les incidents
try {
    $sqlIncidents = "SELECT arret, description, DATE_FORMAT(date, '%d/%m/%Y %H:%i') AS date FROM incidents";
    $incidents = $pdo->query($sqlIncidents)->fetchAll();
} catch (PDOException $e) {
    die("Erreur lors de la récupération des incidents : " . $e->getMessage());
}
// Récupération des incidents enregistrés (uniquement non résolus)
$sqlIncidents = "
SELECT 
    i.id, 
    i.tournee_id, 
    i.type_incident, 
    i.date, 
    i.heure, 
    i.description, 
    i.resolution, 
    i.resolved_at, 
    t.date AS tournee_date, 
    t.heure_debut, 
    t.heure_fin,
    COALESCE(a.libelle, 'Aucun') AS arret_libelle, -- Valeur par défaut si NULL
    COALESCE(r.libelle, 'Aucune') AS rue_libelle  -- Valeur par défaut si NULL
FROM incidents i
LEFT JOIN tournees t ON i.tournee_id = t.id
LEFT JOIN arrets a ON i.arret_id = a.id
LEFT JOIN rues r ON i.rue_id = r.id
WHERE i.resolved_at IS NULL -- Filtrer uniquement les incidents non résolus
ORDER BY i.date DESC, i.heure DESC
";

// Utilisation de PDO
try {
    $resultIncidents = $pdo->query($sqlIncidents)->fetchAll();
} catch (PDOException $e) {
    die("Erreur lors de la récupération des incidents : " . $e->getMessage());
}

// Récupérer les arrêts bloqués à cause des incidents
// Récupérer les arrêts et rues bloqués
$sqlBlockedStops = "
    SELECT 
        a.libelle AS arret_libelle
    FROM incidents i
    LEFT JOIN arrets a ON i.arret_id = a.id
    WHERE i.resolved_at IS NULL
";
try {
    $blockedStops = $pdo->query($sqlBlockedStops)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des arrêts bloqués : " . $e->getMessage());
}

$blockedArrets = array_column($blockedStops, 'arret_libelle');
$blockedRues = array_column($blockedStops, 'rue_libelle');

// Filtrer les arrêts bloqués
// Exclusion des arrêts et rues bloqués
$stopsToVisit = array_filter($stopsToVisit, function ($stop) use ($blockedStops) {
    return !in_array(trim($stop), $blockedStops);
});

// Réinitialiser les itinéraires
$allRoutes = [];
$visitedStops = [];

// Générer les itinéraires sans les arrêts bloqués
while (!empty($stopsToVisit)) {
    $route = [$startStop];
    $newStops = [];

    foreach ($stopsToVisit as $stop) {
        if (count($newStops) < $groupSize) {
            $newStops[] = $stop;
        }
    }

    // Retirer les arrêts visités
    foreach ($newStops as $stop) {
        if (($key = array_search($stop, $stopsToVisit)) !== false) {
            unset($stopsToVisit[$key]);
        }
    }

    $route = array_merge($route, $newStops);
    $route[] = $startStop;

    $allRoutes[] = $route;
}

echo json_encode(['itineraries' => $allRoutes]);

function calculateDistance($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371; // Rayon moyen de la Terre en km

    // Convertir les degrés en radians
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    // Calcul de la distance haversine
    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earthRadius * $c; // Distance en km
}


// Configuration
$autonomy = 50; // km
$autonomyPer20Lights = 1; // km
$winterPenalty = 0.9; // 10% reduction in winter
$wastePenaltyPer50kg = 2; // km
$maxWeight = 200; // kg
$pickupSpeed = 5; // km/h
$routeSpeed = 20; // km/h
$winter = true; // Set true for winter calculations

// Vérifier qu'un itinéraire existe pour éviter les erreurs
if (empty($route)) {
    die("Erreur : Aucun itinéraire trouvé pour effectuer le calcul.");
}

// Initialiser les calculs
$totalDistance = 0;
$totalWeight = 0; // Déchets accumulés
$stopsCoordinates = []; // Coordonnées des arrêts
foreach ($route as $stop) {
    if (isset($stops[$stop])) {
        $stopsCoordinates[] = $stops[$stop];
    } else {
        echo "Erreur : coordonnées non disponibles pour l'arrêt $stop.<br>";
    }
}

// Calculer la distance totale en utilisant les distances réelles entre arrêts
for ($i = 0; $i < count($stopsCoordinates) - 1; $i++) {
    $lat1 = $stopsCoordinates[$i]['lat'];
    $lon1 = $stopsCoordinates[$i]['lng'];
    $lat2 = $stopsCoordinates[$i + 1]['lat'];
    $lon2 = $stopsCoordinates[$i + 1]['lng'];
    $totalDistance += calculateDistance($lat1, $lon1, $lat2, $lon2);
    $totalWeight += 50; // Ajout des déchets (50 kg par arrêt)
}

// Ajuster l'autonomie en fonction des feux et des pénalités
$adjustedAutonomy = $autonomy;
$totalLights = 40; // Exemple de nombre de feux/carrefours
$adjustedAutonomy -= floor($totalLights / 20) * $autonomyPer20Lights; // Pénalité pour feux

if ($winter) {
    $adjustedAutonomy *= $winterPenalty; // Réduction en hiver
}

$adjustedAutonomy -= floor($totalWeight / 50) * $wastePenaltyPer50kg; // Pénalité pour le poids

// Vérifier si un retour à la base est nécessaire
$extraTrips = 0;
if ($totalDistance > $adjustedAutonomy) {
    // Distance aller-retour pour recharger
    $roundTripDistance = 2 * ($adjustedAutonomy / 2);
    $extraTrips = ceil($totalDistance / $adjustedAutonomy) - 1;
    $totalDistance += $extraTrips * $roundTripDistance; // Ajouter la distance supplémentaire pour les trajets de recharge
}

// Calculer le temps total
$timePickup = $totalDistance / $pickupSpeed; // Temps en mode ramassage
$timeRoute = ($extraTrips * $roundTripDistance) / $routeSpeed; // Temps pour les trajets de recharge
$totalTime = $timePickup + $timeRoute;

// Formater le temps en heures et minutes
$totalHours = floor($totalTime);
$totalMinutes = round(($totalTime - $totalHours) * 60);


?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Itinéraires des vélos de la tournée</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Inclure la feuille de style de Bootstrap et Leaflet -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />

    <style>
        #map {
            height: 600px;
            margin-top: 20px;
        }

        body {
            margin: 0;
            padding: 0;
        }

        .agent {
            margin-bottom: 20px;
        }

        .revisited {
            color: red;
            text-decoration: underline;
        }

        .card-title {
            font-size: 1.5rem;
        }

        .list-group-item {
            font-size: 1rem;
            line-height: 1.4;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center text-primary mb-4">Itinéraires des vélos pour la tournée</h1>
        <!-- Bouton retour vers le dashboard -->
        <div class="text-center mb-4">
            <a href="gestionnaire_reseau.php" class="btn btn-secondary">Retour au Dashboard</a>
        </div>
        <div class="text-center mb-4">
            <a href="gestion_tournees.php" class="btn btn-secondary">Retour à la gestion des trajets</a>
        </div>
        <!-- Formulaire -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="get" class="row g-3">
                    <div class="col-md-6">
                        <label for="numAgents" class="form-label">Nombre d'agents (automatique) :</label>
                        <input type="number" class="form-control" id="numAgents" name="numAgents" value="<?php echo $numAgents; ?>" min="1" readonly>
                    </div>
                    <div class="col-md-6 align-self-end">
                        <input type="submit" value="Actualiser les itinéraires" class="btn btn-primary">
                    </div>
                </form>
            </div>
        </div>

                <!-- Temps estimé pour le trajet -->
                <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">Temps Estimé pour le Trajet</h2>
                <p class="text-success">
                    Temps estimé pour compléter l'itinéraire :
                    <strong><?= $totalHours ?> heures et <?= $totalMinutes ?> minutes</strong>.
                </p>
                <p class="text-info">
                    Autonomie ajustée :
                    <strong><?= round($adjustedAutonomy, 2) ?> km</strong>.
                </p>
                <p class="text-primary">
                    Distance totale à parcourir :
                    <strong><?= round($totalDistance, 2) ?> km</strong>.
                </p>
                <?php if ($extraTrips > 0): ?>
                    <p class="text-warning">
                        Nombre de retours à la base pour recharger :
                        <strong><?= $extraTrips ?></strong>.
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Liste des incidents -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">Incidents signalés</h2>
                <?php if (!empty($resultIncidents)): ?>
                    <ul class="list-group">
                        <?php
                        foreach ($resultIncidents as $incident) {
                            $arretLibelle = !empty($incident['arret_libelle']) ? htmlspecialchars($incident['arret_libelle']) : 'Aucun';
                            $rueLibelle = !empty($incident['rue_libelle']) ? htmlspecialchars($incident['rue_libelle']) : 'Aucune';
                            $description = htmlspecialchars($incident['description']);
                            $date = htmlspecialchars($incident['date']);

                            echo "<li class='list-group-item'>";
                            echo "<strong>Arrêt :</strong> $arretLibelle, ";
                            echo "<strong>Rue :</strong> $rueLibelle<br>";
                            echo "<strong>Description :</strong> $description<br>";
                            echo "<strong>Date :</strong> $date";
                            echo "</li>";
                        }
                        ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">Aucun incident signalé.</p>
                <?php endif; ?>
            </div>
        </div>



        <!-- Liste des agents -->
        <div class="card mb-4">
            <div class="card-body">
                <h2 class="card-title">Agents et leurs vélos attribués</h2>
                <ul class="list-group">
                    <?php
                    for ($i = 0; $i < $numAgents; $i++) {
                        $agent = $utilisateursDisponibles[$i];
                        $velo = $velosDisponibles[$i];
                        echo "<li class='list-group-item'>Agent " . ($i + 1) . ": " . htmlspecialchars($agent['prenom'] . " " . $agent['nom']) .
                            " (Vélo #" . htmlspecialchars($velo['numero']) . ")</li>";
                    }
                    ?>
                </ul>
            </div>
        </div>

        <!-- Sélection de l'agent -->
        <div class="card mb-4">
            <div class="card-body">
                <label for="agentSelect" class="form-label">Sélectionnez un agent :</label>
                <select id="agentSelect" class="form-select">
                    <option value="all">Tous les agents</option>
                    <?php
                    foreach ($itineraries as $agent => $routes) {
                        echo '<option value="agent' . $agent . '">Agent ' . ($agent + 1) . '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <!-- Affichage des itinéraires -->
        <div>
            <?php
            foreach ($itineraries as $agent => $routes) {
                echo "<div class='agent card mb-4' id='agent" . $agent . "'>";
                echo "<div class='card-body'>";
                echo "<h2 class='card-title'>Itinéraire pour l'agent " . ($agent + 1) . "</h2>";
                foreach ($routes as $routeIndex => $route) {
                    echo "<h3>Route " . ($routeIndex + 1) . "</h3>";
                    echo "<ul class='list-group'>";
                    foreach ($route as $stop) {
                        echo "<li class='list-group-item'>" . htmlspecialchars($stop) . "</li>";
                    }
                    echo "</ul>";
                }
                echo "</div>";
                echo "</div>";
            }
            ?>
        </div>


        <!-- Carte -->
        <div id="map" class="rounded border"></div>
    </div>

    <!-- Inclure Bootstrap JS et Leaflet JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <div class="text-center mt-4">
    <button id="nextButton" class="btn btn-primary">Suivant</button>
</div>
    <script>
        // Initialiser la carte
        var map = L.map('map').setView([48.8566, 2.3522], 12);

        // Ajouter une couche de tuiles OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png', {
            maxZoom: 20,
            attribution: '&copy; OpenStreetMap France | Données &copy; contributeurs OpenStreetMap'
        }).addTo(map);

        // Données des arrêts depuis PHP
        var stops = <?php echo json_encode($stops); ?>;

        // Données des itinéraires depuis PHP
        var itineraries = <?php echo json_encode($itineraries); ?>;

        // Couleurs pour chaque agent
        var colors = ['red', 'blue', 'green', 'orange', 'purple', 'brown', 'cyan', 'magenta'];

        // Stocker les polylignes et marqueurs pour chaque agent
        var agentLayers = {};

        var agentIndex = 0;
        for (var agent in itineraries) {
            var routes = itineraries[agent];
            var agentLayerGroup = L.layerGroup(); // Créer un groupe de calques pour cet agent

            for (var i = 0; i < routes.length; i++) {
                var route = routes[i];
                var latlngs = [];
                for (var j = 0; j < route.length; j++) {
                    var stopName = route[j];
                    if (stops[stopName]) {
                        latlngs.push([stops[stopName].lat, stops[stopName].lng]);
                    }
                }
                var polyline = L.polyline(latlngs, {
                    color: colors[agentIndex % colors.length],
                    weight: 3,
                    opacity: 0.7
                });
                polyline.bindPopup("<b>Agent " + (parseInt(agent) + 1) + " - Route " + (i + 1) + "</b>");
                agentLayerGroup.addLayer(polyline);
            }

            // Ajouter les arrêts à la carte
            for (var stop in stops) {
                var marker = L.circleMarker([stops[stop].lat, stops[stop].lng], {
                    radius: 5,
                    fillColor: '#0000FF',
                    color: '#0000FF',
                    weight: 1,
                    opacity: 1,
                    fillOpacity: 0.8
                });
                marker.bindPopup("<b>" + stop + "</b>");
                agentLayerGroup.addLayer(marker);
            }

            agentLayers['agent' + agent] = agentLayerGroup;
            agentIndex++;
        }

        var cyclistMarkers = {}; // Pour suivre les cyclistes
   // Ajouter les itinéraires et les cyclistes
   for (var agent in itineraries) {
        var routes = itineraries[agent];
        var agentLayerGroup = L.layerGroup(); // Créer un groupe de calques pour cet agent

        // Position initiale du cycliste (premier arrêt du premier itinéraire)
        var initialPosition = null;

        for (var i = 0; i < routes.length; i++) {
            var route = routes[i];
            var latlngs = [];
            for (var j = 0; j < route.length; j++) {
                var stopName = route[j];
                if (stops[stopName]) {
                    latlngs.push([stops[stopName].lat, stops[stopName].lng]);
                }
            }
            // Enregistrer la position initiale
            if (i === 0 && latlngs.length > 0) {
                initialPosition = latlngs[0];
            }

            // Ajouter une polyligne pour l'itinéraire
            var polyline = L.polyline(latlngs, {
                color: colors[agentIndex % colors.length],
                weight: 3,
                opacity: 0.7
            });
            polyline.bindPopup("<b>Agent " + (parseInt(agent) + 1) + " - Route " + (i + 1) + "</b>");
            agentLayerGroup.addLayer(polyline);
        }

        // Ajouter un marqueur pour le cycliste
        if (initialPosition) {
            var cyclistIcon = L.icon({
                iconUrl: 'https://cdn-icons-png.flaticon.com/512/194/194933.png', // Icône pour le cycliste
                iconSize: [30, 30], // Taille de l'icône
                iconAnchor: [15, 15], // Point d'ancrage
                popupAnchor: [0, -15] // Point où afficher la popup
            });

            var cyclistMarker = L.marker(initialPosition, { icon: cyclistIcon });
            cyclistMarker.bindPopup("<b>Cycliste de l'Agent " + (parseInt(agent) + 1) + "</b>");
            agentLayerGroup.addLayer(cyclistMarker);

            // Enregistrer le marqueur du cycliste
            cyclistMarkers['agent' + agent] = cyclistMarker;
        }

        // Ajouter les arrêts à la carte
        for (var stop in stops) {
            var marker = L.circleMarker([stops[stop].lat, stops[stop].lng], {
                radius: 5,
                fillColor: '#0000FF',
                color: '#0000FF',
                weight: 1,
                opacity: 1,
                fillOpacity: 0.8
            });
            marker.bindPopup("<b>" + stop + "</b>");
            agentLayerGroup.addLayer(marker);
        }

        agentLayers['agent' + agent] = agentLayerGroup;
        agentIndex++;
    }


        // Fonction pour afficher uniquement l'itinéraire de l'agent sélectionné
        function showAgent(agentId) {
            // Supprimer toutes les couches de la carte
            for (var key in agentLayers) {
                map.removeLayer(agentLayers[key]);
            }

            if (agentId === 'all') {
                // Afficher tous les agents
                for (var key in agentLayers) {
                    agentLayers[key].addTo(map);
                }
                // Afficher tous les itinéraires textuels
                var agentDivs = document.getElementsByClassName('agent');
                for (var i = 0; i < agentDivs.length; i++) {
                    agentDivs[i].style.display = 'block';
                }
            } else {
                // Afficher uniquement l'agent sélectionné
                agentLayers[agentId].addTo(map);

                // Cacher les itinéraires des autres agents
                var agentDivs = document.getElementsByClassName('agent');
                for (var i = 0; i < agentDivs.length; i++) {
                    if (agentDivs[i].id === agentId) {
                        agentDivs[i].style.display = 'block';
                    } else {
                        agentDivs[i].style.display = 'none';
                    }
                }
            }
        }

        // Ajouter un écouteur d'événement sur le sélecteur d'agent
        var agentSelect = document.getElementById('agentSelect');
        agentSelect.addEventListener('change', function() {
            var selectedAgent = this.value;
            showAgent(selectedAgent);
        });

        // Afficher initialement tous les agents
        showAgent('all');

        setInterval(() => {
            fetch('http://localhost/projet_poubelle_verte/gestionnaire_reseau/fetch_incidents.php')
                .then(response => response.json())
                .then(data => {
                    const blockedStops = data.blockedStops.map(stop => stop.arret_libelle);

                    // Mettez à jour vos itinéraires en excluant les arrêts bloqués
                    updateBlockedStops(blockedStops);
                    regenerateRoutes(); // Appellez cette fonction pour redessiner les itinéraires
                })
                .catch(error => console.error("Erreur lors de la récupération des incidents : ", error));

        }, 30000); // Vérifier les incidents toutes les 30 secondes

        function updateBlockedStops(blockedStops) {
            // Mise à jour des arrêts bloqués au niveau global
            blockedArrets = blockedStops;

            // Filtrer les itinéraires existants pour exclure les arrêts bloqués
            for (const agent in itineraries) {
                itineraries[agent] = itineraries[agent].map(route => {
                    return route.filter(stop => !blockedArrets.includes(stop));
                });
            }
        }


        function regenerateRoutes() {
            // Logique pour redessiner les itinéraires en excluant les arrêts bloqués
            console.log("Itinéraires mis à jour !");
        }

        function regenerateRoutes() {
            // Supprimer toutes les couches actuelles
            for (const key in agentLayers) {
                map.removeLayer(agentLayers[key]);
            }

            // Reconstruire les itinéraires avec les arrêts/rues mises à jour
            agentLayers = {}; // Réinitialiser les couches
            let agentIndex = 0;

            for (const agent in itineraries) {
                const routes = itineraries[agent];
                const agentLayerGroup = L.layerGroup();

                for (let i = 0; i < routes.length; i++) {
                    const route = routes[i];
                    const latlngs = route
                        .filter(stop => !blockedArrets.includes(stop)) // Exclusion des arrêts bloqués
                        .map(stop => {
                            if (stops[stop]) {
                                return [stops[stop].lat, stops[stop].lng];
                            }
                            return null;
                        })
                        .filter(coord => coord !== null);

                    if (latlngs.length > 1) {
                        const polyline = L.polyline(latlngs, {
                            color: colors[agentIndex % colors.length],
                            weight: 3,
                            opacity: 0.7,
                        });
                        polyline.bindPopup(`<b>Agent ${parseInt(agent) + 1} - Route ${i + 1}</b>`);
                        agentLayerGroup.addLayer(polyline);
                    }
                }

                agentLayers['agent' + agent] = agentLayerGroup;
                agentIndex++;
            }

            // Afficher tous les itinéraires
            showAgent('all');
        }

        
    </script>

</body>

</html>