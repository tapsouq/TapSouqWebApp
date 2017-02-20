<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Validator, DB;
use App\Models\SimilarCategory, App\Models\Category;
use App\Models\Campaign, App\Models\Application;

class SimilarCategoriesCtrl extends Controller
{
	private $_mTitle;
	private $_similarCats;
    /**
     * __construct. To init the class.
     *
     * @param  param
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function __construct()
    {
    	$this->_mTitle = trans("admin.SimilarCategories");
    	$this->_similarCats = Category::leftJoin('similar_cats', 'similar_cats.cat_id', '=', 'categories.id')
    								->select('categories.*', 'similar_cats.simi_cats')
    								->get();
    }

    /**
     * index. To show the category similarity page.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function index( Request $request )
    {
    	$mTitle = $this->_mTitle;
    	$title = trans('admin.SimilarCategories');
    	$similarCats = $this->_similarCats;

    	$data = [ 'mTitle', 'title', 'similarCats' ];
    	return view('admin.similar-cats.index')
    				->with( compact($data) );
    }

    /**
     * edit. To show edit page.
     *
     * @param  param
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function edit()
    {
    	$mTitle =$this->_mTitle;
    	$title = trans('admin.edit');
    	$similarCats = $this->_similarCats;

    	$data = ['title', 'mTitle', 'similarCats'];
    	return view('admin.similar-cats.edit')
    				->with(compact($data));
    }

    /**
     * save. To save the category similarities.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function save( Request $request )
    {
    	$inputArray = $request->except('_token');
    	
    	// To assuer that we have category to save
    	if( count($inputArray) == 0 ){
    		return redirect()->back()
    					->with('success', trans('admin.successEditWithZeroSimiCats'));
    	}
  		
  		if( $this->_saveSimilarCategories($inputArray) ){
  			return redirect()->back()
  						->with('success', trans('admin.successEditSimiCats'));
  		}else{
  			return redirect()->back()
  						->with('error', trans('admin.thereAreSuchError'));
  		}
    }

    /**
     * _saveSimilarCategories. To save similar categories to the database.
     *
     * @param  array $inputs
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _saveSimilarCategories($inputs)
    {
    	
    	foreach ($inputs as $key => $value) {
    		
    		// Select controls names are cat_{categoryId}, that's why we substr from the char 4.
    		$categoryId = substr($key, 4);
    		$simiCats = implode(',', $value);

    		$similarCategory = SimilarCategory::where("cat_id", $categoryId)
    								->first();

    		$array = ['simi_cats' => $simiCats, 'cat_id' => $categoryId ];

    		if( $similarCategory == null ){
    			SimilarCategory::insert($array);
    		}else{
	    		$similarCategory->update($array);
    		}
    	}

    	// upadte the similar categories for the applications and campaigns due to the change in the similar categories.
    	$this->_updateCampAndAppsSimiCats();
    }

    /**
     * _updateCampAndAppsSimiCats. To update campaigns and applications similar categories.
     *
     * @param  param
     * @return return
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function _updateCampAndAppsSimiCats()
    {
    	$simiCats = SimilarCategory::getAppsSimiCats();
    	
    }
}