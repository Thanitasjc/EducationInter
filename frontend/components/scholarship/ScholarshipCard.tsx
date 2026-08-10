import { getTranslations } from "next-intl/server";
import { Link } from "@/i18n/navigation";
import type { Scholarship } from "@/types/catalog";
import { localized } from "@/lib/utils";

type Props = {
  scholarship: Scholarship;
  locale: string;
};

export async function ScholarshipCard({ scholarship, locale }: Props) {
  const t = await getTranslations("catalog");

  return (
    <Link
      href={`/scholarships/${scholarship.slug}`}
      className="card-soft block transition hover:-translate-y-1 hover:border-win-purple/30"
    >
      <p className="font-bold text-win-ink">
        {localized(scholarship, locale, "title")}
      </p>
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
    </Link>
  );
}
