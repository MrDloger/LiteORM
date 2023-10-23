<?
namespace App\Core\DataBase;



abstract class Model
{
	
	//private \PDOStatement $stmt;
	private array $values;

	static protected string $table;
	static protected array $fillable;
	static protected string $primoryKey = 'id';

	public function __construct($values = null)
	{
		if ($values) $this->fill($values);
	}
	public function fill(array $values):static
	{
		if (isset($values[static::$primoryKey])) $this->values[static::$primoryKey] = $values[static::$primoryKey];
		foreach (static::$fillable as $field) {
			if (isset($values[$field])) $this->values[$field] = $values[$field];
		}
		return $this;
	}
	public function get(string $field):mixed
	{
		return $this->values[$field];
	}
	public function set(string $field, mixed $value):static
	{
		$this->values[$field] = $value;
		return $this;
	}
	static public function getTableName():string
	{
		return static::$table;
	}
	static public function findById($id, string $columns = '*'):false|static
	{
		$res = Db::find(static::$table, $columns, static::$primoryKey . '= ?', [$id]);
		if (!$res) return false;
		return new static($res->fetch());
	}
	static public function findFirst()
	{

	}
	static public function create(array $values):false|static
	{
		$values = static::loadValues($values);
		$id  = Db::insert(static::$table, $values);
		if (!$id){
			return false;
		}
		return self::findById($id);

	}
	static public function update($id, $values):bool
	{
		
		return Db::update(self::getTableName(), self::$primoryKey . '=' . $id, $values);

	}
	static public function where($field = null, $operator = null, $value = null, $boolean = 'AND'):Builder
	{
		return (new Builder(static::class))->where($field, $operator, $value, $boolean);
	}
	// static public function whereAnd($field, $operator = null, $value = null):Builder
	// {
	// 	return self::where($field, $operator, $value, 'AND');
	// }
	// static public function whereOr($field, $operator = null, $value = null):Builder
	// {
	// 	return self::where($field, $operator, $value, 'OR');
	// }
	static public function select(string $columns, string $filter, array $values):\PDOStatement
	{
		return Db::select(static::getTableName(), $columns, $filter, $values);

	}
	public function toArray():array
	{
		return $this->values;
	}
	static public function loadValues(array $values):array
	{
		$res = [];
		foreach ($values as $key => $value) {
			if (in_array($key, static::$fillable)) $res[$key] = $value;
		}
		return $res;
	}
	public function save():bool
	{
		return $this->update($this->get(self::$primoryKey), self::loadValues($this->values));
	}
}