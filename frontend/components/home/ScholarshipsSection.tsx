"use client";

import { Check, ChevronLeft, ChevronRight } from "lucide-react";
import { useCallback, useEffect, useMemo, useRef, useState, type ReactNode } from "react";
import { Link } from "@/i18n/navigation";
import type { Scholarship } from "@/types/catalog";
import { cn, localized } from "@/lib/utils";

type Props = {
  scholarships: Scholarship[];
  locale: string;
  title: string;
  subtitle: ReactNode;
  seeMore: string;
  applyScholarship: string;
  amountTbd: string;
};

type UniversityGroup = {
  key: string;
  name: string;
  tagline: string;
  href: string;
  cover: string;
  logo: string | null;
  logoLabel: string;
  items: Scholarship[];
};

const FALLBACK_COVERS: Record<string, string> = {
  "university-of-manchester":
    "https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1200&q=80",
  "university-college-london":
    "https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1200&q=80",
  "university-of-melbourne":
    "https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1200&q=80",
  "university-of-toronto":
    "https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1200&q=80",
};

const DEFAULT_COVER =
  "https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1200&q=80";

function mediaUrl(path?: string | null) {
  if (!path) return null;
  if (path.startsWith("http://") || path.startsWith("https://")) return path;
  const base = process.env.NEXT_PUBLIC_API_URL?.replace(/\/api\/?$/, "") ?? "";
  return `${base}/storage/${path.replace(/^\/+/, "")}`;
}

function groupScholarships(scholarships: Scholarship[], locale: string): UniversityGroup[] {
  const groups = new Map<string, UniversityGroup>();

  for (const item of scholarships) {
    const uni = item.university;
    const key = uni ? `uni-${uni.id}` : `sch-${item.id}`;
    const existing = groups.get(key);

    if (existing) {
      existing.items.push(item);
      continue;
    }

    const slug = uni?.slug ?? item.slug;
    const cover =
      mediaUrl(item.cover_path) ||
      mediaUrl(uni?.cover_path) ||
      FALLBACK_COVERS[slug] ||
      DEFAULT_COVER;
    const logo = mediaUrl(item.logo_path) || mediaUrl(uni?.logo_path);
    // Match brand mock: university title stays English; tagline follows locale.
    const name = uni
      ? String(uni.name_en || localized(uni, locale, "name"))
      : localized(item, locale, "title");
    const tagline = uni
      ? localized(uni, locale, "about") ||
        (locale === "th"
          ? "มหาวิทยาลัยพันธมิตรของ Education Interntions"
          : "Education Interntions partner university")
      : locale === "th"
        ? "ทุนการศึกษาจากมหาวิทยาลัยชั้นนำ"
        : "Scholarship from a leading university";

    groups.set(key, {
      key,
      name,
      tagline,
      href: uni ? `/universities/${uni.slug}` : `/scholarships/${item.slug}`,
      cover,
      logo,
      logoLabel: name
        .replace(/^The\s+/i, "")
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part[0]?.toUpperCase() ?? "")
        .join(""),
      items: [item],
    });
  }

  return Array.from(groups.values());
}

