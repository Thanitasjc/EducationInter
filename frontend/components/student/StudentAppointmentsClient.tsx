"use client";

import { useTranslations } from "next-intl";
import { useEffect, useState } from "react";
import { getStudentAppointments } from "@/lib/api";

export function StudentAppointmentsClient() {
  const t = useTranslations("student");
  const [items, setItems] = useState<Array<Record<string, unknown>> | null>(null);

  useEffect(() => {
    getStudentAppointments()
      .then((res) => setItems(res.data))
      .catch(() => setItems([]));
  }, []);

  if (!items) {
    return <div className="card-soft text-win-muted">{t("loading")}</div>;
  }

  return (
    <div className="space-y-4">
      <h1 className="text-3xl font-bold">{t("appointments")}</h1>
      {items.length === 0 && <div className="card-soft text-win-muted">{t("noAppointments")}</div>}
      {items.map((item) => (
        <article key={String(item.id)} className="card-soft">
          <p className="font-bold">{String(item.title)}</p>
          <p className="mt-1 text-sm text-win-muted capitalize">{String(item.type)} · {String(item.status)}</p>
          <p className="mt-2 text-sm">
            {item.starts_at ? new Date(String(item.starts_at)).toLocaleString() : "-"}
          </p>
          {item.notes ? <p className="mt-2 text-sm text-win-muted">{String(item.notes)}</p> : null}
        </article>
      ))}
    </div>
  );
}
