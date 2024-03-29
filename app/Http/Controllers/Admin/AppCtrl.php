<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Validator, Auth, DB;
use App\Models\Category, App\Models\Application;
use App\Models\Zone, App\Models\PlacementLog;
use App\User, App\Models\SimilarCategory;
class AppCtrl extends Controller
{
    /**
     * shared Validation rules between create and edit.
     */
    private $_initRules;

    // the main Title of all pages controlled by this controller
    protected $_mTitle;
    // authenticated user
    private $_user;
    // All categories
    private $_categories;
    
    /**
     * __construct. To init the class
     *
     * @param void
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function __construct (){
        $this->_mTitle      = trans( 'admin.applications' );
        $this->_categories   = Category::all();
        $this->_user = Auth::user();

        $this->_initRules = [
                'name'          => 'required|max:255',
                'platform'      => 'required|in:' . implode( ',' , array_keys( config('consts.app_platforms') ) ), 
                'package_id'    => 'required|max:255',
                'fcategory'     => 'required|exists:categories,id'
            ];
    }

    /**
     * index. To show all apps page.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $user_id
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function index ( Request $request, $user_id = null ){

        $mTitle = $this->_mTitle;
        $title  = trans( 'admin.all_applications' );

        $apps   = Application::leftJoin('ad_placement', 'ad_placement.app_id', '=', 'applications.id')
                            ->leftJoin('placement_log', 'placement_log.ads_id', '=', 'ad_placement.id')
                            ->join( 'users', 'users.id', '=', 'applications.user_id' )
                            ->select( 
                                'applications.*',
                                'placement_log.created_at as time',
                                DB::raw('DATE(placement_log.created_at) AS date'),
                                DB::raw('SUM(`placement_log`.`requests`) AS requests'), 
                                DB::raw('SUM(`placement_log`.`impressions`) AS impressions'), 
                                DB::raw('SUM(`placement_log`.`clicks`) AS clicks'),
                                DB::raw('SUM(`placement_log`.`installed`) AS installed')
                            );

        // Filter applications by time period.
        filterByTimeperiod($apps, $request, 'placement_log');

        // get the count of all placement ads
        $adsCount = Zone::leftJoin('applications', 'applications.id', '=', 'ad_placement.app_id');

        // To get only the undeleted user's applications if his role isn't admin. 
        if( $this->_user->role != ADMIN_PRIV ){

            // Get all apps to compelete zero records apps that won't be retrieve within the main query. 
            $allApps = Application::where('user_id', '=', $this->_user->id)
                    ->where('status', '!=', DELETED_APP);
            
            // Apps within the main query.
            $apps ->where( 'applications.user_id', '=', $this->_user->id )
                  ->where( 'applications.status', '!=', DELETED_APP )
                  ->where(function($query){
                        $query  ->whereNull('ad_placement.status')
                                ->orWhere('ad_placement.status', '!=', DELETED_ZONE);
                });
            
            // To get the ads count in the applications.
            $adsCount ->where('applications.user_id', '=', $this->_user->id )
                      ->where('applications.status', '!=', DELETED_APP )
                      ->where('ad_placement.status', '!=', DELETED_ZONE );

        // If the authorized user is the admin. and view the apps for specific user with id $user_id
        }else if( $user_id != null ){ // if the user is an admin and check user apps

            $user = User::find($user_id);
            // To validate the user
            if( $user == null ){
                return redirect('admin')
                            ->with('warning', trans('lang.spam_msg'));
            }            
            // Get all apps to compelete zero records apps that won't be retrieve within the main query. 
            $allApps = Application::where('applications.user_id', '=', $user_id);

            $title = $title . trans('admin.belongs_to') . "{$user->fname}  {$user->lname}"; 
            
            // Apps within the main query.
            $apps->where( 'applications.user_id', '=', $user_id );
            
            // To get the ads count in the applications.
            $adsCount->where( 'applications.user_id', '=', $user_id );
        
        // If the authorized user is the admin and view all the applications.
        }else{

            // Get all apps to compelete zero records apps that won't be retrieve within the main query. 
            $allApps = new Application;
        }

        // Get the array data for the applications's main chart.
        $chartData = adaptChartData( clone($apps), 'placement_log' );
        
        // Get the main array for the apps to be shown in the apps list.
        $apps = $apps->groupBy('applications.id')
                    ->orderBy('applications.created_at', 'ASC')
                    ->get();
        
        // Get the ads count within the applications.
        $adsCount = $adsCount->count();

        // Get all the valid application according the authorized user.
        $allApps  = $allApps->get();
            
        $data = [ 'title', 'mTitle', 'apps', 'adsCount', 'chartData', 'user_id', 'allApps' ];
        return view( 'admin.app.index' )
                    ->with( compact( $data ) );
    }

    /**
     * create. To show create app page
     *
     * @param void
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function create ( ){
        $mTitle = $this->_mTitle;
        $title  = trans( 'admin.create_app' );
        $categories = $this->_categories;

        $data = [ 'mTitle', 'title', 'categories' ];
        return view( 'admin.app.create' )
                    ->with( compact( $data ) );
    }

    /**
     * store. To store created application. respond Http Post Request 
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function store ( Request $request ){
        $createRules = [
                'icon'      => 'required|image|mimes:jpeg,jpg,bmp,png,gif',
                'scategory' => 'required|exists:categories,id|not_in:' . $request->input('fcategory') // to make sure that secondary category differ than first category
            ];

        // Merge the repated (_initRules) with create app rules.
        $rules =array_merge( $this->_initRules, $createRules );
        
        $validator = Validator::make( $request->all(), $rules );
        if( $validator->fails() ){
            return redirect()->back()
                            ->withInput()
                            ->withErrors( $validator )
                            ->with( 'error', trans( 'lang.validate_msg' ) );
        }else{
            $app = $this->_store( $request );
            
            // After creating application successfully, redirect to the application page to create a new ad placement.
            return redirect('zone/all/' . $app->id )
                            ->with( 'success', trans( 'admin.created_app_msg' ) );
        }
    }

    /**
     * edit. To show edit application page
     *
     * @param int $app_id
     * @return \Illuminate\Http\Response
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function edit ( $app_id ){
        $mTitle     = $this->_mTitle;
        $title      = trans( 'admin.edit_app' );
        $categories = $this->_categories;

        // Change the $app to $_app not to conflicit with the main laravel $app object through run time application.
        $_app = Application::where( 'id', '=', $app_id );

        // To enable edit only undeleted applications if the user isn't admin.
        $_app = $this->_user->role == ADMIN_PRIV ? $_app : $_app->where( 'status', '!=', DELETED_APP );
        $_app = $_app->first();
        
        // If the application isn't valid, redirect to dashboard page with spam message.                           
        if( is_null( $_app ) ){
            return redirect('admin')
                        ->with( 'warning', trans( 'lang.spam_msg' ) );
        }

        $data = [ 'mTitle', 'title', '_app', 'categories' ];
        return view( 'admin.app.create' )
                    ->with( compact( $data ) );
    }

    /**
     * save. To save edited application. Respond to HTTP Post requets
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function save ( Request $request ){

        $editRules  = [ 
                'id'        => 'required|exists:applications,id',
                'icon'      => 'image|mimes:jpeg,jpg,bmp,png,gif',
                'scategory' => 'required|exists:categories,id|not_in:' . $request->input('fcategory'),
                'status'    => 'in:' . implode( ',' , array_keys( config('consts.app_status') ) )
            ]; 
        // Merge the repeated rules(_initRules) with edit rules
        $rules      = array_merge( $this->_initRules, $editRules );

        $validator  = Validator::make( $request->all(), $rules );
        if( $validator->fails() ){
            return redirect()->back()
                            ->withInput()
                            ->withErrors( $validator )
                            ->with( 'error', trans( 'lang.validate_msg' ) );
        }else{
            $this->_store( $request );
            return redirect()->back()
                            ->with( 'success', trans( 'admin.created_app_msg' ) );
        }
    }

    /**
      * destroy. To deactivate the application.
      *
      * @param \Illuminate\Http\Request $request
      * @return void
      * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
      * @copyright Smart Applications Co. <www.smartapps-ye.com>
      */
    public function destroy ( Request $request ){
        if( $request->has( 'id' ) && $request->has('token') ){
            $app = Application::find( $request->id );
            $session_token = session( "_token" );

            // Token Validation. and assure that app_id exists in apps DB table 
            if( $request->token == $session_token && $app != null ){
                // To prevent users to delete another users's applications ( than more step )
                if( $this->_user->role != ADMIN_PRIV && $app->user_id != $this->_user->id ){ 
                    return redirect()->back()
                                    ->with( 'warning', trans( 'lang.spam_msg' ) );
                }
                $app->status = DELETED_APP;
                $app->deleted_at = date( 'Y-m-d H:i:s' );
                $app->save();
                
                //delete all ads connected to that application.
                Zone::where( 'app_id', '=', $app->id )
                        ->update( [ 'status' => DELETED_ZONE ] );

                return redirect()->back()
                                ->with( 'success', trans( 'admin.deleted_app_msg' ) );
            }
        }
        // Redirect to the dashboard page with spam message if the token, or the application id aren't valid values.
        return redirect()->back()
                        ->with( 'warning', trans( 'lang.spam_msg' ) );
    }

