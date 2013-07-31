<?php
/**
 * Class Database - MySQL Handeling - Engine PDO;
 * Lekker he :$
 */

class Database
{	
	private $connected, $link, $result, $params = null;
	
	public function __Construct($hostname, $username, $password, $database)
	{
		if($this->connected)
		{
			return;
		}
		
		try
		{
			$this->link = new PDO("mysql:dbname={$database};host={$hostname}", $username, $password);
		}
		catch(PDOException $exception)
		{
			trigger_error($exception->getMessage());
		}
		
		$this->connected = true;
	}
	
	public function query($sql)
	{
		return $this->prepare($sql)->execute();
	}
	
	public function prepare($sql)
	{
		if(!$this->result = $this->link->prepare($sql))
		{
			return print_r($this->result->errorInfo());
		}
		
		return $this;
	}
	
	public function bind_param()
	{
		$this->params = func_get_args();
		
		return $this;
	}
	
	public function execute()
	{
		if(!$this->result->execute($this->params))
		{
			return print_r($this->result->errorInfo());
		}
		
		return new PDOResult($this->result, $this->link->lastInsertId());
	}
	
	public function insert_array($table, $array)
	{
		$values = array();
		foreach($array as $key => $value)
		{
			$values[] = "'{$value}'";
		}
	
		$key = implode(',', array_keys($array));
		$value = implode(',', $values);
		
		return $this->prepare('INSERT INTO '.$table.' ('.$key.') VALUES ('.$value.')')->execute();
	}
}

class PDOResult
{
	public $num_rows, $insert_id;
	private $result;
	
	public function __Construct($result, $insert_id)
	{
		$this->result = $result;
		
		$this->insert_id = $insert_id;
		
		$this->num_rows = $result->rowCount();
	}
	
	public function result()
	{
		return $this->result->fetchColumn();
	}
	
	public function fetch_assoc()
	{
		return $this->result->fetch(PDO::FETCH_ASSOC);
	}
}
?>
