import { getTranslations, setRequestLocale } from "next-intl/server";
import { CatalogCta } from "@/components/catalog/CatalogCta";
import { getPageContent } from "@/lib/api";
import { buildMetadata } from "@/lib/seo";

type Props = { params: Promise<{ locale: string }> };

export async function generateMetadata({ params }: Props) {
  const { locale } = await params;
  const t = await getTranslations({ locale, namespace: "about" });
  return buildMetadata({
    locale,
    path: "/about",
    title: `${t("title")} | Education Interntions`,
    description: t("subtitle"),
  });
}

export default async function AboutPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  const t = await getTranslations("about");
  const page = await getPageContent("about");
  const value = page?.value ?? {};

  const title = String(
    value[`title_${locale}`] || value.title_th || t("title"),
  );
  const body = String(value[`body_${locale}`] || value.body_th || t("body"));

  return (
    <section className="section">
      <div className="mx-auto max-w-4xl">
        <h1 className="section-title">{title}</h1>
        <p className="section-subtitle">{t("subtitle")}</p>
        <div className="card-soft mt-8 whitespace-pre-line leading-relaxed text-win-ink/90">
          {body}
        </div>
        <CatalogCta />
      </div>
    </section>
  );
}
