#Shopware to Lizards and Pumpkins Connector

Not sure the api call works.

Add it to `custom/plugins` and add a path directory or directly via composer.

###composer.json

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "custom/plugins/LizardsAndPumpkinsConnector"
        }
    ],
    "require": {
        "lizards-and-pumpkins/shopware6-connector": "dev-master"
    }
}

```
