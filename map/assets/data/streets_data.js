// Données des rues et arrêts avec distances et feux de circulation
const ruesEtArrets = {
    "Rue Croix-Baragnon": {
        stops: ["La Défense", "Esplanade de la Défense", "Pont de Neuilly", "Les Sablons", "Porte Maillot", "Argentine", "Charles de Gaulle-Étoile", "George V", "Franklin D. Roosevelt", "Champs-Élysées-Clemenceau", "Concorde", "Tuileries", "Palais Royal-Musée du Louvre", "Louvre-Rivoli", "Châtelet", "Hôtel de Ville", "Saint-Paul", "Bastille", "Gare de Lyon", "Reuilly-Diderot", "Nation", "Porte de Vincennes", "Saint-Mandé", "Bérault", "Château de Vincennes"],
        distances: Array(24).fill(0.5),
        trafficLights: [2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3]
    },
    "Rue des Arts": {
        stops: ["Porte Dauphine", "Victor Hugo", "Charles de Gaulle-Étoile", "Ternes", "Courcelles", "Monceau", "Villiers", "Rome", "Place de Clichy", "Blanche", "Pigalle", "Anvers", "Barbès-Rochechouart", "La Chapelle", "Stalingrad", "Jaurès", "Colonel Fabien", "Belleville", "Couronnes", "Ménilmontant", "Père Lachaise", "Philippe Auguste", "Alexandre Dumas", "Avron", "Nation"],
        distances: Array(25).fill(0.5),
        trafficLights: [2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3]
    },
    "Rue Pargaminières": {
        stops: ["Pont de Levallois-Bécon", "Anatole France", "Louise Michel", "Porte de Champerret", "Pereire", "Wagram", "Malesherbes", "Villiers", "Europe", "Saint-Lazare", "Havre-Caumartin", "Opéra", "Quatre-Septembre", "Bourse", "Sentier", "Réaumur-Sébastopol", "Arts et Métiers", "Temple", "République", "Parmentier", "Rue Saint-Maur", "Père Lachaise", "Gambetta", "Porte de Bagnolet", "Gallieni"],
        distances: Array(25).fill(0.5),
        trafficLights: [2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3]
    },
    "Rue Saint-Rome": {
        stops: ["Gambetta", "Pelleport", "Saint-Fargeau", "Porte des Lilas"],
        distances: Array(4).fill(0.5),
        trafficLights: [1, 0, 2]
    },
    "Rue Saint-Antoine du T": {
        stops: ["Porte de Clignancourt", "Simplon", "Marcadet-Poissonniers", "Château Rouge", "Barbès-Rochechouart", "Gare du Nord", "Gare de l'Est", "Château d'Eau", "Strasbourg-Saint-Denis", "Réaumur-Sébastopol", "Étienne Marcel", "Les Halles", "Châtelet", "Cité", "Saint-Michel-Notre-Dame", "Odéon", "Saint-Germain-des-Prés", "Saint-Sulpice", "Saint-Placide", "Montparnasse-Bienvenüe", "Vavin", "Raspail", "Denfert-Rochereau", "Mouton-Duvernet", "Alésia", "Porte d'Orléans"],
        distances: Array(26).fill(0.5),
        trafficLights: [2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1]
    },
    "Rue de la Fonderie": {
        stops: ["Bobigny-Pablo Picasso", "Bobigny-Pantin-Raymond Queneau", "Église de Pantin", "Hoche", "Porte de Pantin", "Ourcq", "Laumière", "Jaurès", "Stalingrad", "Gare du Nord", "Gare de l'Est", "Jacques Bonsergent", "République", "Oberkampf", "Richard-Lenoir", "Bréguet-Sabin", "Bastille", "Quai de la Rapée", "Gare d'Austerlitz", "Saint-Marcel", "Campo-Formio", "Place d'Italie"],
        distances: Array(22).fill(0.5),
        trafficLights: [2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2]
    },
    "Rue Peyrolières": {
        stops: ["Charles de Gaulle-Étoile", "Kléber", "Boissière", "Trocadéro", "Passy", "Champ de Mars-Tour Eiffel", "Dupleix", "La Motte-Picquet-Grenelle", "Cambronne", "Sèvres-Lecourbe", "Pasteur", "Montparnasse-Bienvenüe", "Edgar Quinet", "Raspail", "Denfert-Rochereau", "Saint-Jacques", "Glacière", "Corvisart", "Place d'Italie", "Nationale", "Chevaleret", "Quai de la Gare", "Bercy", "Dugommier", "Daumesnil", "Bel-Air", "Picpus","Nation"],
        distances: Array(26).fill(0.5),
        trafficLights: [2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2]
    },
    "Rue Genty-Magre": {
        stops: ["La Courneuve - 8 Mai 1945", "Fort d'Aubervilliers", "Aubervilliers-Pantin-Quatre Chemins", "Porte de la Villette", "Corentin Cariou", "Crimee", "Riquet", "Stalingrad", "Louis Blanc", "Château-Landon", "Gare de l'Est", "Poissonnière", "Cadet", "Le Peletier", "Chaussée d'Antin - La Fayette", "Opéra", "Pyramides", "Palais Royal - Musée du Louvre", "Pont Neuf", "Châtelet", "Pont Marie", "Sully - Morland", "Jussieu", "Place Monge", "Censier - Daubenton", "Les Gobelins", "Place d'Italie", "Tolbiac", "Maison Blanche", "Porte d'Italie", "Porte de Choisy", "Porte d'Ivry", "Pierre et Marie Curie", "Mairie d'Ivry"],
        distances: Array(33).fill(0.5),
        trafficLights: [2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0]
    },
    "Rue d'Alsace-Lorraine": {
        stops: ["Louis Blanc", "Jaurès", "Bolivar", "Buttes Chaumont", "Botzaris", "Place des Fêtes", "Pré Saint-Gervais"],
        distances: Array(7).fill(0.5),
        trafficLights: [1, 2, 0, 1, 2, 1]
    },
    "Rue Peyras": {
        stops: ["Balard", "Lourmel", "Boucicaut", "Félix Faure", "Commerce", "La Motte-Picquet-Grenelle", "École Militaire", "La Tour-Maubourg", "Invalides", "Concorde", "Madeleine", "Opéra", "Richelieu-Drouot", "Grands Boulevards", "Bonne Nouvelle", "Strasbourg-Saint-Denis", "République", "Filles du Calvaire", "Saint-Sébastien-Froissart", "Chemin Vert", "Bastille", "Ledru-Rollin", "Faidherbe-Chaligny", "Reuilly-Diderot", "Montgallet", "Daumesnil", "Michel Bizot", "Porte Dorée", "Porte de Charenton", "Liberté", "Charenton-Écoles", "École Vétérinaire de Maisons-Alfort", "Maisons-Alfort-Stade", "Maisons-Alfort-Les Juilliottes", "Créteil-L'Échat", "Créteil-Université", "Créteil-Préfecture"],
        distances: Array(37).fill(0.5),
        trafficLights: [2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1]
    },
    "Rue du Taur": {
        stops: ["Pont de Sèvres", "Billancourt", "Marcel Sembat", "Porte de Saint-Cloud", "Exelmans", "Michel-Ange-Molitor", "Michel-Ange-Auteuil", "Jasmin", "Ranelagh", "La Muette", "Rue de la Pompe", "Trocadéro", "Iéna", "Alma-Marceau", "Franklin D. Roosevelt", "Saint-Philippe du Roule", "Miromesnil", "Saint-Augustin", "Havre-Caumartin", "Chaussée d'Antin-La Fayette", "Richelieu-Drouot", "Grands Boulevards", "Bonne Nouvelle", "Strasbourg-Saint-Denis", "République", "Oberkampf", "Saint-Ambroise", "Voltaire", "Charonne", "Rue des Boulets", "Nation", "Buzenval", "Maraîchers", "Porte de Montreuil", "Robespierre", "Croix de Chavaux", "Mairie de Montreuil"],
        distances: Array(37).fill(0.5),
        trafficLights: [2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1]
    },
    "Allée Jean Jaurès": {
        stops: ["Boulogne - Pont de Saint-Cloud", "Boulogne - Jean Jaurès", "Porte d'Auteuil", "Michel-Ange-Auteuil", "Église d'Auteuil", "Javel-André Citroën", "Charles Michels", "Avenue Émile Zola", "La Motte-Picquet - Grenelle", "Ségur", "Duroc", "Vaneau", "Sèvres-Babylone", "Mabillon", "Odéon", "Cluny-La Sorbonne", "Maubert-Mutualité", "Cardinal Lemoine", "Jussieu", "Gare d'Austerlitz"],
        distances: Array(20).fill(0.5),
        trafficLights: [2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1]
    },
    "Rue du May": {
        stops: ["Châtelet", "Hôtel de Ville", "Rambuteau", "Arts et Métiers", "République", "Goncourt", "Belleville", "Pyrénées", "Jourdain", "Place des Fêtes", "Télégraphe", "Porte des Lilas", "Mairie des Lilas"],
        distances: Array(13).fill(0.5),
        trafficLights: [1, 2, 0, 1, 2, 1, 0, 1, 2, 0, 1]
    },
    "Rue des Filatiers": {
        stops: ["Porte de la Chapelle", "Marx Dormoy", "Marcadet-Poissonniers", "Jules Joffrin", "Lamarck-Caulaincourt", "Abbesses", "Pigalle", "Saint-Georges", "Notre-Dame-de-Lorette", "Trinité-d'Estienne d'Orves", "Saint-Lazare", "Madeleine", "Concorde", "Assemblée nationale", "Solférino", "Rue du Bac", "Sèvres-Babylone", "Rennes", "Notre-Dame-des-Champs", "Montparnasse-Bienvenüe", "Falguière", "Pasteur", "Volontaires", "Vaugirard", "Convention", "Porte de Versailles", "Corentin Celton", "Mairie d'Issy"],
        distances: Array(28).fill(0.5),
        trafficLights: [2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1]
    },
    "Rue Mage": {
        stops: ["Saint-Denis - Université", "Basilique de Saint-Denis", "Saint-Denis - Porte de Paris", "Carrefour Pleyel", "Mairie de Saint-Ouen", "Garibaldi", "Porte de Saint-Ouen", "Guy Môquet", "La Fourche", "Place de Clichy", "Liège", "Saint-Lazare", "Miromesnil", "Champs-Élysées-Clemenceau", "Invalides", "Varenne", "Saint-François-Xavier", "Duroc", "Montparnasse-Bienvenüe", "Gaîté", "Pernety", "Plaisance", "Porte de Vanves", "Malakoff - Plateau de Vanves", "Malakoff - Rue Etienne Dolet", "Châtillon-Montrouge"],
        distances: Array(26).fill(0.5),
        trafficLights: [2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1]
    },
    "Rue d'Espinasse": {
        stops: ["Saint-Lazare", "Madeleine", "Pyramides", "Châtelet", "Gare de Lyon", "Bercy", "Cour Saint-Émilion", "Bibliothèque François Mitterrand", "Olympiades"],
        distances: Array(9).fill(0.5),
        trafficLights: [2, 1, 0, 3, 1, 2, 1, 0]
    },
    "Rue des Gestes": {
        stops: ["Gare Saint-Denis", "Théâtre Gérard Philipe", "Marché de Saint-Denis", "Basilique de Saint-Denis", "Cimetière de Saint-Denis", "Hôpital Delafontaine", "Cosmonautes", "La Courneuve - Six Routes", "Hôtel de Ville de La Courneuve", "Stade Géo André", "Danton", "La Courneuve - 8 Mai 1945", "Maurice Lachâtre", "Drancy - Avenir", "Hôpital Avicenne", "Gaston Roulaud", "Escadrille Normandie-Niémen", "La Ferme", "Libération", "Hôtel de Ville de Bobigny", "Bobigny - Pablo Picasso", "Jean Rostand", "Auguste Delaune", "Pont de Bondy", "Petit Noisy", "Noisy-le-Sec"],
        distances: Array(26).fill(0.5),
        trafficLights: [2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1]
    },
    "Quai de la Daurade": {
        stops: ["La Défense", "Puteaux", "Belvédère", "Suresnes-Longchamp", "Les Coteaux", "Les Milons", "Parc de Saint-Cloud", "Musée de Sèvres", "Brimborion", "Meudon-sur-Seine", "Les Moulineaux", "Jacques-Henri Lartigue", "Issy - Val de Seine", "Balard", "Porte de Versailles"],
        distances: Array(15).fill(0.5),
        trafficLights: [2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3]
    },
    "Rue Bédelières": {
        stops: ["Pont du Garigliano", "Balard", "Desnouettes", "Porte de Versailles", "Georges Brassens", "Brancion", "Porte de Vanves", "Didot", "Jean Moulin", "Porte d'Orléans", "Montsouris", "Cité universitaire", "Stade Charléty", "Poterne des Peupliers", "Porte d'Italie", "Porte de Choisy", "Porte d'Ivry", "Bibliothèque François Mitterrand", "Porte de Charenton", "Porte Dorée", "Montempoivre", "Porte de Vincennes"],
        distances: Array(22).fill(0.5),
        trafficLights: [2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2]
    },
    "Rue Merlane": {
        stops: ["La Défense", "Charles de Gaulle-Étoile", "Auber", "Châtelet-Les Halles", "Gare de Lyon", "Nation"],
        distances: Array(6).fill(0.5),
        trafficLights: [2, 1, 0, 3, 1]
    },
    "Rue Vélane": {
        stops: ["Gare du Nord", "Châtelet-Les Halles", "Saint-Michel-Notre-Dame", "Luxembourg", "Port-Royal", "Denfert-Rochereau", "Cité Universitaire"],
        distances: Array(7).fill(0.5),
        trafficLights: [2, 1, 0, 3, 1, 2]
    },
    "Rue Étroite": {
        stops: ["Porte de Clichy", "Pereire", "Porte Maillot", "Avenue Foch", "Avenue Henri Martin", "La Muette", "Avenue du Président Kennedy", "Champ de Mars-Tour Eiffel", "Pont de l'Alma", "Invalides", "Musée d'Orsay", "Saint-Michel-Notre-Dame", "Gare d'Austerlitz", "Bibliothèque François Mitterrand"],
        distances: Array(14).fill(0.5),
        trafficLights: [2, 1, 0, 3, 1, 2, 1, 0, 3, 1, 2, 1, 0, 3]
    },
    "Rue des Tourneurs": {
        stops: ["Gare du Nord", "Châtelet-Les Halles", "Gare de Lyon"],
        distances: Array(3).fill(0.5),
        trafficLights: [2, 1]
    },
    "Rue de la Trinité": {
        stops: ["Saint-Lazare", "Gare du Nord"],
        distances: [0.5],
        trafficLights: [1]
    }
};

const velos = [
    { id: "velo1", position: "Porte d'Ivry", autonomie: 50, capacite: 200, charge: 0, distanceParcourue: 0, feuxRencontres: 0, saison: "été", tournee: [] },
];

console.log("streets_data.js is loaded and data is:", { ruesEtArrets, velos });