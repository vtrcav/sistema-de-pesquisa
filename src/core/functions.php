<?php

/**
 * Verifica se um valor é nulo, vazio ou a string '<NULL>' e retorna um valor padrão.
 * Também aplica htmlspecialchars por segurança.
 *
 * @param mixed $value O valor a ser verificado.
 * @param string $default O valor padrão a ser retornado.
 * @return string
 */
function display($value, $default = 'N/D') {
    // Retorna o padrão se o valor for estritamente nulo, uma string vazia ou a string "<NULL>".
    if ($value === null || $value === '' || $value === '<NULL>') {
        return $default;
    }
    return htmlspecialchars($value);
}

/**
 * Formata uma data ou retorna um valor padrão.
 *
 * @param string|null $date_string A data em formato de string.
 * @param string $format O formato de saída desejado.
 * @param string $default O valor padrão.
 * @return string
 */
function display_date($date_string, $format = 'd-m-Y', $default = 'N/D') {
    // Retorna o padrão se for nulo, string vazia ou uma data inválida do banco.
    if ($date_string === null || $date_string === '' || strtotime($date_string) <= 0) {
        return $default;
    }
    
    return date($format, strtotime($date_string));
}

?>