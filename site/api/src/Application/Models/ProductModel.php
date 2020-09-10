<?php
namespace App\Application\Models;
use Psr\Container\ContainerInterface;
use Monolog\Logger;
use DateTime;
use PDO;

class ProductModel extends APIModel {
    private $RowsPerPage = 16;
    private $ImagePath = '/images/products/';

    public function __construct(ContainerInterface $container, Logger $logger, PDO $db) {
    	parent::__construct($container, $logger, $db);
    }

    private function GetCategoryCollection ($sql, $category = null, $collection = null) {
        $statement = $this->prepSQL($sql);

        if ($category !== null) {
            $statement->bindValue(":Category", $category);
        }

        if ($collection !== null) {
            $statement->bindValue(":Collection", $collection);
        }

        try {
            $statement->execute();
        } catch (PDOException $e) {
			$this->Logger->error('Category/Collection Lookup (PDO Exception)', $e);
            return ["error" => ($e->getMessage())];
        }

        if (!$statement) {
			$this->Logger->error('Category/Collection Lookup (Statement)', $statement->errorInfo());
            return ["error" => $statement->errorInfo()];
        }

        return $statement->fetchAll();
    }

    private function GetImage ($sku) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'].$this->ImagePath.$sku.'_1.jpg')) {
            return $this->ImagePath.$sku.'_1.jpg';
        } elseif ($_SERVER['DOCUMENT_ROOT'].file_exists($this->ImagePath.$sku.'.jpg')) {
            return $this->ImagePath.$sku.'.jpg';
        }

        return 'https://via.placeholder.com/3000/ffffff/212529?text=IMG+Coming+Soon';
    }

    private function GetProducts ($start_from = 0, $category = null, $collection = null, $subcollection = null) {
        $sql = "SELECT Prefix, Name, SKU 
                FROM regency_products 
                ".($category !== null ? " WHERE Category = :Category" : "").
                ($collection !== null ? " AND `Collection` = :Collection " : "").
                ($subcollection !== null ? " AND Sub_Collection = :SubCollection " : "")."
                GROUP BY Prefix 
                ORDER BY Prefix, Name ASC 
                LIMIT $start_from, ".$this->RowsPerPage;

        $statement = $this->prepSQL($sql);

        if ($category !== null) {
            $statement->bindValue(":Category", $category);
        }

        if ($collection !== null) {
            $statement->bindValue(":Collection", $collection);
        }

        if ($subcollection !== null) {
            $statement->bindValue(":SubCollection", $subcollection);
        }

        try {
            $statement->execute();
        } catch (PDOException $e) {
			$this->Logger->error('Category/Collection Lookup (PDO Exception)', $e);
            return ["error" => ($e->getMessage())];
        }

        if (!$statement) {
			$this->Logger->error('Category/Collection Lookup (Statement)', $statement->errorInfo());
            return ["error" => $statement->errorInfo()];
        }

        $products = $statement->fetchAll();
        foreach ($products as $k => $product) {
            $products[$k]['Image'] = $this->GetImage($product['SKU']);
        }

        return $products;
    }

    public function Categories ($getProducts = true) {
        $sql = "SELECT DISTINCT Category AS Value
                FROM regency_products
                WHERE Category != ''
                ORDER BY Category";

        $return = [
            'Filter' => $this->GetCategoryCollection($sql)
        ];

        if ($getProducts) {
            $return['Products'] = $this->GetProducts();
        }

        return $return;
    }

    public function Collections ($category, $getProducts = true) {
        $sql = "SELECT DISTINCT `Collection` AS Value
                FROM regency_products
                WHERE Category = :Category AND `Collection` != ''
                ORDER BY `Collection`";
        
        $return = [
            'Filter' => $this->GetCategoryCollection($sql, $category)
        ];

        if ($getProducts) {
            $return['Products'] = $this->GetProducts(0, $category);
        }

        return $return;
    }

    public function SubCollections ($category, $collection, $getProducts = true) {
        $sql = "SELECT DISTINCT Sub_Collection AS Value
                FROM regency_products 
                WHERE Category = :Category AND `Collection` = :Collection
                    AND Sub_Collection != ''
                ORDER BY Sub_Collection";
        
        $return = [
            'Filter' => $this->GetCategoryCollection($sql, $category, $collection)
        ];

        if ($getProducts) {
            $return['Products'] = $this->GetProducts(0, $category, $collection);
        }

        return $return;
    }

    public function Products ($category, $collection, $subcollection) {
        return [ 'Products' => $this->GetProducts(0, $category, $collection, $subcollection) ];
    }
}