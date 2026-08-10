import { getTranslations, setRequestLocale } from "next-intl/server";
import { CatalogCta } from "@/components/catalog/CatalogCta";
import { Link } from "@/i18n/navigation";
import { getServices } from "@/lib/api";
import { localized } from "@/lib/utils";

type Props = { params: Promise<{ locale: string }> };

export default async function ServicesPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  const t = await getTranslations("servicesPage");
  const services = await getServices();

  return (
    <section className="section">
      <div className="mx-auto max-w-7xl">
        <h1 className="section-title">{t("title")}</h1>
        <p className="section-subtitle">{t("subtitle")}</p>
        <div className="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          {services.map((service) => (
            <Link
              key={service.id}
              href={`/services/${service.slug}`}
              className="card-soft block transition hover:-translate-y-1 hover:border-win-purple/30"
            >
              <p className="text-lg font-bold text-win-blue">
                {localized(service, locale, "title")}
              </p>
              <p className="mt-2 line-clamp-3 text-sm text-win-muted">
                {localized(service, locale, "summary")}
              </p>
              <span className="mt-4 inline-block text-sm font-semibold text-win-purple">
                {localized(service, locale, "cta_label") || t("learnMore")} →
              </span>
            </Link>
          ))}
        </div>
        <CatalogCta />
      </div>
    </section>
  );
}
