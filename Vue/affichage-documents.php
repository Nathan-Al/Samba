<html>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-10646"/>
        <meta charset="UTF-8">
        <LINK rel="icon" type="image/png" href=<?php echo $IconeSite ?> /> <!-- Icone de l'onglet de la page web -->

        <link rel="stylesheet" href=<?php echo $liens_css_document ?> /> <!-- Importations du css -->
            
            <head>
                <title>Documents</title> <!-- Titre de l'onglet de la page web -->
            </head>
    <body>
        <nav class="Nav-Accueil-Liens">
            <nav class="Nav-Box">
<!-- BOX DU TITRE -->
                <nav class="Titre-Nav">
                    <h1>Dossier</h1>
                </nav>

                    <nav class="Nav-Dossier">
                        <?php
                            $liens = 0;
                            if($dossier!=false)
                            {
                                for($o = 0; $o < sizeof($dossier); $o++)  
                                {

                                    ?>
                                        <div class="div-documents">
                                            <?php
                                                echo "<a href='".$lien_retour_documents.$dossier[$liens]."' class='a-doc'>".$o." :".$dossier[$liens]."</a>";
                                                echo "<br>";
                                            ?>
                                        </div>
                                    <?php
                                $liens++;    
                                }
                            }else
                            {
                                ?>
                                <h1>AUCUN DOSSIER</h1>
                                <?php
                            }
                        ?>
                    </nav>
            </nav>
            <div class="headers-flo">
                <a href="../" class="a-flo">ACCUEIL</a>
            </div>
            <nav class="Nav-Box">
<!-- BOX DU TITRE -->
                <nav class="Titre-Nav">
                    <h1>Fichiers</h1>
                </nav>

                    <nav class="Nav-Fichiers">
                        <?php
                            $liens = 0;
                            if($fichiers!=false)
                            {
                                for($o = 0; $o < sizeof($fichiers); $o++) 
                                    {
                                        if($fichiers[$liens] != "0")
                                            {
                                                ?>
                                                    <div class="div-documents">
                                                        <?php
                                                        echo "<a href='".$liens_fichiers[$liens]."' class='a-doc'>".$o." :".$fichiers[$liens]."</a>";
                                                            echo "<br>";
                                                        ?>
                                                    </div>
                                                <?php
                                            }
                                            $liens++;    
                                    }
                            }else
                            {
                                ?>
                                <h1>AUCUN FICHIERS</h1>
                                <?php
                            } 
                                    //closedir($dir);
                                
                        ?>
                    </nav>
            </nav>
        </nav>
    </body>
</html>