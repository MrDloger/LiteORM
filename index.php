<?

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
//dd(db()->executeQuery('SELECT * FROM companies WHERE id IN (2,3,6)')->fetchAll());
$company = Company::where('id', 'in', '1,4,|6')->get();
// $company = Company::where('id', '=', '6')->get();

// d(new Company(['title' => 'test new']));

d($company);