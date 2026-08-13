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
/**
 * Insert-only CRM demo data for production.
 * Uses firstOrCreate — never overwrites existing rows.
 */
class SafeDemoCrmSeeder extends Seeder
{
    public function run(): void
    {
        $this->command?->info('SafeDemoCrmSeeder: insert-only (no overwrite).');

        foreach ([
            ['slug' => 'passport', 'name_th' => 'พาสปอร์ต', 'name_en' => 'Passport', 'is_required' => true],
            ['slug' => 'transcript', 'name_th' => 'ทรานสคริปต์', 'name_en' => 'Transcript', 'is_required' => true],
            ['slug' => 'ielts', 'name_th' => 'ผล IELTS', 'name_en' => 'IELTS', 'is_required' => true],
            ['slug' => 'certificate', 'name_th' => 'วุฒิการศึกษา', 'name_en' => 'Certificate', 'is_required' => false],
        ] as $type) {
            DocumentType::query()->firstOrCreate(['slug' => $type['slug']], $type);
        }

        $consultantUser = User::query()->firstOrCreate(
            ['email' => 'consultant@wineducation.local'],
            [
                'name' => 'Education Interntions Consultant',
                'password' => 'password',
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

        $studentUser = User::query()->firstOrCreate(
            ['email' => 'student@wineducation.local'],
            [
                'name' => 'สมชาย ใจดี',
                'phone' => '0812345678',
                'password' => 'password',
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

        $demoLeads = [
            [
                'email' => 'lead.new@example.com',
                'name' => 'ณัฐชา สายฝน',
                'phone' => '0891112233',
                'source' => 'website',
                'status' => LeadStatus::New,
                'message' => 'สนใจเรียนต่อ UK ปี 2026',
                'student_id' => null,
            ],
            [
                'email' => 'lead.contacted@example.com',
                'name' => 'James Wong',
                'phone' => '0823344556',
                'source' => 'line',
                'status' => LeadStatus::Contacted,
                'message' => 'ขอคำปรึกษาทุน Australia',
                'student_id' => null,
            ],
            [
                'email' => 'lead.doc@example.com',
                'name' => 'พิมพ์ใจ อรุณ',
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
                    'student_id' => $leadData['student_id'],
                    'message' => $leadData['message'],
                    'notes' => 'Demo lead for CRM pipeline',
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

        $docLead = Lead::query()->where('email', 'lead.doc@example.com')->first();

        $passportTypeId = DocumentType::query()->where('slug', 'passport')->value('id');
        $transcriptTypeId = DocumentType::query()->where('slug', 'transcript')->value('id');
        $ieltsTypeId = DocumentType::query()->where('slug', 'ielts')->value('id');

        foreach ([
            [
                'name' => 'Passport - Somchai',
                'document_type_id' => $passportTypeId,
                'status' => 'approved',
                'path' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=1200&q=80',
                'review_note' => 'Approved in demo seed',
            ],
            [
                'name' => 'Transcript - Bachelor',
                'document_type_id' => $transcriptTypeId,
                'status' => 'pending',
                'path' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=1200&q=80',
                'review_note' => null,
            ],
            [
                'name' => 'IELTS Result - pending',
                'document_type_id' => $ieltsTypeId,
                'status' => 'rejected',
                'path' => 'https://images.unsplash.com/photo-1456513080080-7e9b1b0c5f2f?auto=format&fit=crop&w=1200&q=80',
                'review_note' => 'Please upload a clearer scan',
            ],
        ] as $doc) {
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

        foreach ([
            [
                'title' => 'Consultation with Education Interntions Advisor',
                'type' => 'consultation',
                'starts_at' => now()->addDays(5)->setTime(14, 0),
                'ends_at' => now()->addDays(5)->setTime(15, 0),
                'notes' => 'Discuss documents and intake timeline',
            ],
            [
                'title' => 'Document review call',
                'type' => 'document_review',
                'starts_at' => now()->addDays(8)->setTime(10, 30),
                'ends_at' => now()->addDays(8)->setTime(11, 0),
                'notes' => 'Review passport + transcript before submission',
            ],
        ] as $appt) {
            Appointment::query()->firstOrCreate(
                [
                    'student_id' => $student->id,
                    'title' => $appt['title'],
                ],
                [
                    'lead_id' => $docLead?->id,
                    'consultant_id' => $consultantUser->id,
                    'type' => $appt['type'],
                    'starts_at' => $appt['starts_at'],
                    'ends_at' => $appt['ends_at'],
                    'status' => 'scheduled',
                    'notes' => $appt['notes'],
                ]
            );
        }

        foreach ([
            [
                'title' => 'Welcome to Education Interntions Student Portal',
                'type' => 'info',
                'body' => 'Upload your documents and track your application progress here.',
            ],
            [
                'title' => 'Document reminder',
                'type' => 'warning',
                'body' => 'Please upload your IELTS result for application WIN-DEMO-0001.',
            ],
        ] as $notif) {
            StudentNotification::query()->firstOrCreate(
                [
                    'user_id' => $studentUser->id,
                    'title' => $notif['title'],
                ],
                [
                    'type' => $notif['type'],
                    'body' => $notif['body'],
                    'link' => '/student/documents',
                    'read_at' => null,
                ]
            );
        }

        $this->command?->info(sprintf(
            'Done. leads=%d docs=%d appts=%d notifs=%d',
            Lead::query()->whereIn('email', [
                'lead.new@example.com',
                'lead.contacted@example.com',
                'lead.doc@example.com',
            ])->count(),
            Document::query()->where('student_id', $student->id)->count(),
            Appointment::query()->where('student_id', $student->id)->count(),
            StudentNotification::query()->where('user_id', $studentUser->id)->count(),
        ));
    }
}
