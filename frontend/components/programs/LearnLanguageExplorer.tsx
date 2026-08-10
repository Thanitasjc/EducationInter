"use client";

import { useMemo, useState } from "react";
import {
  Award,
  Calendar,
  Globe2,
  Heart,
  Layers,
  MapPin,
  User,
  Users,
  Zap,
} from "lucide-react";
import { useTranslations } from "next-intl";
import { Link } from "@/i18n/navigation";
import { coverFor } from "@/lib/media";
import {
  AGE_GROUPS,
  filterProgramsByAge,
  type AgeGroup,
  type ProgramItem,
} from "@/lib/programs";
import { localized } from "@/lib/utils";

type CountryLite = {
  id: number | string;
  slug: string;
  name_th?: string;
  name_en?: string;
  cover_path?: string | null;
};

type Props = {
  programs: ProgramItem[];
  countries: CountryLite[];
  locale: string;
  initialAge?: AgeGroup;
  title: string;
  introHtml: string[];
  heroImage: string;
};

const LANGUAGE_CARDS = [
  {
    key: "english",
    cover:
      "https://images.unsplash.com/photo-1456513080080-7e9b1b0c5f2f?auto=format&fit=crop&w=900&q=80",
  },
  {
    key: "japanese",
    cover:
      "https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?auto=format&fit=crop&w=900&q=80",
  },
  {
    key: "korean",
    cover:
      "https://images.unsplash.com/photo-1517154421773-0529f29ea451?auto=format&fit=crop&w=900&q=80",
  },
  {
    key: "chinese",
    cover:
      "https://images.unsplash.com/photo-1508804185872-d7badad00f7d?auto=format&fit=crop&w=900&q=80",
  },
] as const;

const DESTINATION_LABELS: Record<string, { th: string; en: string }> = {
  uk: { th: "อังกฤษ", en: "the UK" },
  usa: { th: "อเมริกา", en: "the USA" },
  australia: { th: "ออสเตรเลีย", en: "Australia" },
  canada: { th: "แคนาดา", en: "Canada" },
  singapore: { th: "สิงคโปร์", en: "Singapore" },
  japan: { th: "ญี่ปุ่น", en: "Japan" },
  korea: { th: "เกาหลี", en: "Korea" },
  "new-zealand": { th: "นิวซีแลนด์", en: "New Zealand" },
  ireland: { th: "ไอร์แลนด์", en: "Ireland" },
};

function ageMessageKey(group: AgeGroup) {
  return `ages.${group.replaceAll("-", "_")}`;
}

function destinationName(slug: string, locale: string) {
  const map = DESTINATION_LABELS[slug];
  if (map) return locale === "th" ? map.th : map.en;
  return slug.toUpperCase();
}

