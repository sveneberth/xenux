<?php
class XenuxDB
{
	private $db;
	private $lastQuerys	= null;
	private $querys		= array();

	private $columnQuote = "`";
	private $stringQuote = "'";
	
	public function __construct()
	{
		$this->db = db::getConnection();
	}


	
	/**
	* query:
	* Query successfull:
	* 	return response
	* Query failed:
	* 	echo errormessage / return false
	*/
	public function query($statement)
	{
		try
		{
			$statement .= ';';

			$this->setLastQuery($statement);
			if (!(defined('DEBUG') && DEBUG == true))
				log::writeLog('DB Query: ' . $statement);

			$result = $this->db->query($statement);
		
			if($result === false)
			{
				throw new Exception("MySQL Error: (" . $this->db->errno . ") " . $this->db->error . ' - Failed Statement: "' . $statement . '"');
			}
		
			return $result;
		}
		catch (Exception $e)
		{
			log::setDBError($e);
			echo($e->getMessage());
			return false;
		}
	}


	private function column_push($columns)
	{
		if ($columns == '*')
		{
			return $columns;
		}

		if (is_string($columns))
		{
			$columns = array($columns);
		}

		$stack = array();

		foreach ($columns as $key => $value)
		{
			preg_match('/([a-zA-Z0-9_\-\.]*)\s*\(([a-zA-Z0-9_\-]*)\)/i', $value, $match);
			# column(as-column)

			if(strpos($value, '#') === 0)
			{
				preg_match('/(#?)([a-zA-Z0-9_\-]*)\(([a-zA-Z0-9_\-\.]*)(?:\,\s\'([a-zA-Z0-9_\-\.\,\:\% ]*)\')?\)(?:\(([a-zA-Z0-9_\-\.]*)\))?/i', $value, $fn_match);

				if(isset($fn_match[2], $fn_match[3]) && !empty($fn_match[2]) && !empty($fn_match[3]))
				{
					array_push($stack, $fn_match[2] . '(' . $this->quoteColumn($fn_match[3]) . (isset($fn_match[4]) && !empty($fn_match[4]) ? ", '{$fn_match[4]}'" : '') . ')' . (isset($fn_match[5]) ? ' AS '.$this->quoteColumn($fn_match[5]) : ''));
					continue;
				}
			}


			if (isset($match[1], $match[2])) // if AS desirable
			{
				array_push($stack, $this->quoteColumn($match[1]) . ' AS ' . $this->quoteColumn($match[2]));
			}
			else
			{
				array_push($stack, $this->quoteColumn($value));
			}
		}

		return implode($stack, ',');
	}

	
	private function _inner_conjunct($data, $conjunctor, $outer_conjunctor)
	{
		$haystack = array();

		foreach ($data as $value)
		{
			$haystack[] = '(' . $this->_data_implode($value, $conjunctor) . ')';
		}

		return implode($outer_conjunctor . ' ', $haystack);
	}

	private function _getCondition($column, $key, $value)
	{
		$type = gettype($value);

		switch ($type)
		{
			case 'NULL':
				return $column . ' IS NULL';
				break;
			case 'array':
				return $column . ' IN (' . $this->quoteStringArray($value) . ')';
				break;
			case 'integer':
			case 'double':
				return $column . ' = ' . $value;
				break;
			case 'boolean':
				return $column . ' = ' . ($value ? '1' : '0');
				break;
			case 'string':
				return $column . ' = ' . $this->fn_quote($key, $value);
				break;
		}
	}

