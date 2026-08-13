"use client";

import { useTranslations } from "next-intl";
import { FormEvent, Suspense, useMemo, useState } from "react";
import { useSearchParams } from "next/navigation";
import { Link, useRouter } from "@/i18n/navigation";
import { resetPassword } from "@/lib/api";
import { setToken } from "@/lib/auth";

function ResetPasswordForm() {
  const t = useTranslations("auth");
  const router = useRouter();
  const searchParams = useSearchParams();
  const emailDefault = useMemo(() => searchParams.get("email") ?? "", [searchParams]);
  const tokenDefault = useMemo(() => searchParams.get("token") ?? "", [searchParams]);
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  async function onSubmit(e: FormEvent<HTMLFormElement>) {
    e.preventDefault();
    if (!tokenDefault) {
      setError(t("missingToken"));
      return;
    }
    setLoading(true);
    setError(null);
    const form = new FormData(e.currentTarget);
    const password = String(form.get("password"));
    const confirm = String(form.get("password_confirmation"));
    if (password !== confirm) {
      setError(t("passwordMismatch"));
      setLoading(false);
      return;
    }
    try {
      const res = await resetPassword({
        email: String(form.get("email")),
        token: tokenDefault,
        password,
        password_confirmation: confirm,
      });
      setToken(res.token);
      router.push("/student/dashboard");
    } catch {
      setError(t("resetError"));
    } finally {
      setLoading(false);
    }
  }

  return (
    <form onSubmit={onSubmit} className="card-soft mx-auto max-w-md space-y-4">
      <h1 className="text-2xl font-bold">{t("resetTitle")}</h1>
      <p className="text-sm text-win-muted">{t("resetHint")}</p>
      <input
        name="email"
        type="email"
        required
        defaultValue={emailDefault}
        placeholder={t("email")}
        className="input"
      />
      <input
        name="password"
        type="password"
        required
        minLength={8}
        placeholder={t("password")}
        className="input"
      />
      <input
        name="password_confirmation"
        type="password"
        required
        minLength={8}
        placeholder={t("confirmPassword")}
        className="input"
      />
      <button className="btn-primary w-full" disabled={loading || !tokenDefault}>
        {loading ? "..." : t("setPassword")}
      </button>
      {!tokenDefault && <p className="text-sm text-red-600">{t("missingToken")}</p>}
      {error && <p className="text-sm text-red-600">{error}</p>}
      <p className="text-center text-sm">
        <Link href="/forgot-password" className="font-semibold text-win-purple">
          {t("forgotTitle")}
        </Link>
        {" · "}
        <Link href="/login" className="font-semibold text-win-purple">
          {t("backToLogin")}
        </Link>
      </p>
    </form>
  );
}

export default function ResetPasswordPage() {
  return (
    <section className="section">
      <Suspense fallback={<div className="card-soft mx-auto max-w-md text-win-muted">...</div>}>
        <ResetPasswordForm />
      </Suspense>
    </section>
  );
}
