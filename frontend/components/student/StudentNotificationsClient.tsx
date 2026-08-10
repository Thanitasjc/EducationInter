"use client";

import { useTranslations } from "next-intl";
import { useEffect, useState } from "react";
import { Link } from "@/i18n/navigation";
import {
  getStudentNotifications,
  markAllNotificationsRead,
  markNotificationRead,
} from "@/lib/api";
import { StudentAuthGate } from "@/components/student/StudentAuthGate";

type NotificationItem = {
  id: number;
  type?: string;
  title: string;
  body?: string | null;
  link?: string | null;
  read_at?: string | null;
  created_at?: string;
};

export function StudentNotificationsClient() {
  const t = useTranslations("student");
  const [items, setItems] = useState<NotificationItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  async function load() {
    setLoading(true);
    setError("");
    try {
      const res = await getStudentNotifications();
      setItems((res.data as NotificationItem[]) ?? []);
    } catch {
      setError(t("loadError"));
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    void load();
  }, []);

  async function onRead(id: number) {
    await markNotificationRead(id);
    setItems((prev) =>
      prev.map((item) =>
        item.id === id ? { ...item, read_at: new Date().toISOString() } : item,
      ),
    );
  }

  async function onReadAll() {
    await markAllNotificationsRead();
    const now = new Date().toISOString();
    setItems((prev) => prev.map((item) => ({ ...item, read_at: item.read_at || now })));
  }

  return (
    <StudentAuthGate>
      <div className="space-y-4">
        <div className="flex items-center justify-between gap-3">
          <h1 className="text-2xl font-bold text-win-ink">{t("notifications")}</h1>
          <button
            type="button"
            onClick={() => void onReadAll()}
            className="rounded-lg border border-black/10 px-3 py-2 text-sm font-semibold text-win-purple"
          >
            {t("markAllRead")}
          </button>
        </div>

        {loading && <div className="card-soft text-sm text-win-muted">{t("loading")}</div>}
        {error && <div className="card-soft text-sm text-red-600">{error}</div>}

        {!loading && items.length === 0 && (
          <div className="card-soft text-sm text-win-muted">{t("emptyNotifications")}</div>
        )}

        <div className="space-y-3">
          {items.map((item) => (
            <article
              key={item.id}
              className={`card-soft ${item.read_at ? "opacity-70" : "border-win-purple/30"}`}
            >
              <div className="flex items-start justify-between gap-3">
                <div>
                  <p className="font-semibold text-win-ink">{item.title}</p>
                  {item.body ? <p className="mt-1 text-sm text-win-muted">{item.body}</p> : null}
                  {item.link ? (
                    <Link
                      href={item.link.replace(/^\/(th|en)/, "") || "/student/dashboard"}
                      className="mt-2 inline-block text-sm font-semibold text-win-purple"
                    >
                      {t("openLink")}
                    </Link>
                  ) : null}
                </div>
                {!item.read_at ? (
                  <button
                    type="button"
                    onClick={() => void onRead(item.id)}
                    className="shrink-0 text-xs font-semibold text-win-purple"
                  >
                    {t("markRead")}
                  </button>
                ) : null}
              </div>
            </article>
          ))}
        </div>
      </div>
    </StudentAuthGate>
  );
}
