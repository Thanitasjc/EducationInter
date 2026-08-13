import { getTranslations } from "next-intl/server";
import { Link } from "@/i18n/navigation";
import type { Scholarship } from "@/types/catalog";
import { coverFor } from "@/lib/media";
import { localized } from "@/lib/utils";

type Props = {
  scholarship: Scholarship;
  locale: string;
};

export async function ScholarshipCard({ scholarship, locale }: Props) {
  const t = await getTranslations("catalog");
  const title = localized(scholarship, locale, "title");
  const cover = coverFor(
    scholarship.slug,
    scholarship.cover_path,
    scholarship.university?.cover_path,
  );

  return (
    <Link
      href={`/scholarships/${scholarship.slug}`}
      className="card-soft block overflow-hidden p-0 transition hover:-translate-y-1 hover:border-win-purple/30"
    >
      {/* eslint-disable-next-line @next/next/no-img-element */}
      <img src={cover} alt={title} className="aspect-[16/10] w-full object-cover" />
      <div className="p-5">
        <p className="font-bold text-win-ink">{title}</p>
        <p className="mt-2 text-sm font-semibold text-win-blue">
          {localized(scholarship, locale, "amount_label") || t("amountTbd")}
        </p>
        {scholarship.university ? (
          <p className="mt-2 text-sm text-win-muted">
            {localized(scholarship.university, locale, "name")}
          </p>
        ) : null}
        {scholarship.deadline ? (
          <p className="mt-3 text-xs text-win-muted">
            {t("deadline")}: {scholarship.deadline}
          </p>
        ) : null}
      </div>
    </Link>
  );
}
