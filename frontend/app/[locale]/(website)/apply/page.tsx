import { setRequestLocale } from "next-intl/server";
import { ApplyWizard } from "@/components/application/ApplyWizard";
import { getCountries, getCourses, getUniversities } from "@/lib/api";

export const dynamic = "force-dynamic";

type Props = { params: Promise<{ locale: string }> };

export default async function ApplyPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  const [countries, universitiesRes, coursesRes] = await Promise.all([
    getCountries(),
    getUniversities({ per_page: "100" }),
    getCourses({ per_page: "100" }),
  ]);

  return (
    <section className="section">
      <ApplyWizard
        countries={countries}
        universities={universitiesRes.data}
        courses={coursesRes.data}
      />
    </section>
  );
}
