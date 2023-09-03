<?php

require_once 'models/client/tissu/tissuClientManager.php';
require_once 'models/client/commande/commandeClientManager.php';
require_once 'models/client/clientManager.php';
require_once 'models/client/paiement/paiementClientManager.php';
require_once 'models/modele/modeleManager.php';
require_once 'models/modele_composition/modeleCompManager.php';
require_once 'models/programme/programmeManager.php';
require_once 'models/production/productionManager.php';
require_once 'models/charge/chargeManager.php';
require_once 'models/audit/auditManager.php';
require_once 'models/user/userManager.php';
require_once 'models/agence/agenceManager.php';
require_once 'models/rdv/rdvCommandeManager.php';

class commandeController
{
    private $tissuManager;
    private $clientManager;
    private $modeleManager;
    private $programmeManager;
    private $commandeManager;
    private $commandeCompoManager;
    private $paiementManager;
    private $auditManager;
    private $productionManager;
    private $chargeManager;
    private $userManager;
    private $agenceManager;
    private $rdvManager;

    public function __construct()
    {
        $this->tissuManager =  new tissuClientManager();
        $this->tissuManager->loadTissu();

        $this->clientManager =  new clientManager();
        $this->clientManager->loadClient();

        $this->modeleManager =  new modeleManager();
        $this->modeleManager->loadModele();

        $this->modeleCompoManager =  new modeleCompManager();
        $this->modeleCompoManager->loadModeleComp();

        $this->programmeManager =  new programmeManager();
        $this->programmeManager->loadProgramme();

        $this->commandeManager =  new commandeClientManager();
        $this->commandeManager->loadCommande();

        $this->paiementManager =  new paiementClientManager();
        $this->paiementManager->loadPaiement();

        $this->productionManager =  new productionManager();
        $this->productionManager->loadProduction();

        $this->chargeManager =  new chargeManager();
        $this->chargeManager->loadCharge();

        $this->userManager =  new userManager();
        $this->userManager->loadUser();

        $this->agenceManager =  new agenceManager();
        $this->agenceManager->loadAgence();

        $this->rdvManager =  new rdvCommandeManager();
        $this->rdvManager->loadRdv();

        $this->auditManager =  new auditManager();
    }

    public function addCommande($id)
    {
        $client = $this->clientManager->getClientById($id);
        $commandes_data = $this->commandeManager->getCommandesClient($client);
        $commandes = [];
        if(!empty($commandes_data)){
            foreach ($commandes_data as $cmd){
                if($cmd->getStatutCommande() == 0 ){
                    array_push($commandes, $cmd);
                }
            }
        }

        $tissus_data = $this->tissuManager->getTissusClient($client);
        $tissus = [];
        if (!empty($tissus_data)){
            foreach ($tissus_data as $tissu){
                if ($tissu->getStatutTissu() == 0){
                    array_push($tissus,$tissu);
                }
            }
        }

        $modeles = $this->modeleManager->getModeles();
        $modelesComposition = $this->modeleCompoManager->getModelesComp();

        //Numero commande
        $debut = DateTime::createFromFormat('Y-m-d', date("Y") . '-01-01')->format('Y-m-d');
        $fin = DateTime::createFromFormat('Y-m-d', date("Y") . '-12-31')->format('Y-m-d');
        $commande = $this->commandeManager->getCommandeFromYear($debut,$fin);
        if ( !empty($commande[0])){
            $num = explode('-',$commande[0]['desc_commande']);
            $numero = 'CMDN°-'.($num[1]+1).'-'.date('Y');
        }else{
            $numero = 'CMDN°-'.'1'.'-'.date('Y');
        }
        require "views/client/commande/add.php";
    }


