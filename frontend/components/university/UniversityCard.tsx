import { getTranslations } from "next-intl/server";
import { Link } from "@/i18n/navigation";
import type { University } from "@/types/catalog";
import { coverFor } from "@/lib/media";
import { localized } from "@/lib/utils";

type Props = {
  university: University;
  locale: string;
};

export async function UniversityCard({ university, locale }: Props) {
  const t = await getTranslations("catalog");
  const countryName = university.country
    ? localized(university.country, locale, "name")
    : "";
  const cover = coverFor(university.slug, university.cover_path);
  const name = localized(university, locale, "name");

  return (
    <Link
      href={`/universities/${university.slug}`}
      className="card-soft block overflow-hidden p-0 transition hover:-translate-y-1 hover:border-win-purple/30"
    >
      {/* eslint-disable-next-line @next/next/no-img-element */}
      <img src={cover} alt={name} className="aspect-[16/10] w-full object-cover" />
      <div className="p-5">
        <p className="text-lg font-bold text-win-ink">{name}</p>
        <p className="mt-2 text-sm text-win-muted">
          {countryName}
          {university.ranking_qs ? ` · QS ${university.ranking_qs}` : ""}
        </p>
        {(university.tuition_min || university.tuition_max) && (
          <p className="mt-3 text-sm font-semibold text-win-blue">
            {t("tuitionFrom")} {university.tuition_min ?? "-"}{" "}
            {university.currency ?? ""}
          </p>
        )}
        <span className="mt-4 inline-block text-sm font-semibold text-win-purple">
          {t("viewDetails")} →
        </span>
      </div>
    </Link>
  );
}
