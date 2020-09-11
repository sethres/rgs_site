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

    private function GetCategoryCollection ($sql, $category = null, $collection = null) {
        $statement = $this->prepSQL($sql);

        if (!empty($category)) {
            $statement->bindValue(":Category", $category);
        }

        if (!empty($collection)) {
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

        $return = $statement->fetchAll();
        $statement->closeCursor();

        return $return;
    }

    private function GetImage ($sku) {
        if (file_exists($_SERVER['DOCUMENT_ROOT'].$this->ImagePath.$sku.'_1.jpg')) {
            return $this->ImagePath.$sku.'_1.jpg';
        } elseif ($_SERVER['DOCUMENT_ROOT'].file_exists($this->ImagePath.$sku.'.jpg')) {
            return $this->ImagePath.$sku.'.jpg';
        }

        return 'https://via.placeholder.com/3000/ffffff/212529?text=IMG+Coming+Soon';
    }

    private function BindValues ($statement, $category = null, $collection = null, $subcollection = null) {
        if (!empty($category)) {
            $statement->bindValue(":Category", $category);
        }

        if (!empty($collection)) {
            $statement->bindValue(":Collection", $collection);
        }

        if (!empty($subcollection)) {
            $statement->bindValue(":SubCollection", $subcollection);
        }
    }

    private function GetPages ($sql, $category = null, $collection = null, $subcollection = null) {
        $columns = "COUNT(DISTINCT(Prefix)) ";
        $statement = $this->prepSQL(str_replace('[columns]', $columns, $sql));

        $this->BindValues($statement, $category, $collection, $subcollection);

        try {
            $statement->execute();
        } catch (PDOException $e) {
			$this->Logger->error($type.'Category/Collection Lookup (PDO Exception)', $e);
            return ["error" => ($e->getMessage())];
        }

        if (!$statement) {
			$this->Logger->error($type.'Category/Collection Lookup (Statement)', $statement->errorInfo());
            return ["error" => $statement->errorInfo()];
        }

        $pages = $statement->fetch(PDO::FETCH_COLUMN);
        $statement->closeCursor();

        return ceil($pages / $this->PerPage);
    }

    private function GetProducts ($category = null, $collection = null, $subcollection = null, $page = 1) {
        $start = ($page - 1) * $this->PerPage;
        $columns = "Prefix, Name, SKU ";
        $sql = "SELECT [columns] 
                FROM regency_products 
                ".(!empty($category) ? " WHERE Category = :Category" : "").
                (!empty($collection) ? " AND `Collection` = :Collection " : "").
                (!empty($subcollection) ? " AND Sub_Collection = :SubCollection " : "");
        $groupBy = " GROUP BY Prefix ";
        $orderBy = " ORDER BY Prefix, Name ASC ";
        $limit = " LIMIT $start, ".$this->PerPage;

        $statement = $this->prepSQL(str_replace('[columns]', $columns, $sql).$groupBy.$orderBy.$limit);

        $this->BindValues($statement, $category, $collection, $subcollection);

        try {
            $statement->execute();
        } catch (PDOException $e) {
			$this->Logger->error($type.'Category/Collection Lookup (PDO Exception)', $e);
            return ["error" => ($e->getMessage())];
        }

        if (!$statement) {
			$this->Logger->error($type.'Category/Collection Lookup (Statement)', $statement->errorInfo());
            return ["error" => $statement->errorInfo()];
        }

        $products = $statement->fetchAll();
        $statement->closeCursor();

        foreach ($products as $k => $product) {
            $products[$k]['Image'] = $this->GetImage($product['SKU']);
        }
        $return['Results'] = $products;

        if ($page == 1) { //first page so get total pages
            $return['Pages'] = $this->GetPages($sql, $category, $collection, $subcollection);
        }

        return $return;
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
            $return['Product'] = $this->GetProducts();
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
            $return['Product'] = $this->GetProducts($category);
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
            $return['Product'] = $this->GetProducts($category, $collection);
        }

        return $return;
    }

    public function Products ($category, $collection, $subcollection, $page) {
        return [ 'Product' => $this->GetProducts($category, $collection, $subcollection, $page) ];
    }
}