export function ScholarshipsSection({
  scholarships,
  locale,
  title,
  subtitle,
  seeMore,
  applyScholarship,
  amountTbd,
}: Props) {
  const scrollerRef = useRef<HTMLDivElement>(null);
  const [page, setPage] = useState(0);
  const [pageCount, setPageCount] = useState(1);

  const groups = useMemo(
    () => groupScholarships(scholarships, locale),
    [scholarships, locale],
  );

  const updatePager = useCallback(() => {
    const el = scrollerRef.current;
    if (!el) return;
    const maxScroll = el.scrollWidth - el.clientWidth;
    const pages = Math.max(1, Math.ceil(maxScroll / Math.max(el.clientWidth * 0.85, 1)) + 1);
    setPageCount(pages);
    const ratio = maxScroll <= 0 ? 0 : el.scrollLeft / maxScroll;
    setPage(Math.round(ratio * (pages - 1)));
  }, []);

  useEffect(() => {
    const el = scrollerRef.current;
    if (!el) return;
    updatePager();
    el.addEventListener("scroll", updatePager, { passive: true });
    window.addEventListener("resize", updatePager);
    return () => {
      el.removeEventListener("scroll", updatePager);
      window.removeEventListener("resize", updatePager);
    };
  }, [groups.length, updatePager]);

  const scrollByPage = (dir: -1 | 1) => {
    const el = scrollerRef.current;
    if (!el) return;
    el.scrollBy({ left: dir * el.clientWidth * 0.9, behavior: "smooth" });
  };

  const goToPage = (index: number) => {
    const el = scrollerRef.current;
    if (!el) return;
    const maxScroll = el.scrollWidth - el.clientWidth;
    const left = pageCount <= 1 ? 0 : (index / (pageCount - 1)) * maxScroll;
    el.scrollTo({ left, behavior: "smooth" });
  };

  return (
    <section className="section bg-white">
      <div className="mx-auto max-w-7xl">
        <div className="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
          <div className="max-w-3xl">
            <h2 className="text-3xl font-bold tracking-tight text-win-purple md:text-4xl">
              {title}
            </h2>
            <p className="mt-3 text-sm leading-relaxed text-win-muted md:text-base">
              {subtitle}
            </p>
          </div>
          <Link
            href="/scholarships"
            className="inline-flex shrink-0 items-center justify-center rounded-lg bg-win-purple px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-win-purple-deep"
          >
            {seeMore}
          </Link>
        </div>

        {groups.length > 0 ? (
          <div className="relative mt-10">
            <button
              type="button"
              aria-label="Previous"
              onClick={() => scrollByPage(-1)}
              className="absolute -left-2 top-1/2 z-10 hidden h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-black/5 bg-white text-win-muted shadow-md transition hover:text-win-purple md:flex lg:-left-5"
            >
              <ChevronLeft className="h-5 w-5" />
            </button>
            <button
              type="button"
              aria-label="Next"
              onClick={() => scrollByPage(1)}
              className="absolute -right-2 top-1/2 z-10 hidden h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-black/5 bg-white text-win-muted shadow-md transition hover:text-win-purple md:flex lg:-right-5"
            >
              <ChevronRight className="h-5 w-5" />
            </button>

            <div
              ref={scrollerRef}
              className="flex snap-x snap-mandatory gap-5 overflow-x-auto pb-2 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
            >
              {groups.map((group) => (
                <article
                  key={group.key}
                  className="flex w-[min(100%,320px)] shrink-0 snap-start flex-col overflow-hidden rounded-xl bg-white shadow-[0_12px_32px_rgba(18,24,38,0.1)] sm:w-[min(100%,340px)] md:w-[calc((100%-1.25rem)/2)] lg:w-[calc((100%-2.5rem)/3)]"
                >
                  <div className="relative aspect-[5/3] overflow-hidden bg-win-sky">
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img
                      src={group.cover}
                      alt={group.name}
                      className="h-full w-full object-cover"
                    />
                    <div className="absolute right-3 top-3 flex h-12 w-12 items-center justify-center rounded bg-white p-1 shadow-md sm:h-14 sm:w-14">
                      {group.logo ? (
                        // eslint-disable-next-line @next/next/no-img-element
                        <img
                          src={group.logo}
                          alt=""
                          className="h-full w-full object-contain"
                        />
                      ) : (
                        <span className="text-[11px] font-bold tracking-wide text-win-purple">
                          {group.logoLabel}
                        </span>
                      )}
                    </div>
                  </div>

                  <div className="flex flex-1 flex-col px-4 pb-4 pt-4 sm:px-5">
                    <Link href={group.href} className="block">
                      <h3 className="text-base font-bold text-win-purple sm:text-lg">
                        {group.name}
                      </h3>
                      <p className="mt-1 line-clamp-2 text-sm text-win-muted">{group.tagline}</p>
                    </Link>

                    <ul className="mt-4 space-y-2">
                      {group.items.slice(0, 2).map((item) => (
                        <li
                          key={item.id}
                          className="flex items-center gap-2.5 rounded bg-[#f0f1f3] px-3 py-2.5"
                        >
                          <span className="flex h-5 w-5 shrink-0 items-center justify-center rounded-[3px] bg-win-purple text-white">
                            <Check className="h-3.5 w-3.5" strokeWidth={3} />
                          </span>
                          <div className="min-w-0 leading-snug">
                            <p className="text-sm font-semibold text-[#2f6fed]">
                              {localized(item, locale, "title")}
                            </p>
                            <p className="text-sm text-win-muted">
                              {localized(item, locale, "amount_label") || amountTbd}
                            </p>
                          </div>
                        </li>
                      ))}
                    </ul>

                    <Link
                      href="/apply"
                      className="mt-4 inline-flex w-full items-center justify-center rounded-md bg-win-purple px-4 py-3 text-sm font-semibold text-white transition hover:bg-win-purple-deep"
                    >
                      {applyScholarship}
                    </Link>
                  </div>
                </article>
              ))}
            </div>

            {pageCount > 1 && (
              <div className="mt-6 flex items-center justify-center gap-2">
                {Array.from({ length: pageCount }).map((_, index) => (
                  <button
                    key={index}
                    type="button"
                    aria-label={`Go to page ${index + 1}`}
                    onClick={() => goToPage(index)}
                    className={cn(
                      "h-2.5 w-2.5 rounded-full transition",
                      index === page ? "bg-win-purple" : "bg-win-purple/25",
                    )}
                  />
                ))}
              </div>
            )}
          </div>
        ) : (
          <div className="card-soft mt-8 text-sm text-win-muted">Scholarship cards</div>
        )}
      </div>
    </section>
  );
}
