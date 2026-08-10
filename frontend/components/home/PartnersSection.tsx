"use client";

import { useEffect, useMemo, useRef, useState } from "react";
import { Link } from "@/i18n/navigation";
import { mediaUrl } from "@/lib/media";

export type PartnerItem = {
  id?: number | string;
  name?: string;
  logo_path?: string | null;
  url?: string | null;
  sort_order?: number;
};

type Props = {
  partners: PartnerItem[];
  brand: string;
  title: string;
  seeMore: string;
  seeMoreHref?: string;
};

const FALLBACK_IMAGES = [
  "https://win-ed.com/wp-content/uploads/2026/07/Button_77_11zon-1024x522.webp",
  "https://win-ed.com/wp-content/uploads/2026/07/Button-1_76_11zon-1024x522.webp",
  "https://win-ed.com/wp-content/uploads/2026/07/Button-2_75_11zon-1024x522.webp",
  "https://win-ed.com/wp-content/uploads/2026/07/Button-3_74_11zon-1024x522.webp",
  "https://win-ed.com/wp-content/uploads/2026/07/Button-4_73_11zon-1024x522.webp",
  "https://win-ed.com/wp-content/uploads/2026/07/Button-5_72_11zon-1024x522.webp",
  "https://win-ed.com/wp-content/uploads/2026/07/Button-6_71_11zon-1024x522.webp",
  "https://win-ed.com/wp-content/uploads/2026/07/Button-7_70_11zon-1024x522.webp",
  "https://win-ed.com/wp-content/uploads/2026/07/Button-8_69_11zon-1024x522.webp",
  "https://win-ed.com/wp-content/uploads/2026/07/Button-9_68_11zon-1024x522.webp",
  "https://win-ed.com/wp-content/uploads/2026/07/Button-10_67_11zon-1024x522.webp",
  "https://win-ed.com/wp-content/uploads/2026/07/Button-11_66_11zon-1024x522.webp",
  "https://win-ed.com/wp-content/uploads/2026/07/Button-12_65_11zon-1024x522.webp",
  "https://win-ed.com/wp-content/uploads/2026/07/Button-13_64_11zon-1024x522.webp",
  "https://win-ed.com/wp-content/uploads/2026/07/Button-14_63_11zon-1024x522.webp",
  "https://win-ed.com/wp-content/uploads/2026/07/Button-15_62_11zon-1024x522.webp",
  "https://win-ed.com/wp-content/uploads/2026/07/Button-16_61_11zon-1024x522.webp",
  "https://win-ed.com/wp-content/uploads/2026/07/Button-17_60_11zon-1024x522.webp",
  "https://win-ed.com/wp-content/uploads/2026/07/Button-18_59_11zon-1024x522.webp",
  "https://win-ed.com/wp-content/uploads/2026/07/Button-19_58_11zon-1024x522.webp",
  "https://win-ed.com/wp-content/uploads/2026/07/Button-20_57_11zon-1024x522.webp",
];

function chunkRows<T>(items: T[], rowCount: number): T[][] {
  if (items.length === 0) return [];
  const size = Math.ceil(items.length / rowCount);
  const rows: T[][] = [];
  for (let i = 0; i < rowCount; i += 1) {
    const slice = items.slice(i * size, (i + 1) * size);
    if (slice.length) rows.push(slice);
  }
  return rows;
}

function PartnerMark() {
  return (
    <svg
      xmlns="http://www.w3.org/2000/svg"
      width="24"
      height="24"
      viewBox="0 0 24 24"
      fill="none"
      aria-hidden
    >
      <path
        d="M23.8341 8.50013L11.9991 0.807129L0.164062 8.50013L11.9991 16.1921L19.9991 10.9921V16.0001H21.9991V9.69313L23.8341 8.50013Z"
        fill="#473E91"
      />
      <path
        d="M5 17.5V13.835L12 18.385L19 13.835V17.5C19 18.97 17.986 20.115 16.747 20.838C15.483 21.576 13.802 22 12 22C10.198 22 8.518 21.576 7.253 20.838C6.014 20.115 5 18.97 5 17.5Z"
        fill="#473E91"
      />
    </svg>
  );
}

