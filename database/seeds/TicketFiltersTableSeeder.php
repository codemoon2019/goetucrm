<?php

use Illuminate\Database\Seeder;

class TicketFiltersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('ticket_filters')->delete();
        
        \DB::table('ticket_filters')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => 'TF0001',
                'description' => 'Open Tickets',
                'remarks' => 'and h.status=\'O\'',
                'sequence' => 1,
                'query_sequence' => 1,
                'create_by' => 'Seeder',
                'status' => 'A',
                'is_admin' => 0,
                'created_at' => '2018-06-22 06:22:13',
                'updated_at' => '2018-06-22 06:22:13',
            ),
            1 => 
            array (
                'id' => 2,
                'code' => 'TF0002',
                'description' => 'Unassigned Tickets',
            'remarks' => 'and ifnull(h.assignee,\'\')=\'\'',
                'sequence' => 2,
                'query_sequence' => 2,
                'create_by' => 'Seeder',
                'status' => 'A',
                'is_admin' => 1,
                'created_at' => '2018-06-22 06:22:13',
                'updated_at' => '2018-06-22 06:22:13',
            ),
            2 => 
            array (
                'id' => 3,
                'code' => 'TF0003',
                'description' => 'All Unsolved Tickets',
                'remarks' => 'and h.status<>\'R\'',
                'sequence' => 3,
                'query_sequence' => 3,
                'create_by' => 'Seeder',
                'status' => 'A',
                'is_admin' => 1,
                'created_at' => '2018-06-22 06:22:13',
                'updated_at' => '2018-06-22 06:22:13',
            ),
            3 => 
            array (
                'id' => 4,
                'code' => 'TF0004',
                'description' => 'Recently Updated Tickets',
                'remarks' => 'order by h.update_date desc',
                'sequence' => 4,
                'query_sequence' => 12,
                'create_by' => 'Seeder',
                'status' => 'A',
                'is_admin' => 1,
                'created_at' => '2018-06-22 06:22:13',
                'updated_at' => '2018-06-22 06:22:13',
            ),
            4 => 
            array (
                'id' => 5,
                'code' => 'TF0005',
                'description' => 'New Tickets In Your Group',
                'remarks' => 'order by h.id desc',
                'sequence' => 5,
                'query_sequence' => 13,
                'create_by' => 'Seeder',
                'status' => 'A',
                'is_admin' => 1,
                'created_at' => '2018-06-22 06:22:13',
                'updated_at' => '2018-06-22 06:22:13',
            ),
            5 => 
            array (
                'id' => 6,
                'code' => 'TF0006',
                'description' => 'Pending Tickets',
                'remarks' => 'and h.status=\'P\'',
                'sequence' => 6,
                'query_sequence' => 4,
                'create_by' => 'Seeder',
                'status' => 'A',
                'is_admin' => 0,
                'created_at' => '2018-06-22 06:22:13',
                'updated_at' => '2018-06-22 06:22:13',
            ),
            6 => 
            array (
                'id' => 7,
                'code' => 'TF0007',
                'description' => 'Resolved Ticket',
            'remarks' => 'and h.status IN(\'R\')',
                'sequence' => 7,
                'query_sequence' => 5,
                'create_by' => 'Seeder',
                'status' => 'A',
                'is_admin' => 0,
                'created_at' => '2018-06-22 06:22:13',
                'updated_at' => '2018-06-22 06:22:13',
            ),
            7 => 
            array (
                'id' => 8,
                'code' => 'TF0008',
                'description' => 'Customer Responded',
            'remarks' => 'and h.id in (select ticket_id from ticket_detail where create_by=h.create_by)',
                'sequence' => 8,
                'query_sequence' => 6,
                'create_by' => 'Seeder',
                'status' => 'A',
                'is_admin' => 1,
                'created_at' => '2018-06-22 06:22:13',
                'updated_at' => '2018-06-22 06:22:13',
            ),
            8 => 
            array (
                'id' => 9,
                'code' => 'TF0009',
                'description' => 'Urgent and High Priority',
            'remarks' => 'and h.priority in (\'H\',\'U\')',
                'sequence' => 9,
                'query_sequence' => 7,
                'create_by' => 'Seeder',
                'status' => 'A',
                'is_admin' => 1,
                'created_at' => '2018-06-22 06:22:13',
                'updated_at' => '2018-06-22 06:22:13',
            ),
            9 => 
            array (
                'id' => 10,
                'code' => 'TF0010',
                'description' => 'Overdue Tickets',
            'remarks' => 'and h.due_date < now()',
                'sequence' => 11,
                'query_sequence' => 8,
                'create_by' => 'Seeder',
                'status' => 'A',
                'is_admin' => 1,
                'created_at' => '2018-06-22 06:22:13',
                'updated_at' => '2018-06-22 06:22:13',
            ),
            10 => 
            array (
                'id' => 11,
                'code' => 'TF0011',
                'description' => 'All Tickets',
                'remarks' => '',
                'sequence' => 12,
                'query_sequence' => 9,
                'create_by' => 'Seeder',
                'status' => 'A',
                'is_admin' => 0,
                'created_at' => '2018-06-22 06:22:13',
                'updated_at' => '2018-06-22 06:22:13',
            ),
            11 => 
            array (
                'id' => 12,
                'code' => 'TF0012',
                'description' => 'Starred',
                'remarks' => 'and h.is_starred=1',
                'sequence' => 13,
                'query_sequence' => 10,
                'create_by' => 'Seeder',
                'status' => 'I',
                'is_admin' => 0,
                'created_at' => '2018-06-22 06:22:13',
                'updated_at' => '2018-06-22 06:22:13',
            ),
            12 => 
            array (
                'id' => 13,
                'code' => 'TF0013',
                'description' => 'Trash',
                'remarks' => 'and h.is_deleted=1
',
                'sequence' => 14,
                'query_sequence' => 11,
                'create_by' => 'Seeder',
                'status' => 'A',
                'is_admin' => 1,
                'created_at' => '2018-06-22 06:22:13',
                'updated_at' => '2018-06-22 06:22:13',
            ),
            13 => 
            array (
                'id' => 14,
                'code' => 'TF0014',
                'description' => 'Low and Medium Priority',
            'remarks' => 'and h.priority in (\'L\',\'M\')',
                'sequence' => 10,
                'query_sequence' => 7,
                'create_by' => 'Seeder',
                'status' => 'A',
                'is_admin' => 1,
                'created_at' => '2018-06-22 06:22:13',
                'updated_at' => '2018-06-22 06:22:13',
            ),
            14 => 
            array (
                'id' => 15,
                'code' => 'TF0015',
                'description' => 'Waiting on Customer Tickets',
                'remarks' => 'and h.status = \'WC\'',
                'sequence' => 7,
                'query_sequence' => 5,
                'create_by' => 'Seeder',
                'status' => 'A',
                'is_admin' => 1,
                'created_at' => '2018-06-22 06:22:13',
                'updated_at' => '2018-06-22 06:22:13',
            ),
            15 => 
            array (
                'id' => 16,
                'code' => 'TF0016',
                'description' => 'Waiting on Third Party Tickets',
                'remarks' => 'and h.status = \'WT\'',
                'sequence' => 7,
                'query_sequence' => 5,
                'create_by' => 'Seeder',
                'status' => 'A',
                'is_admin' => 1,
                'created_at' => '2018-06-22 06:22:13',
                'updated_at' => '2018-06-22 06:22:13',
            ),
            16 => 
            array (
                'id' => 17,
                'code' => 'overdue',
                'description' => 'Overdue',
            'remarks' => 'and h.due_date < now()',
                'sequence' => -1,
                'query_sequence' => -1,
                'create_by' => 'Seeder',
                'status' => 'I',
                'is_admin' => 0,
                'created_at' => '2018-06-22 06:22:13',
                'updated_at' => '2018-06-22 06:22:13',
            ),
            17 => 
            array (
                'id' => 18,
                'code' => 'today',
                'description' => 'Overdue Today',
            'remarks' => 'and h.due_date >= DATE_FORMAT(now(),\'%Y-%m-%d\') and h.due_date < DATE_FORMAT(DATE_ADD(now(), INTERVAL 1 DAY),\'%Y-%m-%d\')',
                'sequence' => -1,
                'query_sequence' => -1,
                'create_by' => 'Seeder',
                'status' => 'I',
                'is_admin' => 0,
                'created_at' => '2018-06-22 06:22:13',
                'updated_at' => '2018-06-22 06:22:13',
            ),
            18 => 
            array (
                'id' => 19,
                'code' => 'tomorrow',
                'description' => 'Overdue Tomorrow',
            'remarks' => '		and h.due_date >= DATE_FORMAT(DATE_ADD(now(), INTERVAL 1 DAY),\'%Y-%m-%d\') 
and h.due_date < DATE_FORMAT(DATE_ADD(now(), INTERVAL 2 DAY),\'%Y-%m-%d\') ',
                'sequence' => -1,
                'query_sequence' => -1,
                'create_by' => 'Seeder',
                'status' => 'I',
                'is_admin' => 0,
                'created_at' => '2018-06-22 06:22:13',
                'updated_at' => '2018-06-22 06:22:13',
            ),
            19 => 
            array (
                'id' => 20,
                'code' => 'next_8',
                'description' => 'Next 8 hours',
            'remarks' => 'and h.due_date >= DATE_FORMAT(now(),\'%Y-%m-%d\') and h.due_date < date_sub(now(), interval 8 hour)',
                'sequence' => -1,
                'query_sequence' => -1,
                'create_by' => 'Seeder',
                'status' => 'I',
                'is_admin' => 0,
                'created_at' => '2018-06-22 06:22:13',
                'updated_at' => '2018-06-22 06:22:13',
            ),
            20 => 
            array (
                'id' => 21,
                'code' => 'TF0017',
                'description' => 'Closed Ticket',
            'remarks' => 'and h.status IN(\'C\')',
                'sequence' => 15,
                'query_sequence' => -1,
                'create_by' => 'Seeder',
                'status' => 'A',
                'is_admin' => 0,
                'created_at' => '2018-06-22 06:22:13',
                'updated_at' => '2018-06-22 06:22:13',
            ),
        ));
        
        
    }
}