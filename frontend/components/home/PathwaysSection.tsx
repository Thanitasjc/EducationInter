import { GraduationCap } from "lucide-react";
import { Link } from "@/i18n/navigation";
import { mediaUrl } from "@/lib/media";
import { localized } from "@/lib/utils";

export type HomeSectionItem = {
  number?: number;
  text_th?: string;
  text_en?: string;
  note_th?: string | null;
  note_en?: string | null;
};

export type HomeSectionData = {
  id?: number;
  key: string;
  layout?: string;
  title_th?: string | null;
  title_en?: string | null;
  subtitle_th?: string | null;
  subtitle_en?: string | null;
  cover_path?: string | null;
  cover_url?: string | null;
  items?: HomeSectionItem[] | null;
  cta_label_th?: string | null;
  cta_label_en?: string | null;
  cta_url?: string | null;
};

type Props = {
  section: HomeSectionData;
  locale: string;
};

export function PathwaysSection({ section, locale }: Props) {
  const title = localized(section, locale, "title");
  const cover =
    section.cover_url ||
    mediaUrl(section.cover_path) ||
    "/images/bachelor-pathways.png";
  const items = section.items ?? [];
  const ctaLabel = localized(section, locale, "cta_label");
  const ctaUrl = section.cta_url || "/contact";

  return (
    <section className="section bg-white">
      <div className="mx-auto max-w-7xl">
        <div className="overflow-hidden rounded-3xl border border-black/5 bg-white shadow-[0_16px_40px_rgba(18,24,38,0.08)]">
          <div className="grid items-stretch lg:grid-cols-2">
            <div className="flex flex-col justify-center px-6 py-10 md:px-10 md:py-12">
              <div className="mb-5 flex items-center gap-2 text-win-ink">
                <span className="flex h-9 w-9 items-center justify-center rounded-lg bg-win-purple/10 text-win-purple">
                  <GraduationCap className="h-5 w-5" />
                </span>
                <span className="text-sm font-semibold tracking-wide">Education Interntions</span>
              </div>

              <h2 className="max-w-xl text-2xl font-bold leading-snug text-win-purple md:text-3xl">
                {title}
              </h2>

              <ol className="mt-8 space-y-4">
                {items.map((item, index) => {
                  const text =
                    locale === "th"
                      ? item.text_th || item.text_en || ""
                      : item.text_en || item.text_th || "";
                  const note =
                    locale === "th"
                      ? item.note_th || ""
                      : item.note_en || item.note_th || "";
                  return (
                    <li key={index} className="flex gap-3">
                      <span className="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-[4px] bg-win-purple text-sm font-bold text-white">
                        {item.number ?? index + 1}
                      </span>
                      <div>
                        <p className="text-sm leading-relaxed text-win-ink md:text-base">{text}</p>
                        {note ? (
                          <p className="mt-1 text-xs text-win-muted md:text-sm">*{note}</p>
                        ) : null}
                      </div>
                    </li>
                  );
                })}
              </ol>

              {ctaLabel ? (
                <div className="mt-8">
                  <Link href={ctaUrl.startsWith("/") ? ctaUrl : "/contact"} className="btn-primary">
                    {ctaLabel}
                  </Link>
                </div>
              ) : null}
            </div>

            <div className="relative min-h-[280px] bg-win-sky/40 lg:min-h-full">
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img
                src={cover}
                alt={title}
                className="absolute inset-0 h-full w-full object-cover object-center"
              />
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}
