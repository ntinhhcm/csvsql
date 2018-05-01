<?php

class CSVSql {
	private $file_contents = "";
	private $table_name = "";
	private $primay_key = "";

	const CONFIG_ROW = 0;
	const CONFIG_COL = 3;
	const ITEM_ROW = 4;
	const ITEM_COL_NAME = 1;
	const ITEM_COL_KEY = 2;
	const ITEM_COL_TYPE = 3;
	const ITEM_COL_LEN = 4;
	const FILE_NAME_CSV ="csv_template.csv";

	const SQL_CREATE = "CREATE TABLE %s (" . PHP_EOL
			. "%s "
			. "\t" . "CONSTRAINT %s_primary_key PRIMARY KEY(%s)" . PHP_EOL
		 	.");";

	public function __construct	() {

	}

	public function set_file_contents($file) {
		if ( ! file_exists($file)) {
			die("File $file is not exists");
		}
		$this->file_contents = explode(PHP_EOL, file_get_contents($file));
	}

	public function set_config() {
		$this->table_name = str_getcsv($this->file_contents[self::CONFIG_ROW])[self::CONFIG_COL];
		$this->primay_key = str_getcsv($this->file_contents[self::CONFIG_ROW + 1])[self::CONFIG_COL];
	}

	public function convert() {
		$sql_item = '';
		for ($i = self::ITEM_ROW; $i < count($this->file_contents); $i++) {
			if (empty($this->file_contents[$i])) {
				continue;
			}
			$row = str_getcsv($this->file_contents[$i]);

			$not_null = '';
			if ( ! empty($row[self::ITEM_COL_KEY])) {
				$not_null = 'NOT NULL';
			}

			$len = '';
			if ( ! empty($row[self::ITEM_COL_LEN])) {
				$len = "(" . $row[self::ITEM_COL_LEN] . ")";
			}

			$sql_item .= "\t " . $row[self::ITEM_COL_NAME] . " " . $row[self::ITEM_COL_TYPE] . $len . " $not_null," . PHP_EOL;
		}
		return sprintf(self::SQL_CREATE, $this->table_name, $sql_item, $this->table_name, $this->primay_key);
	}

	public function create_csv_tepmlate() {
		$csv_contents =  ",,Table,," . PHP_EOL
						. ",,Primary,," . PHP_EOL
						. ",,,," . PHP_EOL
						. "#,Column,Key,Type,Length" . PHP_EOL;
		$file = fopen(self::FILE_NAME_CSV,"w");
		$dir = 
		fwrite($file, $csv_contents);
		fclose($file);
	}
}

if (isset($argv[1]) && isset($argv[2])) {	
	$csvFile = $argv[1];
	$option = $argv[2];

	$test = new CSVSql;
	if ($option == "template") {
		$test->create_csv_tepmlate();
		exit;
	}

	if ($option == "sql:create") {
		$test->set_file_contents($csvFile);
		$test->set_config();
		echo $test->convert();
		exit;
	}
}