	private function _data_implode($data, $conjunctor, $outer_conjunctor = null)
	{
		$wheres = array();

		foreach ($data as $key => $value)
		{
			$type = gettype($value);

			if (
				preg_match("/^(AND|OR)\s*#?/i", $key, $relation_match) &&
				$type == 'array'
			)
			{
				$wheres[] = 0 !== count(array_diff_key($value, array_keys(array_keys($value)))) ?
					'(' . $this->_data_implode($value, ' ' . $relation_match[1]) . ')' :
					'(' . $this->_inner_conjunct($value, ' ' . $relation_match[1], $conjunctor) . ')';
			}
			else
			{				
				preg_match('/(#{0,2})([\w\.]+)(\(([a-zA-Z0-9_\-]*)\))?(\[(\>|\>\=|\<|\<\=|\!|\<\>|\>\<|\!?~)\])?/i', $key, $match);

				$column = $this->quoteColumn($match[2]);

				if (isset($match[3]) && !empty($match[3]))
				{
					$column = $match[2] . '(' . $this->quoteColumn($match[4]) . ')';
				}

				if (isset($match[6]))
				{
					$operator = $match[6];

					if ($operator == '!')
					{
						$wheres[] = $this->_getCondition($column, $key, $value);
					}

					if ($operator == '<>' || $operator == '><')
					{
						if ($type == 'array')
						{
							if ($operator == '><')
							{
								$column .= ' NOT';
							}

							if (is_numeric($value[0]) && is_numeric($value[1]))
							{
								$wheres[] = '(' . $column . ' BETWEEN ' . $value[0] . ' AND ' . $value[1] . ')';
							}
							else
							{
								$wheres[] = '(' . $column . ' BETWEEN ' . $this->quoteString($value[0]) . ' AND ' . $this->quoteString($value[1]) . ')';
							}
						}
					}

					if ($operator == '~' || $operator == '!~')
					{
						if ($type == 'string')
						{
							$value = array($value);
						}

						if (!empty($value))
						{
							$like_clauses = array();

							foreach ($value as $item)
							{
								if ($operator == '!~')
								{
									$column .= ' NOT';
								}

								if (preg_match('/^(?!%).+(?<!%)$/', $item))
								{
									$item = '%' . $item . '%';
								}

								$like_clauses[] = $column . ' LIKE ' . $this->fn_quote($key, $item);
							}

							$wheres[] = implode(' OR ', $like_clauses);
						}
					}

					if (in_array($operator, array('>', '>=', '<', '<=')))
					{
						if (is_numeric($value))
						{
							$wheres[] = $column . ' ' . $operator . ' ' . $value;
						}
						else
						{
							$wheres[] = $column . ' ' . $operator . ' ' . $this->fn_quote($key, $value);
						}
					}
				}
				else
				{
					$wheres[] = $this->_getCondition($column, $key, $value);
				}
			}
		}

		return implode($conjunctor . ' ', $wheres);
	}

	private function _where_clause($where)
	{
		$where_clause = '';

		if (is_array($where))
		{
			$where_keys	= array_keys($where);
			$where_AND	= preg_grep("/^AND\s*#?$/i", $where_keys);
			$where_OR	= preg_grep("/^OR\s*#?$/i", $where_keys);

			$single_condition = array_diff_key($where, array_flip(
				explode(' ', 'AND OR GROUP ORDER HAVING LIMIT LIKE MATCH')
			));

			if ($single_condition != array())
			{
				$where_clause = ' WHERE ' . $this->_data_implode($single_condition, '');
			}

			if (!empty($where_AND))
			{
				$value = array_values($where_AND);
				$where_clause = ' WHERE ' . $this->_data_implode($where[$value[0]], ' AND');
			}

			if (!empty($where_OR))
			{
				$value = array_values($where_OR);
				$where_clause = ' WHERE ' . $this->_data_implode($where[$value[0]], ' OR');
			}
		}
		else
		{
			if ($where != null)
			{
				$where_clause .= ' ' . $where;
			}
		}

		return $where_clause;
	}

	private function _group_clause($group)
	{
		$group_clause = '';

		if(!empty($group))
		{	
			$group_clause .= ' GROUP BY ' . $this->quoteColumn($group);

			if (isset($group['having']))
			{
				$group_clause .= ' HAVING ' . $this->_data_implode($group['having'], ' AND');
			}
		}

		return $group_clause;
	}

	private function _order_clause($order)
	{
		$order_clause = '';

		$regexOrder = '/(^[a-zA-Z0-9_\-\.]*)(\s*(DESC|ASC))?/';

		if(!empty($order))
		{	
			if (is_array($order))
			{
				if (
					isset($order[1]) &&
					is_array($order[1])
				)
				{
					$order_clause .= ' ORDER BY FIELD(' . $this->quoteColumn($order[0]) . ', ' . $this->quoteStringArray($order[1]) . ')';
				}
				else
				{
					$stack = array();

					foreach ($order as $column)
					{
						preg_match($regexOrder, $column, $order_match);
						$stack[] = $this->quoteColumn($order_match[1]) . (isset($order_match[3]) ? ' ' . $order_match[3] : '');
					}

					$order_clause .= ' ORDER BY ' . implode($stack, ',');
				}
			}
			else
			{
				preg_match($regexOrder, $order, $order_match);
				$order_clause .= ' ORDER BY ' . $this->quoteColumn($order_match[1]) . (isset($order_match[3]) ? ' ' . $order_match[3] : '');
			}
		}

		return $order_clause;
	}

	private function _limit_clause($limit)
	{
		$limit_clause = '';

		if(!empty($limit))
		{	
			if (is_numeric($limit))
			{
				$limit_clause .= ' LIMIT ' . $limit;
			}

			if (
				is_array($limit) &&
				is_numeric($limit[0]) &&
				is_numeric($limit[1])
			)
			{
				$limit_clause .= ' LIMIT ' . $limit[0] . ',' . $limit[1];
			}
		}

		return $limit_clause;
	}

