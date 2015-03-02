<?php

namespace Bravesheep\Dogmatist\Guesser;

use Bravesheep\Dogmatist\Field;
use Bravesheep\Dogmatist\Util;

class TypeResolver
{
    private static $mappings = [
        'int' => [Field::TYPE_FAKE, false, ['randomNumber']],
        'integer' => [Field::TYPE_FAKE, false, ['randomNumber']],
        'number' => [Field::TYPE_FAKE, false, ['randomNumber']],
        'string' => [Field::TYPE_FAKE, false, ['lexify', ['??????????']]],
        'bool' => [Field::TYPE_FAKE, false, ['boolean']],
        'boolean' => [Field::TYPE_FAKE, false, ['boolean']],
        'float' => [Field::TYPE_FAKE, false, ['randomFloat']],
        'double' => [Field::TYPE_FAKE, false, ['randomFloat']],
        'long' => [Field::TYPE_FAKE, false, ['randomNumber']],
        'word' => [Field::TYPE_FAKE, false, ['word']],
        'sentence' => [Field::TYPE_FAKE, false, ['sentence']],
        'paragraph' => [Field::TYPE_FAKE, false, ['paragraph']],
        'text' => [Field::TYPE_FAKE, false, ['text']],
        'title' => [Field::TYPE_FAKE, false, ['sentence']],
        'name' => [Field::TYPE_FAKE, false, ['name']],
        'first_name' => [Field::TYPE_FAKE, false, ['firstName']],
        'firstname' => [Field::TYPE_FAKE, false, ['firstName']],
        'last_name' => [Field::TYPE_FAKE, false, ['lastName']],
        'lastname' => [Field::TYPE_FAKE, false, ['lastName']],
        'phone' => [Field::TYPE_FAKE, false, ['phoneNumber']],
        'phone_number' => [Field::TYPE_FAKE, false, ['phoneNumber']],
        'phonenumber' => [Field::TYPE_FAKE, false, ['phoneNumber']],
        'address' => [Field::TYPE_FAKE, false, ['streetAddress']],
        'street_address' => [Field::TYPE_FAKE, false, ['streetAddress']],
        'streetaddress' => [Field::TYPE_FAKE, false, ['streetAddress']],
        'city' => [Field::TYPE_FAKE, false, ['city']],
        'country' => [Field::TYPE_FAKE, false, ['country']],
        'building_number' => [Field::TYPE_FAKE, false, ['buildingNumber']],
        'buildingnumber' => [Field::TYPE_FAKE, false, ['buildingNumber']],
        'zipcode' => [Field::TYPE_FAKE, false, ['postcode']],
        'postcode' => [Field::TYPE_FAKE, false, ['postcode']],
        'state' => [Field::TYPE_FAKE, false, ['state']],
        'unix_time' => [Field::TYPE_FAKE, false, ['unixTime']],
        'unixtime' => [Field::TYPE_FAKE, false, ['unixTime']],
        'date' => [Field::TYPE_FAKE, false, ['dateTime']],
        'datetime' => [Field::TYPE_FAKE, false, ['dateTime']],
        'time' => [Field::TYPE_FAKE, false, ['dateTime']],
        'timezone' => [Field::TYPE_FAKE, false, ['timezone']],
        'year' => [Field::TYPE_FAKE, false, ['year']],
        'email' => [Field::TYPE_FAKE, false, ['safeEmail']],
        'domain' => [Field::TYPE_FAKE, false, ['domainName']],
        'tld' => [Field::TYPE_FAKE, false, ['tld']],
        'url' => [Field::TYPE_FAKE, false, ['url']],
        'slug' => [Field::TYPE_FAKE, false, ['slug']],
        'ipaddress' => [Field::TYPE_FAKE, false, ['ipv4']],
        'ip' => [Field::TYPE_FAKE, false, ['ipv4']],
        'user_agent' => [Field::TYPE_FAKE, false, ['userAgent']],
        'useragent' => [Field::TYPE_FAKE, false, ['userAgent']],
        'ua' => [Field::TYPE_FAKE, false, ['userAgent']],
        'credit_card' => [Field::TYPE_FAKE, false, ['creditCardNumber']],
        'creditcard' => [Field::TYPE_FAKE, false, ['creditCardNumber']],
        'color' => [Field::TYPE_FAKE, false, ['hexcolor']],
        'colour' => [Field::TYPE_FAKE, false, ['hexcolor']],
        'mime' => [Field::TYPE_FAKE, false, ['mimeType']],
        'uuid' => [Field::TYPE_FAKE, false, ['uuid']],
        'md5' => [Field::TYPE_FAKE, false, ['md5']],
        'sha1' => [Field::TYPE_FAKE, false, ['sha1']],
        'sha256' => [Field::TYPE_FAKE, false, ['sha256']],
        'locale' => [Field::TYPE_FAKE, false, ['locale']],
        'language' => [Field::TYPE_FAKE, false, ['languageCode']],
        'currency' => [Field::TYPE_FAKE, false, ['currencyCode']],
        'body' => [Field::TYPE_FAKE, false, ['text']],
        'summary' => [Field::TYPE_FAKE, false, ['text']],
    ];

    /**
     * @param string $from
     * @param int    $to
     * @param array  $options
     * @param bool   $multiple
     */
    public static function registerMapping($from, $to, array $options = [], $multiple = false)
    {
        self::$mappings[$from] = [$to, $multiple, $options];
    }

    /**
     * @param array $mappings
     */
    public static function registerMappings(array $mappings)
    {
        foreach ($mappings as $key => $mapping) {
            self::registerMapping($key, $mapping[0], $mapping[2], $mapping[1]);
        }
    }

    /**
     * @param string $type
     * @return array
     */
    public static function resolve($type)
    {
        $name = Util::normalizeName($type);
        if (preg_match("/^(is|has)_/", $name) === 1) {
            return [Field::TYPE_FAKE, false, ['boolean']];
        } elseif (isset(self::$mappings[$name])) {
            return self::$mappings[$name];
        } else {
            return false;
        }
    }
}
