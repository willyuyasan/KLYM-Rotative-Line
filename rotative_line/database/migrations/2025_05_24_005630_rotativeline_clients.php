<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        DB::statement("
        CREATE OR REPLACE VIEW rotativeline_clients as
        with 
        rl_status_piority as (
        select
        rl.issuer_tax_number
        ,rl.rotative_line_status

        ,case
            when rl.rotative_line_status in ('rotative line closed') then 1
            when rl.rotative_line_status in ('ok') then 2
            when rl.rotative_line_status in ('near to cut date') then 3
            when rl.rotative_line_status in ('1-30 default days') then 4
            when rl.rotative_line_status in ('31-60 default days') then 5
            when rl.rotative_line_status in ('61-90 default days') then 6
            when rl.rotative_line_status in ('91-120 default days') then 7
            when rl.rotative_line_status in ('121 or more default days') then 8
            when rl.rotative_line_status in ('CASTIGO') then 9
            when rl.rotative_line_status in ('LEGAL') then 10
            else 0
        end as rl_status_priority

        ,rl.collection_status
        ,case
            when rl.collection_status in ('CERRADA') then 1
            when rl.collection_status in ('VIGENTE') then 2
            when rl.collection_status in ('MORA') then 3
            when rl.collection_status in ('LEGAL VIGENTE') then 4
            when rl.collection_status in ('LEGAL MORA') then 5
            when rl.collection_status in ('CASTIGO') then 6
            else 0
        end as rl_collection_priority
            
        from rotativelines rl
        ),

        client_status as (
        SELECT DISTINCT ON (issuer_tax_number)
        issuer_tax_number
        ,rotative_line_status as client_status
        FROM rl_status_piority
        ORDER  BY issuer_tax_number, rl_status_priority DESC
        ),

        client_collection_status as (
        SELECT DISTINCT ON (issuer_tax_number)
        issuer_tax_number
        ,collection_status as client_collection_status
        FROM rl_status_piority
        ORDER  BY issuer_tax_number, rl_collection_priority DESC
        ),

        clients as (
        select 
        rl.issuer_tax_number
        ,max(rl.issuer_name) as issuer_name
        ,min(rl.disbursement_date) as first_disbursement_date
        ,min(rl.number_line) as min_number_line
        ,count(*) as rls
        ,sum(rl.rl_active_id::int) as active_rls
        ,sum(rl.value_to_issuer) as capital_lend
        ,sum(rl.capital_debt) as capital_debt 
        ,sum(rl.received_payment) as received_payment
        from rotativelines rl
        group by 
        rl.issuer_tax_number
        )
        select 
        row_number() over(order by first_disbursement_date, min_number_line) as id
        ,cls.*
        ,s.client_status
        ,cs.client_collection_status

        ,case
            when s.client_status not in ('rotative line closed') then true
            else False
        end as active_client

        from clients cls
        left join client_status s on (cls.issuer_tax_number = s.issuer_tax_number)
        left join client_collection_status cs on (cls.issuer_tax_number = cs.issuer_tax_number)



        CREATE OR REPLACE VIEW paymentmonths as
        with 
        payments as (
        select 
        p.*
        ,date_part('year', p.credit_dates::date)||'-'||lpad(date_part('month', p.credit_dates::date)::text,2,'0') as payment_month
        from rlpayments p
        )
        select
        row_number() over(order by p.payment_month) as id
        ,p.payment_month
        ,count(*) as payments
        ,sum(p.payment_amount) as payment_amount
        from payments p
        group by 
        p.payment_month
        order by
        p.payment_month


        CREATE OR REPLACE VIEW disbursementmonths as
        with 
        disbursement as (
        select 
        rl.disbursement_date
        ,rl.value_to_issuer
        ,date_part('year', rl.disbursement_date::date)||'-'||lpad(date_part('month', rl.disbursement_date::date)::text,2,'0') as disbursement_month
        from rotativelines rl
        )
        select
        row_number() over(order by d.disbursement_month) as id
        ,d.disbursement_month
        ,count(*) as disbursments
        ,sum(d.value_to_issuer) as disbursement_amount
        from disbursement d
        group by 
        d.disbursement_month
        order by
        d.disbursement_month

        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        DB::statement('DROP VIEW IF EXISTS rotativeline_clients;');
    }
};
