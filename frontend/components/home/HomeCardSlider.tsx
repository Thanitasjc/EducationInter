"use client";

import { ChevronLeft, ChevronRight } from "lucide-react";
import {
  Children,
  useCallback,
  useEffect,
  useRef,
  useState,
  type ReactNode,
} from "react";

type Props = {
  children: ReactNode;
  className?: string;
  itemClassName?: string;
};

export function HomeCardSlider({
  children,
  className = "",
  itemClassName = "w-[min(100%,300px)] sm:w-[min(100%,320px)] md:w-[calc((100%-1.25rem)/2)] lg:w-[calc((100%-2.5rem)/3)]",
}: Props) {
  const scrollerRef = useRef<HTMLDivElement>(null);
  const [page, setPage] = useState(0);
  const [pageCount, setPageCount] = useState(1);
  const items = Children.toArray(children);

  const updatePager = useCallback(() => {
    const el = scrollerRef.current;
    if (!el) return;
    const maxScroll = el.scrollWidth - el.clientWidth;
    const pages = Math.max(
      1,
      Math.ceil(maxScroll / Math.max(el.clientWidth * 0.85, 1)) + (maxScroll > 0 ? 1 : 0),
    );
    setPageCount(pages);
    const ratio = maxScroll <= 0 ? 0 : el.scrollLeft / maxScroll;
    setPage(Math.round(ratio * Math.max(pages - 1, 1)));
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
  }, [items.length, updatePager]);

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
    <div className={["relative", className].filter(Boolean).join(" ")}>
      <button
        type="button"
        aria-label="Previous"
        onClick={() => scrollByPage(-1)}
        className="absolute -left-2 top-[42%] z-10 hidden h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-black/5 bg-white text-win-muted shadow-md transition hover:text-win-purple md:flex lg:-left-5"
      >
        <ChevronLeft className="h-5 w-5" />
      </button>
      <button
        type="button"
        aria-label="Next"
        onClick={() => scrollByPage(1)}
        className="absolute -right-2 top-[42%] z-10 hidden h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full border border-black/5 bg-white text-win-muted shadow-md transition hover:text-win-purple md:flex lg:-right-5"
      >
        <ChevronRight className="h-5 w-5" />
      </button>

      <div
        ref={scrollerRef}
        className="flex snap-x snap-mandatory gap-5 overflow-x-auto pb-2 [-ms-overflow-style:none] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
      >
        {items.map((child, index) => (
          <div
            key={index}
            className={["shrink-0 snap-start", itemClassName].join(" ")}
          >
            {child}
          </div>
        ))}
      </div>

      {pageCount > 1 ? (
        <div className="mt-6 flex justify-center gap-2">
          {Array.from({ length: pageCount }).map((_, index) => (
            <button
              key={index}
              type="button"
              aria-label={`Go to page ${index + 1}`}
              aria-current={index === page}
              onClick={() => goToPage(index)}
              className={[
                "h-2.5 rounded-full transition-all",
                index === page
                  ? "w-7 bg-win-purple"
                  : "w-2.5 bg-win-purple/25 hover:bg-win-purple/50",
              ].join(" ")}
            />
          ))}
        </div>
      ) : null}
    </div>
  );
}
