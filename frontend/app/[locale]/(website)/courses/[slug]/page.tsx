import { getTranslations, setRequestLocale } from "next-intl/server";
import { notFound } from "next/navigation";
import { CatalogCta } from "@/components/catalog/CatalogCta";
import { Link } from "@/i18n/navigation";
import { getCourse } from "@/lib/api";
import { localized } from "@/lib/utils";

type Props = {
  params: Promise<{ locale: string; slug: string }>;
};

export default async function CourseDetailPage({ params }: Props) {
  const { locale, slug } = await params;
  setRequestLocale(locale);

  const course = await getCourse(slug);
  if (!course) notFound();

  const t = await getTranslations("catalog");
  const name = localized(course, locale, "name");
  const uniName = course.university
    ? localized(course.university, locale, "name")
    : "";

  return (
    <section className="section">
      <div className="mx-auto max-w-5xl space-y-8">
        <div className="card-soft">
          <p className="text-sm font-semibold text-win-purple">{uniName}</p>
          <h1 className="mt-2 text-3xl font-bold text-win-ink md:text-4xl">{name}</h1>
          <p className="mt-3 text-win-muted">
            {localized(course, locale, "summary")}
          </p>
          <div className="mt-6 grid gap-3 sm:grid-cols-3">
            <Info label={t("degree")} value={course.degree_level ?? "-"} />
            <Info
              label={t("duration")}
              value={
                course.duration_months
                  ? `${course.duration_months} ${t("months")}`
                  : "-"
              }
            />
            <Info
              label={t("tuition")}
              value={`${course.tuition ?? "-"} ${course.currency ?? ""}`}
            />
          </div>
          {course.intakes?.length ? (
            <p className="mt-4 text-sm text-win-muted">
              {t("intakes")}: {course.intakes.join(", ")}
            </p>
          ) : null}
          <div className="mt-6 flex flex-wrap gap-3">
            <Link href="/apply" className="btn-primary">
              {t("applyNow")}
            </Link>
            <Link href="/contact" className="rounded-xl border border-black/10 px-5 py-3 text-sm font-semibold">
              {t("consult")}
            </Link>
            {course.university ? (
              <Link
                href={`/universities/${course.university.slug}`}
                className="rounded-xl border border-black/10 px-5 py-3 text-sm font-semibold"
              >
                {t("viewUniversity")}
              </Link>
            ) : null}
          </div>
        </div>
        <CatalogCta />
      </div>
    </section>
  );
}

function Info({ label, value }: { label: string; value: string }) {
  return (
    <div className="rounded-xl bg-win-sky/70 px-4 py-3">
      <p className="text-xs font-semibold uppercase text-win-muted">{label}</p>
      <p className="mt-1 font-semibold capitalize text-win-ink">{value}</p>
    </div>
  );
}