	private function select_context($table, $props = array(), $column_fn = null)
	{
		$table = $this->quoteColumn(MYSQL_PREFIX . $table);

		$columns = isset($props['columns']) ? $props['columns'] : '*';
		$join = isset($props['join']) ? $props['join'] : array();

		$table_join = array();

		$join_array = array(
			'>' => 'LEFT',
			'<' => 'RIGHT',
			'<>' => 'FULL',
			'><' => 'INNER'
		);

		foreach($join as $sub_table => $relation)
		{
			preg_match('/(\[(\<|\>|\>\<|\<\>)\])?([a-zA-Z0-9_\-]*)\s?(\(([a-zA-Z0-9_\-]*)\))?/', $sub_table, $match);
			# [join-method]table(as-table)

			if ($match[2] != '' && $match[3] != '')
			{
				if (is_string($relation))
				{
					$relation = 'USING (' . $this->quoteColumn($relation) . ')';
				}

				if (is_array($relation))
				{
					// For ['column1', 'column2']
					if (isset($relation[0]))
					{
						$relation = 'USING (' . $this->quoteColumnArray($relation) . ')';
					}
					else
					{
						$joins = array();

						foreach ($relation as $key => $value)
						{
							$joins[] = (
								strpos($key, '.') > 0 ?
									// For ['tableB.column' => 'column']
									$this->quoteColumn($key) :

									// For ['column1' => 'column2']
									$table . '.'  . $this->quoteColumn($key) // don't quote $table again, $table already quoted
							) .
							' = ' .
							$this->quoteColumn((
								strpos($key, '.') > 0 ?
									$value :
									(isset($match[5]) ? $match[5] : $match[3]) . '.' . $value
							));
						}

						$relation = 'ON ' . implode($joins, ' AND ');
					}
				}

				$match[3] = $this->quoteColumn(MYSQL_PREFIX . $match[3]);

				$table_join[] = $join_array[ $match[2] ] . ' JOIN ' . $match[3] . ' ' .
								(isset($match[5]) ?  'AS ' . $this->quoteColumn(MYSQL_PREFIX . $match[5]) . ' ' : '') .
								$relation;
			}
		}

		$table .= ' ' . implode($table_join, ' ');

		$column = isset($column_fn) ? $column_fn . '(' . $this->column_push($columns) . ') AS result' : $this->column_push($columns); 

		return 	'SELECT ' . $column . ' FROM ' . $table . 
				$this->_where_clause(@$props['where']) .
				$this->_group_clause(@$props['group']) .
				$this->_order_clause(@$props['order']) .
				$this->_limit_clause(@$props['limit']);
	}

	/*
	EXAMPLE QUERY (FULL)
	getList(
	'table1',
		[
			'where' =>
			[
				'AND' =>
				[
					'id' => 5,
					'name' => 'xy'
				]
			],

			'limit' =>
			[
				5, 50
			],
			--or--
			'limit' => 5,
			
			'join' =>
			[
				'[>]table2(temptable)' =>
				[
					'column-table1' => 'column-table2'
				]
			],

			'order' =>
			[
				'column1 ASC',
				'column2 ASC',
				'column3 DESC',
			],
			--or--
			'order' => 'column',
			--or--
			'order' => 'column DESC',
		]
	)
	*/
	


	
	/**
	* getList:
	* Query successfull:
	* 	matches > 0: return array of results
	* 	matches = 0: return empty array
	* Query failed:
	* 	return false
	*/
	public function getList($table, $props = array())
	{
		$result = $this->query($this->select_context($table, $props));

		if($result !== false)
		{
			$data = array();
			while ($row = $result->fetch_object())
			{
				$data[] = $row;
			}
	
			return /*(object) */$data;
		}
		return false;	
	}
	
	/**
	* getEntry:
	* Query successfull:
	* 	matches > 0: return array of result
	* 	matches = 0: return empty array
	* Query failed:
	* 	return false
	*/
	public function getEntry($table, $props = array())
	{
		$result = $this->query($this->select_context($table, $props));

		if($result !== false)
		{
			return $result->fetch_object();
		}

		return false;
	}
	
	/**
	* count:
	* Query successfull:
	* 	matches > 0: return array of result
	* 	matches = 0: return empty array
	* Query failed:
	* 	return false
	*/
	public function count($table, $props = array())
	{
		$result = $this->query($this->select_context($table, $props, 'COUNT'));

		if($result !== false)
		{
			return $result->fetch_object()->result;
		}

		return false;
	}

	/**
	* delete:
	* Query successfull:
	* 	return true
	* Query failed:
	* 	return false
	*/
	public function Delete($table, $props = array())
	{
		$table = $this->quoteColumn(MYSQL_PREFIX . $table);

		$statement =	'DELETE FROM ' . $table . 
						$this->_where_clause(@$props['where']) .
						$this->_group_clause(@$props['group']) .
						$this->_order_clause(@$props['order']) .
						$this->_limit_clause(@$props['limit']);

		return $this->query($statement);
	}