export function PartnersSection({
  partners,
  brand,
  title,
  seeMore,
  seeMoreHref = "/universities",
}: Props) {
  const sectionRef = useRef<HTMLElement>(null);
  const [offset, setOffset] = useState(0);

  const items = useMemo(() => {
    const fromApi = partners
      .map((partner, index) => {
        const src = mediaUrl(partner.logo_path) || partner.logo_path;
        if (!src) return null;
        return {
          id: String(partner.id ?? index),
          name: partner.name || `Partner ${index + 1}`,
          src,
          href: partner.url || seeMoreHref,
        };
      })
      .filter((item): item is { id: string; name: string; src: string; href: string } => !!item);

    if (fromApi.length > 0) return fromApi;

    return FALLBACK_IMAGES.map((src, index) => ({
      id: `fallback-${index}`,
      name: `Partner ${index + 1}`,
      src,
      href: seeMoreHref,
    }));
  }, [partners, seeMoreHref]);

  const rows = useMemo(() => chunkRows(items, 3), [items]);

  useEffect(() => {
    const onScroll = () => {
      const el = sectionRef.current;
      if (!el) return;
      const rect = el.getBoundingClientRect();
      const viewport = window.innerHeight || 1;
      const progress = 1 - Math.min(Math.max((rect.top + rect.height * 0.35) / viewport, 0), 1);
      setOffset(progress * 72);
    };

    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });
    window.addEventListener("resize", onScroll);
    return () => {
      window.removeEventListener("scroll", onScroll);
      window.removeEventListener("resize", onScroll);
    };
  }, []);

  return (
    <section ref={sectionRef} className="overflow-hidden bg-[#f4f5f8] py-14 md:py-20">
      <div className="mx-auto max-w-7xl px-4 md:px-8">
        <div className="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
          <div>
            <div className="flex items-center gap-3">
              <PartnerMark />
              <p className="text-sm font-semibold tracking-wide text-win-purple">{brand}</p>
            </div>
            <h2 className="mt-3 text-3xl font-bold tracking-tight text-win-ink md:text-4xl">
              {title}
            </h2>
          </div>
          <Link
            href={seeMoreHref}
            className="hidden items-center justify-center rounded-lg bg-win-purple px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-win-purple-deep md:inline-flex"
          >
            {seeMore}
          </Link>
        </div>
      </div>

      {/* Desktop / tablet parallax rows */}
      <div className="mt-10 hidden space-y-4 md:block">
        {rows.map((row, rowIndex) => {
          const direction = rowIndex % 2 === 0 ? 1 : -1;
          const translate = offset * direction;
          return (
            <div
              key={`row-${rowIndex}`}
              className="flex w-max gap-4 px-4 transition-transform duration-100 ease-out will-change-transform md:px-8"
              style={{ transform: `translateX(${translate}px)` }}
            >
              {[...row, ...row].map((item, index) => {
                const cardClass =
                  "block w-[220px] shrink-0 overflow-hidden rounded-xl bg-white shadow-[0_10px_28px_rgba(18,24,38,0.08)] transition hover:-translate-y-0.5 hover:shadow-[0_14px_32px_rgba(18,24,38,0.12)] lg:w-[260px]";
                const image = (
                  // eslint-disable-next-line @next/next/no-img-element
                  <img
                    src={item.src}
                    alt={item.name}
                    className="aspect-[800/408] w-full object-cover"
                    loading="lazy"
                  />
                );
                const isExternal = item.href.startsWith("http");
                if (isExternal) {
                  return (
                    <a
                      key={`${item.id}-${index}`}
                      href={item.href}
                      target="_blank"
                      rel="noreferrer"
                      className={cardClass}
                    >
                      {image}
                    </a>
                  );
                }
                return (
                  <Link
                    key={`${item.id}-${index}`}
                    href={item.href}
                    className={cardClass}
                  >
                    {image}
                  </Link>
                );
              })}
            </div>
          );
        })}
      </div>

      {/* Mobile grid */}
      <div className="mt-8 grid grid-cols-2 gap-3 px-4 md:hidden">
        {items.slice(0, 8).map((item) => {
          const isExternal = item.href.startsWith("http");
          const image = (
            // eslint-disable-next-line @next/next/no-img-element
            <img
              src={item.src}
              alt={item.name}
              className="aspect-[800/408] w-full object-cover"
              loading="lazy"
            />
          );
          if (isExternal) {
            return (
              <a
                key={item.id}
                href={item.href}
                target="_blank"
                rel="noreferrer"
                className="overflow-hidden rounded-xl bg-white shadow-sm"
              >
                {image}
              </a>
            );
          }
          return (
            <Link
              key={item.id}
              href={item.href}
              className="overflow-hidden rounded-xl bg-white shadow-sm"
            >
              {image}
            </Link>
          );
        })}
      </div>

      <div className="mt-8 px-4 md:hidden">
        <Link
          href={seeMoreHref}
          className="inline-flex w-full items-center justify-center rounded-lg bg-win-purple px-5 py-3 text-sm font-semibold text-white"
        >
          {seeMore}
        </Link>
      </div>
    </section>
  );
}
