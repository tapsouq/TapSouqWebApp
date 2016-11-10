<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Keyword;
use App\Models\AppDetails;
use Carbon\Carbon, DB;
class KeywordMatcher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'matchKeywords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To match keywords with the applications title and description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $matchedApps        = $this->_matchNewApps();

        $matchedKeywords    = $this->_matchNewKeywords();
        
        $array = array_unique(array_merge($matchedApps,$matchedKeywords), SORT_REGULAR);
        
        DB::table('application_keywords')
            ->insert( $array );
    }

    /**
     * _matchNewApps. To match new applications with all keywords
     *
     * @param void
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _matchNewApps ( ){
        // The array that will contain the app_keywords table rows to be inserted.
        $array      = [];
        // To get the new applications
        $apps       = AppDetails::where( 'updated', '=', PENDING_UPDATED )
                                  ->get();
        // To get all keywords
        $keywords   = Keyword::all();
        if( sizeof( $apps ) ){
            foreach ($apps as $key => $application) {
                $app_id         = $application->id;
                $title          = strtolower($application->title);
                $description    = strtolower($application->description);

                foreach ($keywords as $keyword) {
                    $name       = strtolower($keyword->name);
                    $titleCount = substr_count( $title, $name);
                    $descCount  = substr_count( $description, $name);
                    
                    $count      = $titleCount + $descCount;
                    // Check if keyword is founded in title and description
                    if( $count > 0 ){
                        $array[] = [
                                'app_id'        => $app_id,
                                'keyword_id'    => $keyword->id,
                                'priority'      => $count
                            ];
                    }
                }
            }
            $appIds = array_pluck($apps, 'id');
            AppDetails::whereIn('id', $appIds)->update(['updated' => SUCCESS_UPDATED]);
        }
        return $array;
    }

    /**
     * _matchNewKeywords. To match new keywords with all applications.
     *
     * @param void
     * @return void
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    private function _matchNewKeywords ( ){
        // The array that will contain the app_keywords table rows to be inserted.
        $array      = [];
        // Get all applications
        $apps       = AppDetails::all();
        // Get the new keywords
        $keywords   = Keyword::where( 'updated', '=', PENDING_UPDATED )
                               ->get();
        if( sizeof( $keywords ) > 0 ){
            foreach ($keywords as $keyword){
                $name           = strtolower($keyword->name);
                $keyword_id     =$keyword->id;
                foreach( $apps as $app ){
                    $title  = strtolower($app->title);
                    $desc   = strtolower($app->description);

                    $titleCount = substr_count($title, $name);
                    $descCount  = substr_count($desc, $name);

                    $count      = $titleCount + $descCount;
                    if( $count > 0 ){
                        $array[] = [
                                'app_id'        => $app->id,
                                'keyword_id'    => $keyword_id,
                                'priority'      => $count
                            ];
                    }
                }
            }
            $keywordIds = array_pluck($keywords, 'id');
            Keyword::whereIn('id', $keywordIds)->update(['updated' => SUCCESS_UPDATED]);
        }
        return $array;
    }
}