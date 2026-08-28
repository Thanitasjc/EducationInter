"use client";

import { useLocale, useTranslations } from "next-intl";
import { useEffect, useMemo, useState } from "react";
import { getStudentAppointments } from "@/lib/api";

export function StudentAppointmentsClient() {
  const t = useTranslations("student");
  const locale = useLocale();
  const [items, setItems] = useState<Array<Record<string, unknown>> | null>(null);
  const [now, setNow] = useState<number | null>(null);

  useEffect(() => {
    getStudentAppointments()
      .then((res) => {
        setItems(res.data);
        setNow(Date.now());
      })
      .catch(() => {
        setItems([]);
        setNow(Date.now());
      });
  }, []);

  const { upcoming, past } = useMemo(() => {
    const list = items ?? [];
    const up: Array<Record<string, unknown>> = [];
    const prev: Array<Record<string, unknown>> = [];
    for (const item of list) {
      const start = item.starts_at ? new Date(String(item.starts_at)).getTime() : 0;
      if (start >= (now ?? 0) && String(item.status) !== "cancelled") up.push(item);
      else prev.push(item);
    }
    return { upcoming: up, past: prev };
  }, [items, now]);

  if (!items || now === null) {
    return <div className="card-soft text-win-muted">{t("loading")}</div>;
  }

  function renderCard(item: Record<string, unknown>) {
    const consultant = item.consultant as Record<string, unknown> | undefined;
    return (
      <article key={String(item.id)} className="card-soft">
        <div className="flex flex-wrap items-start justify-between gap-2">
          <p className="font-bold">{String(item.title)}</p>
          <span className="rounded-full bg-win-sky px-3 py-1 text-xs font-semibold uppercase text-win-blue">
            {String(item.status)}
          </span>
        </div>
        <p className="mt-1 text-sm text-win-muted capitalize">
          {String(item.type)}
          {consultant?.name ? ` · ${String(consultant.name)}` : ""}
        </p>
        <p className="mt-2 text-sm font-medium">
          {item.starts_at
            ? new Date(String(item.starts_at)).toLocaleString(locale === "th" ? "th-TH" : "en-GB", {
                dateStyle: "medium",
                timeStyle: "short",
              })
            : "-"}
          {item.ends_at
            ? ` – ${new Date(String(item.ends_at)).toLocaleTimeString(locale === "th" ? "th-TH" : "en-GB", {
                timeStyle: "short",
              })}`
            : ""}
        </p>
        {item.notes ? <p className="mt-2 text-sm text-win-muted">{String(item.notes)}</p> : null}
      </article>
    );
  }

  return (
    <div className="space-y-6">
      <h1 className="text-3xl font-bold">{t("appointments")}</h1>

      <section className="space-y-3">
        <h2 className="text-lg font-semibold">{t("upcoming")}</h2>
        {upcoming.length === 0 ? (
          <div className="card-soft text-win-muted">{t("noAppointments")}</div>
        ) : (
          upcoming.map(renderCard)
        )}
      </section>

      {past.length > 0 && (
        <section className="space-y-3">
          <h2 className="text-lg font-semibold">{t("pastAppointments")}</h2>
          {past.map(renderCard)}
        </section>
      )}
    </div>
  );
}
