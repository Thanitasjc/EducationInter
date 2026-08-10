<?php

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Appointment;
use App\Models\Consultant;
use App\Models\Country;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\DocumentType;
use App\Models\Event;
use App\Models\HomeSection;
use App\Models\PageContent;
use App\Models\Partner;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Program;
use App\Models\Review;
use App\Models\Scholarship;
use App\Models\Service;
use App\Models\Student;
use App\Models\StudentNotification;
use App\Models\University;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@wineducation.local'],
            [
                'name' => 'Education Interntions Admin',
                'password' => Hash::make('password'),
                'is_active' => true,
                'locale' => 'th',
            ]
        );
        $admin->assignRole('super_admin');

        $consultantUser = User::query()->updateOrCreate(
            ['email' => 'consultant@wineducation.local'],
            [
                'name' => 'Education Interntions Consultant',
                'password' => Hash::make('password'),
                'is_active' => true,
                'locale' => 'th',
            ]
        );
        $consultantUser->assignRole('consultant');
        Consultant::query()->updateOrCreate(
            ['user_id' => $consultantUser->id],
            ['employee_code' => 'WIN-C01', 'is_available' => true, 'max_leads' => 40]
        );

        PageContent::query()->updateOrCreate(
            ['key' => 'hero'],
            [
                'value' => [
                    'headline_th' => 'เรียนต่อต่างประเทศกับ Education Interntions',
                    'headline_en' => 'Study Abroad with Education Interntions',
                    'subheadline_th' => 'ค้นหามหาวิทยาลัย หลักสูตร และทุนการศึกษา พร้อมที่ปรึกษาครบวงจร',
                    'subheadline_en' => 'Find universities, courses, and scholarships with end-to-end guidance',
                    'cta_primary_th' => 'ปรึกษาฟรี',
                    'cta_primary_en' => 'Free Consultation',
                    'cta_secondary_th' => 'ค้นหามหาวิทยาลัย',
                    'cta_secondary_en' => 'Search Universities',
                    'cta_primary_url' => '/contact',
                    'cta_secondary_url' => '/universities',
                    'slide_interval_ms' => 5500,
                    'slides' => [
                        [
                            'image' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1920&q=80',
                            'headline_th' => 'เรียนต่อต่างประเทศกับ Education Interntions',
                            'headline_en' => 'Study Abroad with Education Interntions',
                            'subheadline_th' => 'ค้นหามหาวิทยาลัย หลักสูตร และทุนการศึกษา พร้อมที่ปรึกษาครบวงจร',
                            'subheadline_en' => 'Find universities, courses, and scholarships with end-to-end guidance',
                            'link' => '/universities',
                        ],
                        [
                            'image' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1920&q=80',
                            'headline_th' => 'ทุนการศึกษาและเส้นทางสู่มหาวิทยาลัยชั้นนำ',
                            'headline_en' => 'Scholarships and pathways to leading universities',
                            'subheadline_th' => 'วางแผนสมัครเรียนและขอทุนกับทีมที่ปรึกษาผู้เชี่ยวชาญ',
                            'subheadline_en' => 'Plan applications and funding with expert advisors',
                            'link' => '/scholarships',
                        ],
                        [
                            'image' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1920&q=80',
                            'headline_th' => 'เรียนภาษาระยะยาวในต่างประเทศ',
                            'headline_en' => 'Long-term language study abroad',
                            'subheadline_th' => 'เลือกโปรแกรม Academic Year และปลายทางที่เหมาะกับเป้าหมายของคุณ',
                            'subheadline_en' => 'Choose an Academic Year program and destination that fits your goals',
                            'link' => '/learn-language/academic-year',
                        ],
                    ],
                ],
            ]
        );

        PageContent::query()->updateOrCreate(
            ['key' => 'about'],
            [
                'value' => [
                    'title_th' => 'เกี่ยวกับ Education Interntions',
                    'title_en' => 'About Education Interntions',
                    'body_th' => "Education Interntions เป็นที่ปรึกษาเรียนต่อต่างประเทศที่ช่วยนักเรียนไทยวางแผนเลือกประเทศ มหาวิทยาลัย ทุนการศึกษา และวีซ่าแบบครบวงจร\n\nทีมที่ปรึกษาของเราทำงานร่วมกับมหาวิทยาลัยชั้นนำ โดยเฉพาะในสหราชอาณาจักร ออสเตรเลีย แคนาดา และประเทศปลายทางยอดนิยมอื่นๆ",
                    'body_en' => "Education Interntions is a study-abroad consultancy helping Thai students choose destinations, universities, scholarships, and visas end to end.\n\nOur advisors work with leading universities, especially in the UK, Australia, Canada, and other popular destinations.",
                ],
            ]
        );

        $countries = [
            [
                'slug' => 'uk',
                'name_th' => 'สหราชอาณาจักร',
                'name_en' => 'United Kingdom',
                'code' => 'UK',
                'cover_path' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'australia',
                'name_th' => 'ออสเตรเลีย',
                'name_en' => 'Australia',
                'code' => 'AU',
                'cover_path' => 'https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'usa',
                'name_th' => 'สหรัฐอเมริกา',
                'name_en' => 'United States',
                'code' => 'US',
                'cover_path' => 'https://images.unsplash.com/photo-1485738422979-f5c462d49f74?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'canada',
                'name_th' => 'แคนาดา',
                'name_en' => 'Canada',
                'code' => 'CA',
                'cover_path' => 'https://images.unsplash.com/photo-1517935706615-2717063c0395?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'new-zealand',
                'name_th' => 'นิวซีแลนด์',
                'name_en' => 'New Zealand',
                'code' => 'NZ',
                'cover_path' => 'https://images.unsplash.com/photo-1469521669194-babb45599dbd?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'ireland',
                'name_th' => 'ไอร์แลนด์',
                'name_en' => 'Ireland',
                'code' => 'IE',
                'cover_path' => 'https://images.unsplash.com/photo-1590080875515-8a3a8dc5735e?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'singapore',
                'name_th' => 'สิงคโปร์',
                'name_en' => 'Singapore',
                'code' => 'SG',
                'cover_path' => 'https://images.unsplash.com/photo-1525625293386-3f8f99389edd?auto=format&fit=crop&w=1200&q=80',
            ],
        ];

        foreach ($countries as $index => $country) {
            Country::query()->updateOrCreate(
                ['slug' => $country['slug']],
                [
                    ...$country,
                    'summary_th' => "เรียนต่อ{$country['name_th']}กับ Education Interntions",
                    'summary_en' => "Study in {$country['name_en']} with Education Interntions",
                    'content_th' => "{$country['name_th']} เป็นปลายทางยอดนิยมด้านคุณภาพการศึกษา โอกาสทำงานหลังเรียนจบ และเครือข่ายมหาวิทยาลัยระดับโลก",
                    'content_en' => "{$country['name_en']} is a popular destination for academic quality, post-study work options, and world-class universities.",
                    'tuition_info' => ['currency' => 'local', 'note' => 'Varies by university'],
                    'living_cost_info' => ['monthly_min' => 800, 'monthly_max' => 1600],
                    'visa_info' => ['type' => 'student'],
                    'intakes' => ['January', 'September'],
                    'sort_order' => $index + 1,
                    'is_featured' => true,
                    'is_active' => true,
                ]
            );
        }

        $uk = Country::query()->where('slug', 'uk')->firstOrFail();
        $au = Country::query()->where('slug', 'australia')->firstOrFail();
        $ca = Country::query()->where('slug', 'canada')->firstOrFail();

        $universities = [
            [
                'slug' => 'university-of-manchester',
                'country_id' => $uk->id,
                'name_th' => 'มหาวิทยาลัยแมนเชสเตอร์',
                'name_en' => 'The University of Manchester',
                'type' => 'public',
                'ranking_qs' => 34,
                'tuition_min' => 22000,
                'tuition_max' => 32000,
                'currency' => 'GBP',
            ],
            [
                'slug' => 'university-college-london',
                'country_id' => $uk->id,
                'name_th' => 'ยูนิเวอร์ซิตี้คอลเลจลอนดอน',
                'name_en' => 'University College London',
                'type' => 'public',
                'ranking_qs' => 9,
                'tuition_min' => 26000,
                'tuition_max' => 38000,
                'currency' => 'GBP',
            ],
            [
                'slug' => 'university-of-melbourne',
                'country_id' => $au->id,
                'name_th' => 'มหาวิทยาลัยเมลเบิร์น',
                'name_en' => 'The University of Melbourne',
                'type' => 'public',
                'ranking_qs' => 13,
                'tuition_min' => 30000,
                'tuition_max' => 48000,
                'currency' => 'AUD',
            ],
            [
                'slug' => 'university-of-toronto',
                'country_id' => $ca->id,
                'name_th' => 'มหาวิทยาลัยโตรอนโต',
                'name_en' => 'University of Toronto',
                'type' => 'public',
                'ranking_qs' => 25,
                'tuition_min' => 28000,
                'tuition_max' => 62000,
                'currency' => 'CAD',
            ],
        ];

        $universityMeta = [
            'university-of-manchester' => [
                'about_th' => 'มหาวิทยาลัยในเครือ Russell Group',
                'about_en' => 'Russell Group research university',
                'cover_path' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1200&q=80',
            ],
            'university-college-london' => [
                'about_th' => 'มหาวิทยาลัยชั้นนำใจกลางลอนดอน',
                'about_en' => 'World-leading university in central London',
                'cover_path' => 'https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1200&q=80',
            ],
            'university-of-melbourne' => [
                'about_th' => 'มหาวิทยาลัยอันดับต้นของออสเตรเลีย',
                'about_en' => 'One of Australia’s top-ranked universities',
                'cover_path' => 'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1200&q=80',
            ],
            'university-of-toronto' => [
                'about_th' => 'มหาวิทยาลัยชั้นนำของแคนาดา',
                'about_en' => 'Leading university in Canada',
                'cover_path' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1200&q=80',
            ],
        ];

        foreach ($universities as $uni) {
            $meta = $universityMeta[$uni['slug']] ?? [];
            University::query()->updateOrCreate(
                ['slug' => $uni['slug']],
                [
                    ...$uni,
                    'about_th' => $meta['about_th'] ?? "{$uni['name_th']} เป็นมหาวิทยาลัยชั้นนำที่นักเรียนไทยสนใจ",
                    'about_en' => $meta['about_en'] ?? "{$uni['name_en']} is a leading university popular among Thai students",
                    'cover_path' => $meta['cover_path'] ?? null,
                    'entry_requirements' => ['IELTS' => '6.5', 'GPA' => '3.0'],
                    'is_featured' => true,
                    'is_active' => true,
                ]
            );
        }

        $categories = [
            ['slug' => 'business', 'name_th' => 'ธุรกิจ', 'name_en' => 'Business'],
            ['slug' => 'engineering', 'name_th' => 'วิศวกรรม', 'name_en' => 'Engineering'],
            ['slug' => 'computer-science', 'name_th' => 'วิทยาการคอมพิวเตอร์', 'name_en' => 'Computer Science'],
        ];

        foreach ($categories as $index => $category) {
            CourseCategory::query()->updateOrCreate(
                ['slug' => $category['slug']],
                [...$category, 'sort_order' => $index + 1, 'is_active' => true]
            );
        }

        $business = CourseCategory::query()->where('slug', 'business')->firstOrFail();
        $engineering = CourseCategory::query()->where('slug', 'engineering')->firstOrFail();
        $cs = CourseCategory::query()->where('slug', 'computer-science')->firstOrFail();

        $manchester = University::query()->where('slug', 'university-of-manchester')->firstOrFail();
        $ucl = University::query()->where('slug', 'university-college-london')->firstOrFail();
        $melbourne = University::query()->where('slug', 'university-of-melbourne')->firstOrFail();
        $toronto = University::query()->where('slug', 'university-of-toronto')->firstOrFail();

        $courses = [
            [
                'university_id' => $manchester->id,
                'course_category_id' => $business->id,
                'slug' => 'msc-management',
                'name_th' => 'MSc Management',
                'name_en' => 'MSc Management',
                'degree_level' => 'master',
                'duration_months' => 12,
                'tuition' => 28000,
                'currency' => 'GBP',
                'cover_path' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'university_id' => $ucl->id,
                'course_category_id' => $cs->id,
                'slug' => 'msc-computer-science',
                'name_th' => 'MSc Computer Science',
                'name_en' => 'MSc Computer Science',
                'degree_level' => 'master',
                'duration_months' => 12,
                'tuition' => 35000,
                'currency' => 'GBP',
                'cover_path' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'university_id' => $melbourne->id,
                'course_category_id' => $engineering->id,
                'slug' => 'master-of-engineering',
                'name_th' => 'Master of Engineering',
                'name_en' => 'Master of Engineering',
                'degree_level' => 'master',
                'duration_months' => 24,
                'tuition' => 48000,
                'currency' => 'AUD',
                'cover_path' => 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'university_id' => $toronto->id,
                'course_category_id' => $business->id,
                'slug' => 'bachelor-of-commerce',
                'name_th' => 'Bachelor of Commerce',
                'name_en' => 'Bachelor of Commerce',
                'degree_level' => 'bachelor',
                'duration_months' => 48,
                'tuition' => 58000,
                'currency' => 'CAD',
                'cover_path' => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=1200&q=80',
            ],
        ];

        foreach ($courses as $course) {
            Course::query()->updateOrCreate(
                ['university_id' => $course['university_id'], 'slug' => $course['slug']],
                [
                    ...$course,
                    'intakes' => ['September'],
                    'entry_requirements' => ['GPA' => '3.0+'],
                    'english_requirements' => ['IELTS' => '6.5'],
                    'summary_th' => "หลักสูตร{$course['name_th']} ที่ได้รับความนิยม",
                    'summary_en' => "Popular {$course['name_en']} program",
                    'is_popular' => true,
                    'is_active' => true,
                ]
            );
        }

        $scholarships = [
            [
                'slug' => 'manchester-undergraduate-scholarship',
                'country_id' => $uk->id,
                'university_id' => $manchester->id,
                'title_th' => 'ทุนเรียนปริญญาตรี',
                'title_en' => 'Undergraduate Scholarship',
                'amount_label_th' => 'สูงสุด £5,000',
                'amount_label_en' => 'Up to £5,000',
            ],
            [
                'slug' => 'manchester-masters-scholarship',
                'country_id' => $uk->id,
                'university_id' => $manchester->id,
                'title_th' => 'ทุนเรียนปริญญาโท',
                'title_en' => 'Master’s Scholarship',
                'amount_label_th' => 'สูงสุด £5,000',
                'amount_label_en' => 'Up to £5,000',
            ],
            [
                'slug' => 'ucl-global-undergraduate',
                'country_id' => $uk->id,
                'university_id' => $ucl->id,
                'title_th' => 'ทุนเรียนปริญญาตรี',
                'title_en' => 'Undergraduate Scholarship',
                'amount_label_th' => 'เต็มจำนวนค่าเรียน',
                'amount_label_en' => 'Full tuition',
            ],
            [
                'slug' => 'ucl-global-masters',
                'country_id' => $uk->id,
                'university_id' => $ucl->id,
                'title_th' => 'ทุนเรียนปริญญาโท',
                'title_en' => 'Master’s Scholarship',
                'amount_label_th' => 'สูงสุด £10,000',
                'amount_label_en' => 'Up to £10,000',
            ],
            [
                'slug' => 'melbourne-international-undergraduate',
                'country_id' => $au->id,
                'university_id' => $melbourne->id,
                'title_th' => 'ทุนเรียนปริญญาตรี',
                'title_en' => 'Undergraduate Scholarship',
                'amount_label_th' => 'สูงสุด 50% ของค่าเรียน',
                'amount_label_en' => 'Up to 50% of tuition',
            ],
            [
                'slug' => 'melbourne-international-masters',
                'country_id' => $au->id,
                'university_id' => $melbourne->id,
                'title_th' => 'ทุนเรียนปริญญาโท',
                'title_en' => 'Master’s Scholarship',
                'amount_label_th' => 'สูงสุด 50% ของค่าเรียน',
                'amount_label_en' => 'Up to 50% of tuition',
            ],
            [
                'slug' => 'toronto-undergraduate-scholarship',
                'country_id' => $ca->id,
                'university_id' => $toronto->id,
                'title_th' => 'ทุนเรียนปริญญาตรี',
                'title_en' => 'Undergraduate Scholarship',
                'amount_label_th' => 'สูงสุด CAD $10,000',
                'amount_label_en' => 'Up to CAD $10,000',
            ],
            [
                'slug' => 'toronto-masters-scholarship',
                'country_id' => $ca->id,
                'university_id' => $toronto->id,
                'title_th' => 'ทุนเรียนปริญญาโท',
                'title_en' => 'Master’s Scholarship',
                'amount_label_th' => 'สูงสุด CAD $10,000',
                'amount_label_en' => 'Up to CAD $10,000',
            ],
        ];

        Scholarship::query()
            ->where('slug', 'manchester-global-scholarship')
            ->update(['is_active' => false, 'is_featured' => false]);

        foreach ($scholarships as $item) {
            Scholarship::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    ...$item,
                    'deadline' => now()->addMonths(4)->toDateString(),
                    'eligibility' => ['International students', 'Offer holder'],
                    'requirements' => ['Academic transcript', 'Personal statement'],
                    'how_to_apply_th' => 'ปรึกษาที่ปรึกษา Education Interntions เพื่อเตรียมเอกสารและยื่นทุน',
                    'how_to_apply_en' => 'Talk to a Education Interntions advisor to prepare documents and submit your scholarship application.',
                    'is_featured' => true,
                    'is_active' => true,
                ]
            );
        }

        $services = [
            [
                'slug' => 'university-application',
                'title_th' => 'สมัครเรียนมหาวิทยาลัย',
                'title_en' => 'University Application',
                'image_path' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'visa',
                'title_th' => 'วีซ่านักเรียน',
                'title_en' => 'Student Visa',
                'image_path' => 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'accommodation',
                'title_th' => 'ที่พัก',
                'title_en' => 'Accommodation',
                'image_path' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'ielts',
                'title_th' => 'เตรียมสอบ IELTS',
                'title_en' => 'IELTS Preparation',
                'image_path' => 'https://images.unsplash.com/photo-1456513080080-7e9b1b0c5f2f?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'pre-departure',
                'title_th' => 'เตรียมตัวก่อนเดินทาง',
                'title_en' => 'Pre-departure',
                'image_path' => 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'consultation',
                'title_th' => 'ปรึกษาเรียนต่อ',
                'title_en' => 'Consultation',
                'image_path' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=1200&q=80',
            ],
        ];

        foreach ($services as $index => $service) {
            Service::query()->updateOrCreate(
                ['slug' => $service['slug']],
                [
                    ...$service,
                    'summary_th' => "บริการ{$service['title_th']}โดยทีม Education Interntions",
                    'summary_en' => "{$service['title_en']} support by Education Interntions advisors",
                    'cta_label_th' => 'ปรึกษาฟรี',
                    'cta_label_en' => 'Free Consultation',
                    'cta_url' => '/contact',
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }

        $reviews = [
            [
                'student_name' => 'สมชาย ใจดี',
                'university_label' => 'University of Manchester',
                'country_label' => 'UK',
                'quote_th' => 'ที่ปรึกษา Education Interntions ช่วยตั้งแต่เลือกคอร์สจนได้ Offer',
                'quote_en' => 'Education Interntions advisors supported me from course selection to offer.',
                'image_path' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'student_name' => 'พิมพ์ใจ รักเรียน',
                'university_label' => 'University of Melbourne',
                'country_label' => 'Australia',
                'quote_th' => 'ช่วยเรื่องทุนและวีซ่าได้ครบ จนเดินทางไปเรียนได้จริง',
                'quote_en' => 'They helped with scholarships and visa until I could depart.',
                'image_path' => 'https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'student_name' => 'นภัสสร มีสุข',
                'university_label' => 'University of Toronto',
                'country_label' => 'Canada',
                'quote_th' => 'ทีมงานตอบไวและวางแผนชัดเจน',
                'quote_en' => 'Fast responses and a clear study plan.',
                'image_path' => 'https://images.unsplash.com/photo-1517935706615-2717063c0395?auto=format&fit=crop&w=1200&q=80',
            ],
        ];

        foreach ($reviews as $index => $review) {
            Review::query()->updateOrCreate(
                ['student_name' => $review['student_name']],
                [
                    ...$review,
                    'year' => '2025',
                    'sort_order' => $index + 1,
                    'is_featured' => true,
                    'is_active' => true,
                ]
            );
        }

        Partner::query()->where(function ($query) {
            $query->whereNull('logo_path')->orWhere('logo_path', '');
        })->delete();

        $partnerCards = [
            ['name' => 'Partner University 01', 'logo_path' => 'https://win-ed.com/wp-content/uploads/2026/07/Button_77_11zon-1024x522.webp'],
            ['name' => 'Partner University 02', 'logo_path' => 'https://win-ed.com/wp-content/uploads/2026/07/Button-1_76_11zon-1024x522.webp'],
            ['name' => 'Partner University 03', 'logo_path' => 'https://win-ed.com/wp-content/uploads/2026/07/Button-2_75_11zon-1024x522.webp'],
            ['name' => 'Partner University 04', 'logo_path' => 'https://win-ed.com/wp-content/uploads/2026/07/Button-3_74_11zon-1024x522.webp'],
            ['name' => 'Partner University 05', 'logo_path' => 'https://win-ed.com/wp-content/uploads/2026/07/Button-4_73_11zon-1024x522.webp'],
            ['name' => 'Partner University 06', 'logo_path' => 'https://win-ed.com/wp-content/uploads/2026/07/Button-5_72_11zon-1024x522.webp'],
            ['name' => 'Partner University 07', 'logo_path' => 'https://win-ed.com/wp-content/uploads/2026/07/Button-6_71_11zon-1024x522.webp'],
            ['name' => 'Partner University 08', 'logo_path' => 'https://win-ed.com/wp-content/uploads/2026/07/Button-7_70_11zon-1024x522.webp'],
            ['name' => 'Partner University 09', 'logo_path' => 'https://win-ed.com/wp-content/uploads/2026/07/Button-8_69_11zon-1024x522.webp'],
            ['name' => 'Partner University 10', 'logo_path' => 'https://win-ed.com/wp-content/uploads/2026/07/Button-9_68_11zon-1024x522.webp'],
            ['name' => 'Partner University 11', 'logo_path' => 'https://win-ed.com/wp-content/uploads/2026/07/Button-10_67_11zon-1024x522.webp'],
            ['name' => 'Partner University 12', 'logo_path' => 'https://win-ed.com/wp-content/uploads/2026/07/Button-11_66_11zon-1024x522.webp'],
            ['name' => 'Partner University 13', 'logo_path' => 'https://win-ed.com/wp-content/uploads/2026/07/Button-12_65_11zon-1024x522.webp'],
            ['name' => 'Partner University 14', 'logo_path' => 'https://win-ed.com/wp-content/uploads/2026/07/Button-13_64_11zon-1024x522.webp'],
            ['name' => 'Partner University 15', 'logo_path' => 'https://win-ed.com/wp-content/uploads/2026/07/Button-14_63_11zon-1024x522.webp'],
            ['name' => 'Partner University 16', 'logo_path' => 'https://win-ed.com/wp-content/uploads/2026/07/Button-15_62_11zon-1024x522.webp'],
            ['name' => 'Partner University 17', 'logo_path' => 'https://win-ed.com/wp-content/uploads/2026/07/Button-16_61_11zon-1024x522.webp'],
            ['name' => 'Partner University 18', 'logo_path' => 'https://win-ed.com/wp-content/uploads/2026/07/Button-17_60_11zon-1024x522.webp'],
            ['name' => 'Partner University 19', 'logo_path' => 'https://win-ed.com/wp-content/uploads/2026/07/Button-18_59_11zon-1024x522.webp'],
            ['name' => 'Partner University 20', 'logo_path' => 'https://win-ed.com/wp-content/uploads/2026/07/Button-19_58_11zon-1024x522.webp'],
            ['name' => 'Partner University 21', 'logo_path' => 'https://win-ed.com/wp-content/uploads/2026/07/Button-20_57_11zon-1024x522.webp'],
        ];

        foreach ($partnerCards as $index => $partner) {
            Partner::query()->updateOrCreate(
                ['name' => $partner['name']],
                [
                    'logo_path' => $partner['logo_path'],
                    'url' => '/universities',
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }

        $docTypes = [
            ['slug' => 'passport', 'name_th' => 'พาสปอร์ต', 'name_en' => 'Passport', 'is_required' => true],
            ['slug' => 'transcript', 'name_th' => 'ทรานสคริปต์', 'name_en' => 'Transcript', 'is_required' => true],
            ['slug' => 'ielts', 'name_th' => 'ผล IELTS', 'name_en' => 'IELTS', 'is_required' => true],
            ['slug' => 'certificate', 'name_th' => 'วุฒิการศึกษา', 'name_en' => 'Certificate', 'is_required' => false],
        ];

        foreach ($docTypes as $type) {
            DocumentType::query()->updateOrCreate(['slug' => $type['slug']], $type);
        }

        $studentUser = User::query()->updateOrCreate(
            ['email' => 'student@wineducation.local'],
            [
                'name' => 'สมชาย ใจดี',
                'phone' => '0812345678',
                'password' => Hash::make('password'),
                'is_active' => true,
                'locale' => 'th',
            ]
        );
        $studentUser->assignRole('student');

        $student = Student::query()->updateOrCreate(
            ['user_id' => $studentUser->id],
            [
                'nationality' => 'Thai',
                'education_level' => 'Bachelor',
                'preferred_locale' => 'th',
            ]
        );

        $application = Application::query()->updateOrCreate(
            ['application_no' => 'WIN-DEMO-0001'],
            [
                'student_id' => $student->id,
                'country_id' => $uk->id,
                'university_id' => $manchester->id,
                'course_id' => Course::query()->where('slug', 'msc-management')->value('id'),
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

        Appointment::query()->updateOrCreate(
            [
                'student_id' => $student->id,
                'title' => 'Consultation with Education Interntions Advisor',
            ],
            [
                'type' => 'consultation',
                'starts_at' => now()->addDays(5)->setTime(14, 0),
                'ends_at' => now()->addDays(5)->setTime(15, 0),
                'status' => 'scheduled',
                'notes' => 'Discuss documents and intake timeline',
            ]
        );

        $blogCategories = [
            ['slug' => 'study-abroad', 'name_th' => 'เรียนต่อต่างประเทศ', 'name_en' => 'Study Abroad'],
            ['slug' => 'uk', 'name_th' => 'อังกฤษ', 'name_en' => 'UK'],
            ['slug' => 'scholarship', 'name_th' => 'ทุนการศึกษา', 'name_en' => 'Scholarship'],
            ['slug' => 'visa', 'name_th' => 'วีซ่า', 'name_en' => 'Visa'],
        ];

        foreach ($blogCategories as $category) {
            PostCategory::query()->updateOrCreate(['slug' => $category['slug']], $category);
        }

        $posts = [
            [
                'slug' => 'why-study-uk-2026',
                'category' => 'uk',
                'title_th' => 'ทำไมต้องเรียนต่ออังกฤษปี 2026',
                'title_en' => 'Why Study in the UK in 2026',
                'excerpt_th' => 'สรุปจุดเด่น คุณภาพการศึกษา และโอกาสหลังเรียนจบ',
                'excerpt_en' => 'Key reasons to choose the UK for your next degree',
                'cover_path' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'how-to-prepare-scholarship',
                'category' => 'scholarship',
                'title_th' => 'เตรียมเอกสารสมัครทุนอย่างไรให้ผ่าน',
                'title_en' => 'How to Prepare a Strong Scholarship Application',
                'excerpt_th' => 'เช็กลิสต์เอกสารและวิธีเขียน Personal Statement',
                'excerpt_en' => 'Document checklist and personal statement tips',
                'cover_path' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'student-visa-checklist',
                'category' => 'visa',
                'title_th' => 'เช็กลิสต์วีซ่านักเรียนฉบับเข้าใจง่าย',
                'title_en' => 'Simple Student Visa Checklist',
                'excerpt_th' => 'เอกสารสำคัญที่ควรเตรียมก่อนยื่นวีซ่า',
                'excerpt_en' => 'Essential documents before you apply for a visa',
                'cover_path' => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&w=1200&q=80',
            ],
        ];

        foreach ($posts as $post) {
            $categoryId = PostCategory::query()->where('slug', $post['category'])->value('id');
            $postModel = Post::query()->updateOrCreate(
                ['slug' => $post['slug']],
                [
                    'post_category_id' => $categoryId,
                    'author_id' => $admin->id,
                    'title_th' => $post['title_th'],
                    'title_en' => $post['title_en'],
                    'excerpt_th' => $post['excerpt_th'],
                    'excerpt_en' => $post['excerpt_en'],
                    'cover_path' => $post['cover_path'],
                    'content_th' => $post['excerpt_th']."\n\nติดต่อที่ปรึกษา Education Interntions เพื่อวางแผนเรียนต่อแบบรายบุคคล",
                    'content_en' => $post['excerpt_en']."\n\nTalk to a Education Interntions advisor for a personalized study plan.",
                    'published_at' => now()->subDays(rand(1, 20)),
                    'is_active' => true,
                ]
            );

            $postModel->seo()->updateOrCreate(
                ['locale' => 'th'],
                [
                    'meta_title' => $post['title_th'].' | Education Interntions',
                    'meta_description' => $post['excerpt_th'],
                    'robots' => 'index,follow',
                    'schema_json' => [
                        '@context' => 'https://schema.org',
                        '@type' => 'Article',
                        'headline' => $post['title_th'],
                    ],
                ]
            );
        }

        $events = [
            [
                'slug' => 'uk-education-fair-bkk',
                'title_th' => 'UK Education Fair Bangkok',
                'title_en' => 'UK Education Fair Bangkok',
                'summary_th' => 'พบมหาวิทยาลัยอังกฤษและที่ปรึกษา Education Interntions ในงานเดียว',
                'summary_en' => 'Meet UK universities and Education Interntions advisors in one event',
                'location' => 'Bangkok',
                'starts_at' => now()->addDays(14)->setTime(10, 0),
                'cover_path' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'scholarship-workshop',
                'title_th' => 'เวิร์กช็อปสมัครทุนการศึกษา',
                'title_en' => 'Scholarship Application Workshop',
                'summary_th' => 'เรียนรู้วิธีเตรียมเอกสารและเขียน Personal Statement',
                'summary_en' => 'Learn how to prepare documents and write a personal statement',
                'location' => 'Online / Zoom',
                'starts_at' => now()->addDays(21)->setTime(19, 0),
                'cover_path' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'slug' => 'ielts-strategy-session',
                'title_th' => 'วางแผนคะแนน IELTS ก่อนสมัครเรียน',
                'title_en' => 'IELTS Strategy Session',
                'summary_th' => 'วิเคราะห์คะแนนเป้าหมายตามมหาวิทยาลัยที่สนใจ',
                'summary_en' => 'Map target IELTS scores to your university shortlist',
                'location' => 'Education Interntions Office',
                'starts_at' => now()->addDays(28)->setTime(13, 0),
                'cover_path' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80',
            ],
        ];

        foreach ($events as $event) {
            Event::query()->updateOrCreate(
                ['slug' => $event['slug']],
                [
                    ...$event,
                    'content_th' => $event['summary_th']."\n\nลงทะเบียนล่วงหน้ากับทีม Education Interntions เพื่อรับที่นั่งและเอกสารเตรียมตัว",
                    'content_en' => $event['summary_en']."\n\nRegister with Education Interntions in advance for a seat and prep materials.",
                    'ends_at' => $event['starts_at']->copy()->addHours(3),
                    'is_featured' => true,
                    'is_active' => true,
                ]
            );
        }

        $application->update(['consultant_id' => $consultantUser->id]);

        StudentNotification::query()->updateOrCreate(
            [
                'user_id' => $studentUser->id,
                'title' => 'Welcome to Education Interntions Student Portal',
            ],
            [
                'type' => 'info',
                'body' => 'Upload your documents and track your application progress here.',
                'link' => '/student/documents',
                'read_at' => null,
            ]
        );

        StudentNotification::query()->updateOrCreate(
            [
                'user_id' => $studentUser->id,
                'title' => 'Document reminder',
            ],
            [
                'type' => 'warning',
                'body' => 'Please upload your IELTS result for application WIN-DEMO-0001.',
                'link' => '/student/documents',
                'read_at' => null,
            ]
        );

        $homeSections = [
            [
                'key' => 'countries',
                'layout' => 'cards',
                'title_th' => 'ประเทศยอดนิยม',
                'title_en' => 'Popular Countries',
                'sort_order' => 10,
            ],
            [
                'key' => 'universities',
                'layout' => 'cards',
                'title_th' => 'มหาวิทยาลัยแนะนำ',
                'title_en' => 'Featured Universities',
                'sort_order' => 20,
            ],
            [
                'key' => 'courses',
                'layout' => 'cards',
                'title_th' => 'หลักสูตรยอดนิยม',
                'title_en' => 'Popular Courses',
                'sort_order' => 30,
            ],
            [
                'key' => 'learn-language',
                'layout' => 'program_categories',
                'title_th' => 'สำรวจประเภทโปรแกรม',
                'title_en' => 'Explore program types',
                'subtitle_th' => null,
                'subtitle_en' => null,
                'sort_order' => 32,
                'items' => [
                    [
                        'title_th' => 'คอร์สเรียนภาษาที่ต่างประเทศ',
                        'title_en' => 'Language courses abroad',
                        'summary_th' => 'เลือกเรียนภาษาที่หลากหลาย จากจุดหมายปลายทางทั่วโลกกับ Education Interntions',
                        'summary_en' => 'Choose from language courses across global destinations with Education Interntions',
                        'href' => '/learn-language',
                        'external' => false,
                        'cover_path' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80',
                    ],
                    [
                        'title_th' => 'เรียนต่างประเทศระยะยาว (Academic Year)',
                        'title_en' => 'Academic Year abroad',
                        'summary_th' => 'รวมการเรียนภาษาและการเรียนเชิงวิชาการในต่างประเทศแบบระยะยาว',
                        'summary_en' => 'Combine language study with academic immersion for a full term or year',
                        'href' => '/learn-language/academic-year',
                        'external' => false,
                        'cover_path' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1200&q=80',
                    ],
                    [
                        'title_th' => 'โรงเรียนมัธยมศึกษาในต่างประเทศ',
                        'title_en' => 'High school abroad',
                        'summary_th' => 'วางแผนเรียนมัธยม / หลักสูตรอินเตอร์ในต่างประเทศ พร้อมที่ปรึกษา Education Interntions',
                        'summary_en' => 'Plan high school or international curricula abroad with Education Interntions advisors',
                        'href' => '/study-abroad',
                        'external' => false,
                        'cover_path' => 'https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1200&q=80',
                    ],
                    [
                        'title_th' => 'เรียนมหาวิทยาลัยต่างประเทศ',
                        'title_en' => 'University abroad',
                        'summary_th' => 'โปรแกรมเตรียมเข้ามหาวิทยาลัยและหลักสูตรปริญญาในต่างประเทศ',
                        'summary_en' => 'University pathway and degree programs overseas',
                        'href' => '/universities',
                        'external' => false,
                        'cover_path' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1200&q=80',
                    ],
                    [
                        'title_th' => 'เรียนภาษาอังกฤษ / IELTS',
                        'title_en' => 'English & IELTS',
                        'summary_th' => 'วางแผนคะแนนเป้าหมายและคอร์สภาษาที่เหมาะกับเส้นทางเรียนต่อ',
                        'summary_en' => 'Map target scores and language courses to your study pathway',
                        'href' => '/ielts',
                        'external' => false,
                        'cover_path' => 'https://images.unsplash.com/photo-1456513080080-7e9b1b0c5f2f?auto=format&fit=crop&w=1200&q=80',
                    ],
                    [
                        'title_th' => 'การฝึกอบรมสำหรับองค์กร',
                        'title_en' => 'Corporate training',
                        'summary_th' => 'หลักสูตรภาษาและการพัฒนาทักษะสำหรับองค์กรและทีมงาน',
                        'summary_en' => 'Language and skills programs for companies and teams',
                        'href' => '/contact',
                        'external' => false,
                        'cover_path' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1200&q=80',
                    ],
                ],
            ],
            [
                'key' => 'bachelor-pathways',
                'layout' => 'pathways_split',
                'title_th' => 'ทางเลือกในการ เรียนต่อ ปริญญาตรี ต่างประเทศ',
                'title_en' => 'Pathways to study a Bachelor’s degree abroad',
                'cover_path' => 'sections/bachelor-pathways.png',
                'sort_order' => 35,
                'items' => [
                    [
                        'number' => 1,
                        'text_th' => 'เรียนหลักสูตรอินเตอร์ ยื่นคะแนนสอบ A-level',
                        'text_en' => 'Study an international curriculum and apply with A-level results',
                    ],
                    [
                        'number' => 2,
                        'text_th' => 'เรียนหลักสูตรอินเตอร์ ยื่นผลการเรียนในระบบ IB (International Baccalaureate)',
                        'text_en' => 'Study an international curriculum and apply with IB results',
                    ],
                    [
                        'number' => 3,
                        'text_th' => 'เรียนหลักสูตรไทย เรียนหลักสูตร Foundation',
                        'text_en' => 'Study a Thai curriculum, then complete a Foundation pathway',
                        'note_th' => 'เพื่อให้มีผลการเรียนเข้ากับระบบของต่างประเทศ',
                        'note_en' => 'to align academic results with overseas entry systems',
                    ],
                    [
                        'number' => 4,
                        'text_th' => 'เรียนหลักสูตรไทย สมัครเรียนต่อและเข้าเรียนหลักสูตร International Year One (IYO)',
                        'text_en' => 'Study a Thai curriculum, then enter International Year One (IYO)',
                        'note_th' => 'ในบางสาขาวิชา บางมหาวิทยาลัย',
                        'note_en' => 'available for selected fields and universities',
                    ],
                ],
                'cta_label_th' => 'ปรึกษาเส้นทางเรียนต่อ',
                'cta_label_en' => 'Discuss your pathway',
                'cta_url' => '/contact',
            ],
            [
                'key' => 'scholarships',
                'layout' => 'cards',
                'title_th' => 'Scholarships',
                'title_en' => 'Scholarships',
                'sort_order' => 40,
            ],
            [
                'key' => 'services',
                'layout' => 'cards',
                'title_th' => 'บริการของ Education Interntions',
                'title_en' => 'Education Interntions Services',
                'sort_order' => 50,
            ],
            [
                'key' => 'reviews',
                'layout' => 'cards',
                'title_th' => 'เรื่องราวความสำเร็จ',
                'title_en' => 'Success Stories',
                'sort_order' => 60,
            ],
            [
                'key' => 'events',
                'layout' => 'cards',
                'title_th' => 'กิจกรรมใกล้ถึง',
                'title_en' => 'Upcoming Events',
                'sort_order' => 70,
            ],
            [
                'key' => 'blog',
                'layout' => 'cards',
                'title_th' => 'บทความและอัปเดต',
                'title_en' => 'Insights & Updates',
                'sort_order' => 80,
            ],
            [
                'key' => 'cta',
                'layout' => 'cta',
                'title_th' => 'พร้อมเริ่มต้นเส้นทางเรียนต่อหรือยัง?',
                'title_en' => 'Ready to start your study abroad journey?',
                'cover_path' => null,
                'sort_order' => 90,
            ],
        ];

        foreach ($homeSections as $section) {
            HomeSection::query()->updateOrCreate(
                ['key' => $section['key']],
                [
                    ...$section,
                    'is_active' => true,
                ]
            );
        }

        PageContent::query()->updateOrCreate(
            ['key' => 'learn-language'],
            [
                'value' => [
                    'title_th' => 'เรียนภาษาต่างประเทศ',
                    'title_en' => 'Learn a language abroad',
                    'subtitle_th' => 'ค้นหาหลักสูตรสำหรับช่วงอายุของคุณ พร้อมปลายทางและระยะเวลาที่เหมาะกับเป้าหมาย',
                    'subtitle_en' => 'Find a language program by age group, destination, and study length',
                    'intro_th_1' => 'หลายคนอยากเรียนภาษา คุณเคยคิดอยากเรียนภาษาที่ต่างประเทศบ้างไหม? การเรียนภาษาในต่างประเทศเปิดโอกาสให้ใช้ภาษาใหม่ตลอด 24 ชั่วโมง และเข้าใจวัฒนธรรมได้อย่างแท้จริง',
                    'intro_th_2' => 'Education Interntions ช่วยวางแผนคอร์สเรียนภาษาในต่างประเทศ เลือกประเทศ ระยะเวลา และระดับที่เหมาะกับคุณ พร้อมที่ปรึกษาดูแลตั้งแต่เริ่มต้นจนถึงวันเดินทาง',
                    'intro_en_1' => 'Thinking about learning a language abroad? Immersion lets you practice around the clock and experience culture firsthand.',
                    'intro_en_2' => 'Education Interntions helps you choose the right country, duration, and level — with advisors supporting you from planning to departure.',
                ],
            ]
        );

        PageContent::query()->updateOrCreate(
            ['key' => 'academic-year'],
            [
                'value' => [
                    'title_th' => 'ใช้ภาษาได้อย่างคล่องแคล่วในเมืองในฝันของคุณ',
                    'title_en' => 'Become fluent in the city of your dreams',
                    'subtitle_th' => 'เรียนรู้ภาษาใหม่ให้คล่องแคล่ว พร้อมซึมซับวัฒนธรรมท้องถิ่น ด้วยประสบการณ์การใช้ชีวิตอย่างคนท้องถิ่น เตรียมพร้อมสำหรับเส้นทางอาชีพและการเรียนต่อทั้งในประเทศและต่างประเทศ',
                    'subtitle_en' => 'Build real fluency while living like a local — with culture immersion and life skills that prepare you for university and global careers.',
                    'meta_title_th' => 'เรียนต่างประเทศระยะยาว (Academic Year)',
                    'meta_title_en' => 'Academic Year abroad',
                    'meta_description_th' => 'โปรแกรมเรียนภาษาระยะยาวในต่างประเทศกับ Education Interntions — 2–4 เทอม ปลายทางทั่วโลก พร้อมที่ปรึกษาดูแลครบวงจร',
                    'meta_description_en' => 'Long-term language immersion abroad with Education Interntions — 2–4 terms, global destinations, end-to-end advisor support',
                    'promo_banner_th' => 'เรียนภาษา + ใช้ชีวิตในต่างประเทศ อายุ 16 ปีขึ้นไป | ระยะเวลาประมาณ 6 เดือน – 1 ปี',
                    'promo_banner_en' => 'Language study + life abroad for ages 16+ | About 6 months – 1 year',
                    'why_title_th' => 'ทำไมคุณถึงชอบเรียนกับ Education Interntions',
                    'why_title_en' => 'Why students love studying with Education Interntions',
                    'hero_image' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1600&q=80',
                    'usps' => [
                        [
                            'title_th' => 'ได้รับความคล่องแคล่ว',
                            'title_en' => 'Real fluency',
                            'body_th' => 'คล่องแคล่วในภาษาและวัฒนธรรมท้องถิ่น สร้างเครือข่ายเพื่อนทั่วโลก',
                            'body_en' => 'Become fluent in language and local culture while building a global network of friends',
                        ],
                        [
                            'title_th' => 'การเดินทางเชิงวิชาการ',
                            'title_en' => 'An academic journey',
                            'body_th' => 'เริ่มต้นเส้นทางสู่งานในฝันหรือมหาวิทยาลัย เรียน 2, 3 หรือ 4 เทอม และรับใบรับรองที่ยืนยันทักษะภาษา',
                            'body_en' => 'Start your path to university or a dream career — study 2, 3, or 4 terms and earn a certificate of language progress',
                        ],
                        [
                            'title_th' => 'ครอบคลุม',
                            'title_en' => 'All-inclusive structure',
                            'body_th' => 'ศึกษาตามตารางเวลาของคุณ เริ่มเรียนได้หลายช่วงของปี รวมค่าเล่าเรียน ที่พัก และกิจกรรมที่ออกแบบมาให้ครบ',
                            'body_en' => 'Study on a timetable that fits you, with tuition, housing, and activities designed as a complete package',
                        ],
                        [
                            'title_th' => 'การเปลี่ยนแปลง',
                            'title_en' => 'Life-changing growth',
                            'body_th' => 'เติบโตผ่านประสบการณ์ต่างประเทศ และเรียนรู้ทักษะชีวิตที่จำเป็นต่ออนาคต',
                            'body_en' => 'Grow through an overseas experience and gain life skills that set you up for the future',
                        ],
                    ],
                    'faq_title_th' => 'คำถามที่พบบ่อย',
                    'faq_title_en' => 'Frequently asked questions',
                    'faq_body_th' => 'ค้นหาคำตอบสำหรับคำถามที่พบบ่อย หรือติดต่อเรา — ทีม Education Interntions พร้อมช่วยเหลือ',
                    'faq_body_en' => 'Find answers to common questions — or contact us. The Education Interntions team is ready to help.',
                    'faqs' => [
                        [
                            'question_th' => 'ใครเหมาะกับโปรแกรม Academic Year?',
                            'question_en' => 'Who is Academic Year best for?',
                            'answer_th' => 'เหมาะกับผู้ที่อยากพัฒนาภาษาอย่างจริงจังในต่างประเทศ และเปิดโอกาสสู่การเรียนต่อหรืออาชีพระดับสากล โดยทั่วไปอายุ 16 ปีขึ้นไป',
                            'answer_en' => 'Anyone who wants serious language progress abroad and a pathway toward university or a global career — typically ages 16+.',
                        ],
                        [
                            'question_th' => 'สัปดาห์ทั่วไปเป็นอย่างไร?',
                            'question_en' => 'What does a typical week look like?',
                            'answer_th' => 'มีชั้นเรียนประมาณ 4–5 ชั่วโมงต่อวัน สลับเช้า/บ่าย นอกห้องเรียนมีกิจกรรมทางวัฒนธรรมและสังคม ช่วงสุดสัปดาห์เหมาะกับการสำรวจเมืองและท่องเที่ยว',
                            'answer_en' => 'Expect about 4–5 hours of class per day, plus cultural and social activities. Weekends are great for exploring the city.',
                        ],
                        [
                            'question_th' => 'ฉันจะคล่องภาษาได้จริงไหม?',
                            'question_en' => 'Will I really become fluent?',
                            'answer_th' => 'โครงสร้างเทอมและการเรียนแบบ immersion ช่วยให้พัฒนาทีละขั้น พร้อมหลักฐานความก้าวหน้าผ่านใบรับรองและผลการทดสอบตามโปรแกรม',
                            'answer_en' => 'The term structure and immersion approach help you progress step by step, with certificates and assessments depending on the program.',
                        ],
                        [
                            'question_th' => 'จบแล้วได้อะไร?',
                            'question_en' => 'What do I receive when I finish?',
                            'answer_th' => 'ใบรับรองความสำเร็จจากโรงเรียน/โปรแกรม และอาจรวมใบรับรองสอบภาษาหรือประสบการณ์เพิ่มเติมตามแพ็กเกจที่เลือก',
                            'answer_en' => 'A certificate of achievement from the school/program, and possibly exam certificates or extras depending on your package.',
                        ],
                        [
                            'question_th' => 'ระยะเวลาหนึ่งเทอมคือเท่าไร?',
                            'question_en' => 'How long is one term?',
                            'answer_th' => 'โดยทั่วไป 2 เทอม ≈ 6 เดือน, 3 เทอม ≈ 9 เดือน, 4 เทอม ≈ 11 เดือน — วันเริ่มเรียนขึ้นอยู่กับปลายทาง',
                            'answer_en' => 'Typically 2 terms ≈ 6 months, 3 terms ≈ 9 months, 4 terms ≈ 11 months — start dates depend on the destination.',
                        ],
                        [
                            'question_th' => 'ถ้าคิดถึงบ้านหรือกังวลเรื่องการปรับตัว?',
                            'question_en' => 'What if I feel homesick or nervous?',
                            'answer_th' => 'ทีม Education Interntions และเจ้าหน้าที่ในพื้นที่พร้อมสนับสนุน คุณจะมีกิจกรรม ระบบเพื่อน และการดูแลตลอดการปรับตัวในเมืองใหม่',
                            'answer_en' => 'Education Interntions and on-site staff support you throughout. You’ll have activities, peer connections, and guidance while you settle in.',
                        ],
                    ],
                ],
            ]
        );

        $programs = [
            [
                'slug' => 'teen-language-camp',
                'title_th' => 'ค่ายเรียนภาษาวัยทีน',
                'title_en' => 'Teen Language Camp',
                'summary_th' => 'คอร์สระยะสั้นพร้อมผู้นำกลุ่ม ดูแลใกล้ชิด สร้างเพื่อนจากทั่วโลก',
                'summary_en' => 'Short supervised language trip with group leaders and friends worldwide',
                'age_min' => 12,
                'age_max' => 16,
                'duration_label_th' => '2–4 สัปดาห์',
                'duration_label_en' => '2–4 weeks',
                'language' => 'english',
                'destinations' => ['uk', 'usa', 'australia'],
                'cover_path' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80',
                'sort_order' => 1,
            ],
            [
                'slug' => 'young-adult-intensive',
                'title_th' => 'คอร์สภาษาเข้มข้นวัยรุ่น-นักศึกษา',
                'title_en' => 'Young Adult Intensive English',
                'summary_th' => 'เรียนที่วิทยาเขตในต่างประเทศ เริ่มได้ทุกสัปดาห์ พัฒนาภาษาอย่างรวดเร็ว',
                'summary_en' => 'Campus-based intensive English starting every week for rapid progress',
                'age_min' => 16,
                'age_max' => 25,
                'duration_label_th' => '2–24 สัปดาห์',
                'duration_label_en' => '2–24 weeks',
                'language' => 'english',
                'destinations' => ['uk', 'usa', 'australia', 'canada', 'singapore'],
                'cover_path' => 'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80',
                'sort_order' => 2,
            ],
            [
                'slug' => 'professional-english',
                'title_th' => 'ภาษาอังกฤษสำหรับวัยทำงาน',
                'title_en' => 'Professional English Abroad',
                'summary_th' => 'คอร์สสำหรับผู้ใหญ่และวัยทำงาน เน้นภาษาเพื่ออาชีพในเมืองชั้นนำ',
                'summary_en' => 'Career-focused English for adults in leading global cities',
                'age_min' => 25,
                'age_max' => 99,
                'duration_label_th' => '2–24 สัปดาห์',
                'duration_label_en' => '2–24 weeks',
                'language' => 'english',
                'destinations' => ['uk', 'usa', 'australia', 'canada', 'singapore'],
                'cover_path' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1200&q=80',
                'sort_order' => 3,
            ],
            [
                'slug' => 'mature-learners',
                'title_th' => 'คอร์สภาษาสำหรับผู้ใหญ่ 50+',
                'title_en' => 'Mature Learners 50+',
                'summary_th' => 'ออกแบบสำหรับผู้เรียน 50+ เน้นบทเรียนเข้าใจง่ายและกิจกรรมที่น่าสนใจ',
                'summary_en' => 'Designed for learners 50+ with clear lessons and engaging activities',
                'age_min' => 50,
                'age_max' => 99,
                'duration_label_th' => '2–52 สัปดาห์',
                'duration_label_en' => '2–52 weeks',
                'language' => 'english',
                'destinations' => ['uk', 'usa', 'canada', 'singapore'],
                'cover_path' => 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?auto=format&fit=crop&w=1200&q=80',
                'sort_order' => 4,
            ],
            [
                'slug' => 'long-term-immersion',
                'title_th' => 'เรียนภาษาระยะยาวแบบ Immersion',
                'title_en' => 'Long-term Language Immersion',
                'summary_th' => 'เทอมหรือปีในเมืองในฝัน พัฒนาจนสื่อสารคล่องและรับประกาศนียบัตร',
                'summary_en' => 'Term or year abroad to become conversationally fluent and earn a certificate',
                'age_min' => 16,
                'age_max' => 99,
                'duration_label_th' => 'เทอมหรือปี',
                'duration_label_en' => 'Term or year',
                'language' => 'english',
                'destinations' => ['uk', 'usa', 'australia', 'canada', 'japan', 'korea'],
                'cover_path' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1200&q=80',
                'sort_order' => 5,
            ],
            [
                'slug' => 'japanese-language-abroad',
                'title_th' => 'เรียนภาษาญี่ปุ่นที่ญี่ปุ่น',
                'title_en' => 'Japanese Language in Japan',
                'summary_th' => 'หลักสูตรภาษาญี่ปุ่นสำหรับวัยรุ่นถึงวัยทำงาน พร้อมวัฒนธรรมแบบ immersion',
                'summary_en' => 'Japanese language courses for teens to adults with cultural immersion',
                'age_min' => 16,
                'age_max' => 35,
                'duration_label_th' => '4–48 สัปดาห์',
                'duration_label_en' => '4–48 weeks',
                'language' => 'japanese',
                'destinations' => ['japan'],
                'cover_path' => 'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?auto=format&fit=crop&w=1200&q=80',
                'sort_order' => 6,
            ],
        ];

        foreach ($programs as $program) {
            Program::query()->updateOrCreate(
                ['slug' => $program['slug']],
                [
                    ...$program,
                    'content_th' => $program['summary_th']."\n\nปรึกษาที่ปรึกษา Education Interntions เพื่อเลือกประเทศ ระยะเวลา และงบประมาณที่เหมาะกับคุณ",
                    'content_en' => $program['summary_en']."\n\nTalk to a Education Interntions advisor to choose destination, duration, and budget.",
                    'cta_label_th' => 'ปรึกษาฟรี',
                    'cta_label_en' => 'Free consultation',
                    'cta_url' => '/contact',
                    'is_featured' => true,
                    'is_active' => true,
                ]
            );
        }
    }
}