    public function facture_article($id)
    {
        $commande = $this->commandeManager->getCommandeById($id);
        $client = $this->clientManager->getClientById($commande->getClient());
        $programmes = $this->programmeManager->getProgrammesCommandeAndStatut($commande->getIdCommande(),1);
        $programmesCompo = $this->programmeManager->getProgrammesCommandeAndStatutComposition($commande->getIdCommande(),1);

        $data = [];
        foreach ($programmes as $item){
            $array = array(
                'modele'=> $item['nom_modele'],
                'tissu'=> $item['nom_tissu'],
                'qte'=> $item['quantite_cmt'],
            );
            array_push($data, $array);
        }
        foreach ($programmesCompo as $item){
            $array = array(
                'modele'=> $item['nom_mod_comp'],
                'tissu'=> $item['nom_tissu'],
                'qte'=> $item['quantite_cmt'],
            );
            array_push($data, $array);
        }

        $programmesCours = $this->programmeManager->getProgrammesCommandeAndStatut($commande->getIdCommande(),0);
        $programmesCoursCompo = $this->programmeManager->getProgrammesCommandeAndStatutComposition($commande->getIdCommande(),0);
        $dataEnCours = [];
        foreach ($programmesCours as $item){
            $array = array(
                'modele'=> $item['nom_modele'],
                'tissu'=> $item['nom_tissu'],
                'qte'=> $item['quantite_cmt'],
            );
            array_push($dataEnCours, $array);
        }
        foreach ($programmesCoursCompo as $item){
            $array = array(
                'modele'=> $item['nom_mod_comp'],
                'tissu'=> $item['nom_tissu'],
                'qte'=> $item['quantite_cmt'],
            );
            array_push($dataEnCours, $array);
        }
        $user = $this->userManager->getUserById($_SESSION['id']);
        $agence = $this->agenceManager->getAgenceById($_SESSION['agence']);
        require "views/client/commande/pdf/article.php";
    }

    public function commande_compo_pdf($id)
    {
        $commande = $this->commandeManager->getCommandeById($id);
        $client = $this->clientManager->getClientById($commande->getClient());
        $programmes =$this->programmeManager->getProgrammesCommande($commande);
        $data = [];
        foreach ($programmes as $programme){
            //Modele simple & composition
            $simpleModele = $this->modeleManager->getModeleById($programme->getModele());
            $compoModele = $this->modeleCompoManager->getModeleCompById($programme->getModele());

            $item = array(
                'nom_modele'=>(!empty( $simpleModele))? $simpleModele->getNomModele():$compoModele->getNomModComp(),
                'qte'=>$programme->getQuantiteCmt()
            );

            array_push($data,$item);
        }
        $user = $this->userManager->getUserById($_SESSION['id']);
        $agence = $this->agenceManager->getAgenceById($_SESSION['agence']);
        require "views/client/commande/pdf/composition.php";
    }

    public function commande_report_pdf($id)
    {
        $commande = $this->commandeManager->getCommandeById($id);
        $client = $this->clientManager->getClientById($commande->getClient());

        $rdv = $this->rdvManager->getRdvsCommande($commande);

        $user = $this->userManager->getUserById($_SESSION['id']);
        $agence = $this->agenceManager->getAgenceById($_SESSION['agence']);
        require "views/client/commande/pdf/report.php";
//        test git
    }

    public function commande_satisfaction_pdf($id)
    {
        $commande = $this->commandeManager->getCommandeById($id);
        $client = $this->clientManager->getClientById($commande->getClient());

        $rdv = $this->rdvManager->getRdvsCommande($commande);

        $user = $this->userManager->getUserById($_SESSION['id']);
        $agence = $this->agenceManager->getAgenceById($_SESSION['agence']);

        require "views/client/commande/pdf/satisfaction.php";
    }

    public function affectCommande($id)
    {
        $commande = $this->commandeManager->getCommandeById($id);
        $client = $this->clientManager->getClientById($commande->getClient());
        $tissus_data = $this->tissuManager->getTissusClient($client);
        $tissus = [];
        if (!empty($tissus_data)){
            foreach ($tissus_data as $tissu){
                if ($tissu->getStatutTissu() == 0){
                    array_push($tissus,$tissu);
                }
            }
        }
        $modeles = $this->modeleManager->getModeles();
        $compoModeles = $this->modeleCompoManager->getModelesComp();
        require "views/client/commande/affect.php";

    }

