<?php ob_start();
?>
    <!-- Data Table Css -->
    <link rel="stylesheet" type="text/css" href="<?= URL ?>public/bower_components/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="<?= URL ?>public/bower_components/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css">
<?php
$header = ob_get_clean();
ob_start();
?>
    <!--body-->
    <div class="main-body">
        <div class="page-wrapper">
            <!-- Page-header start -->
            <?php if ( $_SESSION['role']=='admin' || $_SESSION['role']=='super_admin')  :?>
                <div class="card">
                    <div class="card-block">
                        <div class="row">
                            <div class="col-sm-12">
                                <p id="nom_agence_select"><?= $_SESSION['agence_nom'] ?></p>
                                <label for="agence" class="form-control-label">Agence</label>
                                <select name="agence" id="agence_id" class="form-control">
                                    <?php  foreach ($agences as $agence):?>
                                        <option value="<?= $agence->getIdAgence() ?>"><?= $agence->getNomAgence() ?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                            <div class="col-sm-12 mt-3">
                                <button class="btn btn-primary" id="agence_select">Choisir</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif;?>

            <div class="page-header">
                <div class="page-header-title">
                    <?php if ( $_SESSION['role']=='super_admin')  :?>
                    <a href="<?= URL ?>agence/ajouter"><button class="btn btn-outline-info btn-lg">Ajouter <i class="fa fa-user-plus"></i></button></a>
                    <?php endif;?>
                    <span>Liste des agences 1</span>
                </div>
                <div class="page-header-breadcrumb">
                    <ul class="breadcrumb-title">
                        <li class="breadcrumb-item">
                            <a href="<?= URL ?>accueil">
                                <i class="icofont icofont-home"></i>
                            </a>
                        </li>
                        <li class="breadcrumb-item"><a href="<?= URL ?>agence">Agence</a>
                        </li>
                        <li class="breadcrumb-item"><a href="<?= URL ?>agence">Consulter</a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Page-header end -->
            <!-- Page-body start -->
            <div class="page-body">
                <div class="row">
                    <div class="col-sm-12">
                        <!-- Zero config.table start -->
                        <div class="card">
                            <div class="card-block">
                                <div class="dt-responsive table-responsive">
                                    <table id="simpletable" class="table table-striped table-bordered nowrap">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nom</th>
                                            <th>Contact</th>
                                            <th>Adresse</th>
                                            <th>Statut</th>
                                            <th></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i=1; foreach ($agences as $agence):?>
                                            <?php if ( $_SESSION['role']=='admin' || $_SESSION['role']=='super_admin' || $_SESSION['agence']==$agence->getIdAgence())  :?>
                                                <tr>
                                                    <td><?= $i ?></td>
                                                    <td><?= $agence->getNomAgence() ?></td>
                                                    <td><?= $agence->getContactAgence() ?></td>
                                                    <td><?= $agence->getAdresseAgence() ?></td>
                                                    <td><?= $agence->getStatutAgence() ?></td>
                                                    <td>
                                                        <?php if ($_SESSION['agence_role'] =='Principale'):
                                                            if( $_SESSION['role'] == 'super_admin'): ?>
                                                                <a href="<?= URL ?>/agence/modifier/<?= $agence->getIdAgence() ?>">
                                                                    <button class="btn btn-success btn-md text-light"><i class="icofont icofont-pencil-alt-5"></i></button>
                                                                </a>
                                                            <?php endif;
                                                        else:
                                                            if ( $_SESSION['role']=='admin' || $_SESSION['role']=='super_admin') :?>
                                                                <a href="<?= URL ?>/agence/modifier/<?= $agence->getIdAgence() ?>">
                                                                    <button class="btn btn-success btn-md text-light"><i class="icofont icofont-pencil-alt-5"></i></button>
                                                                </a>
                                                            <?php endif;
                                                        endif;
                                                        if ($_SESSION['role']=='super_admin')  :?>
                                                            <form class="d-inline" method="post" onsubmit="return confirm('Voulez-vous vraiment supprimer ?');" action="<?= URL ?>agence/sv/<?= $agence->getIdAgence() ?>" >
                                                                <button id="btn-delete-form" class="btn btn-danger btn-md" type="submit"><i class="icofont icofont-trash"></i></button>
                                                            </form>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endif;?>
                                            <?php $i++; endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th>#</th>
                                            <th>Nom</th>
                                            <th>Contact</th>
                                            <th>Adresse</th>
                                            <th>Statut</th>
                                            <th></th>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- Zero config.table end -->
                        <!-- Default ordering table start -->
                    </div>
                </div>
            </div>
            <!-- Page-body end -->
        </div>
    </div>
    <!--end Body-->
<?php $content = ob_get_clean() ;

ob_start();
?>
    <!-- data-table js -->
    <script src="<?= URL ?>public/bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?= URL ?>public/bower_components/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?= URL ?>public/bower_components/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?= URL ?>public/bower_components/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
    <script src="<?= URL ?>public/assets/js/cdn/sweetarlert.js"></script>
    <script>
        $(document).ready(function (){
            $('#agence_select').click( function(e)
            {
                var agence = $('#agence_id').val();
                $.post(
                    '<?= URL ?>agence/agence_session',
                    {
                        agence:agence
                    },
                    function(response)
                    {
                        $('#nom_agence_select').html(response);
                        swal({
                            title: "Bravo ",
                            text: "Agence sélectionnée avec succès!",
                            icon: "success"
                        }).then(function() {
                            window.location ="<?= URL ?>agence";
                        });
                    });
                return false;
            });
        });
    </script>
<?php
$footer = ob_get_clean();
require "views/partials/template.php";
?>