import { getTranslations, setRequestLocale } from "next-intl/server";
import { notFound } from "next/navigation";
import { CatalogCta } from "@/components/catalog/CatalogCta";
import { ScholarshipCard } from "@/components/scholarship/ScholarshipCard";
import { UniversityCard } from "@/components/university/UniversityCard";
import { Link } from "@/i18n/navigation";
import { getCountry } from "@/lib/api";
import { localized } from "@/lib/utils";

type Props = {
  params: Promise<{ locale: string; slug: string }>;
};

export default async function CountryDetailPage({ params }: Props) {
  const { locale, slug } = await params;
  setRequestLocale(locale);

  const country = await getCountry(slug);
  if (!country) notFound();

  const t = await getTranslations("catalog");
  const name = localized(country, locale, "name");

  return (
    <section className="section">
      <div className="mx-auto max-w-7xl space-y-10">
        <div className="card-soft">
          <p className="text-sm font-semibold uppercase tracking-wide text-win-purple">
            {country.code}
          </p>
          <h1 className="mt-2 text-3xl font-bold text-win-ink md:text-4xl">{name}</h1>
          <p className="mt-3 max-w-3xl text-win-muted">
            {localized(country, locale, "summary")}
          </p>
          <div className="mt-6 flex flex-wrap gap-3">
            <Link href="/contact" className="btn-primary">
              {t("consult")}
            </Link>
            <Link
              href={`/universities?country=${country.slug}`}
              className="rounded-xl border border-black/10 px-5 py-3 text-sm font-semibold"
            >
              {t("universities")}
            </Link>
          </div>
        </div>

        <div>
          <h2 className="section-title">{t("whyStudy")} {name}</h2>
          <p className="section-subtitle mt-3 whitespace-pre-line">
            {localized(country, locale, "content") ||
              localized(country, locale, "summary")}
          </p>
        </div>

        <div>
          <div className="flex items-end justify-between gap-4">
            <h2 className="section-title">{t("universities")}</h2>
            <Link
              href={`/universities?country=${country.slug}`}
              className="text-sm font-semibold text-win-purple"
            >
              {t("viewAll")}
            </Link>
          </div>
          <div className="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            {(country.universities ?? []).map((university) => (
              <UniversityCard
                key={university.id}
                university={university}
                locale={locale}
              />
            ))}
          </div>
        </div>

        {(country.scholarships?.length ?? 0) > 0 && (
          <div>
            <h2 className="section-title">{t("scholarships")}</h2>
            <div className="mt-6 grid gap-4 md:grid-cols-3">
              {country.scholarships!.map((item) => (
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
