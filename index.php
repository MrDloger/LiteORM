<?
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('App/config.php');
require_once('App/function.php');

use App\Models\Company;

// $db = new App\Classes\Db();
//$db->insert('company', ['title' => 'company test 5']);
//$db->update('companes', 'id = 3', ['title' => 'new title2']);
// $companies = $db->select('companies', "*")->fetchAll();
// $companies = $db->find('company', "*", 'id > 2')->fetchAll();
// d($companies);

// $db = App\Classes\Db::getInstance();
// dv($db);
// $company = Company::create(['title' => 'test create object' . time()]);
// $company = Company::update(15, ['title' => 'test ORM update7' . time()]);
// $company = Company::findById(7);
// 						  SELECT * from Companies WHERE id IN (?,?,?) LIMIT ?
// dd(db()->executeQuery('SELECT * FROM companies WHERE id IN (2,3,6) LIMIT 1')->fetchAll());
// $stmt = db()::$pdo->prepare("SELECT * FROM Companies LIMIT :lim");
// $stmt->bindValue(':lim', (int) 1, PDO::PARAM_INT);
// $stmt->execute(); 
// ddv($stmt->fetchAll());

$company = Company::select()->order(['id' => 'DESC'])->get();
// $company = Company::where('id', '=', '6')->get();

// d(new Company(['title' => 'test new']));

d($company);