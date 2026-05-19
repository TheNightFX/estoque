<?php

function formatarData($data) {
    if(empty($data)) {
        return "";
    }

    $timestamp = strtotime($data);

    if(!$timestamp) {
        return $data;
    }

    return date("d/m/Y", $timestamp);
}

function formatarDataHora($data) {
    if(empty($data)) {
        return "";
    }

    $timestamp = strtotime($data);

    if(!$timestamp) {
        return $data;
    }

    return date("d/m/Y H:i", $timestamp);
}

?>
