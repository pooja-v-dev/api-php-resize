<?php

function upld_fn($targett, $newcpy, $w, $h, $extn)
{

    list($origWidth, $origHeight) = getimagesize($targett);

    $ratio = $origWidth / $origHeight;

    if (($w / $h) > $ratio) {
        $w = $h * $ratio;
    }

    $img = "";
    $extn = strtolower($extn);

    if ($extn == "png") {
        $img = imagecreatefrompng($targett);
    } else {
        $img = imagecreatefromjpeg($targett);
    }
    $a = imagecreatetruecolor($w, $h);

    imagecopyresampled($a, $img, 0, 0, 0, 0, $w, $h, $origWidth, $origHeight);
    imagejpeg($a, $newcpy, 80);
}
