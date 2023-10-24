<?

namespace App\Core\DataBase;


class Builder
{
	const OPERATOR = [
		'SELECT' => 'SELECT :columns FROM'
	];
	const ORDER = ['ASC' => 'ASC', 'DESC' => 'DESC'];
	/**
     * Array of condition parameters
     */
	private array $wheres;
	/**
	 * SQL operator
	 */
	private string $operator;
	/**
	 * An array of columns that must be included for the query 
	 */
	private array $columns;
	/**
	 * Create a new query builder instance.
	 * @param string $modelClass
	 */
	/**
	 * limit Query
	 */
	private int|null $limit;
	/**
	 * array Order by Query
	 */
	private array $order;
	/**
	 * offset Query
	 */
	private int|null $offset;
	public function __construct(private string $modelClass)
	{

	}
	/**
	 * Adding a condition for the WHERE operator
	 * @param  string $field
	 * @param  string $operator
	 * @param  string $value
	 * @param  string $boolean
	 * @return static
	 */
	public function where($field, $operator = null, $value = null, $boolean = 'AND'):static
	{
		$boolean = strtoupper($boolean);
		$this->wheres[] = ['field' => $field, 'operator' => $operator, 'value' => $value, 'boolean' => $boolean];
		return $this;
	}
	/**
	 * Runing Query
	 * @param  string|array $columns
	 * @return false|array Model
	 */
	public function get($columns = null):false|array
	{
		$this->setColumns($columns);
		$stmt = db()->executeQuery(...$this->buildQuery());
		$elements = [];
		while($element = $stmt->fetch()){
			$elements[] = new $this->modelClass($element);
		}
		if (empty($elements)) return false;
		return $elements;
	}
	/**
	 * add limit in Query
	 * @param  int    $limit
	 * @param  int|null   $offset
	 * @return static
	 */
	public function limit(int $limit, int|null $offset = null):static
	{
		$this->limit =  $limit;
		$this->offset = $offset;
		return $this;
	}
	public function order(array $orders):static
	{
		foreach($orders as $key => $value){
			$this->order[$key] = strtoupper($value);
		}
		return $this;
	}
	/**
	 * Building a Query for first item
	 * @param  string|array $columns
	 * @return false|array Model
	 */
	public function first($columns = null):false|Model
	{
		$this->setColumns($columns);
		$this->limit = 1;
		return new $this->modelClass(db()->executeQuery(...$this->buildQuery())->fetch()) ?? false;
	}
	/**
	 * Adding Columns to a Query
	 * @param  string|array $columns
	 * @return static
	 */
	public function select(string|array|null $columns = null):static
	{
		//$this->operetor = 'SELECT';
		$this->setColumns($columns);
		return $this;
	}
	/**
	 * Adding Columns to a Query
	 * @param  string|array $columns
	 * @return static
	 */
	private function setColumns(string|array|null $columns):static
	{
		$columns = !empty($columns) ? (is_array($columns) ? $columns : $this->explodeString($columns)) : ['*'];
		foreach($columns as $col){
			if (empty($this->columns[$col])) $this->columns[$col] = $col;
		}
		return $this;
	}
	/**
	 * Splitting a string into an array
	 * @param  string $str
	 * @return array
	 */
	private function explodeString(string $str):array
	{
		return preg_split('/[|\s,]+/', $str);
	}
	/**
	 * Building a Query
	 * @return array
	 */
	private function buildQuery():array
	{
		$columns = implode(', ', $this->columns);
		$query = str_replace(':columns', $columns, self::OPERATOR[$this->operator ?? 'SELECT'])  . ' ' . ($this->modelClass)::getTableName();
		$filter = '';
		$values = [];
		if (isset($this->wheres)) {
			foreach($this->wheres as $key => $wh){
				switch (strtoupper($wh['operator'])) {
					case 'IN':
						if (!is_array($wh['value'])) $wh['value'] = $this->explodeString($wh['value']);
						$placeholder = str_repeat('?,', count($wh['value']) - 1) . '?';
						$filter = "{$wh['field']} " . strtoupper($wh['operator']) . " (" . $placeholder . ")";
						$values = array_merge($values, $wh['value']);
						break;
					case '=':
					case '>':
					case '<':
					case '<=':
					case '>=':
					case '!=':
					case '<>':
						$filter = "{$wh['field']} {$wh['operator']} ?";
						$values[] = $wh['value'];
						break;
				}
			}
			$query .= ' WHERE ' . $filter;
		}
		if (isset($this->order)) {
			foreach($this->order as $columns => $direction) {
				$order[] = $columns . (isset(self::ORDER[$direction]) ? ' ' . self::ORDER[$direction] : '');
			}
			$query .= ' ORDER BY ' . implode(', ', $order);
		}
		if (isset($this->limit)) {
			$query .= ' LIMIT ?';
			$values[] = $this->limit;
		}
		if (isset($this->offset)) {
			$query .= ' OFFSET ?';
			$values[] = $this->offset;
		}
		//ddv(['query' => $query, 'values' => $values]);
		return ['query' => $query, 'values' => $values];
	}
}