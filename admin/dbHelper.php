<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php'; // Database setting constants [DB_HOST, DB_NAME, DB_USERNAME, DB_PASSWORD]
class dbHelper {
    private $db;
    private $err;
    function __construct() {
        $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8';
        try {
            $this->db = new PDO($dsn, DB_USERNAME, DB_PASSWORD, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
        } catch (PDOException $e) {
            $response["status"] = "error";
            $response["message"] = 'Connection failed: ' . $e->getMessage();
            $response["data"] = null;
            //echoResponse(200, $response);
            exit;
        }
    }
    function query($sql){
        return $this->db->query($sql);
    }
    function check_column($column, $table){
        $q = $this->prepare("DESCRIBE ".$table);
        $q->execute();
        $table_fields = $q->fetchAll(PDO::FETCH_COLUMN);
        if(in_array($column, $table_fields)){
            return true;
        }
        else{
            return false;
        }
    }
    function select($table, $columns, $where, $order){
        try{
            $a = array();
            $w = "";
            foreach ($where as $key => $value) {
                $w .= " and " .$key. " like :".$key;
                $a[":".$key] = $value;
            }
            $stmt = $this->db->prepare("select ".$columns." from ".$table." where 1=1 ". $w." ".$order);
            $stmt->execute($a);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if(count($rows)<=0){
                $response["status"] = "warning";
                $response["message"] = "No data found.";
            }else{
                $response["status"] = "success";
                $response["message"] = "Data selected from database";
            }
                $response["data"] = $rows;
        }catch(PDOException $e){
            $response["status"] = "error";
            $response["message"] = 'Select Failed: ' .$e->getMessage();
            $response["data"] = null;
        }
        return $response;
    }
	
	function prepare($query) {
		$response = $this->db->prepare($query);
		return $response;
	}
  	function exec($query) {
		$stmt = $this->db->prepare($query);
		$stmt->execute();
		$id = $this->db->lastInsertId();
		return $id;
	}
	

}

?>
