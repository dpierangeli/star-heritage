<div class="pagina_gestione_abilita">
<?php /*HELP: */ 

/*Controllo permessi utente*/
if ($_SESSION['permessi']<MODERATOR){
    echo '<div class="error">'.gdrcd_filter('out',$MESSAGE['error']['not_allowed']).'</div>';
} else { ?>

<!-- Titolo della pagina -->
<div class="page_title">
   <h2><?php echo "Gestione Upload" ?></h2>
</div>

<!-- Corpo della pagina -->
<div class="page_body">
<?php
if (isset($_POST['op']))
{
  $target_dir = "/web/htdocs/www.swheritage.it/home/themes/advanced/imgs/items/";
  $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
  $uploadOk = 1;
  $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
  // Check if image file is a actual image or fake image
  if(isset($_POST["submit"])) {
      $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
      if($check !== false) {
          echo "File is an image - " . $check["mime"] . ". <br/>";
          $uploadOk = 1;
      } else {
          echo "File is not an image. <br/>";
          $uploadOk = 0;
      }
  }
  // Check if file already exists
  if (file_exists($target_file)) {
      echo "File already exists. <br/>";
      //$uploadOk = 0;
  }
  // Check file size
  if ($_FILES["fileToUpload"]["size"] > 2000000) {
      echo "Sorry, your file is too large. <br/>";
      $uploadOk = 0;
  }
  // Allow certain file formats
  if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
  && $imageFileType != "gif" ) {
      echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed. <br/>";
      $uploadOk = 0;
  }
  // Check if $uploadOk is set to 0 by an error
  if ($uploadOk == 0) {
      echo "Sorry, your file was not uploaded. <br/>";
  // if everything is ok, try to upload file
  } else {
      if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
          echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded. <br/>";
      } else {
          echo "Sorry, there was an error uploading your file. <br/>";
      }
  }
}
?>
<form action="main.php?page=gestione_upload" method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="hidden" name="op" value="uploadfile" />
    <input type="submit" value="Upload Image" name="submit">
</form>

</div>
<?php } ?>
</div>