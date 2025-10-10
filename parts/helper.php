<?php

if (!defined('home')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

/**
 * Original by Emma @ https://stackoverflow.com/users/6553328/emma from:
 * https://stackoverflow.com/questions/56507695/how-to-search-japanese-character-in-string-with-php
 */

function isOnlyKanji($str)
{
    return preg_match('/^[\p{Han}]+$/u', $str) > 0;
}

function itHasKanji($str)
{
    return preg_match('/[\p{Han}㇒]+/u', $str) > 0;
}

function itHasJapanese($str)
{
    return preg_match('/[\p{Katakana}\p{Hiragana}\p{Han}「」㇒]+/u', $str) > 0;
}

function isOnlyJapanese($str)
{
    return preg_match('/^[\p{Katakana}\p{Hiragana}\p{Han}「」]+$/u', $str) > 0;
}

function isOnlyHiragana($str)
{
    return preg_match('/^[\p{Hiragana}\.]+$/u', $str) > 0;
}

function isOnlyKatakana($str)
{
    return preg_match('/^[\p{Katakana}\.]+$/u', $str) > 0;
}

function obtainKanjis($str)
{
    preg_match_all('/[p\p{Han}㇒]/u', $str, $matches);
    return $matches;
}

/**
 * Original by @ https://github.com/kinosuke01 from:
 * https://github.com/kinosuke01/convertible-romaji
 */
function toHiragana($text = '')
{
    $decision_table = [
        'kka' => 'っか',
        'kki' => 'っき',
        'kku' => 'っく',
        'kke' => 'っけ',
        'kko' => 'っこ',
        'ssa' => 'っさ',
        'sshi' => 'っし',
        'ssu' => 'っす',
        'sse' => 'っせ',
        'sso' => 'っそ',
        'ssho' => 'っしょ',
        'tta' => 'った',
        'tti' => 'っち',
        'ttu' => 'っつ',
        'tte' => 'って',
        'tto' => 'っと',
        'ppa' => 'っぱ',
        'ppi' => 'っぴ',
        'ppu' => 'っぷ',
        'ppe' => 'っぺ',
        'ppo' => 'っぽ',
        'ga' => 'が',
        'gi' => 'ぎ',
        'gu' => 'ぐ',
        'ge' => 'げ',
        'go' => 'ご',
        'za' => 'ざ',
        'ji' => 'じ',
        'zu' => 'ず',
        'ze' => 'ぜ',
        'zo' => 'ぞ',
        'da' => 'だ',
        //    'ji' => 'ぢ',
        //    'zu' => 'づ',
        'de' => 'で',
        'do' => 'ど',
        'ba' => 'ば',
        'bi' => 'び',
        'bu' => 'ぶ',
        'be' => 'べ',
        'bo' => 'ぼ',
        'pa' => 'ぱ',
        'pi' => 'ぴ',
        'pu' => 'ぷ',
        'pe' => 'ぺ',
        'po' => 'ぽ',
        'kya' => 'きゃ',
        'kyu' => 'きゅ',
        'kyo' => 'きょ',
        'sha' => 'しゃ',
        'shu' => 'しゅ',
        'sho' => 'しょ',
        'cha' => 'ちゃ',
        'chu' => 'ちゅ',
        'cho' => 'ちょ',
        'nya' => 'にゃ',
        'nyu' => 'にゅ',
        'nyo' => 'にょ',
        'hya' => 'ひゃ',
        'hyu' => 'ひゅ',
        'hyo' => 'ひょ',
        'mya' => 'みゃ',
        'myu' => 'みゅ',
        'myo' => 'みょ',
        'rya' => 'りゃ',
        'ryu' => 'りゅ',
        'ryo' => 'りょ',
        'gya' => 'ぎゃ',
        'gyu' => 'ぎゅ',
        'gyo' => 'ぎょ',
        'ja' => 'じゃ',
        'ju' => 'じゅ',
        'jo' => 'じょ',
        'bya' => 'びゃ',
        'byu' => 'びゅ',
        'byo' => 'びょ',
        'pya' => 'ぴゃ',
        'pyu' => 'ぴゅ',
        'pyo' => 'ぴょ',
        'chi' => 'ち',
        'tsu' => 'つ',
        'ka' => 'か',
        'ki' => 'き',
        'ku' => 'く',
        'ke' => 'け',
        'ko' => 'こ',
        'sa' => 'さ',
        'shi' => 'し',
        'si' => 'し',
        'su' => 'す',
        'se' => 'せ',
        'so' => 'そ',
        'ta' => 'た',
        'te' => 'て',
        'to' => 'と',
        'na' => 'な',
        'ni' => 'に',
        'nu' => 'ぬ',
        'ne' => 'ね',
        'no' => 'の',
        'ha' => 'は',
        'hi' => 'ひ',
        'fu' => 'ふ',
        'he' => 'へ',
        'ho' => 'ほ',
        'ma' => 'ま',
        'mi' => 'み',
        'mu' => 'む',
        'me' => 'め',
        'mo' => 'も',
        'ya' => 'や',
        'yu' => 'ゆ',
        'yo' => 'よ',
        'ra' => 'ら',
        'ri' => 'り',
        'ru' => 'る',
        're' => 'れ',
        'ro' => 'ろ',
        'wa' => 'わ',
        'a' => 'あ',
        'i' => 'い',
        'u' => 'う',
        'e' => 'え',
        'o' => 'お',
        'n' => 'ん',
    ];
    $text =  mb_strtolower($text);
    foreach ($decision_table as $key => $value) {
        $text = mb_ereg_replace($key, $value, $text);
    }
    return $text;
}
function toKatakana($text = '')
{
    $text =  mb_strtolower($text);
    $text = toHiragana($text);
    $text = mb_convert_kana($text, 'C');
    return $text;
}

function parse_story($story)
{
    // links
    $pattern = '/#(.+?)#/';
    $story = preg_replace($pattern, '<a href="kanji.php?literal=$1">$1</a>', $story);

    // emphasis
    $pattern = '/\_(.+?)\_/';
    $story = preg_replace($pattern, '<span>$1</span>', $story);

    // todo
    $pattern = '/\?(.+?)\?/';
    $story = preg_replace($pattern, '<em>TODO: $1</em>', $story);

    return $story;
}


function formatMeanings($str)
{
    if (preg_match('/.+;\(P\)\z/', $str)) {
        $str = substr($str, 0, strlen($str) - 4);
    }

    if (preg_match('/.+\(2\).+/', $str)) {
        preg_match_all('/\([a-z0-9-,]+\) \(\d+\) (.*?)(?=\([a-z0-9-,]+\) \(\d+\).*|\Z)/', $str, $output);
        $meanings = $output[1];

        $result = [];
        foreach ($meanings as $meaning) {
            $meaning = rtrim($meaning, ';');
            $meaning = str_replace(';', ', ', $meaning);
            $result[] = '• ' . $meaning;
        }
        return implode('<br>', $result);
    }

    // is a single definition
    preg_match('/\([a-z0-9-,]+\) (.*)/', $str, $output);
    $meaning = str_replace(';', ', ', $output[1]);
    $meaning = '• ' . $meaning;
    return $meaning;
}

function formatKanjis($str)
{
    if (!str_contains($str, ";")) {
        return $str;
    }

    $meta = [
        'sK' => '',
        'oK' => '',
        'ateji' => '',
        'rK' => '',
        'sK' => '',
        'io' => '',
        'iK' => '',
        'P' => '*'
    ];

    foreach ($meta as $k => $r) {
        $str = str_replace('(' . $k . ')', $r, $str);
    }
    return str_replace(";", " / ", $str);
}



function getImportanceJLPT($jlpt, $totalKnown)
{

    /*
        #   ACCU
        N5   80    80
        N4  167   247
        N3  370   617
        N2  368   985
        N1 1235  2220    
    */
    if ($jlpt == 5) {
        return 'high';
    }

    if ($totalKnown < 80) {
        switch ($jlpt) {
            case 4:
                return 'medium';
            default:
                return 'low';
        }
    }

    if ($totalKnown < 247) {
        switch ($jlpt) {
            case 4:
                return 'high';
            case 3:
                return 'medium';
            default:
                return 'low';
        }
    }

    if ($totalKnown < 617) {
        switch ($jlpt) {
            case 4:
            case 3:
                return 'high';
            case 2:
                return 'medium';
            default:
                return 'low';
        }
    }

    if ($totalKnown < 985) {
        switch ($jlpt) {
            case 4:
            case 3:
            case 2:
                return 'high';
            default:
                return 'medium';
        }
    }

    return "high";
}


function getImportanceKANKEN($kanken, $totalKnown)
{

    if ($kanken == 10) {
        return 'high';
    }

    if ($kanken == 1.5 || $kanken == 1) {
        return 'zero';
    }

    if ($totalKnown < 80) {
        switch ($kanken) {
            case 9:
                return 'medium';
            default:
                return 'low';
        }
    }

    if ($totalKnown < 240) {
        switch ($kanken) {
            case 9:
                return 'high';
            case 8:
                return 'medium';
            default:
                return 'low';
        }
    }

    if ($totalKnown < 440) {
        switch ($kanken) {
            case 9:
            case 8:
                return 'high';
            case 7:
                return 'medium';
            default:
                return 'low';
        }
    }

    if ($totalKnown < 642) {
        switch ($kanken) {
            case 9:
            case 8:
            case 7:
                return 'high';
            case 6:
                return 'medium';
            default:
                return 'low';
        }
    }

    if ($totalKnown < 835) {
        switch ($kanken) {
            case 9:
            case 8:
            case 7:
            case 6:
                return 'high';
            case 5:
                return 'medium';
            default:
                return 'low';
        }
    }

    if ($totalKnown < 1026) {
        switch ($kanken) {
            case 9:
            case 8:
            case 7:
            case 6:
            case 5:
                return 'high';
            case 4:
                return 'medium';
            default:
                return 'low';
        }
    }

    if ($totalKnown < 1339) {
        switch ($kanken) {
            case 9:
            case 8:
            case 7:
            case 6:
            case 5:
            case 4:
                return 'high';
            case 3:
                return 'medium';
            default:
                return 'low';
        }
    }

    if ($totalKnown < 1623) {
        switch ($kanken) {
            case 9:
            case 8:
            case 7:
            case 6:
            case 5:
            case 4:
            case 3:
                return 'high';
            case 2.5:
                return 'medium';
            default:
                return 'low';
        }
    }

    if ($totalKnown < 1951) {
        switch ($kanken) {
            case 9:
            case 8:
            case 7:
            case 6:
            case 5:
            case 4:
            case 3:
            case 2.5:
                return 'high';
            default:
                return 'medium';
        }
    }

    return "high";
}
