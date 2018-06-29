# CityScan addresses API

PHP client for CityScan. Allow you to activate or deactivate addresses.

## Instanciate

Instanciate a new CityScan API with API key (required) and an environment (optional), prod by default or preprod. 

```php
$cs = new \CityScan\CityScan('api_key', 'preprod');
```

## Errors

Throws an exception on error. Error message is filled with the message returned by the API. If needed, the error code is also present.

## Activate an address

### By road

```php
function activateAddressByRoad($road, $postal_code, $city, $external_id = null){}

$address = $cs->activateAddressByRoad('23, rue sébastien mercier', 75015, 'Paris', 'ayctor');
```

Returns:

```json
{
    "status": 0,
    "content": {
        "activation": {
            "source": "IGN",
            "lat": 48.8445,
            "lon": 2.2786,
            "address": {
                "id": 74752,
                "geoloc_id": 69849,
                "route": "23, rue Sébastien Mercier",
                "postal_code": "75015",
                "city": "Paris"
            },
            "externalAddressId": "ayctor"
        }
    }
}
```

### By GPS

```php
function activateAddressByGPS($latitude, $longitude, $external_id = null){}

$address = $cs->activateAddressByGPS(48.8445, 2.2786);
```
Returns:

```json
{
    "activation": {
        "source": "IGN",
        "lat": "48.8445",
        "lon": "2.2786",
        "address": {
            "id": 74728,
            "geoloc_id": 69849,
            "route": "28 rue sebastien mercier",
            "postal_code": "75015",
            "city": "Paris"
        },
        "externalAddressId": null
    }
}
```

## Deactivate an address

```php
function deactivateAddress($id, $isExternal = false)

$res_road = $cs->deactivateAddress('ayctor', true);

$res_gps = $cs->deactivateAddress(74728);
```
Returns:
```json
{
    "deactivation": "true"
}
```