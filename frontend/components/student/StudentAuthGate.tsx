"use client";

import { useTranslations } from "next-intl";
import { useEffect, useState, type ReactNode } from "react";
import { Link, useRouter } from "@/i18n/navigation";
import { getToken } from "@/lib/auth";

export function StudentAuthGate({ children }: { children: ReactNode }) {
  const t = useTranslations("student");
  const router = useRouter();
  const [ready, setReady] = useState(() => typeof window !== "undefined");
  const [authed, setAuthed] = useState(() => Boolean(getToken()));

  useEffect(() => {
    const token = getToken();
    if (!token) {
      router.replace("/login");
    }
    const syncAuth = window.setTimeout(() => {
      setAuthed(Boolean(token));
      setReady(true);
    }, 0);
    return () => window.clearTimeout(syncAuth);
  }, [router]);

  if (!ready) {
    return <div className="card-soft text-win-muted">{t("loading")}</div>;
  }

  if (!authed) {
    return (
      <div className="card-soft space-y-3">
        <p className="font-semibold">{t("loginRequired")}</p>
        <Link href="/login" className="btn-primary inline-flex">
          {t("goLogin")}
        </Link>
      </div>
    );
  }

  return <>{children}</>;
}
