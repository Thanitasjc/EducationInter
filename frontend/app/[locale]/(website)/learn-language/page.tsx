import { getTranslations, setRequestLocale } from "next-intl/server";
import { LearnLanguageExplorer } from "@/components/programs/LearnLanguageExplorer";
import { getCountries, getPageContent, getPrograms } from "@/lib/api";
import { parseAgeGroup } from "@/lib/programs";
import { buildMetadata } from "@/lib/seo";

type Props = {
  params: Promise<{ locale: string }>;
  searchParams: Promise<{ age?: string }>;
};

const HERO_IMAGE =
  "https://images.unsplash.com/photo-1529333166437-7750a6dd5a70?auto=format&fit=crop&w=1400&q=80";

export async function generateMetadata({ params }: Props) {
  const { locale } = await params;
  const t = await getTranslations({ locale, namespace: "learnLanguage" });
  return buildMetadata({
    locale,
    path: "/learn-language",
    title: `${t("title")} | Education Interntions`,
    description: t("subtitle"),
    image: HERO_IMAGE,
  });
}

export default async function LearnLanguagePage({ params, searchParams }: Props) {
  const { locale } = await params;
  const { age: ageParam } = await searchParams;
  setRequestLocale(locale);

  const t = await getTranslations("learnLanguage");
  const age = parseAgeGroup(ageParam);
  const [page, programs, countries] = await Promise.all([
    getPageContent("learn-language"),
    getPrograms({ per_page: "48" }),
    getCountries(),
  ]);

  const value = page?.value ?? {};
  const title = String(
    (locale === "th" ? value.title_th : value.title_en) || t("title"),
  );

  const introFromCms =
    locale === "th"
      ? [value.intro_th_1, value.intro_th_2].filter(Boolean)
      : [value.intro_en_1, value.intro_en_2].filter(Boolean);

  const introHtml =
    introFromCms.length > 0
      ? introFromCms.map(String)
      : [t("intro1"), t("intro2")];

  return (
    <LearnLanguageExplorer
      programs={programs.data}
      countries={countries}
      locale={locale}
      initialAge={age}
      title={title}
      introHtml={introHtml}
      heroImage={HERO_IMAGE}
    />
  );
}
