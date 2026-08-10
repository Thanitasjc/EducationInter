import { getTranslations, setRequestLocale } from "next-intl/server";
import { CatalogCta } from "@/components/catalog/CatalogCta";
import { CountryCard } from "@/components/catalog/CountryCard";
import { getCountries } from "@/lib/api";

type Props = { params: Promise<{ locale: string }> };

export default async function CountriesPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  const t = await getTranslations("catalog");
  const countries = await getCountries();

  return (
    <section className="section">
      <div className="mx-auto max-w-7xl">
        <h1 className="section-title">{t("countriesTitle")}</h1>
        <p className="section-subtitle">{t("countriesSubtitle")}</p>
        <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
          {countries.map((country) => (
            <CountryCard key={country.id} country={country} locale={locale} />
          ))}
        </div>
        <CatalogCta />
      </div>
    </section>
  );
}
