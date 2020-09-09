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

    /**
     * Filters out non-alpha characters to create a URL
     */
    private function GetURL ($text) {
        $text = \preg_replace("/[^A-Za-z0-9& ]/", '', $text);
        $text = \str_replace(['&', ' '], ['and', '-'], $text);
        $text = \strtolower($text);

        return $text;
    }

    /**
     * Accepts an array of strings and creates a key/value pair of URL/string
     */
    private function GetURLs ($data) {
        $return = [];

        foreach ($data as $key=>$value) {
            $return[$key]['URL'] = $this->GetURL($value['Value']);
            $return[$key]['Value'] = $value['Value'];
        }

        return $return;
    }

    /**
     * Takes a URL and converts it to text, reverse of what GetURL does, to use for queries
     */
    private function GetTextFromURL ($text) {
        $text = \str_replace(['and', '-'], ['&', ' '], $text);

        return $text;
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

        $results = $statement->fetchAll();

        return $this->GetURLs($results);
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

    public function Categories () {
        $sql = "SELECT DISTINCT Category AS Value
                FROM regency_products 
                ORDER BY Category";

        return ['Filter' => $this->GetCategoryCollection($sql), 'Products' => $this->GetProducts() ];
    }

    public function Collections ($categoryURL) {
        $sql = "SELECT DISTINCT Collection AS Value
                                     FROM regency_products
                                     WHERE Category = :Category
                                     ORDER BY Collection";

        $category = $this->GetTextFromURL($categoryURL);
        return ['Filter' => $this->GetCategoryCollection($sql, $category), 'Products' => $this->GetProducts(0, $category) ];
    }

    public function SubCollections ($categoryURL, $collectionURL) {
        $sql = "SELECT DISTINCT Sub_Collection AS Value
                                     FROM regency_products 
                                     WHERE Category = :Category AND Collection = :Collection
                                     ORDER BY Sub_Collection";

        $category = $this->GetTextFromURL($categoryURL);
        $collection = $this->GetTextFromURL($collectionURL);
        return ['Filter' => $this->GetCategoryCollection($sql, $category, $collection), 'Products' => $this->GetProducts(0, $category, $collection) ];
    }

    public function Products ($categoryURL, $collectionURL, $subcollectionURL) {
        $category = $this->GetTextFromURL($categoryURL);
        $collection = $this->GetTextFromURL($collectionURL);
        $subcollection = $this->GetTextFromURL($subcollectionURL);

        return [ 'Products' => $this->GetProducts(0, $category, $collection, $subcollection) ];
    }
}