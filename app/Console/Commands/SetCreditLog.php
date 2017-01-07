<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetCreditLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setCreditLog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To set credit every day for every user.';

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
        //
        $users          = \App\User::all()->toArray();
        $insertArray    = array_map(["App\Console\Commands\SetCreditLog", "setInsertArray"], $users);
        
        \DB::table('daily_log')->insert( $insertArray );
    }

    /**
     * setInsertArray. To set the insert array.
     *
     * @param array $row
     * @return array
     * @author Abdulkareem Mohammed <a.esawy.sapps@gmail.com>
     * @copyright Smart Applications Co. <www.smartapps-ye.com>
     */
    public static function setInsertArray ( $row ){
        return [
                'user_id'       => $row['id'],
                'credit'        => $row['credit'],
                'date'          => date("Y-m-d H:i:s", (time() - ( 60 * 60 )) ),      
                'created_at'    => date("Y-m-d H:i:s", (time() - ( 60 * 60 )) ),      
                'updated_at'    => date("Y-m-d H:i:s", (time() - ( 60 * 60 )) )      
            ];
    }
}
