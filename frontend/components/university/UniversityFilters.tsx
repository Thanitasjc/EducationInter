"use client";

import { useTranslations } from "next-intl";
import { usePathname, useRouter } from "@/i18n/navigation";
import { FormEvent, useState } from "react";
import type { Country } from "@/types/catalog";
import { localized } from "@/lib/utils";

type Props = {
  locale: string;
  countries: Country[];
  initialQ?: string;
  initialCountry?: string;
  initialType?: string;
};

export function UniversityFilters({
  locale,
  countries,
  initialQ = "",
  initialCountry = "",
  initialType = "",
}: Props) {
  const t = useTranslations("catalog");
  const router = useRouter();
  const pathname = usePathname();
  const [q, setQ] = useState(initialQ);
  const [country, setCountry] = useState(initialCountry);
  const [type, setType] = useState(initialType);

  function onSubmit(e: FormEvent) {
    e.preventDefault();
    const params = new URLSearchParams();
    if (q.trim()) params.set("q", q.trim());
    if (country) params.set("country", country);
    if (type) params.set("type", type);
    const query = params.toString();
    router.push(query ? `${pathname}?${query}` : pathname);
  }

  return (
    <form
      onSubmit={onSubmit}
      className="card-soft grid gap-3 md:grid-cols-[1.4fr_1fr_1fr_auto]"
    >
      <input
        value={q}
        onChange={(e) => setQ(e.target.value)}
        placeholder={t("searchUniversity")}
        className="rounded-xl border border-black/10 px-4 py-3 outline-none ring-win-purple focus:ring-2"
      />
      <select
        value={country}
        onChange={(e) => setCountry(e.target.value)}
        className="rounded-xl border border-black/10 px-4 py-3 outline-none ring-win-purple focus:ring-2"
      >
        <option value="">{t("allCountries")}</option>
        {countries.map((item) => (
          <option key={item.id} value={item.slug}>
            {localized(item, locale, "name")}
          </option>
        ))}
      </select>
      <select
        value={type}
        onChange={(e) => setType(e.target.value)}
        className="rounded-xl border border-black/10 px-4 py-3 outline-none ring-win-purple focus:ring-2"
      >
        <option value="">{t("allTypes")}</option>
        <option value="public">{t("public")}</option>
        <option value="private">{t("private")}</option>
      </select>
      <button type="submit" className="btn-primary">
        {t("filter")}
      </button>
    </form>
  );
}
