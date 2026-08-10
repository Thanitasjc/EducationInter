import { getTranslations, setRequestLocale } from "next-intl/server";
import { notFound } from "next/navigation";
import { CatalogCta } from "@/components/catalog/CatalogCta";
import { CourseCard } from "@/components/course/CourseCard";
import { ScholarshipCard } from "@/components/scholarship/ScholarshipCard";
import { JsonLd } from "@/components/seo/JsonLd";
import { Link } from "@/i18n/navigation";
import { getUniversity } from "@/lib/api";
import { buildMetadata, type SeoPayload } from "@/lib/seo";
import { localized } from "@/lib/utils";

type Props = {
  params: Promise<{ locale: string; slug: string }>;
};

export async function generateMetadata({ params }: Props) {
  const { locale, slug } = await params;
  const university = await getUniversity(slug);
  if (!university) return {};
  const seo = (university as { seo?: SeoPayload | null }).seo;
  return buildMetadata({
    locale,
    path: `/universities/${slug}`,
    title: `${localized(university, locale, "name")} | Education Interntions`,
    description: localized(university, locale, "about"),
    seo,
  });
}

export default async function UniversityDetailPage({ params }: Props) {
  const { locale, slug } = await params;
  setRequestLocale(locale);

  const university = await getUniversity(slug);
  if (!university) notFound();

  const t = await getTranslations("catalog");
  const name = localized(university, locale, "name");
  const countryName = university.country
    ? localized(university.country, locale, "name")
    : "";

  return (
    <section className="section">
      <JsonLd
        data={{
          "@context": "https://schema.org",
          "@type": "CollegeOrUniversity",
          name,
          description: localized(university, locale, "about"),
        }}
      />
      <div className="mx-auto max-w-7xl space-y-10">
        <div className="overflow-hidden rounded-3xl bg-gradient-to-br from-win-purple via-win-blue to-win-purple-deep p-8 text-white md:p-12">
          <p className="text-sm font-semibold uppercase tracking-wide text-white/70">
            {countryName}
            {university.ranking_qs ? ` · QS #${university.ranking_qs}` : ""}
          </p>
          <h1 className="mt-3 text-3xl font-bold md:text-5xl">{name}</h1>
          <p className="mt-4 max-w-3xl text-white/85">
            {localized(university, locale, "about")}
          </p>
          <div className="mt-6 flex flex-wrap gap-3">
            <Link
              href="/contact"
              className="btn-primary bg-white text-win-purple hover:bg-win-sky"
            >
              {t("consult")}
            </Link>
            <Link href="/apply" className="btn-secondary">
              {t("applyNow")}
            </Link>
          </div>
        </div>

        <div className="grid gap-4 md:grid-cols-3">
          <div className="card-soft">
            <p className="text-xs font-semibold uppercase text-win-muted">
              {t("tuition")}
            </p>
            <p className="mt-2 text-lg font-bold text-win-blue">
              {university.tuition_min ?? "-"} – {university.tuition_max ?? "-"}{" "}
              {university.currency}
            </p>
          </div>
          <div className="card-soft">
            <p className="text-xs font-semibold uppercase text-win-muted">
              {t("type")}
            </p>
            <p className="mt-2 text-lg font-bold capitalize">
              {university.type ?? "-"}
            </p>
          </div>
          <div className="card-soft">
            <p className="text-xs font-semibold uppercase text-win-muted">
              {t("courses")}
            </p>
            <p className="mt-2 text-lg font-bold">
              {university.courses?.length ?? 0}
            </p>
          </div>
        </div>

        <div>
          <h2 className="section-title">{t("courses")}</h2>
          <div className="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            {(university.courses ?? []).map((course) => (
              <CourseCard key={course.id} course={course} locale={locale} />
            ))}
          </div>
        </div>

        {(university.scholarships?.length ?? 0) > 0 && (
          <div>
            <h2 className="section-title">{t("scholarships")}</h2>
            <div className="mt-6 grid gap-4 md:grid-cols-3">
              {university.scholarships!.map((item) => (
                <ScholarshipCard key={item.id} scholarship={item} locale={locale} />
              ))}
            </div>
          </div>
        )}

        <CatalogCta />
      </div>
    </section>
  );
}
