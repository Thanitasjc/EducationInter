import { getTranslations, setRequestLocale } from "next-intl/server";
import { AcademicYearLanding } from "@/components/programs/AcademicYearLanding";
import { JsonLd } from "@/components/seo/JsonLd";
import { getCountries, getPageContent } from "@/lib/api";
import { mediaUrl } from "@/lib/media";
import { buildMetadata } from "@/lib/seo";

type Props = { params: Promise<{ locale: string }> };

const HERO_IMAGE =
  "https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1600&q=80";

function pickLocalized(
  value: Record<string, unknown>,
  locale: string,
  thKey: string,
  enKey: string,
): string | undefined {
  const raw = locale === "th" ? value[thKey] : value[enKey];
  return typeof raw === "string" && raw.trim() ? raw : undefined;
}

function asRecordArray(value: unknown): Array<Record<string, unknown>> {
  if (!Array.isArray(value)) return [];
  return value.filter((item): item is Record<string, unknown> => !!item && typeof item === "object");
}

export async function generateMetadata({ params }: Props) {
  const { locale } = await params;
  const t = await getTranslations({ locale, namespace: "academicYear" });
  const page = await getPageContent("academic-year");
  const value = page?.value ?? {};

  const title =
    pickLocalized(value, locale, "meta_title_th", "meta_title_en") ||
    pickLocalized(value, locale, "title_th", "title_en") ||
    t("metaTitle");
  const description =
    pickLocalized(value, locale, "meta_description_th", "meta_description_en") ||
    pickLocalized(value, locale, "subtitle_th", "subtitle_en") ||
    t("metaDescription");
  const heroRaw = typeof value.hero_image === "string" ? value.hero_image : null;
  const image = mediaUrl(heroRaw) || HERO_IMAGE;

  return buildMetadata({
    locale,
    path: "/learn-language/academic-year",
    title: `${title} | Education Interntions`,
    description,
    image,
  });
}

export default async function AcademicYearPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  const t = await getTranslations("academicYear");
  const [page, countries] = await Promise.all([
    getPageContent("academic-year"),
    getCountries(),
  ]);

  const value = page?.value ?? {};
  const title =
    pickLocalized(value, locale, "title_th", "title_en") || t("heroTitle");
  const subtitle =
    pickLocalized(value, locale, "subtitle_th", "subtitle_en") || t("heroBody");
  const heroRaw = typeof value.hero_image === "string" ? value.hero_image : null;
  const heroImage = mediaUrl(heroRaw) || HERO_IMAGE;

  const usps = asRecordArray(value.usps)
    .map((item) => ({
      title:
        (locale === "th" ? item.title_th : item.title_en) ||
        item.title_th ||
        item.title_en ||
        "",
      body:
        (locale === "th" ? item.body_th : item.body_en) ||
        item.body_th ||
        item.body_en ||
        "",
    }))
    .map((item) => ({
      title: String(item.title),
      body: String(item.body),
    }));

  const faqs = asRecordArray(value.faqs)
    .map((item) => ({
      question:
        (locale === "th" ? item.question_th : item.question_en) ||
        item.question_th ||
        item.question_en ||
        "",
      answer:
        (locale === "th" ? item.answer_th : item.answer_en) ||
        item.answer_th ||
        item.answer_en ||
        "",
    }))
    .map((item) => ({
      question: String(item.question),
      answer: String(item.answer),
    }));

  return (
    <>
      <JsonLd
        data={{
          "@context": "https://schema.org",
          "@type": "Course",
          name: title,
          description: subtitle,
          provider: { "@type": "Organization", name: "Education Interntions" },
          image: heroImage,
        }}
      />
      <AcademicYearLanding
        locale={locale}
        countries={countries}
        heroImage={heroImage}
        title={title}
        subtitle={subtitle}
        cms={{
          promoBanner: pickLocalized(value, locale, "promo_banner_th", "promo_banner_en"),
          whyTitle: pickLocalized(value, locale, "why_title_th", "why_title_en"),
          usps,
          faqTitle: pickLocalized(value, locale, "faq_title_th", "faq_title_en"),
          faqBody: pickLocalized(value, locale, "faq_body_th", "faq_body_en"),
          faqs,
        }}
      />
    </>
  );
}
