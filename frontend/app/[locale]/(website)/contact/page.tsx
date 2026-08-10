import { setRequestLocale } from "next-intl/server";
import { LeadForm } from "@/components/forms/LeadForm";

type Props = {
  params: Promise<{ locale: string }>;
};

export default async function ContactPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  return (
    <section className="section">
      <LeadForm />
    </section>
  );
}
