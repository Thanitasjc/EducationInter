"use client";

import { useLocale, useTranslations } from "next-intl";
import { useEffect, useState } from "react";
import { Link } from "@/i18n/navigation";
import { getStudentDashboard } from "@/lib/api";
import { localized } from "@/lib/utils";

export function StudentDashboardClient() {
  const t = useTranslations("student");
  const locale = useLocale();
  const [data, setData] = useState<Record<string, unknown> | null>(null);
  const [error, setError] = useState(false);

  useEffect(() => {
    getStudentDashboard()
      .then(setData)
      .catch(() => setError(true));
  }, []);

  if (error) {
    return <div className="card-soft text-red-600">{t("loadError")}</div>;
  }

  if (!data) {
    return <div className="card-soft text-win-muted">{t("loading")}</div>;
  }

  const user = data.user as { name?: string };
  const stats = data.stats as {
    applications_count: number;
    documents_count: number;
    documents_approved: number;
    upcoming_appointments: number;
  };
  const applications = (data.applications as Array<Record<string, unknown>>) ?? [];
  const appointments = (data.appointments as Array<Record<string, unknown>>) ?? [];

  return (
    <div className="space-y-6">
      <h1 className="text-3xl font-bold">
        {t("welcome")} {user?.name}
      </h1>

      <div className="grid gap-4 md:grid-cols-3">
        <div className="card-soft">
          <p className="text-sm text-win-muted">{t("applications")}</p>
          <p className="mt-2 text-xl font-bold">{stats.applications_count}</p>
        </div>
        <div className="card-soft">
          <p className="text-sm text-win-muted">{t("documents")}</p>
          <p className="mt-2 text-xl font-bold">
            {stats.documents_approved} / {stats.documents_count}
          </p>
        </div>
        <div className="card-soft">
          <p className="text-sm text-win-muted">{t("appointments")}</p>
          <p className="mt-2 text-xl font-bold">{stats.upcoming_appointments}</p>
        </div>
      </div>

      <div className="card-soft">
        <div className="flex items-center justify-between">
          <h2 className="text-lg font-bold">{t("applications")}</h2>
          <Link href="/student/applications" className="text-sm font-semibold text-win-purple">
            {t("viewAll")}
          </Link>
        </div>
        <div className="mt-4 space-y-3">
          {applications.length === 0 && (
            <p className="text-sm text-win-muted">{t("noApplications")}</p>
          )}
          {applications.map((app) => {
            const uni = app.university as Record<string, unknown> | undefined;
            return (
              <div key={String(app.id)} className="rounded-xl bg-win-sky/50 px-4 py-3">
                <p className="font-semibold">{String(app.application_no)}</p>
                <p className="text-sm text-win-muted">
                  {uni ? localized(uni, locale, "name") : "-"} · {String(app.status)}
                </p>
                {app.next_action ? (
                  <p className="mt-1 text-sm text-win-blue">
                    {t("nextAction")}: {String(app.next_action)}
                  </p>
                ) : null}
              </div>
            );
          })}
        </div>
      </div>

      <div className="card-soft">
        <h2 className="text-lg font-bold">{t("upcoming")}</h2>
        <div className="mt-4 space-y-3">
          {appointments.length === 0 && (
            <p className="text-sm text-win-muted">{t("noAppointments")}</p>
          )}
          {appointments.map((item) => (
            <div key={String(item.id)} className="rounded-xl border border-black/5 px-4 py-3">
              <p className="font-semibold">{String(item.title)}</p>
              <p className="text-sm text-win-muted">
                {item.starts_at ? new Date(String(item.starts_at)).toLocaleString() : "-"}
              </p>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
