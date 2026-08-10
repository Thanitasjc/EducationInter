import { getTranslations, setRequestLocale } from "next-intl/server";
import { CatalogCta } from "@/components/catalog/CatalogCta";
import { Link } from "@/i18n/navigation";
import { getCountries, getServices } from "@/lib/api";
import { buildMetadata } from "@/lib/seo";
import { localized } from "@/lib/utils";

type Props = { params: Promise<{ locale: string }> };

export async function generateMetadata({ params }: Props) {
  const { locale } = await params;
  const t = await getTranslations({ locale, namespace: "studyAbroad" });
  return buildMetadata({
    locale,
    path: "/study-abroad",
    title: `${t("title")} | Education Interntions`,
    description: t("subtitle"),
  });
}

export default async function StudyAbroadPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  const t = await getTranslations("studyAbroad");
  const [countries, services] = await Promise.all([getCountries(), getServices()]);

  return (
    <section className="section">
      <div className="mx-auto max-w-7xl">
        <h1 className="section-title">{t("title")}</h1>
        <p className="section-subtitle">{t("subtitle")}</p>

        <div className="mt-10">
          <h2 className="text-xl font-bold text-win-purple">{t("destinations")}</h2>
          <div className="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {countries.slice(0, 6).map((country) => (
              <Link key={country.id} href={`/countries/${country.slug}`} className="card-soft block">
                <p className="font-bold">{localized(country, locale, "name")}</p>
                <p className="mt-2 line-clamp-2 text-sm text-win-muted">
                  {localized(country, locale, "summary")}
                </p>
              </Link>
            ))}
          </div>
        </div>

        <div className="mt-12">
          <h2 className="text-xl font-bold text-win-purple">{t("support")}</h2>
          <div className="mt-4 grid gap-4 md:grid-cols-3">
            {services.slice(0, 6).map((service) => (
              <Link key={service.id} href={`/services/${service.slug}`} className="card-soft block">
                <p className="font-bold">{localized(service, locale, "title")}</p>
                <p className="mt-2 line-clamp-3 text-sm text-win-muted">
                  {localized(service, locale, "summary")}
                </p>
              </Link>
            ))}
          </div>
        </div>

        <CatalogCta />
      </div>
    </section>
  );
}
