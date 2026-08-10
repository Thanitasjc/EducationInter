"use client";

import { useTranslations } from "next-intl";
import { useRouter } from "@/i18n/navigation";
import { FormEvent, useState } from "react";

export function UniversitySearch() {
  const t = useTranslations("home");
  const router = useRouter();
  const [q, setQ] = useState("");

  function onSubmit(e: FormEvent) {
    e.preventDefault();
    const params = new URLSearchParams();
    if (q.trim()) params.set("q", q.trim());
    router.push(`/universities?${params.toString()}`);
  }

  return (
    <section className="section -mt-8">
      <form
        onSubmit={onSubmit}
        className="mx-auto flex max-w-4xl flex-col gap-3 rounded-2xl bg-white p-4 shadow-[0_20px_50px_rgba(18,24,38,0.12)] md:flex-row md:items-center"
      >
        <div className="flex-1">
          <label className="mb-1 block text-xs font-semibold uppercase tracking-wide text-win-muted">
            {t("searchTitle")}
          </label>
          <input
            value={q}
            onChange={(e) => setQ(e.target.value)}
            placeholder={t("searchPlaceholder")}
            className="w-full rounded-xl border border-black/10 px-4 py-3 outline-none ring-win-purple focus:ring-2"
          />
        </div>
        <button type="submit" className="btn-primary md:self-end">
          {t("searchButton")}
        </button>
      </form>
    </section>
  );
}