    public function addSaveCommande()
    {
        //Extract numero mesure of client
        $creat = $this->fieldValidation($_POST['creat']);
        $year = explode('-',$creat);
        $debut = DateTime::createFromFormat('Y-m-d', $year[0]. '-01-01')->format('Y-m-d');
        $fin = DateTime::createFromFormat('Y-m-d', $year[0] . '-12-31')->format('Y-m-d');
        $commandes = $this->commandeManager->getCommandeFromYear($debut,$fin);
        if (!empty($commandes)){
            $num = explode('-',$commandes[0]['desc_commande']);
            $numero = 'CMDN°-'.($num[1]+1).'-'.$year[0];
        }else{
            $numero = 'CMDN°-'.'1'.'-'.$year[0];
        }

        $data = array(
            'desc' => $numero,
            'rdv' => $this->fieldValidation($_POST['rdv']),
            'client' => $_POST['client'],
            'creat' => $creat,
            'mod' => date("y-m-d"),
            'statut' => 0
        );
        $this->commandeManager->addCommandeBd($data);
        $audit = array(
            'desc'=> "Ajout d'une commande: ".$data['desc']." date de rdv ". date("d-m-Y", strtotime($data['rdv'])),
            'action'=>'Ajout',
            'creat'=>date("Y-m-d"),
            'user'=>$_SESSION['id'],
        );
        $this->auditManager->addAuditBd($audit);
    }

    public function addSaveProgramme($id)
    {
        $client = $this->clientManager->getClientById($id);
        if(!empty($_POST['modele']) && !empty($_POST['tissu'][0]) && !empty($_POST['quantite']) ){
            for($i=0;$i < count($_POST['modele']);$i++ ){

                $modele = $this->modeleManager->getModeleById($_POST['modele'][$i]);
                $modeleComposition = $this->modeleCompoManager->getModeleCompById($_POST['modele'][$i]);
                if($modele){
                    $nom_modele = $modele->getNomModele();
                    $prix_modele = $modele->getPrixModele();

                }elseif($modeleComposition){

                    $nom_modele = $modeleComposition->getNomModComp();
                    $prix_modele = $modeleComposition->getPrixModComp();
                }

                $prix = ($prix_modele * $this->fieldValidation($_POST['quantite'][$i]));
                $remise = (!empty($this->fieldValidation($_POST['remise'][$i])))? ($this->fieldValidation($_POST['remise'][$i]) * $this->fieldValidation($_POST['quantite'][$i])) : 0 ;

                //Add Programme
                $data = array(
                    'commande' => $this->fieldValidation($_POST['commande']),
                    'modele' => $this->fieldValidation($_POST['modele'][$i]),
                    'tissu' => $this->fieldValidation($_POST['tissu'][$i]),
                    'quantite' => $this->fieldValidation($_POST['quantite'][$i]),
                    'remise' => $remise,
                    'prix' => $prix,
                    'creat' => date("Y-m-d"),
                    'mod' => date("Y-m-d"),
                    'statut' =>0,
                );
                $this->programmeManager->addProgrammeBd($data);

                //Update statut tissu
                $data_tissu = array(
                    'statut' => 1,
                    'id' => $data['tissu'],
                    'mod' => date("Y-m-d"),
                );
                $this->tissuManager->updateTissuStatutBd($data_tissu);

                $tissu =$this->tissuManager->getTissuById($data['tissu']);
                $commande =$this->commandeManager->getCommandeById($data['commande']);
                //Add audit
                $audit = array(
                    'desc'=> "Affection du tissu ".$tissu->getNomTissu()." ".$tissu->getDescTissu() ." au modèle : ".$nom_modele." dans la commande ".$commande->getDescCommande(),
                    'action'=>'Ajout',
                    'creat'=>date("Y-m-d"),
                    'user'=>$_SESSION['id'],
                );
                $this->auditManager->addAuditBd($audit);
            }
        }
        else{
            header('location: '.URL.'client/info/'.$client->getIdClient().'/commande_ajouter');
        }
        header('location: '.URL.'client/info/'.$client->getIdClient());
    }

