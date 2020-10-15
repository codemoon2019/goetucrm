<?php

use Illuminate\Database\Seeder;
use App\Models\CalendarReminder;

class CalendarRemindersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CalendarReminder::create([
            'code' => 'CR0001',
            'description' => 'Never',
            'intervals' => '',
            'remarks' => '',
            'sequence' => 1,
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        CalendarReminder::create([
            'code' => 'CR0002',
            'description' => 'At time of event',
            'intervals' => '',
            'remarks' => 'An scheduled calendar appointment is now ongoing.',
            'sequence' => 2,
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        CalendarReminder::create([
            'code' => 'CR0003',
            'description' => '1 minute before event',
            'intervals' => 'INTERVAL 1 MINUTE',
            'remarks' => 'An scheduled calendar appointment will start in 1 minute.',
            'sequence' => 3,
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        CalendarReminder::create([
            'code' => 'CR0004',
            'description' => '5 minutes before event',
            'intervals' => 'INTERVAL 5 MINUTE',
            'remarks' => 'An scheduled calendar appointment will start in 5 minutes.',
            'sequence' => 4,
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        CalendarReminder::create([
            'code' => 'CR0005',
            'description' => '10 minutes before event',
            'intervals' => 'INTERVAL 10 MINUTE',
            'remarks' => 'An scheduled calendar appointment will start in 10 minutes.',
            'sequence' => 5,
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        CalendarReminder::create([
            'code' => 'CR0006',
            'description' => '15 minutes before event',
            'intervals' => 'INTERVAL 15 MINUTE',
            'remarks' => 'An scheduled calendar appointment will start in 15 minutes.',
            'sequence' => 6,
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        CalendarReminder::create([
            'code' => 'CR0007',
            'description' => '30 minutes before event',
            'intervals' => 'INTERVAL 30 MINUTE',
            'remarks' => 'An scheduled calendar appointment will start in 30 minutes.',
            'sequence' => 7,
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        CalendarReminder::create([
            'code' => 'CR0008',
            'description' => '45 minutes before event',
            'intervals' => 'INTERVAL 45 MINUTE',
            'remarks' => 'An scheduled calendar appointment will start in 45 minutes.',
            'sequence' => 8,
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        CalendarReminder::create([
            'code' => 'CR0009',
            'description' => '60 minutes before event',
            'intervals' => 'INTERVAL 60 MINUTE',
            'remarks' => 'An scheduled calendar appointment will start in 60 minutes.',
            'sequence' => 9,
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        CalendarReminder::create([
            'code' => 'CR0010',
            'description' => '2 hours before event',
            'intervals' => 'INTERVAL 120 MINUTE',
            'remarks' => 'An scheduled calendar appointment will start in 2 hours.',
            'sequence' => 10,
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        CalendarReminder::create([
            'code' => 'CR0011',
            'description' => '3 hours before event',
            'intervals' => 'INTERVAL 180 MINUTE',
            'remarks' => 'An scheduled calendar appointment will start in 3 hours.',
            'sequence' => 11,
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        CalendarReminder::create([
            'code' => 'CR0012',
            'description' => '4 hours before event',
            'intervals' => 'INTERVAL 240 MINUTE',
            'remarks' => 'An scheduled calendar appointment will start in 4 hours.',
            'sequence' => 12,
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        CalendarReminder::create([
            'code' => 'CR0013',
            'description' => '5 hours before event',
            'intervals' => 'INTERVAL 300 MINUTE',
            'remarks' => 'An scheduled calendar appointment will start in 5 hours.',
            'sequence' => 13,
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        CalendarReminder::create([
            'code' => 'CR0014',
            'description' => '18 hours before event',
            'intervals' => 'INTERVAL 1080 MINUTE',
            'remarks' => 'An scheduled calendar appointment will start in 18 hours.',
            'sequence' => 14,
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        CalendarReminder::create([
            'code' => 'CR0015',
            'description' => '1 day before event',
            'intervals' => 'INTERVAL 1 DAY',
            'remarks' => 'An scheduled calendar appointment will start tommorow.',
            'sequence' => 15,
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        CalendarReminder::create([
            'code' => 'CR0016',
            'description' => '2 days before event',
            'intervals' => 'INTERVAL 2 DAY',
            'remarks' => 'An scheduled calendar appointment will start 2 days from now.',
            'sequence' => 16,
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        CalendarReminder::create([
            'code' => 'CR0017',
            'description' => '3 days before event',
            'intervals' => 'INTERVAL 3 DAY',
            'remarks' => 'An scheduled calendar appointment will start 3 days from now.',
            'sequence' => 17,
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

        CalendarReminder::create([
            'code' => 'CR0018',
            'description' => '4 days before event',
            'intervals' => 'INTERVAL 4 DAY',
            'remarks' => 'An scheduled calendar appointment will start 4 days from now.',
            'sequence' => 18,
            'create_by' => 'seeder',
            'status' => 'A'
        ]);


        CalendarReminder::create([
            'code' => 'CR0019',
            'description' => '1 week before event',
            'intervals' => 'INTERVAL 7 DAY',
            'remarks' => 'An scheduled calendar appointment will start 7 days from now.',
            'sequence' => 19,
            'create_by' => 'seeder',
            'status' => 'A'
        ]);


        CalendarReminder::create([
            'code' => 'CR0020',
            'description' => '2 weeks before event',
            'intervals' => 'INTERVAL 14 DAY',
            'remarks' => 'An scheduled calendar appointment will start 14 days from now.',
            'sequence' => 20,
            'create_by' => 'seeder',
            'status' => 'A'
        ]);

    }
}
