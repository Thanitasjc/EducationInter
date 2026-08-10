"use client";

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
          <Link href="/login" className="btn-primary !py-2 !text-xs">
            {t("login")}
          </Link>
        </div>
      </div>
    </header>
  );
}
