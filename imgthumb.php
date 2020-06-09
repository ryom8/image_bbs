<?php
function imgThumb($files,$imgMaxsize,$lb){
    
    $imginfo = @getimagesize($files);
    $width = $imginfo[0];
    $height = $imginfo[1];
    $proportion = $width / $height;

    if($proportion > 1){
        $per = ceil($width / $imgMaxsize);
    }else{
        $per = ceil($height / $imgMaxsize);
    }
    
    $imgd = '<a href="' .$files;
    $imgd.= '" data-lightbox="' .$lb;    
    $imgd.= '"><img src="' .$files;
    $imgd.= '" width="' .$width/$per;
    $imgd.= '" height="' .$height/$per;
    $imgd.= '"></a>';

    print($imgd);
}
