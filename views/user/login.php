<!DOCTYPE html>
<html lang="en">

<head>
    <title>Grace Fashion International</title>

    <![endif]-->
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Z3dtech">
    <meta name="author" content="Z3dtech">
    <!-- Favicon icon -->

    <link rel="icon" href="<?= URL ?>public/image/logo/favicon.ico" type="image/x-icon">
    <!-- Google font-->
    <link href="<?= URL ?>public/assets/css/fonts/fonts.css" rel="stylesheet">
    <!-- Required Fremwork -->
    <link rel="stylesheet" type="text/css" href="<?= URL ?>public/bower_components/bootstrap/css/bootstrap.min.css">
    <!-- themify-icons line icon -->
    <link rel="stylesheet" type="text/css" href="<?= URL ?>public/assets/icon/themify-icons/themify-icons.css">
    <!-- ico font -->
    <link rel="stylesheet" type="text/css" href="<?= URL ?>public/assets/icon/icofont/css/icofont.css">
    <!-- Style.css -->
    <link rel="stylesheet" type="text/css" href="<?= URL ?>public/assets/css/style.css">
    <!-- color .css -->

</head>

<body class="fix-menu">
<section class="login p-fixed d-flex text-center bg-primary common-img-bg">
    <!-- Container-fluid starts -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <!-- Authentication card start -->
                <div class="login-card card-block auth-body">
                    <form class="md-float-material" id="form-connexion">
                        <div class="text-center">
                            <img src="<?= URL ?>public/image/logo/logo.jpeg" class="img-thumbnail" width="100" height="100">
                        </div>
                        <div class="auth-box">
                            <div class="row m-b-20">
                                <div class="col-md-12">
                                    <h3 class="text-left txt-primary">Se connecter</h3>
                                </div>
                            </div>
                            <hr/>
                            <div class="input-group">
                                <input type="text" id="username" class="form-control" placeholder="Entrer votre nom d'utilisateur">
                                <span class="md-line"></span>
                            </div>
                            <div class="input-group">
                                <input type="password" id="password" class="form-control" placeholder="Entrer votre mot de passe">
                                <span class="md-line"></span>
                            </div>
                            <div class="row m-t-25 text-left">
                                <div class="col-sm-7 col-xs-12">
                                    <div class="checkbox-fade fade-in-primary">
                                        <label>
                                            <a href="#"> <span class="text-inverse">Mot de passe oublié</span> </a>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row m-t-30">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary btn-md btn-block waves-effect text-center m-b-20">Se connecter</button>
                                </div>
                            </div>
                            <hr/>
                            <div class="row">
                                <div class="col-md-10">
<!--                                    <p class="text-inverse text-left m-b-0">Thank you and enjoy our website.</p>-->
<!--                                    <p class="text-inverse text-left"><b>Your Autentification Team</b></p>-->
                                </div>
                                <div class="col-md-2">
                                    <img src="<?= URL ?>public/image/logo/logo.jpeg" class="img-thumbnail" width="50" height="50">
<!--                                    <img src="--><?//= URL ?><!--public/assets/images/auth/Logo-small-bottom.png" alt="small-logo.png">-->
                                </div>
                            </div>

                        </div>
                    </form>
                    <!-- end of form -->
                </div>
                <!-- Authentication card end -->
            </div>
            <!-- end of col-sm-12 -->
        </div>
        <!-- end of row -->
    </div>
    <!-- end of container-fluid -->
</section>
<!-- Warning Section Starts -->
<!-- Older IE warning message -->
<!--[if lt IE 9]>

<![endif]-->
<!-- Warning Section Ends -->
<!-- Required Jquery -->
<script type="text/javascript" src="<?= URL ?>public/bower_components/jquery/js/jquery.min.js"></script>
<script type="text/javascript" src="<?= URL ?>public/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?= URL ?>public/bower_components/popper.js/js/popper.min.js"></script>
<script type="text/javascript" src="<?= URL ?>public/bower_components/bootstrap/js/bootstrap.min.js"></script>
<!-- jquery slimscroll js -->
<script type="text/javascript" src="<?= URL ?>public/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
<!-- modernizr js -->
<script type="text/javascript" src="<?= URL ?>public/bower_components/modernizr/js/modernizr.js"></script>
<script type="text/javascript" src="<?= URL ?>public/bower_components/modernizr/js/css-scrollbars.js"></script>
<!-- i18next.min.js -->
<script type="text/javascript" src="<?= URL ?>public/bower_components/i18next/js/i18next.min.js"></script>
<script type="text/javascript" src="<?= URL ?>public/bower_components/i18next-xhr-backend/js/i18nextXHRBackend.min.js"></script>
<script type="text/javascript" src="<?= URL ?>public/bower_components/i18next-browser-languagedetector/js/i18nextBrowserLanguageDetector.min.js"></script>
<script type="text/javascript" src="<?= URL ?>public/bower_components/jquery-i18next/js/jquery-i18next.min.js"></script>
<!-- Custom js -->
<!--<script type="text/javascript" src="assets/js/script.js"></script>-->
<!---- color js --->
<script type="text/javascript" src="<?= URL ?>public/assets/js/common-pages.js"></script>
<script src="<?= URL ?>public/assets/js/cdn/sweetarlert.js"></script>
<script>
    $(document).ready(function (){
        $('#form-connexion').submit( function(e)
        {
            var username = $('#username').val();
            var password = $('#password').val();

            $.post(
                '<?= URL ?>utilisateur/login',
                {
                    username:username, password:password
                },
                function(response)
                {
                    if (response == 'unloged'){
                        swal({
                            title: "Echec",
                            text: "Veuillez vérifier vos informations de connexion!",
                            icon: "danger"
                        }).then(function() {
                            window.location ="<?= URL ?>";
                        });
                    }
                    if(response == 'loged'){
                    swal({
                        title: "Bravo",
                        text: "Bienvenue !",
                        icon: "success"
                    }).then(function() {
                        window.location ="<?= URL ?>accueil";
                    });
                }

                });
            return false;
        });
    });
</script>
</body>

</html>
