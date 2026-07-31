<?php

if (! function_exists('brl')) {
    /** Formata um valor em Reais (R$ 1.234,56). */
    function brl($value): string
    {
        return 'R$ '.number_format((float) $value, 2, ',', '.');
    }
}

if (! function_exists('pct')) {
    /** Formata um percentual (12,50%). */
    function pct($value): string
    {
        return number_format((float) $value, 2, ',', '.').'%';
    }
}
