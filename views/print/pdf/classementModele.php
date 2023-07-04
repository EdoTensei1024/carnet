<?php
// include autoloader
require 'public/vendor/autoload.php';

// reference the Dompdf namespace
use Dompdf\Dompdf;
use Dompdf\Options;

// instantiate and use the dompdf class
$options = new Options();
$options->set('defaultFont', 'Colibri');
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled',true);
$dompdf = new Dompdf($options);

ob_start();
?>
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            html {
                height: 100%;
                width: 100%;
            }
            body {
                position: relative;
                min-height: 100%;
                margin: 0;
                padding: 0;
            }
            table {
                font-family: arial, sans-serif;
                border-collapse: collapse;
                width: 100%;
            }

            td, th {
                border: 1px solid #dddddd;
                text-align: left;
                padding: 8px;
            }

            tr:nth-child(even) {
                background-color: #dddddd;
            }
            p{
                position: absolute;
                bottom: 0;
                left: 0;
                text-align: center;
            }
            h3{
                text-align: center;
                text-transform: uppercase;
                border: 1px solid black;
            }
        </style>
        <link rel="stylesheet" href="<?= URL ?>public/assets/css/pdf.css" media="all" />
        <title>
            Classement Modèle Agence
        </title>
    </head>
    <body>
    <div id="logo">
        <img src="<?= URL ?>public/image/logo/logo.jpeg">
    </div>

    <div>
        <h1>
            Classement Modèle Agence  <?= $agence->getNomAgence()?> du <?= date("d/m/Y",strtotime($debut))  ?> au <?=date("d/m/Y",strtotime($fin)) ?>
        </h1>
    </div>
    <h3>Classement des modèles</h3>
    <table>
        <thead>
        <tr>
            <th></th>
            <th class="desc">Modèle</th>
            <th class="desc">Chiffre d'affaire</th>
            <th class="desc">Dépense</th>
            <th class="desc">Charge Prod</th>
            <th class="desc">Côut Montage</th>
            <th class="desc">Côut Découpage</th>
            <th class="desc">Prime Prod</th>
            <th class="desc">Quantité</th>
            <th class="desc">Prix</th>
        </tr>
        </thead>
        <tbody>
        <?php   $i=1;
        if (isset($data) && !empty($data)):
            foreach ($data as $dt):
                if ($dt['recette'] != 0):
                    ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td class="desc"><?= $dt['modele']->getNomModele() ?></td>
                        <td class="desc"><?= $dt['recette'] ?></td>
                        <td class="desc"><?= $dt['depense'] ?></td>
                        <td class="desc"><?= $dt['charge'] ?></td>
                        <td class="desc"><?= $dt['montage'] ?></td>
                        <td class="desc"><?= $dt['decoupage'] ?></td>
                        <td class="desc"><?= $dt['rend'] ?></td>
                        <td class="desc"><?= $dt['quantite'] ?></td>
                        <td class="desc"><?= $dt['modele']->getPrixModele() ?></td>
                    </tr>
                    <?php $i++;
                endif;
            endforeach;
        endif; ?>
        </tbody>
    </table>

    <div>
        <span style="margin-right: 15%; text-decoration: underline; ">Direction</span>
    </div>
    <p> Imprimé le <?= date("d/m/Y H:i:s") ?> par <?= $user->getNomUser().' '.$user->getPrenomUser().' / '.$user->getRoleUser() .' / '.$agenceUser->getNomAgence() ?></p>
    </body>
    </html>
<?php
$html = ob_get_clean();
$dompdf->loadHtml($html);

// (Optional) Setup the paper size and orientation
$dompdf->setPaper('A4', 'landscape');

// Render the HTML as PDF
$dompdf->render();

// Output the generated PDF to Browser
$dompdf->stream('Classement Modèle Période Agence',array('Attachment'=>0));

