import { getTranslations, setRequestLocale } from "next-intl/server";
import { notFound } from "next/navigation";
import { CatalogCta } from "@/components/catalog/CatalogCta";
import { Link } from "@/i18n/navigation";
import { getEvent } from "@/lib/api";
import { JsonLd } from "@/components/seo/JsonLd";
import { buildMetadata } from "@/lib/seo";
import { localized } from "@/lib/utils";

type Props = { params: Promise<{ locale: string; slug: string }> };

export async function generateMetadata({ params }: Props) {
  const { locale, slug } = await params;
  const event = await getEvent(slug);
  if (!event) return {};
  return buildMetadata({
    locale,
    path: `/events/${slug}`,
    title: `${localized(event, locale, "title")} | Education Interntions`,
    description: localized(event, locale, "summary"),
    image: event.cover_path,
  });
}

export default async function EventDetailPage({ params }: Props) {
  const { locale, slug } = await params;
  setRequestLocale(locale);

  const event = await getEvent(slug);
  if (!event) notFound();

  const t = await getTranslations("events");
  const title = localized(event, locale, "title");

  return (
    <section className="section">
      <JsonLd
        data={{
          "@context": "https://schema.org",
          "@type": "Event",
          name: title,
          description: localized(event, locale, "summary"),
          startDate: event.starts_at,
          endDate: event.ends_at,
          location: event.location
            ? { "@type": "Place", name: event.location }
            : undefined,
          image: event.cover_path || undefined,
        }}
      />
      <div className="mx-auto max-w-4xl">
        <Link href="/events" className="text-sm font-semibold text-win-purple">
          ← {t("back")}
        </Link>
        <h1 className="mt-4 text-3xl font-bold text-win-ink md:text-4xl">{title}</h1>
        <p className="mt-3 text-win-muted">
          {event.starts_at
            ? new Date(event.starts_at).toLocaleString(locale === "th" ? "th-TH" : "en-GB", {
                dateStyle: "full",
                timeStyle: "short",
              })
            : t("tba")}
          {event.location ? ` · ${event.location}` : ""}
        </p>
        {event.cover_path ? (
          // eslint-disable-next-line @next/next/no-img-element
          <img
            src={event.cover_path}
            alt={title}
            className="mt-6 aspect-[16/9] w-full rounded-2xl object-cover"
          />
        ) : null}
        <p className="mt-6 whitespace-pre-line text-win-ink/90">
          {localized(event, locale, "content") || localized(event, locale, "summary")}
        </p>
        <div className="mt-8">
          <Link href="/contact" className="btn-primary">
            {t("register")}
          </Link>
        </div>
        <CatalogCta />
      </div>
    </section>
  );
}
