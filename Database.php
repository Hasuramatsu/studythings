<?php

class Database
{
    private string $strServer = "localhost";
    private string $strUser = "scrapper";
    private string $strPassword = "MF@Icf]1!fn0u6vt";
    private string $strDBName = "card_data";
    private PDO $obConnection;

    public function __construct()
    {
        try {
            $this->obConnection = new PDO("mysql:host=$this->strServer;dbname=$this->strDBName", $this->strUser, $this->strPassword);
            $this->obConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //echo "Connected successfully\n";
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /** Execute given query in existing connection and return result*/
    public function query(string $sql)
    {
        try {return $this->obConnection->query($sql);}
        catch (PDOException $e) {echo '<pre>', print_r($e->errorInfo, 1), '</pre>';}
    }

    /** Return all data from a table with given name */
    public function select_all(string $table_name): false|PDOStatement|null
    {
        $obResult = $this->query("SELECT * FROM `".$table_name."`");
        if ($obResult->rowCount() > 0)
        {
            return $obResult;
        }
        return null;
    }

    /** Insert data in table with given name
     *  Override existing cards
     */
    public function insert(string $table_name ,array $data): void
    {
        try {
            $obStatement = $this->obConnection->prepare("INSERT INTO `".$table_name."`
             (CardName, InDeck, InSide, TotalDecks, UsePercent, StapleValue)
            VALUES (:CardName, :InDeck, :InSide, :TotalDecks, :UsePercent, :StapleValue)
            ON DUPLICATE KEY UPDATE
             InDeck = :InDeck,
             InSide = :InSide,
             TotalDecks = :TotalDecks,
             UsePercent = :UsePercent, 
             StapleValue = :StapleValue");
            $obStatement->bindParam(':CardName', $data['CardName']);
            $obStatement->bindParam(':InDeck', $data['InDeck']);
            $obStatement->bindParam(':InSide', $data['InSide']);
            $obStatement->bindParam(':TotalDecks', $data['TotalDecks']);
            $obStatement->bindParam(':UsePercent', $data['UsePercent']);
            $obStatement->bindParam(':StapleValue', $data['StapleValue']);
            $obStatement->execute();
        } catch (PDOException $e) {
          echo $e->getMessage();
        }
    }

    /** Create table with predefined parameters and given name if it not exists */
    public function create_table(string $table_name): false|PDOStatement|null
    {
            $sql = "CREATE TABLE IF NOT EXISTS `".$table_name."` (
            CardName VARCHAR(50) NOT NULL PRIMARY KEY,
            InDeck INT NOT NULL,
            InSide INT NOT NULL,
            TotalDecks INT NOT NULL,
            UsePercent DECIMAL(6,2),
            StapleValue DECIMAL(6,2))";

        return $this->query($sql);
    }

    /** Return all formats tables' names */
    public function get_table_names(): false|PDOStatement|null
    {
        return $this->query("SHOW TABLES FROM `".$this->strDBName."`");
    }
}