import { getTranslations, setRequestLocale } from "next-intl/server";
import { notFound } from "next/navigation";
import { CatalogCta } from "@/components/catalog/CatalogCta";
import { Link } from "@/i18n/navigation";
import { getScholarship } from "@/lib/api";
import { coverFor } from "@/lib/media";
import { localized } from "@/lib/utils";

type Props = {
  params: Promise<{ locale: string; slug: string }>;
};

export default async function ScholarshipDetailPage({ params }: Props) {
  const { locale, slug } = await params;
  setRequestLocale(locale);

  const scholarship = await getScholarship(slug);
  if (!scholarship) notFound();

  const t = await getTranslations("catalog");
  const title = localized(scholarship, locale, "title");
  const cover = coverFor(
    scholarship.slug,
    scholarship.cover_path,
    scholarship.university?.cover_path,
  );

  return (
    <section className="section">
      <div className="mx-auto max-w-4xl space-y-8">
        <div className="overflow-hidden rounded-3xl">
          {/* eslint-disable-next-line @next/next/no-img-element */}
          <img src={cover} alt={title} className="aspect-[21/9] w-full object-cover md:aspect-[2/1]" />
        </div>
        <div className="card-soft">
          <h1 className="text-3xl font-bold text-win-ink md:text-4xl">{title}</h1>
          <p className="mt-3 text-xl font-semibold text-win-blue">
            {localized(scholarship, locale, "amount_label") || t("amountTbd")}
          </p>
          {scholarship.university ? (
            <p className="mt-2 text-win-muted">
              {localized(scholarship.university, locale, "name")}
            </p>
          ) : null}
          {scholarship.deadline ? (
            <p className="mt-4 text-sm text-win-muted">
              {t("deadline")}: {scholarship.deadline}
            </p>
          ) : null}

          {scholarship.eligibility?.length ? (
            <div className="mt-6">
              <h2 className="font-semibold text-win-ink">{t("eligibility")}</h2>
              <ul className="mt-2 list-disc space-y-1 pl-5 text-sm text-win-muted">
                {scholarship.eligibility.map((item) => (
                  <li key={item}>{item}</li>
                ))}
              </ul>
            </div>
          ) : null}

          <p className="mt-6 text-win-muted whitespace-pre-line">
            {localized(scholarship, locale, "how_to_apply")}
          </p>

          <div className="mt-6 flex flex-wrap gap-3">
            <Link href="/contact" className="btn-primary">
              {t("consult")}
            </Link>
            <Link href="/apply" className="rounded-xl border border-black/10 px-5 py-3 text-sm font-semibold">
              {t("applyNow")}
            </Link>
          </div>
        </div>
        <CatalogCta />
      </div>
    </section>
  );
}
