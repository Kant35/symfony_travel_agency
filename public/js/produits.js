$(document).ready( () => {

    // Récupérer le conteneur pour le formulaire des etapes
    let containerEtapes = $('.container-etapes');

    // Bouton pour ajouter une Nouvelle Etape
    let addNewEtape = $('<a href="#" class="add-participant btn btn-secondary m-5">Ajouter une nouvelle etape</a>');

    // Ajouter le Bouton 'nouvelle etape' au conteneur.
    containerEtapes.append(addNewEtape);

    // Permet de numéroté les panels pour qu'ils soit tous diférrents 
    containerEtapes.data('index', containerEtapes.find('.card-etape').length)

    // Ajouter un bouton supprimer pour chaque nouvel Card Etape
    containerEtapes.find('.card-etape').each(function () {  
        addRemoveButton($(this));
    })

    // On capte le click du bouton 'Ajouter une Etape'
    addNewEtape.click(function (e) {
        // On stoppe l'action par défaut
        e.preventDefault()
        // Puis on appel la fonction pour créer un nouveau formulaire
        addNewForm();
    })

    // Fonction qui permet d'ajouter un Nouveau Formulaire d'ajout d'Etape au DOM
    function addNewForm() {
        // Récupération des informations du formulaire via le prototype
        let prototype = containerEtapes.data('prototype');
        
        // Création d'un index pour que chaque card soit unique
        let index = containerEtapes.data('index');

        // Création du formulaire grâce au prototype récupéré plus haut
        let newForm = prototype;
        newForm = newForm.replace(/__name__/g, index); // On définit l'index de la card 

        // Création de la Card
        let card = $('<div class="card-etape col"><div class="card-header bg-primary"></div></div>');

        containerEtapes.data('index', index+1); // Incrémentation de l'index pour la prochaine card à créer.

        // Création du body-card et ajouter le formulaire dedans
        let panelBody = $('<div class="card-body"></div>').append(newForm);

        // Ajout du body au panel
        card.append(panelBody);

        // On ajoute le bouton supprimer à la card.
        addRemoveButton(card)

        // Ajouter le nouveau Panel au addNewEtape pour que le AddNewEtape soit toujours en dernier
        addNewEtape.before(card);
    }

    // Fonction qui permet d'ajouter un bouton supprimer au panel indiqué dans les paramètres
    function addRemoveButton(panel) {
        // création du bouton Remove 
        let removeButton = $('<a href="#" class="btn btn-danger">Supprimer</a>');

        // Création du footer pour le panel et ajout du bouton remove
        let panelFooter = $('<div class="card-footer"></div>').append(removeButton)

        // Gestion du click
        removeButton.click(function (e) {
            e.preventDefault();
            $(e.target).parents('.card-etape').slideUp(500, function () {
                $(this).remove()
            });
        })

        // Ajouter le footer au panel
        panel.append(panelFooter);
    }
});
