<?php
/**
 * Database sınıfı - Veritabanı işlemlerini gerçekleştiren sınıf
 * Singleton pattern kullanılarak tasarlanmıştır
 */
class Database {
    private static $instance = null;
    private $connection;
    private $statement;
    
    /**
     * Constructor - Veritabanı bağlantısını başlatır
     */
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            if (DEBUG) {
                die("Veritabanı bağlantı hatası: " . $e->getMessage());
            } else {
                die("Veritabanı bağlantısı sağlanamadı.");
            }
        }
    }
    
    /**
     * Singleton pattern - Tek bir veritabanı bağlantısı oluşturur
     * 
     * @return Database Veritabanı sınıfı örneği
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }
    
    /**
     * SQL sorgusu çalıştırır
     * 
     * @param string $query SQL sorgusu
     * @param array $params Parametreler
     * @return Database Sorgu sonucu
     */
    public function query($query, $params = []) {
        $this->statement = $this->connection->prepare($query);
        $this->statement->execute($params);
        return $this;
    }
    
    /**
     * Tüm sonuçları döndürür
     * 
     * @return array Sonuç kümesi
     */
    public function getAll() {
        return $this->statement->fetchAll();
    }
    
    /**
     * Tek bir satır döndürür
     * 
     * @return array|false Sonuç satırı
     */
    public function getRow() {
        return $this->statement->fetch();
    }
    
    /**
     * Tek bir değer döndürür
     * 
     * @return mixed Sonuç değeri
     */
    public function getValue() {
        $result = $this->statement->fetch(PDO::FETCH_NUM);
        return $result[0] ?? null;
    }
    
    /**
     * Etkilenen satır sayısını döndürür
     * 
     * @return int Etkilenen satır sayısı
     */
    public function rowCount() {
        return $this->statement->rowCount();
    }
    
    /**
     * Son eklenen kaydın ID'sini döndürür
     * 
     * @return string Son eklenen kaydın ID'si
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Transaction başlatır
     * 
     * @return bool Başarılıysa true
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Transaction'ı tamamlar
     * 
     * @return bool Başarılıysa true
     */
    public function commit() {
        return $this->connection->commit();
    }
    
    /**
     * Transaction'ı geri alır
     * 
     * @return bool Başarılıysa true
     */
    public function rollBack() {
        return $this->connection->rollBack();
    }
    
    /**
     * Tekil kayıt ekler
     * 
     * @param string $table Tablo adı
     * @param array $data Eklenecek veriler
     * @return int|bool Son eklenen kaydın ID'si veya başarısızsa false
     */
    public function insert($table, $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $query = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        $this->statement = $this->connection->prepare($query);
        $result = $this->statement->execute(array_values($data));
        
        if ($result) {
            return $this->connection->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Kayıt günceller
     * 
     * @param string $table Tablo adı
     * @param array $data Güncellenecek veriler
     * @param string $where Koşul (WHERE)
     * @param array $params Koşul parametreleri
     * @return int Etkilenen satır sayısı
     */
    public function update($table, $data, $where, $params = []) {
        $sets = [];
        
        foreach (array_keys($data) as $column) {
            $sets[] = "{$column} = ?";
        }
        
        $setStr = implode(', ', $sets);
        
        $query = "UPDATE {$table} SET {$setStr} WHERE {$where}";
        
        $this->statement = $this->connection->prepare($query);
        $this->statement->execute(array_merge(array_values($data), $params));
        
        return $this->statement->rowCount();
    }
    
    /**
     * Kayıt siler
     * 
     * @param string $table Tablo adı
     * @param string $where Koşul (WHERE)
     * @param array $params Koşul parametreleri
     * @return int Etkilenen satır sayısı
     */
    public function delete($table, $where, $params = []) {
        $query = "DELETE FROM {$table} WHERE {$where}";
        
        $this->statement = $this->connection->prepare($query);
        $this->statement->execute($params);
        
        return $this->statement->rowCount();
    }
    
    /**
     * Tek bir kayıt getirir
     * 
     * @param string $table Tablo adı
     * @param string $where Koşul (WHERE)
     * @param array $params Koşul parametreleri
     * @param string $columns Sütunlar
     * @return array|false Sonuç satırı
     */
    public function getOne($table, $where, $params = [], $columns = '*') {
        $query = "SELECT {$columns} FROM {$table} WHERE {$where} LIMIT 1";
        
        $this->statement = $this->connection->prepare($query);
        $this->statement->execute($params);
        
        return $this->statement->fetch();
    }
    
    /**
     * Birden fazla kayıt getirir
     * 
     * @param string $table Tablo adı
     * @param string $where Koşul (WHERE)
     * @param array $params Koşul parametreleri
     * @param string $columns Sütunlar
     * @param string $orderBy Sıralama
     * @param string $limit Limit
     * @return array Sonuç kümesi
     */
    public function getMany($table, $where = '', $params = [], $columns = '*', $orderBy = '', $limit = '') {
        $query = "SELECT {$columns} FROM {$table}";
        
        if (!empty($where)) {
            $query .= " WHERE {$where}";
        }
        
        if (!empty($orderBy)) {
            $query .= " ORDER BY {$orderBy}";
        }
        
        if (!empty($limit)) {
            $query .= " LIMIT {$limit}";
        }
        
        $this->statement = $this->connection->prepare($query);
        $this->statement->execute($params);
        
        return $this->statement->fetchAll();
    }
    
    /**
     * Kayıt sayısı getirir
     * 
     * @param string $table Tablo adı
     * @param string $where Koşul (WHERE)
     * @param array $params Koşul parametreleri
     * @return int Kayıt sayısı
     */
    public function count($table, $where = '', $params = []) {
        $query = "SELECT COUNT(*) FROM {$table}";
        
        if (!empty($where)) {
            $query .= " WHERE {$where}";
        }
        
        $this->statement = $this->connection->prepare($query);
        $this->statement->execute($params);
        
        return (int) $this->statement->fetchColumn();
    }
}
?> 