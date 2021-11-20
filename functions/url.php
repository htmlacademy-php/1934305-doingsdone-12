<?php
/**
 * Формирует ссылку с параметрами запроса
 * @param string $scriptName адрес текущего сценария
 * @param array $params параметры запроса
 * @return string готовая ссылка
 */
function makeURL(string $scriptName, array $params): string
{
    return "/" . $scriptName . "?" . http_build_query($params);
}