	/**
	* clearTable:
	* Query successfull:
	* 	return true
	* Query failed:
	* 	return false
	*/
	public function clearTable($table)
	{
		$table = $this->quoteColumn(MYSQL_PREFIX . $table);

		$statement = 'TRUNCATE TABLE ' . $table;

		return $this->query($statement);
	}

	/**
	* Insert:
	* Query successfull:
	* 	return insert ID
	* Query failed:
	* 	return false
	*/
	public function Insert($table, $datas = array())
	{
		$table = $this->quoteColumn(MYSQL_PREFIX . $table);

		$lastInsertId = array();

		if (!isset($datas[0]))
			$datas = array($datas);

		foreach ($datas as $data)
		{
			$columns	= array();
			$values		= array();

			foreach ($data as $column => $value)
			{
				$columns[] = $this->quoteColumn($column);

				switch (gettype($value))
				{
					case 'NULL':
						$values[] = 'NULL';
						break;
					case 'boolean':
						$values[] = ($value ? '1' : '0');
						break;
					case 'integer':
					case 'double':
					case 'string':
						$values[] = $this->fn_quote($column, $value);
						break;
				}
			}

			$this->query('INSERT INTO ' . $table . ' (' . implode(', ', $columns) . ') VALUES (' . implode($values, ', ') . ')');

			$lastInsertId[] = $this->db->insert_id;
		}

		return count($lastInsertId) > 1 ? $lastInsertId : $lastInsertId[0];
	}

	/**
	* Update:
	* Query successfull:
	* 	return true
	* Query failed:
	* 	return false
	*/
	public function Update($table, $data = array(), $where = null)
	{
		$table = $this->quoteColumn(MYSQL_PREFIX . $table);

		$fields = array();

		foreach ($data as $column => $value)
		{
			preg_match('/([\w]+)(\[(\+|\-|\*|\/)\])?/i', $column, $match);

			if (isset($match[3]))
			{
				if (is_numeric($value))
				{
					$fields[] = $this->quoteColumn($match[1]) . ' = ' . $this->quoteColumn($match[1]) . ' ' . $match[3] . ' ' . $value;
				}
			}
			else
			{
				$column = $this->quoteColumn($column);

				switch (gettype($value))
				{
					case 'NULL':
						$fields[] = $column . ' = NULL';
						break;
					case 'boolean':
						$fields[] = $column . ' = ' . ($value ? '1' : '0');
						break;
					case 'integer':
					case 'double':
					case 'string':
						$fields[] = $column . ' = ' . $this->fn_quote($column, $value);
						break;
				}
			}
		}

		return $this->query('UPDATE ' . $table . ' SET ' . implode(', ', $fields) . $this->_where_clause($where));
	}
	
	public function closeConnection()
	{
		$result = $this->db->close();
		
		return $result;
	}




	public function setLastQuery($query)
	{
		$this->querys[] = $query;
		return true;
	}
	public function getLastQuery()
	{
		return end($this->querys);
	}

	public function escapeString($str)
	{
		return $this->db->real_escape_string($str);
	}

	public function quoteString($str, $escapeString = true)
	{
		return $this->stringQuote . ($escapeString ? $this->escapeString($str) : $str) . $this->stringQuote;
	}
	public function quoteStringArray($array, $returnAsArray = false)
	{
		$temp = array();

		foreach ($array as $value)
		{
			$temp[] = is_int($value) ? $value : $this->quoteString($value);
		}

		return $returnAsArray ? $temp : implode($temp, ',');
	}
	
	public function quoteColumn($column, $addPrexif = true, $escapeColumn = true)
	{
		$column = str_replace('#', '', $column);
		$column = $escapeColumn ? $this->escapeString($column) : $column;
		return $this->columnQuote .
				(
					strpos($column, '.') > 0 ?
					($addPrexif ? MYSQL_PREFIX : '') . str_replace('.', $this->columnQuote.'.'.$this->columnQuote, $column) :
					$column
				) .
				$this->columnQuote;
	}
	public function quoteColumnArray($array, $returnAsArray = false)
	{
		$temp = array();

		foreach ($array as $value)
		{
			$temp[] = is_int($value) ? $value : $this->quoteColumn($value);
		}

		return $returnAsArray ? $temp : implode($temp, ',');
	}

	private function fn_quote($column, $string)
	{
		/**
		* fn_quote
		* @param column
		* @param string
		* @return:
		* ## + column : string
		* #  + column : quoted string
		*      column : escaped and quoted string
		*/
		return (strpos($column, '##') === 0) ?
				$string :
				((strpos($column, '#') === 0) ?
					$this->quoteString($string, false):
					$this->quoteString($string));
	}
}
?>