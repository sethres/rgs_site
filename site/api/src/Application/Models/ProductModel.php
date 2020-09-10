<?php
namespace App\Application\Models;
use Psr\Container\ContainerInterface;
use Monolog\Logger;
use DateTime;
use PDO;

class ProductModel extends APIModel {
    private $RowsPerPage = 16;

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

        /*
        if (file_exists('images/products/'.$row['SKU'].'_1.jpg')) {
                            ?>
                            <img src="images/products/<?php echo $row['SKU']?>_1.jpg" style="object-fit: contain; padding: 1rem; height: 20rem; width: 100%" class="img-responsive" alt="<?php echo $row['Prefix']?>.jpg">
                            <?php
                        }

                        elseif (file_exists('images/products/'.$row['SKU'].'.jpg')){
                            ?>
                            <img src="images/products/<?php echo $row['SKU']?>.jpg" style="object-fit: contain; padding: 1rem; height: 20rem; width: 100%" class="img-responsive" alt="<?php echo $row['Prefix']?>.jpg">
                            <?php
                        }

                        else{
                            ?>
                            <img src="https://via.placeholder.com/3000/ffffff/212529?text=IMG+Coming+Soon" style="object-fit: contain; padding: 1rem; height: 20rem; width: 100%" class="img-responsive" alt="<?php echo $row['Prefix']?>.jpg">
                            <?php
                        }*/

        return $statement->fetchAll();
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