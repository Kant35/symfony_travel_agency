$(document).ready( () => {

    // Récupérer le conteneur pour le formulaire des participants
    let containerParticipants = $('.container-participants');

    // Bouton pour ajouter un nouveau Participant
    let addNewParticipant = $('<div class="text-center mt-4 col-12"><a href="#" class="text-dark text-decoration-none"><i class="fas fa-plus fs-2"></i></a><div>');

    // Ajouter le Bouton 'nouveau participant' au conteneur.
    containerParticipants.before(addNewParticipant);

    // Permet de numéroté les panels pour qu'ils soit tous diférrents 
    containerParticipants.data('index', containerParticipants.find('.card-participant').length)

    // Ajouter un bouton supprimer pour chaque nouvel Card Participant
    containerParticipants.find('.card-participant').each(function () {  
        addRemoveButton($(this));
    })
    
    // Récupération du prix par personne 
    let prixPersonne = $($('.totalPrice')[0]).text();

    // On capte le click du bouton 'Ajouter un participant'
    addNewParticipant.click(function (e) {
        // On stoppe l'action par défaut
        e.preventDefault()
        // Puis on appel la fonction pour créer un nouveau formulaire
        addNewForm();

        // Ajout du prix pour chaque personne
        $($('.totalPrice')[0]).text(prixPersonne * containerParticipants.find('.card-participant').length);
    })

    // Fonction qui permet d'ajouter un Nouveau Formulaire d'ajout de Participant au DOM
    function addNewForm() {
        // Récupération des informations du formulaire via le prototype
        let prototype = containerParticipants.data('prototype');
        
        // Création d'un index pour que chaque card soit unique
        let index = containerParticipants.data('index');

        // Création du formulaire grâce au prototype récupéré plus haut
        let newForm = prototype;
        newForm = newForm.replace(/__name__/g, index); // On définit l'index de la card 

        // Création de la Card
        let card = $(`<div class="card-participant col-3 mx-auto mt-1"><div class="card-header text-center">Participant ${index+1}</div></div>`);

        containerParticipants.data('index', index+1); // Incrémentation de l'index pour la prochaine card à créer.

        // Création du body-card et ajouter le formulaire dedans
        let panelBody = $('<div class="card-body"></div>').append(newForm);

        // Ajout du body au panel
        card.append(panelBody);

        // On ajoute le bouton supprimer à la card.
        addRemoveButton(card)

        // Ajouter le nouveau Panel au addNewParticipant pour que le AddNewParticipant soit toujours en dernier
        containerParticipants.append(card);
    }

    // Fonction qui permet d'ajouter un bouton supprimer au panel indiqué dans les paramètres
    function addRemoveButton(panel) {
        // création du bouton Remove 
        let removeButton = $('<div class="d-flex justify-content-center"><a href="#" class="btn btn-danger">Supprimer</a></div>');

        // Création du footer pour le panel et ajout du bouton remove
        let panelFooter = $('<div class="card-footer"></div>').append(removeButton)

        // Gestion du click
        removeButton.click(function (e) {
            e.preventDefault();
            $(e.target).parents('.card-participant').slideUp(500, function () {
                $(this).remove()
            });

            let index = containerParticipants.data('index');
            containerParticipants.data('index', index-1);

            // Mise à jour du prix
            $($('.totalPrice')[0]).text($($('.totalPrice')[0]).text() - prixPersonne);

        })

        // Ajouter le footer au panel
        panel.append(panelFooter);
    }






});
