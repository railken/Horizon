<?php

namespace Work\Controller\Admin;

use CoreWine\Http\Request;

class CustomerController extends AdminController{

	/**
	 * ORM\Model
	 *
	 * @var
	 */
	public $model = 'Work\Model\Customer';

	/**
	 * Url
	 *
	 * @var
	 */
	public $url = 'admin/work/customers';

	/**
	 * Url
	 *
	 * @var
	 */
	public $url_alias = 'work:admin.customers';

	/**
	 * User data
	 *
	 * @var bool
	 */
	public $user_data = true;


	/**
	 * Set views
	 *
	 * @param  $views
	 */
	public function views($views){
		
		$views -> all(function($view){
			$view -> id('id');
			$view -> text('fullname');
			$view -> text('address');
			$view -> text('vat');
			$view -> text('tax_code');
		});

		$views -> add(function($view){
			$view -> text('fullname');
			$view -> text('address');
			$view -> text('vat');
			$view -> text('tax_code');
			$view -> textarea('notes');
		});

		$views -> edit(function($view){
			$view -> text('fullname');
			$view -> text('address');
			$view -> text('vat');
			$view -> text('tax_code');
			$view -> textarea('notes');
		});

		$views -> get(function($view){
			$view -> id('id');
			$view -> text('fullname');
			$view -> text('address');
			$view -> text('vat');
			$view -> text('tax_code');
			$view -> textarea('notes');
		});
		
		$views -> search(function($view){
			$view -> id('id');
			$view -> text('fullname');
			$view -> text('address');
			$view -> text('vat');
			$view -> text('tax_code');
		});
	}


	/**
	 * Index
	 *
	 * @return Response
	 */
	public function index(Request $request){
		if(!\Auth::user() -> permission -> has(\Auth\Model\User::PEX_CHESS))
			return abort(404);
		

		return parent::index($request);
	}
}

?>