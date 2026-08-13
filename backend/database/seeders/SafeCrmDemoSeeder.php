<?php

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Enums\LeadStatus;
use App\Models\Application;
use App\Models\ApplicationActivity;
use App\Models\Appointment;
use App\Models\Consultant;
use App\Models\Country;
use App\Models\Course;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Student;
use App\Models\StudentNotification;
use App\Models\University;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Production-safe CRM demo data.
 * - Never truncates / never deletes existing rows
 * - Only upserts clearly demo-tagged records
 * - Uses firstOrCreate for leads/docs/appointments so real data is not overwritten
 */
class SafeCrmDemoSeeder extends Seeder
{
    public function run(): void
    {
        $consultantUser = User::query()->firstOrCreate(
            ['email' => 'consultant@wineducation.local'],
            [
                'name' => 'Education Interntions Consultant',
                'password' => Hash::make('password'),
                'is_active' => true,
                'locale' => 'th',
            ]
        );
        if (! $consultantUser->hasRole('consultant')) {
            $consultantUser->assignRole('consultant');
        }

        Consultant::query()->firstOrCreate(
            ['user_id' => $consultantUser->id],
            ['employee_code' => 'WIN-C01', 'is_available' => true, 'max_leads' => 40]
        );

        foreach ([
            ['slug' => 'passport', 'name_th' => 'พาสปอร์ต', 'name_en' => 'Passport', 'is_required' => true],
            ['slug' => 'transcript', 'name_th' => 'ทรานสคริปต์', 'name_en' => 'Transcript', 'is_required' => true],
            ['slug' => 'ielts', 'name_th' => 'ผล IELTS', 'name_en' => 'IELTS', 'is_required' => true],
            ['slug' => 'certificate', 'name_th' => 'วุฒิการศึกษา', 'name_en' => 'Certificate', 'is_required' => false],
        ] as $type) {
            DocumentType::query()->firstOrCreate(['slug' => $type['slug']], $type);
        }

        $studentUser = User::query()->firstOrCreate(
            ['email' => 'student@wineducation.local'],
            [
                'name' => 'สมชาย ใจดี',
                'phone' => '0812345678',
                'password' => Hash::make('password'),
                'is_active' => true,
                'locale' => 'th',
            ]
        );
        if (! $studentUser->hasRole('student')) {
            $studentUser->assignRole('student');
        }

        $student = Student::query()->firstOrCreate(
            ['user_id' => $studentUser->id],
            [
                'nationality' => 'Thai',
                'education_level' => 'Bachelor',
                'preferred_locale' => 'th',
            ]
        );

        $countryId = Country::query()->where('slug', 'uk')->value('id')
            ?? Country::query()->orderBy('id')->value('id');
        $universityId = University::query()->where('slug', 'university-of-manchester')->value('id')
            ?? University::query()->orderBy('id')->value('id');
        $courseId = Course::query()->where('slug', 'msc-management')->value('id')
            ?? Course::query()->orderBy('id')->value('id');

        $application = Application::query()->firstOrCreate(
            ['application_no' => 'WIN-DEMO-0001'],
            [
                'student_id' => $student->id,
                'consultant_id' => $consultantUser->id,
                'country_id' => $countryId,
                'university_id' => $universityId,
                'course_id' => $courseId,
                'intake' => 'September 2026',
                'status' => ApplicationStatus::DocumentRequired,
                'next_action' => 'Upload IELTS result',
                'current_step' => 6,
                'personal_data' => [
                    'name' => $studentUser->name,
                    'email' => $studentUser->email,
                    'phone' => $studentUser->phone,
                ],
                'submitted_at' => now()->subDays(3),
            ]
        );

        if (! $application->consultant_id) {
            $application->update(['consultant_id' => $consultantUser->id]);
        }

        $demoLeads = [
            [
                'email' => 'demo.lead.new@wineducation.local',
                'name' => 'ณัฐชา สายฝน (Demo)',
                'phone' => '0891112233',
                'source' => 'website',
                'status' => LeadStatus::New,
                'message' => 'สนใจเรียนต่อ UK ปี 2026',
            ],
            [
                'email' => 'demo.lead.contacted@wineducation.local',
                'name' => 'James Wong (Demo)',
                'phone' => '0823344556',
                'source' => 'line',
                'status' => LeadStatus::Contacted,
                'message' => 'ขอคำปรึกษาทุน Australia',
            ],
            [
                'email' => 'demo.lead.document@wineducation.local',
                'name' => 'พิมพ์ใจ อรุณ (Demo)',
                'phone' => '0819988776',
                'source' => 'event',
                'status' => LeadStatus::Document,
                'message' => 'เตรียมเอกสารสมัคร Manchester',
                'student_id' => $student->id,
            ],
        ];

        foreach ($demoLeads as $leadData) {
            $lead = Lead::query()->firstOrCreate(
                ['email' => $leadData['email']],
                [
                    'name' => $leadData['name'],
                    'phone' => $leadData['phone'],
                    'source' => $leadData['source'],
                    'status' => $leadData['status'],
                    'assigned_to' => $consultantUser->id,
                    'country_id' => $countryId,
                    'university_id' => $universityId,
                    'course_id' => $courseId,
                    'student_id' => $leadData['student_id'] ?? null,
                    'message' => $leadData['message'],
                    'notes' => 'Safe CRM demo lead — safe to keep or delete',
                    'last_contact_at' => now()->subDay(),
                ]
            );

            LeadActivity::query()->firstOrCreate(
                [
                    'lead_id' => $lead->id,
                    'type' => 'note',
                    'body' => 'Initial CRM demo note for '.$lead->name,
                ],
                [
                    'user_id' => $consultantUser->id,
                    'from_status' => null,
                    'to_status' => $lead->status instanceof LeadStatus ? $lead->status->value : (string) $lead->status,
                ]
            );
        }

        $docLead = Lead::query()->where('email', 'demo.lead.document@wineducation.local')->first();

        $demoDocuments = [
            [
                'name' => '[Demo] Passport - Somchai',
                'document_type_id' => DocumentType::query()->where('slug', 'passport')->value('id'),
                'status' => 'approved',
                'path' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=1200&q=80',
                'review_note' => 'Approved in demo seed',
            ],
            [
                'name' => '[Demo] Transcript - Bachelor',
                'document_type_id' => DocumentType::query()->where('slug', 'transcript')->value('id'),
                'status' => 'pending',
                'path' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=1200&q=80',
                'review_note' => null,
            ],
            [
                'name' => '[Demo] IELTS Result',
                'document_type_id' => DocumentType::query()->where('slug', 'ielts')->value('id'),
                'status' => 'rejected',
                'path' => 'https://images.unsplash.com/photo-1456513080080-7e9b1b0c5f2f?auto=format&fit=crop&w=1200&q=80',
                'review_note' => 'Please upload a clearer scan',
            ],
        ];

        foreach ($demoDocuments as $doc) {
            Document::query()->firstOrCreate(
                [
                    'student_id' => $student->id,
                    'application_id' => $application->id,
                    'name' => $doc['name'],
                ],
                [
                    'document_type_id' => $doc['document_type_id'],
                    'path' => $doc['path'],
                    'status' => $doc['status'],
                    'review_note' => $doc['review_note'],
                ]
            );
        }

        ApplicationActivity::query()->firstOrCreate(
            [
                'application_id' => $application->id,
                'type' => 'note',
                'body' => 'Demo: waiting for IELTS re-upload',
            ],
            [
                'user_id' => $consultantUser->id,
                'from_status' => ApplicationStatus::Consultation->value,
                'to_status' => ApplicationStatus::DocumentRequired->value,
            ]
        );

        Appointment::query()->firstOrCreate(
            [
                'student_id' => $student->id,
                'title' => '[Demo] Consultation with Education Interntions Advisor',
            ],
            [
                'lead_id' => $docLead?->id,
                'consultant_id' => $consultantUser->id,
                'type' => 'consultation',
                'starts_at' => now()->addDays(5)->setTime(14, 0),
                'ends_at' => now()->addDays(5)->setTime(15, 0),
                'status' => 'scheduled',
                'notes' => 'Discuss documents and intake timeline',
            ]
        );

        Appointment::query()->firstOrCreate(
            [
                'student_id' => $student->id,
                'title' => '[Demo] Document review call',
            ],
            [
                'lead_id' => $docLead?->id,
                'consultant_id' => $consultantUser->id,
                'type' => 'document_review',
                'starts_at' => now()->addDays(8)->setTime(10, 30),
                'ends_at' => now()->addDays(8)->setTime(11, 0),
                'status' => 'scheduled',
                'notes' => 'Review passport + transcript before submission',
            ]
        );

        StudentNotification::query()->firstOrCreate(
            [
                'user_id' => $studentUser->id,
                'title' => '[Demo] Welcome to Student Portal',
            ],
            [
                'type' => 'info',
                'body' => 'Upload your documents and track your application progress here.',
                'link' => '/student/documents',
                'read_at' => null,
            ]
        );

        StudentNotification::query()->firstOrCreate(
            [
                'user_id' => $studentUser->id,
                'title' => '[Demo] Document reminder',
            ],
            [
                'type' => 'warning',
                'body' => 'Please upload your IELTS result for application WIN-DEMO-0001.',
                'link' => '/student/documents',
                'read_at' => null,
            ]
        );

        $this->command?->info('SafeCrmDemoSeeder finished (no deletes, demo-only upserts).');
        $this->command?->info('Documents: '.Document::query()->where('name', 'like', '[Demo]%')->count());
        $this->command?->info('Leads: '.Lead::query()->where('email', 'like', 'demo.lead.%')->count());
        $this->command?->info('Appointments: '.Appointment::query()->where('title', 'like', '[Demo]%')->count());
    }
}
