<?php
namespace App;
//require_once APP_PATH . '/vendor/autoload.php';

use App\DataContainers\CardData;
use PDO;
use PDOException;
use PDOStatement;

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
        try {
            return $this->obConnection->query($sql);
        } catch (PDOException $e) {
            echo '<pre>', print_r($e->errorInfo, 1), '</pre>';
            return $e;
        }
    }

    /** Return all data from a table with given name */
    public function select_all(string $table_name): false|PDOStatement|null
    {
        $obResult = $this->query("SELECT * FROM `" . $table_name . "`");
        if ($obResult->rowCount() > 0) {
            return $obResult;
        }
        return null;
    }

    /** Insert data in table with given name
     *  Override existing cards
     */
    public function insert(string $table_name, CardData $data): void
    {
        try {
            $obStatement = $this->obConnection->prepare("INSERT INTO `" . $table_name . "`
             (CardName, InDeck, InSide, TotalDecks, UsePercent, StapleValue)
            VALUES (:CardName, :InDeck, :InSide, :TotalDecks, :UsePercent, :StapleValue)
            ON DUPLICATE KEY UPDATE
             InDeck = :InDeck,
             InSide = :InSide,
             TotalDecks = :TotalDecks,
             UsePercent = :UsePercent, 
             StapleValue = :StapleValue");
            $strCardName = $data->getName();
            $obStatement->bindParam(':CardName', $strCardName);
            $iInDeck = $data->getDeckQuantity();
            $obStatement->bindParam(':InDeck', $iInDeck);
            $iInSide = $data->getSideQuantity();
            $obStatement->bindParam(':InSide', $iInSide);
            $iTotalDecks = $data->getTotalDecks();
            $obStatement->bindParam(':TotalDecks', $iTotalDecks);
            $fUsePercent = $data->getUsePercent();
            $obStatement->bindParam(':UsePercent', $fUsePercent);
            $fStapleValue = $data->getStapleValue();
            $obStatement->bindParam(':StapleValue', $fStapleValue);
            $obStatement->execute();
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    /** Create table with predefined parameters and given name if it not exists */
    public function createTable(string $table_name): false|PDOStatement|null
    {
        $sql = "CREATE TABLE IF NOT EXISTS `" . $table_name . "` (
            CardName VARCHAR(50) NOT NULL PRIMARY KEY,
            InDeck INT NOT NULL,
            InSide INT NOT NULL,
            TotalDecks INT NOT NULL,
            UsePercent DECIMAL(6,2),
            StapleValue DECIMAL(6,2))";

        return $this->query($sql);
    }

    /** Return all formats tables' names */
    public function getTableNames(): false|PDOStatement|null
    {
        return $this->query("SHOW TABLES FROM `" . $this->strDBName . "`");
    }
}