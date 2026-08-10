import { getTranslations, setRequestLocale } from "next-intl/server";
import { CatalogCta } from "@/components/catalog/CatalogCta";
import { CourseCard } from "@/components/course/CourseCard";
import { getCourses } from "@/lib/api";

type Props = {
  params: Promise<{ locale: string }>;
  searchParams: Promise<Record<string, string | undefined>>;
};

export default async function CoursesPage({ params, searchParams }: Props) {
  const { locale } = await params;
  const query = await searchParams;
  setRequestLocale(locale);

  const t = await getTranslations("catalog");
  const courses = await getCourses({
    q: query.q,
    degree_level: query.degree_level,
    university: query.university,
  });

  return (
    <section className="section">
      <div className="mx-auto max-w-7xl">
        <h1 className="section-title">{t("coursesTitle")}</h1>
        <p className="section-subtitle">{t("coursesSubtitle")}</p>
        <p className="mt-4 text-sm text-win-muted">
          {t("results", { count: courses.total })}
        </p>
        <div className="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          {courses.data.map((course) => (
            <CourseCard key={course.id} course={course} locale={locale} />
          ))}
        </div>
        {courses.data.length === 0 && (
          <div className="card-soft mt-6 text-win-muted">{t("emptyCourses")}</div>
        )}
        <CatalogCta />
      </div>
    </section>
  );
}
