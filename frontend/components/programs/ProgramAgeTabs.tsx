"use client";

import { useMemo, useState } from "react";
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

type Props = {
  programs: ProgramItem[];
  locale: string;
  initialAge?: AgeGroup;
  /** When true, sync age to URL ?age= */
  syncUrl?: boolean;
  limit?: number;
  showViewAll?: boolean;
};

export function ProgramAgeTabs({
  programs,
  locale,
  initialAge = "all",
  syncUrl = false,
  limit,
  showViewAll = false,
}: Props) {
  const t = useTranslations("learnLanguage");
  const [age, setAge] = useState<AgeGroup>(initialAge);

  const filtered = useMemo(() => {
    const list = filterProgramsByAge(programs, age);
    return typeof limit === "number" ? list.slice(0, limit) : list;
  }, [programs, age, limit]);

  function selectAge(next: AgeGroup) {
    setAge(next);
    if (!syncUrl || typeof window === "undefined") return;
    const url = new URL(window.location.href);
    if (next === "all") {
      url.searchParams.delete("age");
    } else {
      url.searchParams.set("age", next);
    }
    window.history.replaceState({}, "", url.toString());
  }

  return (
    <div>
      <div className="flex flex-wrap gap-2">
        {AGE_GROUPS.map((group) => {
          const active = age === group;
          return (
            <button
              key={group}
              type="button"
              onClick={() => selectAge(group)}
              className={[
                "rounded-lg px-3 py-2 text-sm font-semibold transition",
                active
                  ? "bg-win-purple text-white"
                  : "border border-black/10 bg-white text-win-ink hover:border-win-purple/40 hover:text-win-purple",
              ].join(" ")}
            >
              {t(`ages.${group.replaceAll("-", "_")}`)}
            </button>
          );
        })}
      </div>

      <div className="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        {filtered.length > 0 ? (
          filtered.map((program) => {
            const title = localized(program, locale, "title");
            const cover = coverFor(
              program.slug,
              program.cover_url,
              program.cover_path,
            );
            const duration = localized(program, locale, "duration_label");
            return (
              <Link
                key={program.id}
                href={`/learn-language/${program.slug}`}
                className="card-soft overflow-hidden p-0 transition hover:-translate-y-1 hover:border-win-purple/30"
              >
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img
                  src={cover}
                  alt={title}
                  className="aspect-[16/10] w-full object-cover"
                />
                <div className="p-5">
                  <div className="flex flex-wrap items-center gap-2 text-xs font-semibold uppercase tracking-wide text-win-purple">
                    {program.age_label ? <span>{program.age_label}</span> : null}
                    {duration ? <span>· {duration}</span> : null}
                  </div>
                  <p className="mt-2 text-lg font-bold text-win-ink">{title}</p>
                  <p className="mt-2 line-clamp-3 text-sm text-win-muted">
                    {localized(program, locale, "summary")}
                  </p>
                  {program.destinations && program.destinations.length > 0 ? (
                    <p className="mt-3 text-xs text-win-muted">
                      {program.destinations.map((d) => d.toUpperCase()).join(" · ")}
                    </p>
                  ) : null}
                </div>
              </Link>
            );
          })
        ) : (
          <div className="card-soft text-sm text-win-muted md:col-span-2 lg:col-span-3">
            {t("empty")}
          </div>
        )}
      </div>

      {showViewAll ? (
        <div className="mt-8">
          <Link href="/learn-language" className="text-sm font-semibold text-win-purple">
            {t("viewAll")}
          </Link>
        </div>
      ) : null}
    </div>
  );
}
