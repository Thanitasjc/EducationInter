"use client";

import { useRouter } from "@/i18n/navigation";
import { loginWithLineLiff } from "@/lib/api";
import { getToken, setToken } from "@/lib/auth";
import {
  clearLineLiffLoginIntent,
  getLineIdToken,
  initializeLiff,
  isLiffLoginIntent,
  isLineInAppBrowser,
} from "@/lib/liff";
import { useTranslations } from "next-intl";
import { type ReactNode, useEffect, useState } from "react";

type Props = {
  children: ReactNode;
};

export function LiffAuthProvider({ children }: Props) {
  const router = useRouter();
  const t = useTranslations("auth");
  const [checking, setChecking] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    let cancelled = false;

    async function authenticateWithLiff() {
      if (getToken()) {
        return;
      }

      const inLineApp = isLineInAppBrowser();
      const desktopLogin = isLiffLoginIntent() && !inLineApp;

      if (!inLineApp && !desktopLogin) {
        return;
      }

      setChecking(true);
      setError(null);

      try {
        const state = await initializeLiff({
          withLoginOnExternalBrowser: desktopLogin,
        });
        if (cancelled) {
          return;
        }

        if (!state.isInClient && !desktopLogin) {
          setChecking(false);
          return;
        }

        const idToken = await getLineIdToken({ allowExternalBrowser: desktopLogin });
        if (cancelled) {
          return;
        }

        if (!idToken) {
          // liff.login() redirected away; keep the loading state for the return trip.
          return;
        }

        const response = await loginWithLineLiff(idToken);
        if (cancelled) {
          return;
        }

        clearLineLiffLoginIntent();
        setToken(response.token);
        router.replace("/student/dashboard");
      } catch {
        if (!cancelled) {
          clearLineLiffLoginIntent();
          setError(t("liffLoginError"));
          setChecking(false);
        }
      }
    }

    void authenticateWithLiff();

    return () => {
      cancelled = true;
    };
  }, [router, t]);

  if (checking && !getToken()) {
    return (
      <div className="flex min-h-[50vh] flex-col items-center justify-center gap-3 px-4 text-center">
        <p className="text-sm text-win-muted">{t("liffSigningIn")}</p>
        {error && <p className="text-sm text-red-600">{error}</p>}
      </div>
    );
  }

  return <>{children}</>;
}
