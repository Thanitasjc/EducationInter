"use client";

import { useMemo, useState } from "react";
import {
  Award,
  BookOpen,
  Calendar,
  ChevronDown,
  Globe2,
  Heart,
  MapPin,
  Plane,
  Sparkles,
  Users,
} from "lucide-react";
import { useTranslations } from "next-intl";
import { Link } from "@/i18n/navigation";
import { coverFor } from "@/lib/media";
import { localized } from "@/lib/utils";

type CountryLite = {
  id: number | string;
  slug: string;
  name_th?: string;
  name_en?: string;
  cover_path?: string | null;
};

export type AcademicYearCms = {
  promoBanner?: string;
  whyTitle?: string;
  usps?: Array<{ title: string; body: string }>;
  faqTitle?: string;
  faqBody?: string;
  faqs?: Array<{ question: string; answer: string }>;
};

type Props = {
  locale: string;
  countries: CountryLite[];
  heroImage: string;
  title: string;
  subtitle: string;
  cms?: AcademicYearCms;
};

const LANGUAGES = ["english", "japanese", "korean", "chinese"] as const;
const USP_ICONS = [Sparkles, BookOpen, Calendar, Plane] as const;

export function AcademicYearLanding({
  locale,
  countries,
  heroImage,
  title,
  subtitle,
  cms,
}: Props) {
  const t = useTranslations("academicYear");
  const [language, setLanguage] = useState("");
  const [destination, setDestination] = useState("");
  const [openFaq, setOpenFaq] = useState<number | null>(0);

  const exploreHref = useMemo(() => {
    if (destination) return `/countries/${destination}`;
    if (language) return `/learn-language?age=all`;
    return "/countries";
  }, [destination, language]);

  const uspFallback = [
    { icon: Sparkles, key: "usp1" as const },
    { icon: BookOpen, key: "usp2" as const },
    { icon: Calendar, key: "usp3" as const },
    { icon: Plane, key: "usp4" as const },
  ];

  const terms = [
    { key: "term1" as const, focus: "term1Focus" as const, points: ["term1a", "term1b"] as const },
    { key: "term2" as const, focus: "term2Focus" as const, points: ["term2a", "term2b"] as const },
    { key: "term3" as const, focus: "term3Focus" as const, points: ["term3a", "term3b"] as const },
  ];

  const faqFallback = ["faq1", "faq2", "faq3", "faq4", "faq5", "faq6"] as const;
  const cmsUsps = cms?.usps?.filter((item) => item.title.trim()) ?? [];
  const cmsFaqs = cms?.faqs?.filter((item) => item.question.trim()) ?? [];
  const whyTitle = cms?.whyTitle || t("whyTitle");
  const promoBanner = cms?.promoBanner || t("promoBanner");
  const faqTitle = cms?.faqTitle || t("faqTitle");
  const faqBody = cms?.faqBody || t("faqBody");

  return (
    <div className="pb-24">
      {/* Promo strip */}
      <div className="bg-win-sky/80">
        <Link
          href="/contact"
          className="mx-auto flex max-w-7xl items-center justify-center gap-3 px-4 py-3 text-sm text-win-ink transition hover:text-win-purple md:px-8"
        >
          <span className="inline-flex h-5 w-5 items-center justify-center rounded-full bg-win-purple/15 text-win-purple">
            i
          </span>
          <span>{promoBanner}</span>
          <span aria-hidden>→</span>
        </Link>
      </div>

      {/* Hero */}
      <section className="overflow-hidden bg-white">
        <div className="mx-auto grid max-w-7xl lg:grid-cols-2">
          <div className="order-2 flex flex-col justify-center px-4 py-10 md:px-8 md:py-16 lg:order-1 lg:px-12 lg:py-20">
            <p className="text-sm font-semibold uppercase tracking-[1.5px] text-win-purple">
              {t("eyebrow")}
            </p>
            <h1 className="mt-3 text-3xl font-bold leading-tight text-win-ink md:text-4xl lg:text-5xl">
              {title}
            </h1>
            <p className="mt-4 text-base leading-relaxed text-win-muted md:text-lg">
              {subtitle}
            </p>

            <div className="mt-8 space-y-3">
              <label className="block">
                <span className="mb-1.5 block text-xs font-semibold uppercase text-win-muted">
                  {t("language")}
                </span>
                <select
                  value={language}
                  onChange={(e) => setLanguage(e.target.value)}
                  className="input h-14 rounded-xl"
                >
                  <option value="">{t("selectLanguage")}</option>
                  {LANGUAGES.map((lang) => (
                    <option key={lang} value={lang}>
                      {t(`languages.${lang}`)}
                    </option>
                  ))}
                </select>
              </label>
              <label className="block">
                <span className="mb-1.5 block text-xs font-semibold uppercase text-win-muted">
                  {t("destination")}
                </span>
                <select
                  value={destination}
                  onChange={(e) => setDestination(e.target.value)}
                  className="input h-14 rounded-xl"
                >
                  <option value="">{t("selectDestination")}</option>
                  {countries.map((c) => (
                    <option key={String(c.id)} value={c.slug}>
                      {localized(c, locale, "name")}
                    </option>
                  ))}
                </select>
              </label>
              <Link href={exploreHref} className="btn-primary mt-2 w-full rounded-full px-6 py-3.5">
                {t("explore")}
              </Link>
            </div>
          </div>
          <div className="relative order-1 min-h-[280px] lg:order-2 lg:min-h-[520px]">
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img
              src={heroImage}
              alt={title}
              className="absolute inset-0 h-full w-full object-cover"
            />
          </div>
        </div>
      </section>

      {/* USP */}
      <section className="px-4 py-14 md:px-8 md:py-20">
        <div className="mx-auto max-w-7xl">
          <div className="grid gap-10 lg:grid-cols-3">
            <div>
              <h2 className="text-2xl font-bold text-win-ink md:text-3xl">{whyTitle}</h2>
            </div>
            <div className="lg:col-span-2">
              <ul className="grid gap-6 md:grid-cols-2">
                {cmsUsps.length > 0
                  ? cmsUsps.map((item, index) => {
                      const Icon = USP_ICONS[index % USP_ICONS.length];
                      return (
                        <li key={`${item.title}-${index}`} className="flex gap-4">
                          <span className="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#efefef] text-win-ink">
                            <Icon className="h-5 w-5" />
                          </span>
                          <div>
                            <h3 className="text-lg font-bold text-win-ink">{item.title}</h3>
                            <p className="mt-1 text-sm leading-relaxed text-win-muted">{item.body}</p>
                          </div>
                        </li>
                      );
                    })
                  : uspFallback.map(({ icon: Icon, key }) => (
                      <li key={key} className="flex gap-4">
                        <span className="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#efefef] text-win-ink">
                          <Icon className="h-5 w-5" />
                        </span>
                        <div>
                          <h3 className="text-lg font-bold text-win-ink">{t(`${key}Title`)}</h3>
                          <p className="mt-1 text-sm leading-relaxed text-win-muted">
                            {t(`${key}Body`)}
                          </p>
                        </div>
                      </li>
                    ))}
              </ul>
            </div>
          </div>
        </div>
      </section>

      {/* Promo */}
      <section className="px-4 pb-8 md:px-8">
        <div className="mx-auto max-w-7xl overflow-hidden rounded-2xl border border-black/10 bg-gradient-to-r from-[#FFF0D4] via-[#FFF1F7] to-[#e8f7f3] px-5 py-5 md:flex md:items-center md:justify-between md:px-8">
          <div className="flex items-center gap-3">
            <Award className="h-8 w-8 text-win-purple" />
            <p className="text-lg font-bold text-win-ink">{t("offerTitle")}</p>
          </div>
          <Link
            href="/contact"
            className="mt-4 inline-flex rounded-full border border-win-ink/20 bg-white px-5 py-2.5 text-sm font-semibold text-win-ink transition hover:border-win-purple hover:text-win-purple md:mt-0"
          >
            {t("offerCta")}
          </Link>
        </div>
      </section>

      {/* Destinations */}
      <section id="destinations" className="scroll-mt-24 px-4 py-14 md:px-8 md:py-16">
        <div className="mx-auto max-w-7xl">
          <h2 className="text-2xl font-bold text-win-ink md:text-3xl">{t("whereTitle")}</h2>
          <p className="mt-3 max-w-2xl text-win-muted">{t("whereBody")}</p>
          <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {countries.slice(0, 8).map((country) => {
              const name = localized(country, locale, "name");
              return (
                <Link
                  key={String(country.id)}
                  href={`/countries/${country.slug}`}
                  className="group overflow-hidden rounded-xl border border-black/5 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md"
                >
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img
                    src={coverFor(String(country.slug), country.cover_path)}
                    alt={name}
                    className="aspect-[16/9] w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                  />
                  <p className="p-4 font-semibold text-win-ink">{name}</p>
                </Link>
              );
            })}
          </div>
          <div className="mt-8">
            <Link href="/countries" className="text-sm font-semibold text-win-purple hover:underline">
              {t("viewAllDestinations")} →
            </Link>
          </div>
        </div>
      </section>

      {/* Programs for goals */}
      <section className="bg-[#efefef]/60 px-4 py-14 md:px-8 md:py-20">
        <div className="mx-auto max-w-7xl">
          <h2 className="text-2xl font-bold text-win-ink md:text-3xl">{t("goalsTitle")}</h2>
          <p className="mt-3 max-w-2xl text-win-muted">{t("goalsBody")}</p>
          <div className="mt-6 flex flex-wrap gap-4 text-sm font-semibold text-win-ink">
            <span className="inline-flex items-center gap-2">
              <Calendar className="h-4 w-4" /> {t("goalsMeta1")}
            </span>
            <span className="inline-flex items-center gap-2">
              <BookOpen className="h-4 w-4" /> {t("goalsMeta2")}
            </span>
            <span className="inline-flex items-center gap-2">
              <Globe2 className="h-4 w-4" /> {t("goalsMeta3")}
            </span>
          </div>
          <div className="mt-8 grid gap-4 md:grid-cols-3">
            {(["track1", "track2", "track3"] as const).map((key) => (
              <article
                key={key}
                className="flex h-full flex-col rounded-2xl border border-black/5 bg-white p-6 shadow-sm"
              >
                <h3 className="text-lg font-bold text-win-ink">{t(`${key}Title`)}</h3>
                <p className="mt-2 flex-1 text-sm text-win-muted">{t(`${key}Body`)}</p>
                <Link
                  href="/learn-language"
                  className="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-win-purple"
                >
                  {t("readMore")} →
                </Link>
              </article>
            ))}
          </div>
        </div>
      </section>

      {/* Structured terms */}
      <section className="px-4 py-14 md:px-8 md:py-20">
        <div className="mx-auto max-w-7xl">
          <h2 className="text-2xl font-bold text-win-ink md:text-3xl">{t("termsTitle")}</h2>
          <p className="mt-3 max-w-2xl text-win-muted">{t("termsBody")}</p>
          <div className="mt-10 space-y-8">
            {terms.map((term, index) => (
              <div
                key={term.key}
                className="grid gap-6 rounded-2xl border border-black/8 bg-white p-6 md:grid-cols-[140px_1fr] md:p-8"
              >
                <div>
                  <p className="text-sm font-semibold uppercase tracking-wide text-win-purple">
                    {t("termLabel", { n: index + 1 })}
                  </p>
                  <h3 className="mt-2 text-xl font-bold text-win-ink">{t(term.key)}</h3>
                  <p className="mt-1 text-sm text-win-muted">{t(term.focus)}</p>
                </div>
                <ul className="space-y-3">
                  {term.points.map((point) => (
                    <li key={point} className="flex gap-3 text-sm leading-relaxed text-win-ink/90">
                      <Heart className="mt-0.5 h-4 w-4 shrink-0 text-win-purple" />
                      <span>{t(point)}</span>
                    </li>
                  ))}
                </ul>
              </div>
            ))}
          </div>
          <p className="mt-6 text-sm text-win-muted">{t("termsNote")}</p>
        </div>
      </section>

      {/* What's next */}
      <section className="px-4 pb-14 md:px-8 md:pb-20">
        <div className="mx-auto grid max-w-7xl gap-4 lg:grid-cols-2">
          <div className="relative overflow-hidden rounded-2xl border border-black/5 bg-gradient-to-br from-[#e8f7f3] via-white to-[#fff1f7] px-6 py-10 text-center md:px-10">
            <div className="mx-auto flex h-28 w-28 items-center justify-center rounded-full border-4 border-white bg-win-sky shadow">
              <Users className="h-12 w-12 text-win-purple" />
            </div>
            <h2 className="mt-6 text-2xl font-bold text-win-ink">{t("nextTitle")}</h2>
            <h3 className="mt-2 text-xl font-semibold text-win-ink">{t("nextSubtitle")}</h3>
            <p className="mx-auto mt-3 max-w-md text-sm text-win-muted">{t("nextBody")}</p>
            <div className="mt-6 flex flex-wrap justify-center gap-3">
              <Link
                href="/contact"
                className="inline-flex rounded-full border border-win-ink/20 bg-white px-6 py-3 text-sm font-semibold text-win-ink"
              >
                {t("bookConsult")}
              </Link>
              <Link href="/contact" className="btn-primary rounded-full px-6">
                {t("freeBrochure")}
              </Link>
            </div>
          </div>
          <div className="rounded-2xl bg-[#f5f5f5] px-6 py-10 md:px-10">
            <h2 className="text-2xl font-bold text-win-ink">{t("eventsTitle")}</h2>
            <p className="mt-3 text-sm text-win-muted">{t("eventsBody")}</p>
            <hr className="my-8 border-black/10" />
            <p className="text-sm font-semibold text-win-ink">{t("eventsNext")}</p>
            <Link
              href="/events"
              className="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-win-purple"
            >
              {t("viewAllEvents")} →
            </Link>
          </div>
        </div>
      </section>

      {/* FAQ */}
      <section className="border-t border-black/5 px-4 py-14 md:px-8 md:py-16">
        <div className="mx-auto max-w-7xl">
          <h2 className="text-2xl font-bold text-win-ink md:text-3xl">{faqTitle}</h2>
          <p className="mt-3 max-w-2xl text-win-muted">{faqBody}</p>
          <div className="mt-8 grid gap-x-10 lg:grid-cols-2">
            {(cmsFaqs.length > 0
              ? cmsFaqs.map((item, index) => ({
                  key: `cms-faq-${index}`,
                  question: item.question,
                  answer: item.answer,
                }))
              : faqFallback.map((key) => ({
                  key,
                  question: t(`${key}Q`),
                  answer: t(`${key}A`),
                }))
            ).map((item, index) => {
              const open = openFaq === index;
              return (
                <div key={item.key} className="border-b border-black/10">
                  <button
                    type="button"
                    onClick={() => setOpenFaq(open ? null : index)}
                    className="flex w-full items-center justify-between gap-4 py-4 text-left"
                  >
                    <span className="font-semibold text-win-ink">{item.question}</span>
                    <ChevronDown
                      className={[
                        "h-5 w-5 shrink-0 text-win-muted transition",
                        open ? "rotate-180" : "",
                      ].join(" ")}
                    />
                  </button>
                  {open ? (
                    <p className="pb-4 text-sm leading-relaxed text-win-muted">{item.answer}</p>
                  ) : null}
                </div>
              );
            })}
          </div>
        </div>
      </section>

      {/* Contact blurb */}
      <section className="px-4 pb-10 md:px-8">
        <div className="mx-auto max-w-7xl rounded-2xl border border-black/8 bg-white p-6 md:p-10">
          <h2 className="text-2xl font-bold text-win-ink">{t("contactTitle")}</h2>
          <div className="mt-6 grid gap-6 md:grid-cols-3">
            <div className="flex gap-3">
              <MapPin className="mt-0.5 h-5 w-5 text-win-purple" />
              <p className="text-sm text-win-muted whitespace-pre-line">{t("contactAddress")}</p>
            </div>
            <div>
              <p className="text-sm font-semibold text-win-ink">{t("contactHoursTitle")}</p>
              <p className="mt-2 text-sm text-win-muted">{t("contactHours")}</p>
            </div>
            <div>
              <Link href="/contact" className="btn-primary rounded-full px-6">
                {t("contactCta")}
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* Sticky CTA */}
      <div className="pointer-events-none fixed inset-x-0 bottom-0 z-40 bg-gradient-to-t from-white via-white/95 to-transparent px-4 pb-4 pt-10 md:px-8">
        <div className="pointer-events-auto mx-auto flex max-w-7xl flex-col gap-2 sm:flex-row sm:justify-end">
          <Link
            href="/contact"
            className="inline-flex items-center justify-center rounded-full border border-win-ink/20 bg-white px-6 py-3 text-sm font-semibold text-win-ink shadow-sm"
          >
            {t("freeBrochure")}
          </Link>
          <Link href="/apply" className="btn-primary rounded-full px-6 shadow-sm">
            {t("getQuote")}
          </Link>
        </div>
      </div>
    </div>
  );
}
