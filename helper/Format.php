<?php

namespace fool\helper;

class Format
{

    public static function camelize($uncamelized_words,$separator='_', $isFirstCapital = true)
    {
        if ($isFirstCapital) {
            $uncamelized_words = '_' . $uncamelized_words;
        }
        $uncamelized_words = $separator . str_replace($separator, " ", strtolower($uncamelized_words));
        return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator );
    }

    public static function uncamelize($camelCaps,$separator='_')
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $separator . "$2", $camelCaps));
    }
}
