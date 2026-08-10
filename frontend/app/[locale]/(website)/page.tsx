import { setRequestLocale } from "next-intl/server";
import { Hero } from "@/components/hero/Hero";
import { HomeSections } from "@/components/home/HomeSections";
import { UniversitySearch } from "@/components/home/UniversitySearch";
import { getHome } from "@/lib/api";

type Props = {
  params: Promise<{ locale: string }>;
};

export default async function HomePage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  const data = await getHome(locale);

  return (
    <>
      <Hero hero={data?.hero} locale={locale} />
      <UniversitySearch />
      <HomeSections data={data} locale={locale} />
    </>
  );
}
