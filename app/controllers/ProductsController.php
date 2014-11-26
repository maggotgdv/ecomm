<?php
class ProductsController extends BaseController
{
	public function _construct()
	{
		$this->beforeFilter('csr',array('on'=>'post'));
	}
	public function getIndex() 
	{
		$categories = array();
		foreach(Category::all() as $category)
		{
			$categories[$category->id]=$category->name;
		}
		return View::make('products.index')
			->with('products',Product::all())
			->with('categories', $categories);
	}
	public function postCreate()
	{
		$validator = Validator::make(Input::all(), Product::$rules);
		if($validator->passes())
		{
			$product = new Product;
			$product->category_id = Input::get('category_id');
			$product->title = Input::get('title');
			$product->description = Input::get('description');
			$product->price = Input::get('price');


			$image = Input::file('image');
			$filename = date('Y-m-d-h-i-s').'-'.$image->getClientOriginalName();
			Image::make($image->getRealPath())->resize(468, 249)->save('public/img/products/'.$filename);
			$product->image = 'img/products/'.$filename;
			$product->save();
			return Redirect::to('admin/products/index')
				->with('mesage','Product Created');
		}
		return Redirect::to('admin/products/index')
			->with('mesage','Something went wrong')
			->withErrors($validator)
			->withInput();
	}
	public function postDestroy()
	{
		$product = Product::find(Input::get('id'));
		if($product)
		{
			File::delete();
			$product->delete('public/'.$product->image);
			return Redirect::to('admin/products/index')
				->with('mesage','Category Deleted');
		}
		return Redirect::to('admin/products/index')
			->with('mesage','Something went wrong please try again');
	}

	public function postToggleAvailability()
	{
		$product = Product::find(Input::get('id'));

		if($product)
		{
			$product->availability = Input::get('availability');
			$product->save();
			return Redirect::to('admin/products/index')->with('message', 'Product Updated');
		}
		return Redirect::to('admin/products/index')->with('message','Invalid Product');

	}
}
?>