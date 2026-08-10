import { Layers } from "lucide-react";
import { Link } from "@/i18n/navigation";
import type { HomeSection } from "@/lib/api";
import { coverFor } from "@/lib/media";
import { localized } from "@/lib/utils";

export type ProgramCategoryItem = {
  title_th?: string;
  title_en?: string;
  summary_th?: string;
  summary_en?: string;
  href?: string;
  external?: boolean;
  cover_path?: string | null;
};

type Props = {
  locale: string;
  section?: HomeSection | null;
};

const FALLBACK_ITEMS: ProgramCategoryItem[] = [
  {
    title_th: "คอร์สเรียนภาษาที่ต่างประเทศ",
    title_en: "Language courses abroad",
    summary_th: "เลือกเรียนภาษาที่หลากหลาย จากจุดหมายปลายทางทั่วโลกกับ Education Interntions",
    summary_en: "Choose from language courses across global destinations with Education Interntions",
    href: "/learn-language",
    cover_path:
      "https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1200&q=80",
  },
  {
    title_th: "เรียนต่างประเทศระยะยาว (Academic Year)",
    title_en: "Academic Year abroad",
    summary_th: "รวมการเรียนภาษาและการเรียนเชิงวิชาการในต่างประเทศแบบระยะยาว",
    summary_en: "Combine language study with academic immersion for a full term or year",
    href: "/learn-language/academic-year",
    cover_path:
      "https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1200&q=80",
  },
  {
    title_th: "โรงเรียนมัธยมศึกษาในต่างประเทศ",
    title_en: "High school abroad",
    summary_th: "วางแผนเรียนมัธยม / หลักสูตรอินเตอร์ในต่างประเทศ พร้อมที่ปรึกษา Education Interntions",
    summary_en: "Plan high school or international curricula abroad with Education Interntions advisors",
    href: "/study-abroad",
    cover_path:
      "https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=1200&q=80",
  },
  {
    title_th: "เรียนมหาวิทยาลัยต่างประเทศ",
    title_en: "University abroad",
    summary_th: "โปรแกรมเตรียมเข้ามหาวิทยาลัยและหลักสูตรปริญญาในต่างประเทศ",
    summary_en: "University pathway and degree programs overseas",
    href: "/universities",
    cover_path:
      "https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1200&q=80",
  },
  {
    title_th: "เรียนภาษาอังกฤษ / IELTS",
    title_en: "English & IELTS",
    summary_th: "วางแผนคะแนนเป้าหมายและคอร์สภาษาที่เหมาะกับเส้นทางเรียนต่อ",
    summary_en: "Map target scores and language courses to your study pathway",
    href: "/ielts",
    cover_path:
      "https://images.unsplash.com/photo-1456513080080-7e9b1b0c5f2f?auto=format&fit=crop&w=1200&q=80",
  },
  {
    title_th: "การฝึกอบรมสำหรับองค์กร",
    title_en: "Corporate training",
    summary_th: "หลักสูตรภาษาและการพัฒนาทักษะสำหรับองค์กรและทีมงาน",
    summary_en: "Language and skills programs for companies and teams",
    href: "/contact",
    cover_path:
      "https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1200&q=80",
  },
];

function ArrowIcon({ external }: { external?: boolean }) {
  if (external) {
    return (
      <svg
        className="h-5 w-5 transition group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
        viewBox="0 0 24 24"
        fill="none"
        aria-hidden
      >
        <path
          d="M12.75 11.25L20.25 3.75M20.25 9.75V3.75H14.25M17.25 13.125V19.5a.75.75 0 01-.75.75H4.5a.75.75 0 01-.75-.75V7.5a.75.75 0 01.75-.75h6.375"
          stroke="currentColor"
          strokeWidth="2"
          strokeLinecap="round"
          strokeLinejoin="round"
        />
      </svg>
    );
  }

  return (
    <svg
      className="h-5 w-5 transition group-hover:translate-x-1"
      viewBox="0 0 24 24"
      fill="none"
      aria-hidden
    >
      <path
        d="M4 12h16M13 5l7 7-7 7"
        stroke="currentColor"
        strokeWidth="2.3"
        strokeLinecap="round"
        strokeLinejoin="round"
      />
    </svg>
  );
}

export function LearnLanguageSection({ locale, section }: Props) {
  const title =
    (section ? localized(section, locale, "title") : "") ||
    (locale === "th" ? "สำรวจประเภทโปรแกรม" : "Explore program types");

  const rawItems = (section?.items as ProgramCategoryItem[] | null | undefined) ?? [];
  const items =
    rawItems.length > 0 && rawItems.some((i) => i.title_th || i.title_en)
      ? rawItems
      : FALLBACK_ITEMS;

  return (
    <section id="by-program" className="section relative bg-white">
      <div className="mx-auto max-w-7xl">
        <div className="mx-auto max-w-xl px-3 pb-8 pt-4 text-center lg:pb-12 lg:pt-8">
          <div className="mb-4 inline-flex rounded-full bg-pink-100 p-4 text-pink-600">
            <Layers className="h-6 w-6" aria-hidden />
          </div>
          <h2 className="text-2xl font-bold text-win-ink md:text-3xl">{title}</h2>
        </div>

        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {items.map((item, index) => {
            const itemTitle =
              locale === "th"
                ? item.title_th || item.title_en || ""
                : item.title_en || item.title_th || "";
            const summary =
              locale === "th"
                ? item.summary_th || item.summary_en || ""
                : item.summary_en || item.summary_th || "";
            const href = item.href || "/learn-language";
            const cover = coverFor(`program-cat-${index}`, item.cover_path);
            const external = Boolean(item.external) || /^https?:\/\//i.test(href);

            const body = (
              <>
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img
                  src={cover}
                  alt={itemTitle}
                  className="aspect-[16/10] w-full object-cover transition duration-300 group-hover:scale-[1.02]"
                />
                <div className="flex flex-1 flex-col gap-3 p-5 md:p-6">
                  <div>
                    <h3 className="text-start text-xl font-bold leading-snug text-win-ink md:text-2xl">
                      {itemTitle}
                    </h3>
                    <p className="mt-3 text-start text-sm leading-relaxed text-win-muted md:text-base">
                      {summary}
                    </p>
                  </div>
                  <div className="mt-auto flex justify-end pt-2 text-win-purple">
                    <ArrowIcon external={external} />
                  </div>
                </div>
              </>
            );

            const className =
              "group flex h-full flex-col overflow-hidden rounded-xl border border-black/8 bg-white shadow-[0_1px_0_rgba(18,24,38,0.06)] transition duration-300 hover:-translate-y-0.5 hover:border-win-purple/25 hover:shadow-[0_12px_28px_rgba(18,24,38,0.1)]";

            if (external) {
              return (
                <a
                  key={`${href}-${index}`}
                  href={href}
                  target="_blank"
                  rel="noopener noreferrer"
                  className={className}
                >
                  {body}
                </a>
              );
            }

            return (
              <Link key={`${href}-${index}`} href={href} className={className}>
                {body}
              </Link>
            );
          })}
        </div>
      </div>
    </section>
  );
}
