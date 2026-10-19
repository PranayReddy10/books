<?php

namespace App\Exports;

use App\User;
use App\Department;
use App\SubscriptionPlan;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;


/**
 * Every student account with everything stored on it: contact, academic
 * details, how they signed up, subscription, coins and activity. Read from
 * the query in chunks, so a large user base does not need it all in memory.
 */
class UsersExport implements FromQuery, WithHeadings, ShouldAutoSize, WithEvents, WithMapping
{
    use Exportable;

    /** Department and plan names by id, looked up once instead of per row. */
    private $departments;
    private $plans;

    public function __construct()
    {
        $this->departments = Department::pluck('department_name', 'id')->all();
        $this->plans = SubscriptionPlan::pluck('plan_name', 'id')->all();
    }

    public function query()
    {
        return User::query()->where('usertype', 'User')->orderBy('id', 'DESC');
    }

    public function map($user): array
    {
        $departmentId = $user->getAttribute('department_id');
        $planId = $user->getAttribute('plan_id');
        $expDate = $user->getAttribute('exp_date');

        // Same rule the app uses to send Google users to "complete profile".
        $complete = true;
        foreach (array('phone', 'gender', 'university', 'department_id', 'college') as $field) {
            if (trim((string) $user->getAttribute($field)) === '') {
                $complete = false;
            }
        }

        return [
            $user->id,
            $user->name,
            $user->getAttribute('username'),
            $user->email,
            $user->phone,
            $user->getAttribute('gender'),
            $user->getAttribute('university'),
            $departmentId && isset($this->departments[$departmentId])
                ? stripslashes($this->departments[$departmentId]) : '',
            $user->getAttribute('college'),
            $user->getAttribute('rollnumber'),
            $user->getAttribute('branch'),
            $user->getAttribute('regulation'),
            $user->getAttribute('degree'),
            $user->getAttribute('father_name'),
            $complete ? 'Yes' : 'No',
            $user->getAttribute('social_login_type') ? ucfirst($user->getAttribute('social_login_type')) : 'Email',
            $user->getAttribute('registered_via'),
            $user->getAttribute('is_verified') ? 'Yes' : 'No',
            (string) $user->getAttribute('status') === '0' ? 'Banned' : 'Active',
            $planId && isset($this->plans[$planId]) ? $this->plans[$planId] : '',
            $expDate ? date('d-m-Y', is_numeric($expDate) ? (int) $expDate : strtotime($expDate)) : '',
            (int) $user->getAttribute('coin_balance'),
            $this->date($user->getAttribute('created_at')),
            $this->date($user->getAttribute('last_login_at')),
            $user->getAttribute('last_login_via'),
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Name',
            'Username',
            'Email',
            'Phone',
            'Gender',
            'University',
            'Department',
            'College',
            'Hall Ticket',
            'Branch',
            'Regulation',
            'Degree',
            'Father Name',
            'Profile Complete',
            'Signed Up With',
            'Registered Via',
            'Verified',
            'Status',
            'Plan',
            'Plan Expiry',
            'Coins',
            'Joined',
            'Last Login',
            'Last Login Via',
        ];
    }

    private function date($value)
    {
        if (!$value) {
            return '';
        }
        $ts = $value instanceof \DateTimeInterface ? $value->getTimestamp() : strtotime((string) $value);
        return $ts ? date('d-m-Y H:i', $ts) : '';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $cellRange = 'A1:Y1'; // All headers
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
            },
        ];
    }

}
