<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Application;
use App\Models\AppDetails;
use Raulr\GooglePlayScraper\Scraper;
use Raulr\GooglePlayScraper\Exception\NotFoundException;

class GetApplicationInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getAppsInfo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get the application informtion.';

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
        $scraper = new scraper;
        $apps = Application::where( 'updated', '=', PENDING_UPDATED )
                            ->get();
        if( sizeof( $apps ) > 0 ){
            foreach ($apps as $key => $app) {
                $id = $app->package_id;
                try{
                    $data = $scraper->getApp( $id );
                    if( sizeof( $data ) >0 ){
                        $app->updated = SUCCESS_UPDATED;
                        
                        // get a new instance of AppDetails .
                        $appDetails = AppDetails::find( $app->id );
                        if( is_null($appDetails) ){
                            $appDetails = new AppDetails;
                        }

                        $appDetails->id = $app->id;
                        $appDetails->title = isset($data['title']) ? $data['title'] : '';
                        $appDetails->description = isset($data['description']) ? $data['description'] : '';
                        $appDetails->save();

                    }
                }catch(NotFoundException $e){
                    echo $e->getMessage();
                    $app->updated = ERROR_UPDATED;
                }
                $app->save();
            }
        }
    }

}
