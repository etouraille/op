<?php

namespace App\Service;

class UrlGenerator
{

    public function makeUrl($name,$string) {
        $tab = explode(' ', $string);
        foreach($tab as $index => $elem) {
            while(count(explode(' ', $tab[$index])) > 1 ) {
                $tab[$index] = str_replace(' ', '', $tab[$index]);
            }
        }
        foreach($tab as $index => $elem) {
            $tab[$index] = $this->removeAccent(strtolower($elem));
        }
        return implode('-', $tab) . '-a-' . $name . '.html';
    }

    protected function removeAccent($input) {

        $patterns[0] = '/[á|â|à|å|ä]/';
        $patterns[1] = '/[ð|é|ê|è|ë]/';
        $patterns[2] = '/[í|î|ì|ï]/';
        $patterns[3] = '/[ó|ô|ò|ø|õ|ö]/';
        $patterns[4] = '/[ú|û|ù|ü]/';
        $patterns[5] = '/æ/';
        $patterns[6] = '/ç/';
        $patterns[7] = '/ß/';
        $replacements[0] = 'a';
        $replacements[1] = 'e';
        $replacements[2] = 'i';
        $replacements[3] = 'o';
        $replacements[4] = 'u';
        $replacements[5] = 'ae';
        $replacements[6] = 'c';
        $replacements[7] = 'ss';

        return preg_replace($patterns, $replacements, $input);
    }
}