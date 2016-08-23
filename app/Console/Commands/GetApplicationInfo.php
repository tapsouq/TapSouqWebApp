<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Application;
use App\Models\AppDetails;
use Raulr\GooglePlayScraper\Scraper;

class GetApplicationInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'application:getifo';

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
        $apps = Application::where( 'updated', '=', 0 )
                            ->get();
        if( sizeof( $apps ) > 0 ){
            foreach ($apps as $key => $app) {
                $id = $app->package_id;
                $data = $scraper->getApp( $id );
                
                if( sizeof( $data ) >0 ){
                    $app->updated = 1;
                    
                    // get a new instance of AppDetails .
                    $appDetails = AppDetails::find( $app->id );
                    if( is_null($appDetails) ){
                        $appDetails = new AppDetails;
                    }

                    $appDetails->id = $app->id;
                    $appDetails->title = isset($data['title']) ? $data['title'] : '';
                    $appDetails->description = isset($data['description']) ? $data['description'] : '';
                    $appDetails->save();

                }else{
                    $app->updated = 2;
                }
                $app->save();
            }
        }
    }

}
