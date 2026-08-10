"use client";

import { useEffect, useId, useState } from "react";
import { Menu, X } from "lucide-react";
import { useLocale, useTranslations } from "next-intl";
import { Link, usePathname } from "@/i18n/navigation";

const links = [
  { href: "/about", key: "about" },
  { href: "/learn-language", key: "learnLanguage" },
  { href: "/countries", key: "countries" },
  { href: "/universities", key: "universities" },
  { href: "/scholarships", key: "scholarships" },
  { href: "/services", key: "services" },
  { href: "/events", key: "events" },
  { href: "/blog", key: "blog" },
  { href: "/contact", key: "contact" },
] as const;

export function SiteHeader() {
  const t = useTranslations("nav");
  const brand = useTranslations();
  const locale = useLocale();
  const pathname = usePathname();
  const nextLocale = locale === "th" ? "en" : "th";
  const [open, setOpen] = useState(false);
  const menuId = useId();

  useEffect(() => {
    setOpen(false);
  }, [pathname, locale]);

  useEffect(() => {
    if (!open) return;

    const previous = document.body.style.overflow;
    document.body.style.overflow = "hidden";

    const onKeyDown = (event: KeyboardEvent) => {
      if (event.key === "Escape") setOpen(false);
    };

    window.addEventListener("keydown", onKeyDown);
    return () => {
      document.body.style.overflow = previous;
      window.removeEventListener("keydown", onKeyDown);
    };
  }, [open]);

  return (
    <header className="sticky top-0 z-50 border-b border-black/5 bg-white/90 backdrop-blur">
      <div className="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 md:px-8">
        <Link href="/" className="text-lg font-bold tracking-tight text-win-purple">
          {brand("brand")}
        </Link>

        <nav className="hidden items-center gap-5 text-sm font-medium text-win-ink lg:flex">
          {links.map((link) => (
            <Link
              key={link.href}
              href={link.href}
              className="transition hover:text-win-purple"
            >
              {t(link.key)}
            </Link>
          ))}
        </nav>

        <div className="flex items-center gap-2">
          <Link
            href={pathname}
            locale={nextLocale}
            className="rounded-lg border border-black/10 px-3 py-2 text-xs font-semibold uppercase text-win-muted"
          >
            {nextLocale}
          </Link>
          <Link
            href="/login"
            className="btn-primary hidden !py-2 !text-xs sm:inline-flex"
          >
            {t("login")}
          </Link>
          <button
            type="button"
            className="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-black/10 text-win-ink transition hover:border-win-purple/40 hover:text-win-purple lg:hidden"
            aria-expanded={open}
            aria-controls={menuId}
            aria-label={open ? t("closeMenu") : t("openMenu")}
            onClick={() => setOpen((value) => !value)}
          >
            {open ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
          </button>
        </div>
      </div>

      <div
        id={menuId}
        className={`border-t border-black/5 bg-white lg:hidden ${open ? "block" : "hidden"}`}
      >
        <nav className="mx-auto flex max-w-7xl flex-col gap-1 px-4 py-4 md:px-8">
          {links.map((link) => (
            <Link
              key={link.href}
              href={link.href}
              className="rounded-lg px-3 py-3 text-sm font-semibold text-win-ink transition hover:bg-win-sky/60 hover:text-win-purple"
              onClick={() => setOpen(false)}
            >
              {t(link.key)}
            </Link>
          ))}
          <Link
            href="/login"
            className="btn-primary mt-2 justify-center sm:hidden"
            onClick={() => setOpen(false)}
          >
            {t("login")}
          </Link>
        </nav>
      </div>
    </header>
  );
}
