<?

namespace App\Core\DataBase;


class Builder
{
	const OPERATOR = [
		'SELECT' => 'SELECT :columns from'
	];
	private array $wheres;
	private string $operator;
	private array $columns;
	public function __construct(private string $modelClass)
	{

	}
	public function where($field, $operator = null, $value = null, $boolean = 'AND'):static
	{
		$boolean = strtoupper($boolean);
		$this->wheres[] = ['field' => $field, 'operator' => $operator, 'value' => $value, 'boolean' => $boolean];
		return $this;
	}
	// public function whereAnd($field, $operator = null, $value = null):static
	// {
	// 	$this->where($field, $operator, $value, 'AND');
	// 	return $this;
	// }
	// public function whereOr($field, $operator = null, $value = null):static
	// {
	// 	$this->where($field, $operator, $value, 'OR');
	// 	return $this;
	// }
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
	public function first($columns = null):false|Model
	{
		$this->setColumns($columns);
		return new $this->modelClass(db()->executeQuery(...$this->buildQuery())->fetch()) ?? false;
	}
	public function select(string|array|null $columns = null):static
	{
		$this->operetor = 'SELECT';
		$this->setColumns($columns);
		return $this;
	}
	private function setColumns(string|array|null $columns):static
	{

		$columns = !empty($columns) ? (is_array($columns) ? $columns : $this->explodeString($columns)) : ['*'];
		foreach($columns as $col){
			if (empty($this->columns[$col])) $this->columns[$col] = $col;
		}
		return $this;
	}
	private function explodeString($str):array
	{
		return preg_split('/[|\s,]+/', $str);
	}
	private function buildQuery():array
	{
		$columns = implode(', ', $this->columns);
		$query = str_replace(':columns', $columns, self::OPERATOR[$this->operator ?? 'SELECT'])  . ' ' . ($this->modelClass)::getTableName();
		$filter = '';
		$values = [];
		if (!empty($this->wheres)) {
			foreach($this->wheres as $key => $wh){
				switch (strtoupper($wh['operator'])) {
					case 'IN':
						if (!is_array($wh['value'])) $wh['value'] = $this->explodeString($wh['value']);
						$placeholder = str_repeat('?,', count($wh['value']) - 1) . '?';
						$filter = "{$wh['field']} " . strtoupper($wh['operator']) . " (" . $placeholder . ") ";
						$values = array_merge($values, $wh['value']);
						break;
					
					case '=':
					case '>':
					case '<':
					case '<=':
					case '>=':
					case '!=':
					case '<>':
						$filter = "{$wh['field']} {$wh['operator']} ? ";
						$values[] = $wh['value'];
						break;
				}
				
			}
			$query .= ' WHERE ' . $filter;
		}
		return ['query' => $query, 'values' => $values];
	}
}