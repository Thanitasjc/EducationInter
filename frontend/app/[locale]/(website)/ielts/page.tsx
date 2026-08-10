import { getTranslations, setRequestLocale } from "next-intl/server";
import { CatalogCta } from "@/components/catalog/CatalogCta";
import { Link } from "@/i18n/navigation";
import { getService } from "@/lib/api";
import { buildMetadata } from "@/lib/seo";
import { localized } from "@/lib/utils";

type Props = { params: Promise<{ locale: string }> };

export async function generateMetadata({ params }: Props) {
  const { locale } = await params;
  const t = await getTranslations({ locale, namespace: "ielts" });
  return buildMetadata({
    locale,
    path: "/ielts",
    title: `${t("title")} | Education Interntions`,
    description: t("subtitle"),
  });
}

export default async function IeltsPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  const t = await getTranslations("ielts");
  const service = await getService("ielts");

  return (
    <section className="section">
      <div className="mx-auto max-w-4xl">
        <h1 className="section-title">
          {service ? localized(service, locale, "title") : t("title")}
        </h1>
        <p className="section-subtitle">
          {service ? localized(service, locale, "summary") : t("subtitle")}
        </p>
        <div className="card-soft mt-8 whitespace-pre-line leading-relaxed">
          {service
            ? localized(service, locale, "content") || localized(service, locale, "summary")
            : t("body")}
        </div>
        <div className="mt-6 flex flex-wrap gap-3">
          <Link href="/contact" className="btn-primary">
            {t("consult")}
          </Link>
          <Link href="/apply" className="rounded-xl border border-black/10 px-5 py-3 text-sm font-semibold">
            {t("apply")}
          </Link>
        </div>
        <CatalogCta />
      </div>
    </section>
  );
}
