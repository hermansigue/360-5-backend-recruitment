<?php 
namespace App\Modules\Api\Shopify\Services;

use App\Modules\Common\SafeRequest\Services\SafeRequestService;
class ShopifyService {
    private $BASE_URL;
    private $ACCESS_TOKEN;
    private $PATH_PRODUCTS;
    private $PATH_PRODUCTS_GRAPHQL;
    private $safeRequestService;

    public function __construct() {
        $this->BASE_URL = getenv('SHOPIFY_URL_SHOP');
        $this->ACCESS_TOKEN = getenv('SHOPIFY_API_ACCESS_TOKEN');
        $this->PATH_PRODUCTS = '/admin/api/2024-10/products.json';
        $this->PATH_PRODUCTS_GRAPHQL = '/admin/api/2024-10/graphql.json';

        $this->safeRequestService = new SafeRequestService();
    }

    public function getProducts(){
        header('Content-Type: application/json');
        $params = ['fields'=> 'id,title', 'limit' => 24,];
        $response = $this->safeRequestService->get(
            $this->getApiProducts(), 
            $params, 
            $this->getAuthHeaders(),
        );
        if (empty($response)) return "Error: Empty response from API.";
        $products = (json_decode($response))->products;
        return json_encode($products);
    }

    public function getProduct($id){
        header('Content-Type: application/json');
        $params = ['fields'=> 'id,title,variants','limit' => 1, 'ids' => $id];
        $response = $this->safeRequestService->get(
            $this->getApiProducts(), 
            $params, 
            $this->getAuthHeaders(),
        );
        if (empty($response)) return "Error: Empty response from API.";
        $products = (json_decode($response))->products;
        return json_encode($products[0] ?? null);
    }


    public function getProductsGraphQl(){
        header('Content-Type: application/json');
        $body = [
            'query' => $this->getQueryProductsGraphQl(),
            'variables' => ['productCount' => 10],
        ];
        
        $response = $this->safeRequestService->post(
            $this->getApiProductsGraphQl(),
            $body,
            $this->getAuthHeaders(),
        );
        if (empty($response)) return "Error: Empty response from API.";
        $products = (json_decode($response))->data->products->nodes;
        $products = array_map(function($product) {
            $product->id = $this->globalIdToId('Product', $product->id);
            return $product;
        }, $products);
        
        return json_encode($products);
    }

    public function getProductGraphQl($id){
        header('Content-Type: application/json');
        $body = [
            'query' => $this->getQueryProductGraphQl(),
            'variables' => ['id' => $this->toGlobalId('Product', $id)],
        ];
        
        $response = $this->safeRequestService->post(
            $this->getApiProductsGraphQl(),
            $body,
            $this->getAuthHeaders(),
        );
        if (empty($response)) return "Error: Empty response from API.";

        $product = (json_decode($response))->data->product;
        $product->id = $this->globalIdToId('Product', $product->id);
        if(sizeof($product->variants->nodes) > 0){
            $product->variants = array_map(function($variant){
                $variant->id = $this->globalIdToId('ProductVariant', $variant->id);
                $variant->inventory_quantity = $variant->inventoryQuantity;
                unset($variant->inventoryQuantity);
                return $variant;
            }, $product->variants->nodes);
        }        
        return json_encode($product);
    }

    private function getAuthHeaders() {
        return [
            'Content-Type' => 'application/json',
            'X-Shopify-Access-Token' => $this->ACCESS_TOKEN
        ];
    }

    private function getApiProducts() {
        return "{$this->BASE_URL}{$this->PATH_PRODUCTS}";
    }

    private function getApiProductsGraphQl() {
        return "{$this->BASE_URL}{$this->PATH_PRODUCTS_GRAPHQL}";
    }

    private function getQueryProductsGraphQl() {
        $query = 'query GetProducts($productCount: Int!) { products(first: $productCount) { nodes { id title } } }';
    
        return $query;
    }

    private function getQueryProductGraphQl() {
        $query = '
            query GetProductById($id: ID!) {
                product(id: $id) {
                    id
                    title
                    variants(first: 10) {
                        nodes {
                            id
                            title
                            inventoryQuantity
                        }
                    }
                }
            }';
    
        return $query;
    }

    private function toGlobalId($resourceType, $id) {
        return "gid://shopify/{$resourceType}/{$id}";
    }   

    private function globalIdToId($resourceType, $gId) {
        preg_match("/gid:\/\/shopify\/{$resourceType}\/(\d+)/", $gId, $matches);
        return (int) $matches[1];
    }
    
}