    /**
     * getKeywords. To get all keywords that match the search words. 
     * // Not used method. May be needed later.
     * @param \Illuminate\Http\Request $Request
     * @return json
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public function getKeywords ( Request $request ){
        
        if( $request->has('key') ){
            $ids = [];
            $search = $request->key;
            if( $request->has('present') ){
                $ids = $request->present;
            }
            //$results = DB::select("SELECT * FROM `keywords` WHERE ( `id` NOT IN ( {$ids} ) ) AND ( `name` LIKE :s ) ", [ 's' => "%{$search}%" ] );
            $results = DB::table('keywords')
                            ->whereNotIn( 'id', $ids )
                            ->where( 'name', 'LIKE', "%{$search}%" )
                            ->get();
            return response()->json( $results );
        }
    }

    /** *** ***
     * Private Methods
     */
    /**
     * _store. To store app properties in the DB
     *
     * @param \Illuminate\Http\Request $request
     * @return mixed
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _store ( Request $request ){
        if( $request->has( 'id' ) ){
            $app = Application::find( $request->id );
            
            if( $request->has( 'status' ) ){
                if( $request->status == DELETED_APP ){
                    //delete all ads connected to that application.
                    Zone::where( 'app_id', '=', $app->id )
                            ->update( [ 'status' => DELETED_ZONE ] );
                }
                $app->status =$request->status; 
            }
            // To change updated value to pending(0) state. 
            if( $app->package_id    != $request->package_id ){
                $app->updated = PENDING_UPDATED;
            }
        }else{
            $app = new Application;
            $app->user_id  = Auth::user()->id; 
        }

        $app->name          = $request->name;
        $app->platform      = $request->platform;

        $app->fcategory     = $request->fcategory;
        $app->scategory     = $request->scategory;
        $app->simi_cats     = SimilarCategory::getSimiCats($request->fcategory, $request->scategory);

        $app->package_id    = $request->package_id;

        if( $request->hasFile( 'icon' ) ){
            $app->icon          = uploadFile( $request->icon , 'uploads/app-icons' );
        }


        $app->save();
        return $app;
    }

}
