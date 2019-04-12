<?php
declare(strict_types=1);

namespace LizardsAndPumpkins\Connector\Api;

use Shopware\Core\Content\Product\ProductEntity;

class Caller
{
    public function call(ProductEntity $product): void
    {
        file_put_contents(
            '/Volumes/Web/shopware/custom/plugins/LizardsAndPumpkinsConnector/lala.log',
            var_export($product, true),
            FILE_APPEND
        );

        $productJson = json_encode([
            'sku' => $product->getProductNumber(),
            'type' => 'simple',
            'tax_class' => 'Taxable Goods',
            'attributes' => [
                'backorders' => true,
                'url_key' => 'detail/' . $product->getId(),
                'description' => $product->getDescription(),
            ],
        ]);

        $httpRequestBodyString = json_encode([
            'product_data' => $productJson,
            'data_version' => 'foo-123'
        ]);

        $url = 'https://tl.lap-demo.de/api/product_import';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/vnd.lizards-and-pumpkins.product_import.v1+json']);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($httpRequestBodyString));

        curl_exec($ch);
    }
}
