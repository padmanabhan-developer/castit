<?php

function pp($q) {
    echo '<pre>'; print_r($q); echo '</pre>';
}

function ppe($q){
    pp($q);exit;
}
