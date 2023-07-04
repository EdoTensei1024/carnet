<?php ob_start(); ?>

<div class="main-body">
    <div class="page-wrapper">
        <!-- Page header start -->
        <div class="page-header">
            <div class="page-header-title">
                <h4>Modifier la ressource</h4>

            </div>
            <div class="page-header-breadcrumb">
                <ul class="breadcrumb-title">
                    <li class="breadcrumb-item">
                        <a href="<?= URL ?>accueil">
                            <i class="icofont icofont-home"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item"><a href="<?= URL ?>ressource">Ressource</a>
                    </li>
                    <li class="breadcrumb-item"><a href="<?= URL ?>ressource/modifier">MOdifier</a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- Page header end -->
        <!-- Page body start -->
        <div class="page-body">
            <div class="row">
                <div class="col-sm-12">
                    <!-- Basic Form Inputs card start -->
                    <div class="card">
                        <div class="card-block">
                            <h4 class="sub-title"></h4>
                            <form method="post" action="<?= URL ?>ressource/mv/<?= $ressource->getIdRes()  ?>" enctype="multipart/form-data">
                                <div class="form-group row">
                                    <label class="form-label">Nom</label>
                                    <input type="text" name="nom" value="<?= $ressource->getNomRes() ?>"  class="form-control">
                                </div>
                                <div class="form-group row">
                                    <label class="form-label">Description</label>
                                    <input type="text"  name="desc" value="<?= $ressource->getDescRes() ?>"  class="form-control" >
                                </div>
                                <?php if ($_SESSION['agence_role'] =='Principale' && $_SESSION['role']=='admin'):?>
                                    <div class="form-group row">
                                        <label class="form-label">Agence</label>
                                        <select  id="agence" class="form-control" >
                                            <option value="">------------------------------</option>
                                            <?php foreach ($agences as $agence): ?>
                                                <?php if ($agence->getIdAgence() == $ressource->getAgence()):?>
                                                    <option value="<?= $agence->getIdAgence() ?>" selected>
                                                        <?= $agence->getNomAgence().' <=> '.$agence->getAdresseAgence().' <=> '.$agence->getContactAgence() ?>
                                                    </option>
                                                <?php endif;?>
                                                <option value="<?= $agence->getIdAgence() ?>">
                                                    <?= $agence->getNomAgence().' <=> '.$agence->getAdresseAgence().' <=> '.$agence->getContactAgence() ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php else:?>
                                    <input type="hidden" value="<?=$_SESSION['agence'] ?>" id="agence">
                                <?php endif;?>
                                <div class="form-group row">
                                    <label class="form-label">Image</label>
                                    <input type="file"  name="image" class="form-control">
                                    <input type="hidden"  name="res" value="<?= $ressource->getIdRes() ?>" class="form-control">
                                </div>
                                <div class="form-group row">
                                    <label class="form-label">Image</label>
                                    <img src="<?= URL ?>/public/image/ressource/<?= $ressource->getImageRes() ?>" alt="" width="200" height="200" class="img-thumbnail" data-toggle="modal" data-target="#large-<?= $ressource->getIdRes() ?>">
                                </div>
                                <div class="row">
                                    <div class="col-sm-4 offset-sm-8">
                                        <button class="btn btn-outline-primary mb-2"  type="submit">Modifier</button>
                                        <button class="btn btn-outline-danger mb-2" type="reset">Annuler</button>
                                    </div>
                                </div>
                            </form>
                            <!-- Modal-->
                            <div class="modal fade" id="large-<?= $ressource->getIdRes() ?>" tabindex="-1" role="dialog">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <img src="<?= URL?>public/image/ressource/<?= $ressource->getImageRes() ?>" class="img-responsive"  width="780px">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default waves-effect " data-dismiss="modal">Fermer</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Modal-->
                        </div>
                    </div>
                    <!-- Basic Form Inputs card end -->
                </div>
            </div>
        </div>
        <!-- Page body end -->
    </div>
</div>
<?php $content = ob_get_clean();
$header = '';
$footer ='';
require "views/partials/template.php";
?>
