import { Link } from "@/i18n/navigation";
import type { Country } from "@/types/catalog";
import { coverFor } from "@/lib/media";
import { localized } from "@/lib/utils";

type Props = {
  country: Country;
  locale: string;
};

export function CountryCard({ country, locale }: Props) {
  const name = localized(country, locale, "name");
  const cover = coverFor(country.slug, country.cover_path);

  return (
    <Link
      href={`/countries/${country.slug}`}
      className="card-soft block overflow-hidden p-0 transition hover:-translate-y-1 hover:border-win-purple/30"
    >
      {/* eslint-disable-next-line @next/next/no-img-element */}
      <img src={cover} alt={name} className="aspect-[16/10] w-full object-cover" />
      <div className="p-5">
        <div className="flex items-center justify-between gap-3">
          <p className="text-lg font-bold text-win-blue">{name}</p>
          {country.code ? (
            <span className="rounded-full bg-win-sky px-2.5 py-1 text-xs font-semibold text-win-blue">
              {country.code}
            </span>
          ) : null}
        </div>
        <p className="mt-2 line-clamp-2 text-sm text-win-muted">
          {localized(country, locale, "summary")}
        </p>
      </div>
    </Link>
  );
}
