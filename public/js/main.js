$(document).ready( () => {
    /**
     * Annimation des panels dans la Page Compte
     */
    let boutons = $('.titre-reservation i');

    $.each(boutons, function (index, value) { 
        let toggle = true;
        $(value).click(function () {
            toggle = !toggle;
            let divTitre = $(this).parent();
            let divBookings = divTitre[0].nextElementSibling;
            // console.log(divBookings)
            if (toggle) {
                $(divBookings).removeClass("d-block");
                $(divBookings).addClass("d-none");
                $(value).css("transform", "rotate(180deg)")
        
            } else {
                $(divBookings).removeClass("d-none");
                $(divBookings).addClass("d-block");
                $(value).css("transform", "rotate(0deg)")

            }
        })
    });

    /**
     * 
     * MENU GENERAL
     * 
     */
    let interupteur = false;

    $('#icon_hamburger_menu').click(function(){
        interupteur = !interupteur;
        if (interupteur) {
            $(".bouton_menu").addClass('actif');
            $(".menu").removeClass('d-none')
            $("#menu").removeClass('d-none')
        } else {
            $(".bouton_menu").removeClass('actif')
            $(".menu").addClass('d-none')
            $("#menu").addClass('d-none')
        }
    });

});
