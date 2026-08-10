import { getTranslations, setRequestLocale } from "next-intl/server";
import { CatalogCta } from "@/components/catalog/CatalogCta";
import { Link } from "@/i18n/navigation";
import { getEvents } from "@/lib/api";
import { buildMetadata } from "@/lib/seo";
import { localized } from "@/lib/utils";

type Props = { params: Promise<{ locale: string }> };

export async function generateMetadata({ params }: Props) {
  const { locale } = await params;
  const t = await getTranslations({ locale, namespace: "events" });
  return buildMetadata({
    locale,
    path: "/events",
    title: `${t("title")} | Education Interntions`,
    description: t("subtitle"),
  });
}

export default async function EventsPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  const t = await getTranslations("events");
  const events = await getEvents({ upcoming: "1" });

  return (
    <section className="section">
      <div className="mx-auto max-w-7xl">
        <h1 className="section-title">{t("title")}</h1>
        <p className="section-subtitle">{t("subtitle")}</p>
        <div className="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          {events.data.map((event) => (
            <Link key={event.id} href={`/events/${event.slug}`} className="card-soft block overflow-hidden p-0">
              {event.cover_path ? (
                // eslint-disable-next-line @next/next/no-img-element
                <img src={event.cover_path} alt="" className="h-40 w-full object-cover" />
              ) : null}
              <div className="p-5">
                <p className="text-xs font-semibold uppercase text-win-purple">
                  {event.starts_at
                    ? new Date(event.starts_at).toLocaleString(locale === "th" ? "th-TH" : "en-GB", {
                        dateStyle: "medium",
                        timeStyle: "short",
                      })
                    : t("tba")}
                </p>
                <h2 className="mt-2 text-lg font-bold">{localized(event, locale, "title")}</h2>
                <p className="mt-2 line-clamp-3 text-sm text-win-muted">
                  {localized(event, locale, "summary")}
                </p>
                {event.location ? (
                  <p className="mt-3 text-xs text-win-muted">{event.location}</p>
                ) : null}
              </div>
            </Link>
          ))}
        </div>
        {events.data.length === 0 && (
          <div className="card-soft mt-6 text-win-muted">{t("empty")}</div>
        )}
        <CatalogCta />
      </div>
    </section>
  );
}
