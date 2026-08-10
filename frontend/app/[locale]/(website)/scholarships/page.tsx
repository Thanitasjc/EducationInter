import { getTranslations, setRequestLocale } from "next-intl/server";
import { CatalogCta } from "@/components/catalog/CatalogCta";
import { ScholarshipCard } from "@/components/scholarship/ScholarshipCard";
import { getScholarships } from "@/lib/api";

type Props = {
  params: Promise<{ locale: string }>;
  searchParams: Promise<Record<string, string | undefined>>;
};

export default async function ScholarshipsPage({ params, searchParams }: Props) {
  const { locale } = await params;
  const query = await searchParams;
  setRequestLocale(locale);

  const t = await getTranslations("catalog");
  const scholarships = await getScholarships({ country: query.country });

  return (
    <section className="section">
      <div className="mx-auto max-w-7xl">
        <h1 className="section-title">{t("scholarshipsTitle")}</h1>
        <p className="section-subtitle">{t("scholarshipsSubtitle")}</p>
        <p className="mt-4 text-sm text-win-muted">
          {t("results", { count: scholarships.total })}
        </p>
        <div className="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          {scholarships.data.map((item) => (
            <ScholarshipCard key={item.id} scholarship={item} locale={locale} />
          ))}
        </div>
        {scholarships.data.length === 0 && (
          <div className="card-soft mt-6 text-win-muted">{t("emptyScholarships")}</div>
        )}
        <CatalogCta />
      </div>
    </section>
  );
}
