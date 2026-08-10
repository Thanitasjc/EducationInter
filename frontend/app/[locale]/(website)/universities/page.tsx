import { getTranslations, setRequestLocale } from "next-intl/server";
import { CatalogCta } from "@/components/catalog/CatalogCta";
import { UniversityCard } from "@/components/university/UniversityCard";
import { UniversityFilters } from "@/components/university/UniversityFilters";
import { getCountries, getUniversities } from "@/lib/api";

type Props = {
  params: Promise<{ locale: string }>;
  searchParams: Promise<Record<string, string | undefined>>;
};

export default async function UniversitiesPage({ params, searchParams }: Props) {
  const { locale } = await params;
  const query = await searchParams;
  setRequestLocale(locale);

  const t = await getTranslations("catalog");
  const [countries, universities] = await Promise.all([
    getCountries(),
    getUniversities({
      q: query.q,
      country: query.country,
      type: query.type,
    }),
  ]);

  return (
    <section className="section">
      <div className="mx-auto max-w-7xl space-y-6">
        <div>
          <h1 className="section-title">{t("universitiesTitle")}</h1>
          <p className="section-subtitle">{t("universitiesSubtitle")}</p>
        </div>

        <UniversityFilters
          locale={locale}
          countries={countries}
          initialQ={query.q}
          initialCountry={query.country}
          initialType={query.type}
        />

        <p className="text-sm text-win-muted">
          {t("results", { count: universities.total })}
        </p>

        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          {universities.data.map((university) => (
            <UniversityCard
              key={university.id}
              university={university}
              locale={locale}
            />
          ))}
        </div>

        {universities.data.length === 0 && (
          <div className="card-soft text-win-muted">{t("emptyUniversities")}</div>
        )}

        <CatalogCta />
      </div>
    </section>
  );
}
