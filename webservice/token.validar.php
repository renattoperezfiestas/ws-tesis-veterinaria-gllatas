<?php

require_once '../util/jwt/vendor/autoload.php';
require_once '../util/jwt/auth.php';


function validarToken($token){
    try {
        if ( Auth::Check($token) ){
          return TRUE;
        }
    } catch (Exception $e) {
        throw $e;
    }
}
