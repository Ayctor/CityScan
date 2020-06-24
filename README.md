# CityScan addresses API

PHP client for CityScan. Allow you to activate or deactivate addresses.

## Install

```bash
composer require ayctor/cityscan
```

## Instanciate

Instanciate a new CityScan API with API key (required), client key (required for report) and an environment (optional), prod by default or preprod. 

Be carefull, the function signature has changed with the client key added after api key.

```php
$cs = new \CityScan\CityScan('api_key', 'client_key', 'preprod');
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
```

### By GPS

```php
function activateAddressByGPS($latitude, $longitude, $external_id = null){}

$address = $cs->activateAddressByGPS(48.8445, 2.2786);
```
Returns:

```json
{
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

## Reactivate addresses

```php
function reactivateAddresses($ids, $isExternal = false){}

$cs->reactivateAddresses(['ayctor', 'digibox'], true);

$cs->reactivateAddresses(['ayctor', 'digibox'], true);

$cs->reactivateAddresses([74728, 74729]);
```
Returns:
```json
{
  "status": 0,
  "content": {
    "addresses": [
      {
          "id": 74728,
          "route": "23, rue Sébastien Mericer",
          "postalCode": "75015",
          "city": "Paris",
          "externalAddressId": "ayctor",
          "active": true,
          "activation": "2020-01-03 18:42:47",
          "deactivation": null,
          "lastSeen": null,
          "lat": 48.8445, 
          "lon": 2.2786
      },
      {
          "id": 74729,
          "route": "23, rue Sébastien Mericer",
          "postalCode": "75015",
          "city": "Paris",
          "externalAddressId": "digibox",
          "active": true,
          "activation": "2020-01-03 18:42:47",
          "deactivation": null,
          "lastSeen": null,
          "lat": 48.8445, 
          "lon": 2.2786
      }
    ]
  }
}
```

## Get adresses

### Get active adresses

```php
function getActives()

$adresses = $cs->getActives();
```
Returns:
```json
{
  "status": 0,
  "content": {
    "count": 12,
    "addresses": [
      {
        "id": 68401,
        "route": "73 rue lecourbe",
        "postalCode": "75015",
        "city": "Paris",
        "externalAddressId": null,
        "active": true,
        "activation": "2019-01-02 09:43:48",
        "deactivation": null,
        "lat": 48.843331,
        "lon": 7.230364
      }, ...
    ]
  }
}
```

### Get all adresses

```php
function getAll()

$adresses = $cs->getAll();
```
Returns:
```json
{
  "status": 0,
  "content": {
    "count": 12,
    "addresses": [
      {
        "id": 68401,
        "route": "73 rue lecourbe",
        "postalCode": "75015",
        "city": "Paris",
        "externalAddressId": null,
        "active": true,
        "activation": "2019-01-02 09:43:48",
        "deactivation": null,
        "lat": 48.843331,
        "lon": 7.230364
      }, ...
    ]
  }
}
```

### Get activated

Get the adresses activated between two dates. If null is sent for one of the dates, no limit is applied.

```php
function getActivated($start = null, $end = null)

$adresses = $cs->getActivated('2018-01-01','2018-02-01');
```
Returns:
```json
{
  "status": 0,
  "content": {
    "count": 12,
    "addresses": [
      {
        "id": 68401,
        "route": "73 rue lecourbe",
        "postalCode": "75015",
        "city": "Paris",
        "externalAddressId": null,
        "active": true,
        "activation": "2019-01-02 09:43:48",
        "deactivation": null,
        "lat": 48.843331,
        "lon": 7.230364
      }, ...
    ]
  }
}
```

### Get billed

Get the adresses billed between two dates. If null is sent for one of the dates, no limit is applied.

```php
function getActivated($start = null, $end = null)

$adresses = $cs->getActivated('2018-01-01','2018-02-01');
```
Returns:
```json
{
  "status": 0,
  "content": {
    "count": 12,
    "addresses": [
      {
        "id": 68401,
        "route": "73 rue lecourbe",
        "postalCode": "75015",
        "city": "Paris",
        "externalAddressId": null,
        "active": true,
        "activation": "2019-01-02 09:43:48",
        "deactivation": null,
        "lat": 48.843331,
        "lon": 7.230364
      }, ...
    ]
  }
}
```

### Get deactivated

Get the adresses deactivated between two dates. If null is sent for one of the dates, no limit is applied.

```php
function getDeactivated($start = null, $end = null)

$adresses = $cs->getDeactivated('2018-01-01','2018-02-01');
```
Returns:
```json
{
  "status": 0,
  "content": {
    "count": 12,
    "addresses": [
      {
        "id": 68401,
        "route": "73 rue lecourbe",
        "postalCode": "75015",
        "city": "Paris",
        "externalAddressId": null,
        "active": true,
        "activation": "2019-01-02 09:43:48",
        "deactivation": null,
        "lat": 48.843331,
        "lon": 7.230364
      }, ...
    ]
  }
}
```

## Get report

```php
function report($id, $isExternal = false)

$report = $cs->report('ayctor', true);

```
Returns:
```json
{
    "reportId": "51EA1BE0-6D13-1234-4E78-3A24BAEED2F0",
    "filename": "Rapport CityScan -  23 rue Sébastien Mercier - 75015 Paris.pdf"
}
```