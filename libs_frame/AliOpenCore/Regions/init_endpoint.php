<?php
/**
 * 初始化终端节点
 * User: 姜伟
 * Date: 2019/1/2 0002
 * Time: 14:19
 */
$endpointData = file_get_contents(__DIR__ . '/endpoints.json');

$endpoints = [];
foreach ($endpointData['Endpoints'] as $eEndpoint) {
    $productDomains = [];
    foreach ($eEndpoint['Products'] as $eProduct) {
        $productDomains[] = new \AliOpenCore\Regions\ProductDomain($eProduct['ProductName'], $eProduct['DomainName']);
    }

    $endpoints[] = new \AliOpenCore\Regions\Endpoint($eEndpoint['RegionId'], [
        0 => $eEndpoint['RegionId'],
    ], $productDomains);
}
\AliOpenCore\Regions\EndpointProvider::setEndpoints($endpoints);