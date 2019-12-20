<?php

namespace IMLSdk\Filters;

/**
 * Статичная фабрика для создания объектов для фильтрации коллекций
 */
final class FilterFactory
{

    /**
     * Создание объекта-фильтра для фильтрации
     * @param string $type Возможные варианты: location, pickpoints
     * @return Filter
     * @throws Exception
     */
    public static function create(string $type): Filter
    {
        if ($type == 'location') {
            return new LocationFilter();
        } elseif ($type == 'pickpoints') {
            return new PickPointsFilter();
        }

        throw new \Exception('Указан для создания неизвестный тип фильтра');
    }
    

}