<?php 

if (!defined('home')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

/**
 * Original by Emma @ https://stackoverflow.com/users/6553328/emma from:
 * https://stackoverflow.com/questions/56507695/how-to-search-japanese-character-in-string-with-php
 */

function isOnlyKanji($str) {
    return preg_match('/^[\p{Han}]+$/u', $str) > 0;
}

function itHasKanji($str) {
    return preg_match('/[\p{Han}]+/u', $str) > 0;
}

function itHasJapanese($str) {
    return preg_match('/[\p{Katakana}\p{Hiragana}\p{Han}「」]+/u', $str) > 0;
}

function isOnlyJapanese($str) {
    return preg_match('/^[\p{Katakana}\p{Hiragana}\p{Han}「」]+$/u', $str) > 0;
}

function obtainKanjis($str) {
    preg_match_all('/[p\p{Han}]/u', $str, $matches);
    return $matches;
}
