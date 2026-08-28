"use client";

import { usePathname, useRouter } from "@/i18n/navigation";
import { loginWithLineLiff } from "@/lib/api";
import { AUTH_CHANGE_EVENT, getToken, setToken } from "@/lib/auth";
import {
  cleanupStaleLiffUrl,
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
  const pathname = usePathname();
  const t = useTranslations("auth");
  const [checking, setChecking] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    function onAuthChange() {
      if (!getToken()) {
        setChecking(false);
        setError(null);
      }
    }

    window.addEventListener(AUTH_CHANGE_EVENT, onAuthChange);
    return () => window.removeEventListener(AUTH_CHANGE_EVENT, onAuthChange);
  }, []);

  useEffect(() => {
    let cancelled = false;

    async function authenticateWithLiff() {
      cleanupStaleLiffUrl();

      if (getToken()) {
        setChecking(false);
        return;
      }

      const inLineApp = isLineInAppBrowser();
      const desktopLogin = isLiffLoginIntent() && !inLineApp;

      if (!inLineApp && !desktopLogin) {
        setChecking(false);
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
          window.setTimeout(() => {
            if (!cancelled && !getToken()) {
              clearLineLiffLoginIntent();
              setChecking(false);
            }
          }, 2500);
          return;
        }

        const response = await loginWithLineLiff(idToken);
        if (cancelled) {
          return;
        }

        clearLineLiffLoginIntent();
        cleanupStaleLiffUrl();
        setToken(response.token);
        setChecking(false);
        router.replace("/student/dashboard");
      } catch {
        if (!cancelled) {
          clearLineLiffLoginIntent();
          cleanupStaleLiffUrl();
          setError(t("liffLoginError"));
          setChecking(false);
        }
      }
    }

    void authenticateWithLiff();

    return () => {
      cancelled = true;
    };
  }, [router, pathname, t]);

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
