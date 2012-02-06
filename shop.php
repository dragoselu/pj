<?php
abstract class ShopProduct
{
    const AVAILABLE = 0;
    const OUT_OF_STOCK = 1;

    private $title;
    private $producerMainName;
    private $producerFirstName;
    protected $price;
    private $discount = 0;
    private $id = 0;

    public function __construct($title, $firstName, $mainName, $price)
    {
        $this->title = $title;
        $this->producerFirstName = $firstName;
        $this->producerMainName = $mainName;
        $this->price = $price;
    }

    public function getProducerFirstName()
    {
        return $this->producerFirstName;
    }

    public function getProducerMainName()
    {
        return $this->producerMainName;
    }

    public function setDiscount($num)
    {
        $this->discount = $num;
    }

    public function getDiscount()
    {
        return $this->discount;
    }

    public function getTtitle()
    {
        return $this->title;
    }

    public function getPrice()
    {
        return ($this->price - $this->discount);
    }

    public function getProducer()
    {
        return "{$this->producerFirstName}".
            " {$this->producerMainName}";
    }

    public function getSummaryLine()
    {
        $base  = "{$this->title} ({$this->producerMainName}, ";
        $base .= "{$this->producerFirstName})";

        return $base;
    }

    public function setID($id)
    {
        $this->id = $id;
    }

    public static function getInstance($id, PDO $pdo)
    {
        $stmt = $pdo->prepare("select * from products where id=?");
        $result = $stmt->execute(array($id));
        $row = $stmt->fetch();

        if(empty($row)) { return null; }

        if($row['type'] == "book")
        {
            $product = new BookProduct(
                $row['title'],
                $row['firstname'],
                $row['mainname'],
                $row['price'],
                $row['numpages']);
        }
        else if($row['type'] == "cd")
        {
            $product = new CdProduct(
                $row['title'],
                $row['firstname'],
                $row['mainname'],
                $row['price'],
                $row['playlength']);
        }
        else
        {
            $product = new ShopProduct(
                $row['title'],
                $row['firstname'],
                $row['mainname'],
                $row['price']);
        }
        $product->setID($row['id']);
        $product->setDiscount($row['discount']);

        return $product;
    }
}

class CdProduct extends ShopProduct
{
    private $playLength = 0;

    public function __construct($title, $firstName, $mainName, $price, $playLength)
    {
        parent::__construct($title, $firstName, $mainName, $price);
        $this->playLength = $playLength;
    }

    public function getPlayLength()
    {
        return $this->playLength;
    }

    public function getTtitle()
    {
        parent::getTtitle();
    }

    public function getSummaryLine()
    {
        $base  = parent::getSummaryLine();
        $base .= ": playing time - {$this->playLength}";

        return $base;
    }
}

class BookProduct extends ShopProduct
{
    private $numPages = 0;

    public function __construct($title, $firstName, $mainName, $price, $numPages)
    {
        parent::__construct($title, $firstName, $mainName, $price);
        $this->numPages = $numPages;
    }

    public function getNumberOfPages()
    {
        return $this->numPages;
    }

    public function getSummaryLine()
    {
        $base  = parent::getSummaryLine();
        $base .= ": page count - {$this->numPages}";

        return $base;
    }

    public function getPrice()
    {
        return $this->price;
    }
}

abstract class ShopProductWriter
{
    protected $products = array();

    public function addProduct(ShopProduct $shopProduct)
    {
        $this->products[] = $shopProduct;
    }

    abstract public function write();
}

class XmlProductWriter extends ShopProductWriter
{
    public function write()
    {
        $str = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $str .= "<products>\n";
        foreach($this->products as $shopProduct)
        {
            $str .= "\t<product title=\"{$shopProduct->getTitle()}\">\n";
            $str .= "\t\t<summary>\n";
            $str .= "\t\t{$shopProduct->getSummaryLine()}\n";
            $str .= "\t\t</summary>\n";
            $str .= "\t</product>\n";
        }
        $str .= "</products>\n";
        echo $str;
    }
}

class TextProductWriter extends ShopProductWriter
{
    public function write()
    {
        $str  = "PRODUCTS:\n";
        foreach($this->products as $shopProduct)
        {
            $str .= $shopProduct->getSummaryLine()."\n";
        }
        echo $str;
    }
}
?>
