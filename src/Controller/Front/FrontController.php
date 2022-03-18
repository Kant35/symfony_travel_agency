<?php

namespace App\Controller\Front;

use App\Entity\Client;
use App\Entity\Produit;
use App\Form\ClientType;
use App\Form\ContactType;
use App\Entity\Destination;
use App\Entity\Reservation;
use App\Entity\Utilisateur;
use App\Form\ReservationType;
use App\Form\UtilisateurType;
use App\Form\ChangePasswordType;
use Symfony\Component\Mime\Address;
use App\Repository\ClientRepository;
use App\Repository\ProduitRepository;
use App\Repository\ConseillerRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DestinationRepository;
use App\Repository\ReservationRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class FrontController extends AbstractController
{
    // J'ai préparé 2 propriétés privées pour le manager et l'encoder du password
    // Cela me permet d'y avoir accès dans toutes mes routes de ce controller.
    // Je n'aurais donc plus besoin de les passer en dépendances dans les parenthèses de chaque méthode 
    private $manager;
    private $encoder;

    // Je prépare un constructeur pour remplir automatiquement mes 2 propriétés privées. 
    // A chaque fois qu'une route sera appellée le constructeur sera appelée et remplira le manager et l'encoder
    public function __construct(EntityManagerInterface $manager, UserPasswordHasherInterface $encoder)
    {
        $this->manager = $manager;
        $this->encoder = $encoder;
    }

    /**
     * @Route("/", name="app_front")
     * Page d'accueil du site. On récupère sur cette page tous les conseillers référent pour afficher leur informations.
     */
    public function index(ConseillerRepository $conseillerRepository): Response
    {
        // Récupéraction des conseillers référent. 
        // On utilise la méthode findBy() qui est une méthode prédéfinies dans tous les repository. Ici j'ai appelé le ConseillerRepository pour avoir accès à la récupération en BDD des conseillers. 
        // Le findBy() permet de donner des conditions au niveau de notre récupération. Pour rentrer ces conditions on doit les indiquer dans un tableau. Ici je souhaite récupérer les conseiller qui ont la propriété 'referent' avec la valeur 'true'.
        $conseillersReferent = $conseillerRepository->findBy(['referent' => true]);
        // Si on fait un dd($conseillerReferent) on voit bien que je ne récupère pas tous les conseillers et que tout ceux que j'ai récupéré ont bien le statut 'referent' à 'true'.
        return $this->render('front/index.html.twig', [
            // J'envoie ensuite les données à ma vue pour les récupérer et les traiter en twig.
            'conseillersReferent' => $conseillersReferent
        ]);
    }

    /**
     * @Route("/inscription", name="app_inscription")
     * Page d'inscription. Elle permet à un utilisateur de s'inscrire sur notre site en tant que 'Client'. 
     * Attention les 'Conseiller' ne peuvent pas s'inscrire depuis ce formulaire
     */
    public function inscription(Request $request)
    {
        // On crée un nouvel objet de la classe Client
        $client = new Client();

        // On crée notre formulaire qui sera de type 'ClientType' et on lui passe l'objet '$client' que l'on vient de créer pour le lier au formulaire. Cela permettra de remplir automatiquement l'objet avec les données du formulaire
        $form = $this->createForm(ClientType::class, $client);

        // On utilise handleRequest() pour remplir le formulaire automatiquement avec toutes les données.
        $form->handleRequest($request);

        // On vérifie si le formulaire est soumis et s'il répond à toutes les contraintes/conditions que l'on a indiqué dans notre entité. C'est ici que les 'Asserts' seront vérifié.
        if ($form->isSubmitted() && $form->isValid()) {
            // On hash le MDP du client.
            // On utilise donc '$this->encoder' que nous avons créé plutôt et remplis grâce au '__construct()'. Cet objet 'encoder' nous donne accès la méthode hashPassword() qui attend en paramètre un objet qui implémente la classe 'PasswordAuthenticatedUserInterface' (ici c'est notre objet $client) et le MDP à hasher.
            // Une fois hashé on le replace dans la propriété 'password' grâce au 'setPassword()'
            $client->setPassword($this->encoder->hashPassword($client, $client->getPassword()));

            // On fait un persist en un flush pour envoyer les données en BDD. 
            $this->manager->persist($client);
            $this->manager->flush();

            // Préparation d'un message flash à envoyer à la vue. 
            $this->addFlash('success', 'Vous êtes inscrit. Vous pouvez maintenant vous connecter.');

            // Redirection vers la page de connexion.
            return $this->redirectToRoute('app_login');
        }

        // On utilise ici renderForm() qui nous permet d'envoyer des formulaires sans créer de vue. 
        // Pour rappel nous pouvons également utiliser render() et passer le formulaire dans les paramètre en lui créant une vue manuellement avec la méthode '$form->createView()'
        return $this->renderForm("front/inscription.html.twig", [
            // On envoie le formulaire à notre twig.
            'formClient' => $form
        ]);
    }

    /**
     * @Route("/destination/{id}", name="app_destination")
     * Page d'affichage d'une destination. 
     * Dans la route on passe un paramètre dynamique qui est l'id de notre Destination.
     * En indiquant ensuite dans les parenthèses de la méthode (Destination $destination), Symfony va comprendre automatiquement que l'id récupéré dans la route correspond à la Destination passé dans la méthode. Il va donc récupérer en BDD une destination grâce à l'id et stocker les informations dans l'objet '$destination'
     */
    public function destination(Destination $destination)
    {
        // Si on fait un dd($destination); ici on peut voir que $destination a bien été rempli automatiquement par Symfony.
        // Attention du coup si l'id passé dans la route ne correspond à aucune destination en BDD vous aurez l'erreur : "@ParamConverter annotation"
        return $this->render("front/destination.html.twig", [
            // On envoie la destination à la vue
            'destination' => $destination
        ]);
    }
    /**
     * @Route("/produit/{id}", name="app_produit")
     * Page d'affichage d'un produit. 
     * Idem que pour la route 'destination' au niveau de l'id
     */
    public function produit(Produit $produit, ProduitRepository $produitRepository)
    {
        // Ici je prépare un tableau qui me permettra de récupérer tous mes produits qui ont la même destination que le produit récupéré grâce à l'id de la route. Cela me permettra d'avoir une section qui propose des produits similaires à celui consulté. 
        //Je l'initialise la variable avec un tableau vide.
        $produits=[];
        // Ensuite je fais une boucle sur toutes les destinations de mon produit que j'ai récupéré grâce à l'id de la route.
        // A chaque tour de boucle je récupère une destination
        foreach ($produit->getDestinations() as $destination) {
            // A chaque tour de boucle je récupère grâce à la requête findByDestination() (requête que j'ai créé dans ProduitRepository) les produits qui ont la même destination que le produit récupéré grâce à la route.
            // Ces produits que je récupère, je les stocks dans mon tableau $produits[].
            $produits += $produitRepository->findByDestination($destination);
            foreach ($produitRepository->findByDestination($destination) as $produit) {
                if(!in_array($produit, $produits)){
                    $produits[] = $produit;
                }
            }
        }
        return $this->render("front/produit.html.twig", [
            // Je renvoie à ma vue le produit demandé et les produits qui ont la même destination que le produit demandé.
            'produit' => $produit, 
            'produits' => $produits
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/reservation/{id}", name="app_reservation")
     * Page de réservation d'un produit
     * Cette page est réservé aux personnes qui ont le ROLE_USER
     * On récupère au niveau de cette page le produit grâce à l'id passé dans la route
     */
    public function reservation(Produit $produit, Request $request, ReservationRepository $reservationRepository, ClientRepository $client)
    {
        // Je stock l'utilisateur connecté dans une variable. Pour rappel on a toujours accès à $this->getUser() grâce à la 'classe AbstractController' qui est étendu par tous nos controller. Cette méthode permet de récupérer l'utilisateur connecté s'il y en a un.
        $user = $this->getUser();

        // Je vérifie si la personne connecté à le 'ROLE_ADMIN' ou le 'ROLE_CONSEILLER'.
        // Je ne souhaite pas que ces personnes puissent réserver un séjour. 
        if (in_array("ROLE_CONSEILLER", $user->getRoles()) || in_array("ROLE_ADMIN", $user->getRoles())) {
            // Je crée donc 2 variables que je passerais à la vue pour bloquer certaine fonctionnalités.
            $admin = true;
            $message = "Vous êtes connecté avec un compte Conseiller ou Admin. Vous ne pouvez pas faire de réservation.";
        }

        // Je crée un nouvel objet réservation
        $reservation = new Reservation();

        // Je crée mon formulaire de type 'ReservationType' et je lui passe l'objet '$reservation'
        $form = $this->createForm(ReservationType::class, $reservation);

        // Je remplis mon formulaire grâce à l'objet '$request'
        $form->handleRequest($request);

        // Je vérifie si le formulaire est soumis et validé
        if ($form->isSubmitted() && $form->isValid()) {
            // Je récupère ici tous les participants que j'ai inséré dans mon formulaire
            $participants = $form->getData()->getParticipants();

            // Je fais une boucle sur ce nouveau tableau pour récupérer à chaque fois 1 participant
            foreach ($participants as $participant) {
                // J'ajoute au participant l'objet réservation car 1 participant est lié à 1 réservation
                $participant->setReservation($reservation);
                // Ensuite j'ajoute ce participant à ma réservation. Car 1 réservation est lié à 1 ou plusieurs participants
                $reservation->addParticipant($participant);
            }

            // Création d'une référence de réservation unique. 
            $range = range('A', 'Z'); // Je définie un tableau de valeur allant de A à Z
            $index = array_rand($range); // Je sélectionne un index aléatoire dans le tableau.
            $reference = '#' . time() . $range[$index]; // Utilisation de la fonction time() qui me donnera la date et l'heure sur 10 chiffres. J'ajoute un '#' devant et je rajoute à la fin ma lettre aléatoire.
            // Cela me permet d'avoir une référence unique personnalisée pour chaque réservation. 

            // Ensuite j'hydrate mon objet réservation avec les valeurs qui ne sont pas données par le formulaire.
            $reservation->setReference($reference);
            $reservation->setDateReservation(new \DateTime());
            $reservation->setPrixTotal($produit->getPrix() * count($participants));
            $reservation->setProduit($produit);
            $reservation->setStatut("En Attente");
            $reservation->setClient($user);

            // La méthode 'add()' est créée par défaut dans le repository. Elle nous permet de faire le persist et le flush automatiquement.
            $reservationRepository->add($reservation);

            // On redirige ensuite vers la page d'accueil
            return $this->redirectToRoute('app_front');
        }

        // On passe à la vue plusieurs données qui nous permettrons de traiter le formulaire.
        return $this->renderForm("front/reservation.html.twig", [
            'formReservation' => $form,
            'produit' => $produit,
            'admin' => (isset($admin)) ? $admin : false,
            'message' => (isset($message)) ? $message : false
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/profil", name="app_profil")
     * Page de profil de l'utilisateur
     * Accès limité à l'utilisateur
     */
    public function profil(ReservationRepository $reservationRepository)
    {
        // On renvoie à la vue l'utilisateur qui est connecté.
        // On lui renvoie également dans différentes variables les réservations en fonction de leur statut.
        // Cela nous servira à afficher proprement les réservations faite par le client.
        return $this->render("front/profil.html.twig", [
            'utilisateur' => $this->getUser(),
            'reservationAttente' =>  $reservationRepository->findBy(['client'=> $this->getUser(), 'statut' => 'En Attente']),
            'reservationValide' =>  $reservationRepository->findBy(['client'=> $this->getUser(), 'statut' => 'Validé']),
            'reservationTermine' =>  $reservationRepository->findBy(['client'=> $this->getUser(), 'statut' => 'Terminé'])
        ]);
    }

    /**
     * @Route("/contact", name="app_contact")
     * Page de formulaire de contact.
     * Permet d'envoyer un mail.
     */
    public function contact(Request $request, MailerInterface $mailer)
    {
        // On crée un formulaire de type 'ContactType' on ne lui passe pas d'objet particulier car ContactTYpe n'est lié à aucune table en BDD et qu'il ne me servira pas à faire un enregistrement en BDD
        $form = $this->createForm(ContactType::class);
        // Je remplis mon formulaire
        $form->handleRequest($request);

        // Je vérifie s'il est soumis et validé
        if ($form->isSubmitted() && $form->isValid()) {
            
            // Je récupère les données du formulaire pour pouvoir les envoyer dans un template.
            $contact = $form->getData(); 

            // Pour créer un email nous avons besoin d'un objet de la classe 'TemplatedEmail()'.
            // Cet objet attend une configuration particulière.
            $email = (new TemplatedEmail())
            ->from(new Address('contact@gmail.com', 'Formulaire de Contact')) // définition de l'adresse de l'envoyeur
            ->to('quentin.vilfeu.dev@gmail.com') // définition de l'adresse de destination
            ->subject('Nouveau Message') // définition de l'objet du mail
            ->htmlTemplate('email/email.html.twig') // on lui précise que l'on souhaite utiliser le template 'email/email.html.twig'. Qui nous permettra de faire la mise en forme de l'email.
            ->context([ // context n'est pas obligatoire mais permet d'envoye au template des informations. 
                'contact' => $contact // Ici on souhaite lui envoyer les données du formulaire.
            ]);

            // Cette section est ommenté pour le moment car nous sommes dans un exercice et que la variable MAILER_DSN n'est pas configuré dans le fichier '.env'
            // Une fois que le mail est préparé nous pouvons l'envoyer grâce à la classe 'MailerInterface' de symfony et à sa méthode 'send()'
            // $mailer->send($email);

            // On envoie un message flash
            $this->addFlash('success', 'Votre message a bien été envoyé !');

            // On redirige vers l'accueil
            return $this->redirectToRoute('app_front');
        }

        return $this->renderForm('front/contact.html.twig', [
            'formContact' => $form
        ]);
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/modifier_mot_de_passe", name="app_update_pwd")
     * Page qui permet la modification du mot de passe de l'utilisateur
     */
    public function updatePassword(Request $request)
    {
        // On récupère l'utilisateur connecté.
        $user = $this->getUser();
        // Création et remplissage du formulaire. Formulaire qui n'est pas lié directement à une table en BDD donc pas d'objet à passer en paramètre
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        // On vérifie si le formulaire est soumis et validé
        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère l'ancien mot de passe.
            $old_pwd = $form->get('old_password')->getData();

            // On vérifie si l'ancien mot de passe est le bon. 
            // Une méthode dans l'encoder nous permet de faire cela. 
            // C'est la méthode isPasswordValid() elle attend en paramètre l'utilisateur connecté et le mot de passe à vérifier.
            // Si le mot de passe est correct on rentre dans la condition
            if ( $this->encoder->isPasswordValid($user, $old_pwd) ) {
                // On stock le nouveau mot de passe dans une variable
                $new_pwd = $form->get('new_password')->getData();
                // On hash ce nouveau mot de passe
                $password = $this->encoder->hashPassword($user, $new_pwd);

                // On redéfinie le mot de passe dans l'objet utilisateur grâce au setPassword()
                $user->setPassword($password);

                // On envoie en BDD les modifications.
                $this->manager->persist($user);
                $this->manager->flush();

                $this->addFlash('success', 'Votre mot de passe a bien été mis à jour');
                return $this->redirectToRoute('app_profil');
            } else {
                $this->addFlash('warning', 'Mot de Passe éronné');
            }
        }

        return $this->renderForm("front/update_password.html.twig", [
            'form' => $form
        ]);
    }
}