    public function updateCommande($id)
    {
        $commande =  $this->commandeManager->getCommandeById($id);
        $client = $this->clientManager->getClientById($commande->getClient());
        $programmes = $this->programmeManager->getProgrammesCommande($commande);
        $modeles = $this->modeleManager->getModeles();
        $modelesComp = $this->modeleCompoManager->getModelesComp();
        $tissus = $this->tissuManager->getTissusClient($client);
        $data = [];
        foreach ($programmes as $programme){
            $simpleModele =  $this->modeleManager->getModeleById($programme->getModele());
            $compoModele =  $this->modeleCompoManager->getModeleCompById($programme->getModele());
            if(!empty($simpleModele)){
                $idModele = $simpleModele->getIdModele();
                $nomModele = $simpleModele->getNomModele();
                $prixModele = $simpleModele->getPrixModele();
            }
            if(!empty($compoModele)){
                $idModele = $compoModele->getIdModComp();
                $nomModele = $compoModele->getNomModComp();
                $prixModele = $compoModele->getPrixModComp();
            }
            $tissuProg = $this->tissuManager->getTissuById($programme->getTissu());

            $item = array(
                'id'=>$programme->getIdCmt(),
                'modele_id'=>$idModele,
                'modele_nom'=>$nomModele,
                'modele_prix'=>$prixModele,
                'statut'=>$programme->getStatutCmt(),
                'tissu_id'=>$tissuProg->getIdTissu(),
                'tissu'=>$tissuProg->getNomTissu(),
                'tissu_desc'=>$tissuProg->getDescTissu(),
                'prix'=>$programme->getPrixCmt(),
                'qte'=>$programme->getQuantiteCmt(),
                'remise'=>$programme->getRemiseCmt()
            );
            array_push($data,$item);
        }
   
        require "views/client/commande/update.php";
    }

    public function updateSaveCommande($id)
    {
        $data = array(
            'desc'=>$this->fieldValidation($_POST['desc']),
            'rdv'=>$this->fieldValidation($_POST['rdv']),
            'statut'=>$this->fieldValidation($_POST['statut']),
            'id'=>$this->fieldValidation($_POST['commande']),
            'mod'=>date("Y-m-d"),
            'creat'=>$this->fieldValidation($_POST['creat'])
        );

        $this->commandeManager->updateCommandeBD($data);
        $audit = array(
            'desc'=> "Modification de la commande: ".$data['desc']." date de rdv ". date("d-m-Y", strtotime($data['rdv'])),
            'action'=>'Modification',
            'creat'=>date("Y-m-d"),
            'user'=>$_SESSION['id'],
        );
        $this->auditManager->addAuditBd($audit);
    }
    public function updateSaveProgramme($id)
    {

        $client = $this->clientManager->getClientById($this->commandeManager->getCommandeById($id)->getClient());
        if(!empty($_POST['modele']) && !empty($_POST['tissu']) && !empty($_POST['quantite']) && !empty($_POST['prix']) && !empty($_POST['statut']) ){
            for($i=0;$i < count($_POST['id_cmt']);$i++ ){

                $prog = $this->programmeManager->getProgrammeById($this->fieldValidation($_POST['id_cmt'][$i]));
                //modele simple & composition
                $simpleModele = $this->modeleManager->getModeleById($this->fieldValidation($_POST['modele'][$i]));
                $compoModele = $this->modeleCompoManager->getModeleCompById($this->fieldValidation($_POST['modele'][$i]));
                if (!empty($simpleModele))
                {
                    $prix = $simpleModele->getPrixModele()*$this->fieldValidation($_POST['quantite'][$i]);
                    $nomModele = $simpleModele->getNomModele();
                }
                if ( !empty($compoModele))
                {
                    $prix = $compoModele->getPrixModComp()*$this->fieldValidation($_POST['quantite'][$i]);
                    $nomModele = $compoModele->getNomModComp();
                }
                $remise = ($this->fieldValidation($_POST['remise'][$i]) == $prog->getRemiseCmt())? $prog->getRemiseCmt() : ($this->fieldValidation($_POST['remise'][$i]) * $this->fieldValidation($_POST['quantite'][$i])) ;

                $data = array(
                    'id' => $this->fieldValidation($_POST['id_cmt'][$i]),
                    'modele' => $this->fieldValidation($_POST['modele'][$i]),
                    'tissu' => $this->fieldValidation($_POST['tissu'][$i]),
                    'qte' => $this->fieldValidation($_POST['quantite'][$i]),
                    'remise' => $remise,
                    'prix' =>$prix,
                    'statut' => $this->fieldValidation($_POST['statut'][$i]),
                    'commande' => $id,
                    'mod' => date("Y-m-d"),
                );
                $this->programmeManager->updateProgrammeBD($data);
                $tissu =$this->tissuManager->getTissuById($data['tissu']);
                $commande =$this->commandeManager->getCommandeById($data['commande']);

                $audit = array(
                    'desc'=> "Modification de l'article avec le tissu ".$tissu->getNomTissu()." ".$tissu->getDescTissu() ." au modèle : ".$nomModele." dans la commande ".$commande->getDescCommande(),
                    'action'=>'Modification',
                    'creat'=>date("Y-m-d"),
                    'user'=>$_SESSION['id'],
                );
                $this->auditManager->addAuditBd($audit);
            }
        }
        header('location: '.URL.'client/info/'.$client->getIdClient());
    }

