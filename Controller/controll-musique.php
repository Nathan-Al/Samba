<?php
   require "../Outil/lecteur-liens.php";
   require $require_lecteur_fichier;
   require $require_lecteur_musique;
   require $require_model_musique;
   require_once($require_decodeur_id3);
   
   $getID3 = new getID3;
   $PageEncoding = 'UTF-8';
   $cover_musique = array();
   $ValidTagTypes = array('id3v1', 'id3v2.3', 'ape');
   //$fichier = ListerTotalitefichier($liensHomeMusique);
   $fichier = chargeLiens($liensHomeMusique);
   $liens_musique = array();
   $data_musique = array();
   $cover_musique = array();
   $s = "img-min-liens-musique";
   $liens = "";
   $musique="";
   $musique_classer = array();
   $pages = 0;
   $nb = 0;
   $pages_get = (isset($_GET["page"])? $_GET["page"]:false);
   if($pages_get>=0)
   {
      if($pages_get=1)
         $pages_get=$pages_get-1;
  
  if($fichier!=false)
  {
      $index= 1;
      for($r=0;$r<sizeof($fichier);$r++)
      {
        
         $Line_liens = $fichier[$r];
         $s = strrchr($Line_liens,'/');
         $m = substr($s,1);
         if(strpos($m,".")!=0)
         {
            if(substr($m,strrpos($m,".")+1)=="mp3")
            {
               $liens_musique[$index] = str_replace("\\","/", $fichier[$r]);
               $data_musique[$index] = read_mp3_tags($Line_liens);
               $index++;
            }
           
         }
      }
  }
  if(isset($_POST["recup"]))
  {
      $liens_mu = $_POST["recup"];
      $info_musique = read_mp3_tags($liens_mu);
      $cover = read_mp3_tags($liens_mu,true);
      $data = ["titre"=>$info_musique[0]->getTitre(),"album"=>$info_musique[0]->getAlbum(),"artiste"=>$info_musique[0]->getArtiste(),"temps"=>$info_musique[0]->getTemps(),"genre"=>$info_musique[0]->getGenre(),"annee"=>$info_musique[0]->getAnnee(),"cover"=>$cover[0]->getImage()];
      echo json_encode($data);
  }
  else
  {
     $info_music [] =  new Musique (
        "Titre",
        "Album",
        "Artiste",
        "Genre",
        "Durée",
        "Date",
        "",
        null ,
        "",
        "");
  }

  if(isset($_POST["modification"]))
  {
     if(isset($_POST["titre"]))
     $titre_modif = $_POST["titre"];
     if(isset($_POST["album"]))
     $album_modif = $_POST["album"];
     if(isset($_POST["artiste"]))
     $artiste_modif = $_POST["artiste"];
     if(isset($_POST["genre"]))
     $genre_modif = $_POST["genre"];
     if(isset($_POST["datecreation"]))
     $datecreation_modif = $_POST["datecreation"];
     if(isset($_POST["liensmusique"]))
     $liensmusique_modif = $_POST["liensmusique"];


     //echo " ".$liensmusique_modif." ".$titre_modif." ".$album_modif." ".$artiste_modif." ".$genre_modif." ".$datecreation_modif ;

     require_once($require_write_id3);
     $TagFormatsToWrite = (isset($_POST['TagFormatsToWrite']) ? $_POST['TagFormatsToWrite'] : array());
        //echo 'starting to write tag(s)<BR>';
     
           $tagwriter = new getid3_writetags;
           $tagwriter->filename = $liensmusique_modif;
           $tagwriter->tagformats = $TagFormatsToWrite;
           $tagwriter->overwrite_tags    = true;  // if true will erase existing tag data and write only passed data; if false will merge passed data with existing tag data (experimental)
           $tagwriter->remove_other_tags = false; // if true removes other tag formats (e.g. ID3v1, ID3v2, APE, Lyrics3, etc) that may be present in the file and only write the specified tag format(s). If false leaves any unspecified tag formats as-is.
           $tagwriter->tag_encoding      = $TextEncoding;
           $tagwriter->remove_other_tags = false;

           //populate data array
           $TagData = array(
              'title'                  => array($titre_modif),
              'artist'                 => array($artiste_modif),
              'album'                  => array($album_modif),
              'year'                   => array($datecreation_modif),
              'genre'                  => array($genre_modif),
           );

           $TagFormatsToWrite = (isset($_POST['TagFormatsToWrite']) ? $_POST['TagFormatsToWrite'] : array());
           if (!empty($TagFormatsToWrite)) {
             
              if (!empty($_FILES['fichiers']['tmp_name'])) {
               
                 if (in_array('id3v2.4', $tagwriter->tagformats) || in_array('id3v2.3', $tagwriter->tagformats) || in_array('id3v2.2', $tagwriter->tagformats)) {
                   
                    if (is_uploaded_file($_FILES['fichiers']['tmp_name'])) {
                       
                       if ($APICdata = file_get_contents($_FILES['fichiers']['tmp_name'])) {
                             
                          if ($exif_imagetype = exif_imagetype($_FILES['fichiers']['tmp_name'])) {
                                
                             $TagData['attached_picture'][0]['data']          = $APICdata;
                             $TagData['attached_picture'][0]['picturetypeid'] = $_POST['APICpictureType'];
                             $TagData['attached_picture'][0]['description']   = $_FILES['fichiers']['name'];
                             $TagData['attached_picture'][0]['mime']          = image_type_to_mime_type($exif_imagetype);
        
                          } else {
                             echo '<b>invalid image format (only GIF, JPEG, PNG)</b><br>';
                          }
                       } else {
                          echo '<b>cannot open '.htmlentities($_FILES['fichiers']['tmp_name']).'</b><br>';
                       }
                    } else {
                       echo '<b>!is_uploaded_file('.htmlentities($_FILES['userfile']['tmp_name']).')</b><br>';
                    }
                 } else {
                    echo '<b>WARNING:</b> Can only embed images for ID3v2<br>';
                 }
              }
           }
        $tagwriter->tag_data = $TagData;

        // write tags
        if ($tagwriter->WriteTags()) {
           //echo 'Successfully wrote tags<br>';
           $fait = true;
           $liens = $liensHomeMusique.$_GET["musique"];
              echo "<meta http-equiv='refresh' content='0; URL=controll-musique.php?musique=".$liens."'>";
           $message = "Modification effectué ! ";
              echo "<script type='text/javascript'>alert('$message');</script>";
           if (!empty($tagwriter->warnings)) {
              //echo 'There were some warnings:<br>'.implode('<br><br>', $tagwriter->warnings);
              $fait = "middle";
           }
        } else {
           //echo 'Failed to write tags!<br>'.implode('<br><br>', $tagwriter->errors);
           $fait = false;
        }
     
  }
   }else
   {
      throw new Exception ('Page inexistante');
   }
   if(!isset($_POST["recup"]))
   {
      require $require_vue_affichage_musique;
   }
?>


