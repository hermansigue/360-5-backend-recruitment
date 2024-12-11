<?php
namespace App\Modules\Api\Shopify\Controllers;

use App\Modules\Api\Shopify\Services\ShopifyService;


class ShopifyController{
    private $shopifyService;

    public function __construct() {
        $this->shopifyService = new ShopifyService();
    }

    public function getData(){
        // REST API Version
        // return $this->shopifyService->getProducts(); 

        // GraphQl Version
        return $this->shopifyService->getProductsGraphQl();

    }

    public function getDetail($id){
        // REST API Version
        // return $this->shopifyService->getProduct($id);

        // GraphQl Version
        return $this->shopifyService->getProductGraphQl($id);
    }
}