    public function displayCommande($id)
    {
        $commande =  $this->commandeManager->getCommandeById($id);
        $client = $this->clientManager->getClientById($commande->getClient());
        $programmes =$this->programmeManager->getProgrammesCommande($commande);
        $data = [];
        foreach ($programmes as $programme){
            //Modele simple & composition
            $simpleModele = $this->modeleManager->getModeleById($programme->getModele());
            $compoModele = $this->modeleCompoManager->getModeleCompById($programme->getModele());

            $tissuProg = $this->tissuManager->getTissuById($programme->getTissu());
            $item = array(
                'id'=>$programme->getIdCmt(),
                'modele_id'=> (!empty( $simpleModele))? $simpleModele->getIdModele():$compoModele->getIdModComp(),
                'modele_nom'=>(!empty( $simpleModele))? $simpleModele->getNomModele():$compoModele->getNomModComp(),
                'modele_prix'=>(!empty( $simpleModele))? $simpleModele->getPrixModele():$compoModele->getPrixModComp(),
                'statut'=>$programme->getStatutCmt(),
                'tissu_id'=>$tissuProg->getIdTissu(),
                'tissu'=>$tissuProg->getNomTissu(),
                'tissu_desc'=>$tissuProg->getDescTissu(),
                'prix'=>$programme->getPrixCmt(),
                'qte'=>$programme->getQuantiteCmt()
            );

            array_push($data,$item);
        }
 
        require "views/client/commande/detail.php";
    }

    public function deleteCommande($id)
    {
        $commande = $this->commandeManager->getCommandeById($id);
        $client = $this->clientManager->getClientById($commande->getClient());
        //Delete Programmes
        $programmes =  $this->programmeManager->getProgrammesCommande($commande);

        foreach ($programmes as $programme){
            $tissu = $this->tissuManager->getTissuById($programme->getTissu());
            $item = array(
                'statut'=>0,
                'mod'=>date("Y-m-d"),
                'id'=>$tissu->getIdTissu(),
            );
            $this->tissuManager->updateTissuStatutBd($item);
            $this->programmeManager->deleteProgrammeBD($programme->getIdCmt());
        }
        //Delete Paiements
        $paiements = $this->paiementManager->getPaiementsCommande($commande);
        foreach ($paiements as $paiement){
            $this->paiementManager->deletePaiementBD($paiement->getIdPaie());
        }

        $audit = array(
            'desc'=> "Suppression de la commande: ".$commande->getDescCommande()." date de rdv ". date("d-m-Y", strtotime($commande->getRdvCommande())),
            'action'=>'Modification',
            'creat'=>date("Y-m-d"),
            'user'=>$_SESSION['id'],
        );
        $this->auditManager->addAuditBd($audit);
        $this->commandeManager->deleteCommandeBD($id);
        header('location: '.URL.'client/info/'.$client->getIdClient());
    }

