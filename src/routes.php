<?php
use App\Modules\Api\Shopify\Controllers\ShopifyController;
use App\Modules\Web\Home\Controllers\HomeController;

    function route($path) {
        # Web Apps
        if ($path === 'home') { return (new HomeController())->index();}
        # End of Web Apps


        # API Shopify Products Route
        if ($path === 'api/shopify/products') { return (new ShopifyController())->getData();}
    
        if (preg_match('#^api/shopify/products/(\d+)$#', $path, $matches)) {
            // Ekstrak ID
            $id = $matches[1];
            return (new ShopifyController())->getDetail($id);
        }
        # End Of API Shopify Products Route
    
        return "404 - Page Not Found";
    
    }
