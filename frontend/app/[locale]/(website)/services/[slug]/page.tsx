import { getTranslations, setRequestLocale } from "next-intl/server";
import { notFound } from "next/navigation";
import { CatalogCta } from "@/components/catalog/CatalogCta";
import { Link } from "@/i18n/navigation";
import { getService } from "@/lib/api";
import { localized } from "@/lib/utils";

type Props = {
  params: Promise<{ locale: string; slug: string }>;
};

export default async function ServiceDetailPage({ params }: Props) {
  const { locale, slug } = await params;
  setRequestLocale(locale);

  const service = await getService(slug);
  if (!service) notFound();

  const t = await getTranslations("servicesPage");
  const title = localized(service, locale, "title");
  const cta = localized(service, locale, "cta_label") || t("consult");

  return (
    <section className="section">
      <div className="mx-auto max-w-4xl space-y-8">
        <div className="card-soft">
          <h1 className="text-3xl font-bold text-win-ink md:text-4xl">{title}</h1>
          <p className="mt-3 text-win-muted">
            {localized(service, locale, "summary")}
          </p>
          <p className="mt-6 whitespace-pre-line text-win-ink">
            {localized(service, locale, "content") ||
              localized(service, locale, "summary")}
          </p>
          <div className="mt-8 flex flex-wrap gap-3">
            <Link href="/contact" className="btn-primary">
              {cta}
            </Link>
            <Link href="/apply" className="rounded-xl border border-black/10 px-5 py-3 text-sm font-semibold">
              {t("apply")}
            </Link>
          </div>
        </div>
        <CatalogCta />
      </div>
    </section>
  );
}
