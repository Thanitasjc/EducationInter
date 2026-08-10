import { getTranslations, setRequestLocale } from "next-intl/server";
import { notFound } from "next/navigation";
import { CatalogCta } from "@/components/catalog/CatalogCta";
import { JsonLd } from "@/components/seo/JsonLd";
import { Link } from "@/i18n/navigation";
import { getProgram } from "@/lib/api";
import { coverFor } from "@/lib/media";
import { buildMetadata } from "@/lib/seo";
import { localized } from "@/lib/utils";

type Props = { params: Promise<{ locale: string; slug: string }> };

export async function generateMetadata({ params }: Props) {
  const { locale, slug } = await params;
  const program = await getProgram(slug);
  if (!program) return {};
  return buildMetadata({
    locale,
    path: `/learn-language/${slug}`,
    title: `${localized(program, locale, "title")} | Education Interntions`,
    description: localized(program, locale, "summary"),
    image: program.cover_url || program.cover_path || undefined,
  });
}

export default async function ProgramDetailPage({ params }: Props) {
  const { locale, slug } = await params;
  setRequestLocale(locale);

  const program = await getProgram(slug);
  if (!program) notFound();

  const t = await getTranslations("learnLanguage");
  const title = localized(program, locale, "title");
  const cover = coverFor(program.slug, program.cover_url, program.cover_path);
  const ctaLabel =
    localized(program, locale, "cta_label") || t("consult");
  const ctaHref = program.cta_url || "/contact";

  return (
    <section className="section">
      <JsonLd
        data={{
          "@context": "https://schema.org",
          "@type": "Course",
          name: title,
          description: localized(program, locale, "summary"),
          provider: { "@type": "Organization", name: "Education Interntions" },
          image: cover,
        }}
      />
      <div className="mx-auto max-w-4xl">
        <Link href="/learn-language" className="text-sm font-semibold text-win-purple">
          ← {t("back")}
        </Link>
        <h1 className="mt-4 text-3xl font-bold text-win-ink md:text-4xl">{title}</h1>
        <p className="mt-3 text-win-muted">
          {[program.age_label, localized(program, locale, "duration_label"), program.language]
            .filter(Boolean)
            .join(" · ")}
        </p>
        {/* eslint-disable-next-line @next/next/no-img-element */}
        <img
          src={cover}
          alt={title}
          className="mt-6 aspect-[16/9] w-full rounded-2xl object-cover"
        />
        <p className="mt-6 whitespace-pre-line text-win-ink/90">
          {localized(program, locale, "content") || localized(program, locale, "summary")}
        </p>
        {program.destinations && program.destinations.length > 0 ? (
          <p className="mt-4 text-sm text-win-muted">
            {t("destinations")}:{" "}
            {program.destinations.map((d) => d.toUpperCase()).join(", ")}
          </p>
        ) : null}
        <div className="mt-8">
          <Link href={ctaHref} className="btn-primary">
            {ctaLabel}
          </Link>
        </div>
        <CatalogCta />
      </div>
    </section>
  );
}