    public function deleteProgramme($client,$commande,$id)
    {
        $this->programmeManager->deleteProgrammeBD($id);
        header('location: '.URL.'client/info/'.$client.'/commande/'.$commande);
    }

    public function pdf($id)
    {
        $user = $this->userManager->getUserById($_SESSION['id']);
        $agence = $this->agenceManager->getAgenceById($_SESSION['agence']);
        $commande = $this->commandeManager->getCommandeById($id);
        $client = $this->clientManager->getClientById($commande->getClient());
        $commandeDetail = $this->commandeManager->loadCommandeDetail($id);
        $commandeDetailComposition = $this->commandeManager->loadCommandeDetailComposition($id);

        $data_cmd = [];
        $remise_existe = false;
        if (!empty($commandeDetail)){
            foreach ($commandeDetail as $cmd){
                $data_charge = [];
                $somme_charge = 0;
                $prod = $this->productionManager->getProductionByCmt($cmd['id_cmt']);
                if(!empty($prod)){
                    foreach ($prod as $p){
                        $charges = $this->chargeManager->getChargesRessource($p->getIdProd());
                        //Get data charge
                        if(!empty($charges[0]) ){
                            foreach ($charges as $charge){
                                $somme_charge += $charge['prix_charge'];
                                $item = array(
                                    'ressource'=>$charge['nom_res'],
                                    'prix'=>$charge['prix_charge'],
                                );
                                array_push($data_charge, $item);
                            }
                        }
                    }
                }
                //Get data commande and charge
                $item_cmd = array(
                    'modele'=>$cmd['nom_modele'],
                    'tissu'=>$cmd['nom_tissu'],
                    'tissu_qte'=>$cmd['quantite_tissu'],
                    'tissu_prix'=>$cmd['prix_tissu'],
                    'qte'=>$cmd['quantite_cmt'],
                    'prix'=>$cmd['prix_modele'],
                    'remise'=>$cmd['remise_cmt'],
                    'charge'=> $data_charge,
                    'somme_charge'=> $somme_charge,
                );
                array_push($data_cmd, $item_cmd);

                if ($cmd['remise_cmt']!=0){
                    $remise_existe = true;
                }
            }
        }
        //Modele composition
        if (!empty($commandeDetailComposition)){
            foreach ($commandeDetailComposition as $cmd){
                $data_charge = [];
                $somme_charge = 0;
                $prod = $this->productionManager->getProductionByCmt($cmd['id_cmt']);
                if(!empty($prod)){
                    foreach ($prod as $p){
                        $charges = $this->chargeManager->getChargesRessource($p->getIdProd());
                        //Get data charge
                        if(!empty($charges[0]) ){
                            foreach ($charges as $charge){
                                $somme_charge += $charge['prix_charge'];
                                $item = array(
                                    'ressource'=>$charge['nom_res'],
                                    'prix'=>$charge['prix_charge'],
                                );
                                array_push($data_charge, $item);
                            }
                        }
                    }
                }
                //Get data commande and charge
                $item_cmd = array(
                    'modele'=>$cmd['nom_mod_comp'],
                    'tissu'=>$cmd['nom_tissu'],
                    'tissu_qte'=>$cmd['quantite_tissu'],
                    'tissu_prix'=>$cmd['prix_tissu'],
                    'qte'=>$cmd['quantite_cmt'],
                    'prix'=>$cmd['prix_mod_comp'],
                    'remise'=>$cmd['remise_cmt'],
                    'charge'=> $data_charge,
                    'somme_charge'=> $somme_charge,
                );
                array_push($data_cmd, $item_cmd);

                if ($cmd['remise_cmt']!=0){
                    $remise_existe = true;
                }
            }
        }

        require 'views/client/commande/pdf/facture.php';
    }

    public function fieldValidation($param)
    {
        return htmlspecialchars(strip_tags($param));

    }
}