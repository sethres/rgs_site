<?php
namespace App\Application\Models;
use Psr\Container\ContainerInterface;
use Monolog\Logger;
use DateTime;
use PDO;

class ProductModel extends APIModel {
    private $PerPage = 16;
    private $ImagePath = '/images/products/';

    public function __construct(ContainerInterface $container, Logger $logger, PDO $db) {
    	parent::__construct($container, $logger, $db);
    }

    private function GetImage ($sku) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'].$this->ImagePath.$sku.'_1.jpg')) {
            
            return $this->ImagePath.$sku.'_1.jpg';
        } elseif (file_exists($_SERVER['DOCUMENT_ROOT'].$this->ImagePath.$sku.'.jpg')) {
            return $this->ImagePath.$sku.'.jpg';
        }

        return 'https://via.placeholder.com/3000/ffffff/212529?text=IMG+Coming+Soon';
    }

    private function BindParams ($category = null, $collection = null, $subcollection = null) {
        $params = [];
        if (!empty($category)) {
            $params[':Category'] = $category;
        }

        if (!empty($collection)) {
            $params[':Collection'] = $collection;
        }

        if (!empty($subcollection)) {
            $params[':SubCollection'] = $subcollection;
        }

        return $params;
    }

    private function GetPages ($sql, $category = null, $collection = null, $subcollection = null) {
        $columns = "COUNT(DISTINCT(Prefix)) ";
        $params = $this->BindParams($category, $collection, $subcollection);
        $pages = $this->GetResults(str_replace('[columns]', $columns, $sql), $params, 'Product Pages', PDO::FETCH_COLUMN, false);

        return ceil($pages / $this->PerPage);
    }

    private function GetProducts ($category = null, $collection = null, $subcollection = null, $page = 1, $getPages = false) {
        $start = (intval($page) - 1) * $this->PerPage;
        $columns = "Prefix, MIN(Name) AS Name, MIN(SKU) AS SKU, MIN(Color) AS Color, MIN(Configuration) AS Configuration ";
        $sql = "SELECT [columns] 
                FROM regency_products 
                ".(!empty($category) ? " WHERE Category = :Category" : "").
                (!empty($collection) ? " AND `Collection` = :Collection " : "").
                (!empty($subcollection) ? " AND Sub_Collection = :SubCollection " : "");
        $groupBy = " GROUP BY Prefix ";
        $orderBy = " ORDER BY Prefix";
        $limit = " LIMIT $start, ".$this->PerPage;
        $params = $this->BindParams($category, $collection, $subcollection);
        $products = $this->GetResults(str_replace('[columns]', $columns, $sql).$groupBy.$orderBy.$limit, $params, 'Product Lookup');

        foreach ($products as $k => $product) {
            $products[$k]['Image'] = $this->GetImage($product['SKU']);
        }
        $return['Results'] = $products;

        if ($page == 1 || $getPages) { //first page so get total pages
            $return['Pages'] = $this->GetPages($sql, $category, $collection, $subcollection);
        }

        return $return;
    }

    public function Categories () {
        $sql = "SELECT DISTINCT Category AS Value
                FROM regency_products
                WHERE Category != ''
                ORDER BY Category";

        $return = [
            'Filter' => $this->GetResults($sql, [], 'Category Lookup')
        ];

        return $return;
    }

    public function Collections ($category) {
        $sql = "SELECT DISTINCT `Collection` AS Value
                FROM regency_products
                WHERE Category = :Category AND `Collection` != ''
                ORDER BY `Collection`";
                
        $return = [
            'Filter' => $this->GetResults($sql, [ ':Category' => $category ], 'Collection Lookup')
        ];

        return $return;
    }

    public function SubCollections ($category, $collection) {
        $sql = "SELECT DISTINCT Sub_Collection AS Value
                FROM regency_products 
                WHERE Category = :Category AND `Collection` = :Collection
                    AND Sub_Collection != ''
                ORDER BY Sub_Collection";
        
        $return = [
            'Filter' => $this->GetResults($sql, [ ':Category' => $category, ':Collection' => $collection ], 'Subcollection Lookup')
        ];

        return $return;
    }

    public function Products ($category, $collection, $subcollection, $page, $getPages) {
        return [ 'Product' => $this->GetProducts($category, $collection, $subcollection, $page, $getPages) ];
    }

    private function Colors ($prefix) {
        $sql = "SELECT DISTINCT Color 
            FROM regency_products 
            WHERE Prefix = :Prefix
            ORDER BY Color";
        
        return $this->GetResults($sql, [':Prefix' => $prefix], 'Product Color Lookup', PDO::FETCH_COLUMN);
    }

    private function Configurations ($prefix) {
        $sql = "SELECT DISTINCT `Configuration`
                FROM regency_products 
                WHERE Prefix = :Prefix
                ORDER BY `Configuration`";
        
        return $this->GetResults($sql, [':Prefix' => $prefix], 'Product Configuration Lookup', PDO::FETCH_COLUMN);
    }

    private function DefaultOptions ($prefix) {
        $sql = "SELECT DISTINCT `Configuration`, Color
            FROM regency_products 
            WHERE Prefix = :Prefix
            ORDER BY Color, `Configuration`
            LIMIT 1";

        return $this->GetResults($sql, [':Prefix' => $prefix], 'Product Default Configuration Lookup', PDO::FETCH_ASSOC, false);
    }

    public function ProductOptions ($prefix, $getDefault) {
        $return = [];
        $return['Colors'] = $this->Colors($prefix);
        $return['Configurations'] = $this->Configurations($prefix);

        if ($getDefault) {
            //get the default (1st) color/configuration option
            $return['DefaultConfig'] = $this->DefaultOptions($prefix);
        }

        return $return;
    }

    private function ProductData ($prefix, $color, $configuration) {
        $sql = "SELECT * 
            FROM regency_products 
            WHERE Prefix = :Prefix AND Color = :Color AND `Configuration` = :Config";

        return $this->GetResults($sql, [':Prefix' => $prefix, ':Color' => $color, ':Config' => $configuration], 'Product Default Configuration Lookup', PDO::FETCH_ASSOC, false);
    }

    private function ProductImages ($sku) {
        return glob($this->ImagePath.$sku.'*.jpg');
    }

    public function Product ($prefix, $color, $configuration) {
        $return = [];

        $return['Product'] = $this->ProductData($prefix, $color, $configuration);
        if (array_key_exists('SKU', $return['Product'])) {
            $return['Images'] = $this->ProductImages($return['Product']['SKU']);
        }

        return $return;
    }
}