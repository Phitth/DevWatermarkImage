<?php
  function setThumbnail($image_name,$uploadDir,$moveToDir,$moveToresult,$fixWdth){
      /*
      FUNCTION DESCRIPTION
        $image_name - Name of the image which is uploaded
        $new_width - Width of the resized photo (maximum)
        $new_height - Height of the resized photo (maximum)
        $uploadDir - Directory of the original image
        $moveToDir - Directory to save the resized image
        $fixWdth - Width of image

        TMC [WIDTH: 108, HEIGHT: 134 PIXELS]
      */

      $fixWidthSize = (isset($fixWdth) && $fixWdth != ""?$fixWdth:'');

      $path = $uploadDir . '/' . $image_name;

      $mime = @getimagesize($path);

      if($mime['mime']=='image/png') {
          $src_img = imagecreatefrompng($path);
      }//end if

      if($mime['mime']=='image/jpg' || $mime['mime']=='image/jpeg' || $mime['mime']=='image/png') {
          $src_img = imagecreatefromjpeg($path);
      }//end if

      $old_x          =   imageSX($src_img);
      $old_y          =   imageSY($src_img);

      /*
      if($old_x == $old_y){
        $thumb_w    =   $fixWidthSize;
        $thumb_h    =   $old_y;
      }else{
        $nHeight = ($old_y/$old_x)*$fixWidthSize;
        $thumb_w    =   $fixWidthSize;
        $thumb_h    =   $nHeight;
      }//end if
      */

      //NEW RESIZE
      $w =  imageSX($src_img);
      $h = imageSY($src_img);
      $fixw = $fixWdth;
      if($w < $h){
        $minfirst = max($fixw,(($h/$w)*$fixw));
        $minlast = max($w,$h);

        $targetw = $targeth = min($minfirst, $minlast);

        $thumb_h = round($targeth);
        $thumb_w = $fixw;

      }else if($w == $h){
        $thumb_h = $fixw;
        $thumb_w = $fixw;

      }else if($w > $h){
        $minfirst = max($fixw,(($h/$w)*$fixw));
        $minlast = max($w,$h);

        $targetw = $targeth = min($minfirst, $minlast);

        $thumb_h = round($targetw / $ratio);
        $thumb_w = $fixw;
      }else{
        //no match
        echo '<br>SPECIAL';
        $nHeight = ($old_y/$old_x)*$fixWidthSize;
        $thumb_w    =   $fixWidthSize;
        $thumb_h    =   $nHeight;
      }
      //END NEW RESIZE

      //calculate
      $ratio = (int)$w/$h;
      $convertratio = round($ratio);

      $dst_img        =   ImageCreateTrueColor($thumb_w,$thumb_h);

      imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y);

      // New save location
      $new_thumb_loc = $moveToDir.'/'.$image_name;

      if($mime['mime']=='image/png'){
          $result = imagepng($dst_img,$new_thumb_loc,8);
      }//end if

      if($mime['mime']=='image/jpg' || $mime['mime']=='image/jpeg' || $mime['mime']=='image/png') {
          $result = imagejpeg($dst_img,$new_thumb_loc,80);
      }//end if

      imagedestroy($result);

      ///////WATER MASK
      //new path update 18-06-12062020
      //watermark_files/tmc_water_mask_modified_18_06_2020_11_39.png

      //old path
      //watermark_files/tmc_water_mask_modified.png'
      $sourceImage = 'watermark_files/tmc_water_mask_modified_18_06_2020_14_29.png';
      //set the destination image (background)
      $destImage = $new_thumb_loc;
      //get the size of the source image, needed for imagecopy()
      list($srcWidth, $srcHeight) = getimagesize($sourceImage);
      //create a new image from the source image
      $src = imagecreatefrompng($sourceImage);
      //create a new image from the destination image
      $dest = imagecreatefromjpeg($destImage);
      //set the x and y positions of the source image on top of the destination image
      $src_xPosition = 0; //10 pixels from the left
      $src_yPosition = 0; //10 pixels from the top
      //set the x and y positions of the source image to be copied to the destination image
      $src_cropXposition = 0; //do not crop on the side
      $src_cropYposition = 0; //do not crop at the top
      //merge the source and destination images
      imagecopy($dest,$src,$src_xPosition,$src_yPosition,$src_cropXposition,$src_cropYposition,$srcWidth,$srcHeight);
      //output the merged images to a file
      $result_img_loc = $moveToresult.'/'.$image_name;

      imagejpeg($dest,$result_img_loc);
      //destroy the source image
      imagedestroy($src);
      //destroy the destination image
      imagedestroy($dest);

      return $result;
  }//end function

 ?>
