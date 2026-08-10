"use client";

import { useCallback, useEffect, useState } from "react";
import { ChevronLeft, ChevronRight } from "lucide-react";
import { Link } from "@/i18n/navigation";

export type HeroSlideView = {
  image: string;
  headline: string;
  subheadline: string;
  link?: string;
};

type Props = {
  brand: string;
  slides: HeroSlideView[];
  ctaPrimary: string;
  ctaSecondary: string;
  ctaPrimaryHref: string;
  ctaSecondaryHref: string;
  intervalMs?: number;
};

export function HeroSlider({
  brand,
  slides,
  ctaPrimary,
  ctaSecondary,
  ctaPrimaryHref,
  ctaSecondaryHref,
  intervalMs = 5500,
}: Props) {
  const [index, setIndex] = useState(0);
  const count = slides.length;
  const safeIndex = count > 0 ? index % count : 0;
  const current = slides[safeIndex];

  const go = useCallback(
    (next: number) => {
      if (count === 0) return;
      setIndex(((next % count) + count) % count);
    },
    [count],
  );

  useEffect(() => {
    if (count <= 1) return;
    const id = window.setInterval(() => {
      setIndex((prev) => (prev + 1) % count);
    }, Math.max(2500, intervalMs));
    return () => window.clearInterval(id);
  }, [count, intervalMs]);

  if (!current) {
    return null;
  }

  return (
    <section className="relative min-h-[78vh] overflow-hidden text-white md:min-h-[88vh]">
      {slides.map((slide, i) => (
        <div
          key={`${slide.image}-${i}`}
          aria-hidden={i !== safeIndex}
          className={[
            "absolute inset-0 transition-opacity duration-700 ease-out",
            i === safeIndex ? "opacity-100" : "opacity-0",
          ].join(" ")}
        >
          {/* eslint-disable-next-line @next/next/no-img-element */}
          <img
            src={slide.image}
            alt=""
            className={[
              "h-full w-full object-cover transition-transform duration-[6500ms] ease-out",
              i === safeIndex ? "scale-105" : "scale-100",
            ].join(" ")}
          />
          <div className="absolute inset-0 bg-gradient-to-r from-win-purple-deep/90 via-win-purple/70 to-win-blue/45" />
          <div className="absolute inset-0 bg-gradient-to-t from-black/55 via-transparent to-black/25" />
        </div>
      ))}

      <div className="relative mx-auto flex min-h-[78vh] max-w-7xl flex-col justify-end px-4 pb-16 pt-28 md:min-h-[88vh] md:px-8 md:pb-24 md:pt-36">
        <p className="text-sm font-semibold uppercase tracking-[0.2em] text-white/75">
          {brand}
        </p>
        <h1
          key={`h-${safeIndex}`}
          className="mt-4 max-w-3xl text-4xl font-bold leading-tight transition-opacity duration-500 md:text-5xl lg:text-6xl"
        >
          {current.headline}
        </h1>
        <p
          key={`s-${safeIndex}`}
          className="mt-4 max-w-2xl text-base text-white/90 transition-opacity duration-500 md:text-lg"
        >
          {current.subheadline}
        </p>
        <div className="mt-8 flex flex-wrap gap-3">
          <Link
            href={ctaPrimaryHref}
            className="btn-primary bg-white text-win-purple hover:bg-win-sky"
          >
            {ctaPrimary}
          </Link>
          <Link href={current.link || ctaSecondaryHref} className="btn-secondary">
            {ctaSecondary}
          </Link>
        </div>

        {count > 1 ? (
          <div className="mt-10 flex items-center gap-4">
            <button
              type="button"
              aria-label="Previous slide"
              onClick={() => go(safeIndex - 1)}
              className="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/35 bg-black/20 backdrop-blur transition hover:bg-white/15"
            >
              <ChevronLeft className="h-5 w-5" />
            </button>
            <div className="flex items-center gap-2">
              {slides.map((_, i) => (
                <button
                  key={i}
                  type="button"
                  aria-label={`Go to slide ${i + 1}`}
                  aria-current={i === safeIndex}
                  onClick={() => go(i)}
                  className={[
                    "h-2.5 rounded-full transition-all",
                    i === safeIndex
                      ? "w-8 bg-white"
                      : "w-2.5 bg-white/45 hover:bg-white/75",
                  ].join(" ")}
                />
              ))}
            </div>
            <button
              type="button"
              aria-label="Next slide"
              onClick={() => go(safeIndex + 1)}
              className="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/35 bg-black/20 backdrop-blur transition hover:bg-white/15"
            >
              <ChevronRight className="h-5 w-5" />
            </button>
          </div>
        ) : null}
      </div>
    </section>
  );
}