export function LearnLanguageExplorer({
  programs,
  countries,
  locale,
  initialAge = "all",
  title,
  introHtml,
  heroImage,
}: Props) {
  const t = useTranslations("learnLanguage");
  const [age, setAge] = useState<AgeGroup>(initialAge);
  const [language, setLanguage] = useState<string | null>(null);

  const filtered = useMemo(() => {
    const byAge = filterProgramsByAge(programs, age);
    if (!language) return byAge;
    return byAge.filter((p) => p.language === language);
  }, [programs, age, language]);

  function selectAge(next: AgeGroup) {
    setAge(next);
    if (typeof window === "undefined") return;
    const url = new URL(window.location.href);
    if (next === "all") url.searchParams.delete("age");
    else url.searchParams.set("age", next);
    window.history.replaceState({}, "", url.toString());
    document.getElementById("programs")?.scrollIntoView({ behavior: "smooth" });
  }

  function selectLanguage(next: string) {
    setLanguage((prev) => (prev === next ? null : next));
    setAge("all");
    document.getElementById("programs")?.scrollIntoView({ behavior: "smooth" });
  }

  return (
    <div>
      {/* Hero / stage */}
      <section className="border-b border-black/5 bg-white px-4 py-10 md:px-8 md:py-16">
        <div className="mx-auto grid max-w-7xl items-center gap-10 lg:grid-cols-2 lg:gap-16">
          <div>
            <Link
              href="/"
              className="text-sm font-semibold text-win-purple hover:underline"
            >
              ← {t("backPrograms")}
            </Link>
            <h1 className="mt-6 text-3xl font-bold tracking-tight text-win-ink md:text-5xl">
              {title}
            </h1>
            <div className="mt-6 space-y-4 text-base leading-relaxed text-win-muted md:text-lg">
              {introHtml.map((paragraph, i) => (
                <p key={i}>{paragraph}</p>
              ))}
            </div>
            <div className="mt-8 flex flex-wrap gap-3">
              <a href="#destinations" className="btn-primary rounded-full px-6">
                {t("exploreDestinations")}
              </a>
              <Link
                href="/contact"
                className="inline-flex items-center justify-center rounded-full border border-win-ink/20 bg-white px-6 py-3 text-sm font-semibold text-win-ink transition hover:border-win-purple hover:text-win-purple"
              >
                {t("freeBrochure")}
              </Link>
            </div>
          </div>
          <div className="relative">
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img
              src={heroImage}
              alt={title}
              className="w-full rounded-2xl object-cover p-2 shadow-[0_16px_40px_rgba(18,24,38,0.12)]"
            />
          </div>
        </div>
      </section>

      {/* Programs by age */}
      <section className="bg-[#f7f5ff] px-4 py-12 md:px-8 md:py-20">
        <div className="mx-auto max-w-7xl overflow-hidden rounded-2xl border border-[#D1D1D1] bg-white/80 px-4 py-10 md:px-8 md:py-16">
          <div className="mx-auto max-w-xl text-center">
            <h2 className="text-2xl font-bold text-win-ink md:text-[32px] md:leading-10">
              {t("findByAge")}
            </h2>
          </div>

          {/* Tabs */}
          <div id="programs" className="mt-10 scroll-mt-24">
            <ul className="flex flex-wrap justify-center gap-2 border-b border-black/10 pb-1">
              {AGE_GROUPS.map((group) => {
                const active = age === group;
                return (
                  <li key={group}>
                    <button
                      type="button"
                      onClick={() => selectAge(group)}
                      className={[
                        "relative px-4 py-3 text-sm font-semibold transition",
                        active
                          ? "text-win-purple after:absolute after:inset-x-2 after:bottom-0 after:h-0.5 after:bg-win-purple"
                          : "text-win-muted hover:text-win-ink",
                      ].join(" ")}
                    >
                      {t(ageMessageKey(group))}
                    </button>
                  </li>
                );
              })}
            </ul>

            <div className="mt-8 space-y-6">
              {filtered.length > 0 ? (
                filtered.map((program) => {
                  const programTitle = localized(program, locale, "title");
                  const cover = coverFor(
                    program.slug,
                    program.cover_url,
                    program.cover_path,
                  );
                  const duration = localized(program, locale, "duration_label");
                  const destLabels = (program.destinations ?? []).map((slug) =>
                    destinationName(slug, locale),
                  );

                  return (
                    <article
                      key={program.id}
                      className="flex flex-col overflow-hidden rounded-2xl border border-[#DADADA] bg-white p-2 lg:flex-row lg:items-stretch lg:p-6 lg:pl-8"
                    >
                      <Link
                        href={`/learn-language/${program.slug}`}
                        className="relative block h-[148px] w-full shrink-0 overflow-hidden rounded-lg lg:h-[220px] lg:w-[316px]"
                      >
                        {/* eslint-disable-next-line @next/next/no-img-element */}
                        <img
                          src={cover}
                          alt={programTitle}
                          className="absolute inset-0 h-full w-full object-cover"
                        />
                      </Link>
                      <div className="flex flex-1 flex-col px-4 pb-4 pt-4 lg:pl-8 lg:pr-6 lg:pt-0">
                        <h3 className="text-lg font-black uppercase tracking-[1px] text-win-ink">
                          {programTitle}
                        </h3>
                        <div className="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-sm font-bold text-win-ink">
                          {program.age_label ? (
                            <span className="inline-flex items-center gap-2">
                              <User className="h-3.5 w-3.5" />
                              {program.age_label} {locale === "th" ? "ปี" : ""}
                            </span>
                          ) : null}
                          {duration ? (
                            <span className="inline-flex items-center gap-2">
                              <Calendar className="h-3.5 w-3.5" />
                              {duration}
                            </span>
                          ) : null}
                        </div>
                        <p className="mt-3 text-base text-win-ink/90">
                          {localized(program, locale, "summary")}
                        </p>
                        {destLabels.length > 0 ? (
                          <p className="mt-3 text-sm text-win-muted">
                            <span className="font-medium text-win-ink">
                              {t("destinations")}:{" "}
                            </span>
                            {destLabels.join(", ")}
                          </p>
                        ) : null}
                        <div className="mt-auto flex flex-col gap-2 pt-6 sm:flex-row sm:justify-end">
                          <Link
                            href="/contact"
                            className="inline-flex items-center justify-center rounded-full border border-win-ink/20 px-6 py-3 text-sm font-semibold text-win-ink transition hover:border-win-purple hover:text-win-purple"
                          >
                            {t("freeBrochure")}
                          </Link>
                          <Link
                            href={`/learn-language/${program.slug}`}
                            className="btn-primary rounded-full px-6"
                          >
                            {t("learnMore")}
                          </Link>
                        </div>
                      </div>
                    </article>
                  );
                })
              ) : (
                <div className="rounded-2xl border border-dashed border-black/15 bg-white p-8 text-center text-win-muted">
                  {t("empty")}
                </div>
              )}
            </div>
          </div>
        </div>
      </section>

      {/* Destinations */}
      <section id="destinations" className="scroll-mt-24 px-4 py-14 md:px-8 md:py-20">
        <div className="mx-auto max-w-7xl">
          <div className="mb-6 text-center">
            <div className="mb-4 inline-flex rounded-full bg-pink-100 p-4 text-pink-600">
              <MapPin className="h-6 w-6" />
            </div>
            <h2 className="text-2xl font-bold text-win-ink md:text-3xl">
              {t("byCountryTitle")}
            </h2>
          </div>
          <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {countries.map((country) => {
              const name = localized(country, locale, "name");
              const label =
                locale === "th"
                  ? `${t("studyIn")} ${name}`
                  : `${t("studyIn")} ${name}`;
              return (
                <Link
                  key={String(country.id)}
                  href={`/countries/${country.slug}`}
                  className="group overflow-hidden rounded-xl"
                >
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img
                    src={coverFor(String(country.slug), country.cover_path)}
                    alt={label}
                    className="aspect-[16/9] w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                  />
                  <p className="mt-3 text-center text-sm font-semibold text-win-ink md:text-base">
                    {label}
                  </p>
                </Link>
              );
            })}
          </div>
        </div>
      </section>

      {/* Languages */}
      <section className="bg-win-sky/40 px-4 py-14 md:px-8 md:py-20">
        <div className="mx-auto max-w-7xl">
          <div className="mb-6 text-center">
            <div className="mb-4 inline-flex rounded-full bg-pink-100 p-4 text-pink-600">
              <Layers className="h-6 w-6" />
            </div>
            <h2 className="text-2xl font-bold text-win-ink md:text-3xl">
              {t("byLanguageTitle")}
            </h2>
          </div>
          <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {LANGUAGE_CARDS.map((lang) => (
              <button
                key={lang.key}
                type="button"
                onClick={() => selectLanguage(lang.key)}
                className={[
                  "group overflow-hidden rounded-xl text-left",
                  language === lang.key ? "ring-2 ring-win-purple" : "",
                ].join(" ")}
              >
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img
                  src={lang.cover}
                  alt={t(`languages.${lang.key}`)}
                  className="aspect-[16/9] w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                />
                <p className="mt-3 text-center text-sm font-semibold text-win-ink md:text-base">
                  {t(`languages.${lang.key}`)}
                </p>
              </button>
            ))}
          </div>
        </div>
      </section>

      {/* Deep dive */}
      <section className="px-4 py-14 md:px-8 md:py-16">
        <div className="mx-auto max-w-3xl space-y-12">
          <div>
            <h2 className="text-2xl font-bold text-win-ink">{t("deepdive1Title")}</h2>
            <p className="mt-4 leading-relaxed text-win-muted">{t("deepdive1Body")}</p>
          </div>
          <div>
            <h2 className="text-2xl font-bold text-win-ink">{t("deepdive2Title")}</h2>
            <p className="mt-4 leading-relaxed text-win-muted">{t("deepdive2Body")}</p>
            <div className="mt-8 text-center">
              <Link href="/contact" className="btn-primary rounded-full px-8">
                {t("seePricing")}
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* Why Education Interntions */}
      <section className="px-4 pb-16 md:px-8 md:pb-24">
        <div className="mx-auto max-w-7xl overflow-hidden rounded-2xl border border-[#D1D1D1] bg-[rgba(250,249,255,0.7)] px-4 py-10 md:px-8 md:py-14">
          <div className="mx-auto max-w-xl text-center">
            <div className="mb-4 inline-flex rounded-full bg-pink-100 p-4 text-pink-600">
              <Heart className="h-6 w-6" />
            </div>
            <h2 className="text-2xl font-bold text-win-ink md:text-3xl">
              {t("whyTitle")}
            </h2>
          </div>
          <div className="mt-10 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            {(
              [
                { key: "why1", icon: Award },
                { key: "why2", icon: Users },
                { key: "why3", icon: Zap },
                { key: "why4", icon: Globe2 },
                { key: "why5", icon: MapPin },
              ] as const
            ).map(({ key, icon: Icon }) => (
              <div
                key={key}
                className="flex items-start gap-4 md:flex-col md:items-center md:text-center"
              >
                <div className="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-win-purple/10 text-win-purple">
                  <Icon className="h-6 w-6" />
                </div>
                <p className="text-base font-medium text-win-ink md:max-w-[23ch]">
                  {t(key)}
                </p>
              </div>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
}
