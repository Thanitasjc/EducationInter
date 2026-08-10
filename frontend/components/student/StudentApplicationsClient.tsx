"use client";

import { useLocale, useTranslations } from "next-intl";
import { useEffect, useState } from "react";
import { getStudentApplications } from "@/lib/api";
import { localized } from "@/lib/utils";

export function StudentApplicationsClient() {
  const t = useTranslations("student");
  const locale = useLocale();
  const [items, setItems] = useState<Array<Record<string, unknown>> | null>(null);

  useEffect(() => {
    getStudentApplications()
      .then((res) => setItems(res.data))
      .catch(() => setItems([]));
  }, []);

  if (!items) {
    return <div className="card-soft text-win-muted">{t("loading")}</div>;
  }

  return (
    <div className="space-y-4">
      <h1 className="text-3xl font-bold">{t("applications")}</h1>
      {items.length === 0 && <div className="card-soft text-win-muted">{t("noApplications")}</div>}
      {items.map((app) => {
        const uni = app.university as Record<string, unknown> | undefined;
        const course = app.course as Record<string, unknown> | undefined;
        return (
          <article key={String(app.id)} className="card-soft">
            <div className="flex flex-wrap items-start justify-between gap-3">
              <div>
                <p className="text-sm font-semibold text-win-purple">{String(app.application_no)}</p>
                <h2 className="mt-1 text-xl font-bold">
                  {uni ? localized(uni, locale, "name") : t("noUniversity")}
                </h2>
                <p className="mt-1 text-sm text-win-muted">
                  {course ? localized(course, locale, "name") : "-"}
                  {app.intake ? ` · ${String(app.intake)}` : ""}
                </p>
              </div>
              <span className="rounded-full bg-win-sky px-3 py-1 text-xs font-semibold uppercase text-win-blue">
                {String(app.status)}
              </span>
            </div>
            {app.next_action ? (
              <p className="mt-4 text-sm">
                <span className="font-semibold">{t("nextAction")}:</span> {String(app.next_action)}
              </p>
            ) : null}
          </article>
        );
      })}
    </div>
  );
}
