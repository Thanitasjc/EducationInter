"use client";

import { useRouter } from "@/i18n/navigation";
import { loginWithLineLiff } from "@/lib/api";
import { getToken, setToken } from "@/lib/auth";
import { getLineIdToken, initializeLiff } from "@/lib/liff";
import { useTranslations } from "next-intl";
import { type ReactNode, useEffect, useState } from "react";

type Props = {
  children: ReactNode;
};

export function LiffAuthProvider({ children }: Props) {
  const router = useRouter();
  const t = useTranslations("auth");
  const [checking, setChecking] = useState(false);

  useEffect(() => {
    let cancelled = false;

    async function authenticateWithLiff() {
      if (getToken()) {
        return;
      }

      setChecking(true);

      try {
        const state = await initializeLiff();
        if (cancelled) {
          return;
        }

        if (!state.isInClient) {
          setChecking(false);
          return;
        }

        const idToken = await getLineIdToken();
        if (cancelled || !idToken) {
          return;
        }

        const response = await loginWithLineLiff(idToken);
        if (cancelled) {
          return;
        }

        setToken(response.token);
        router.replace("/student/dashboard");
      } catch {
        if (!cancelled) {
          setChecking(false);
        }
      }
    }

    void authenticateWithLiff();

    return () => {
      cancelled = true;
    };
  }, [router]);

  if (checking && !getToken()) {
    return (
      <div className="flex min-h-[50vh] items-center justify-center px-4">
        <p className="text-sm text-win-muted">{t("liffSigningIn")}</p>
      </div>
    );
  }

  return <>{children}</>;
}
