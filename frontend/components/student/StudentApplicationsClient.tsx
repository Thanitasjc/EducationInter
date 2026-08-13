"use client";

import { useLocale, useTranslations } from "next-intl";
import { useEffect, useState } from "react";
import { getStudentApplications } from "@/lib/api";
import { localized } from "@/lib/utils";

type Activity = {
  id?: number;
  type?: string;
  from_status?: string | null;
  to_status?: string | null;
  body?: string | null;
  created_at?: string;
};

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
        const activities = (app.activities as Activity[] | undefined) ?? [];
        return (
          <article key={String(app.id)} className="card-soft space-y-4">
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
                {String(app.status).replaceAll("_", " ")}
              </span>
            </div>
            {app.next_action ? (
              <p className="text-sm">
                <span className="font-semibold">{t("nextAction")}:</span> {String(app.next_action)}
              </p>
            ) : null}

            <div>
              <h3 className="text-sm font-semibold text-win-ink">{t("timeline")}</h3>
              {activities.length === 0 ? (
                <p className="mt-2 text-sm text-win-muted">{t("noTimeline")}</p>
              ) : (
                <ol className="mt-3 space-y-3 border-l-2 border-win-purple/20 pl-4">
                  {activities.map((activity, index) => (
                    <li key={String(activity.id ?? index)} className="relative">
                      <span className="absolute -left-[1.4rem] top-1.5 h-2.5 w-2.5 rounded-full bg-win-purple" />
                      <p className="text-sm font-semibold text-win-ink">
                        {activity.to_status
                          ? String(activity.to_status).replaceAll("_", " ")
                          : activity.type || t("timelineEvent")}
                      </p>
                      {activity.body ? (
                        <p className="mt-0.5 text-sm text-win-muted">{activity.body}</p>
                      ) : null}
                      {activity.created_at ? (
                        <p className="mt-0.5 text-xs text-win-muted">
                          {new Date(activity.created_at).toLocaleString(
                            locale === "th" ? "th-TH" : "en-GB",
                          )}
                        </p>
                      ) : null}
                    </li>
                  ))}
                </ol>
              )}
            </div>
          </article>
        );
      })}
    </div>
  );
}
