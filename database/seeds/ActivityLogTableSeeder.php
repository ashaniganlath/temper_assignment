<?php

use Flynsarmy\CsvSeeder\CsvSeeder;

class ActivityLogTableSeeder extends CsvSeeder
{

    public function __construct()
    {
        $this->table = 'activity_log';
        $this->filename = database_path('seeds/csvs/temper_onboarding_data.csv');
        $this->csv_delimiter = ';';
        $this->offset_rows = 1;
        $this->mapping = [
            0 => 'user_id',
            1 => 'created_at',
            2 => 'onboarding_percentage',
        ];
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        parent::run();
    }
}
