"use client";

import { useTranslations } from "next-intl";
import { FormEvent, useState } from "react";
import { Link, useRouter } from "@/i18n/navigation";
import { getSocialLoginUrl, login } from "@/lib/api";
import { setToken } from "@/lib/auth";

export default function LoginPage() {
  const t = useTranslations("auth");
  const router = useRouter();
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  async function onSubmit(e: FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setLoading(true);
    setError(null);
    const form = new FormData(e.currentTarget);
    try {
      const res = await login(String(form.get("email")), String(form.get("password")));
      setToken(res.token);
      router.push("/student/dashboard");
    } catch {
      setError(t("loginError"));
    } finally {
      setLoading(false);
    }
  }

  return (
    <section className="section">
      <form onSubmit={onSubmit} className="card-soft mx-auto max-w-md space-y-4">
        <h1 className="text-2xl font-bold">{t("loginTitle")}</h1>
        <input name="email" type="email" required placeholder={t("email")} className="input" />
        <input name="password" type="password" required placeholder={t("password")} className="input" />
        <p className="text-right text-sm">
          <Link href="/forgot-password" className="font-semibold text-win-purple">
            {t("forgotTitle")}
          </Link>
        </p>
        <button className="btn-primary w-full" disabled={loading}>
          {loading ? "..." : t("login")}
        </button>
        {error && <p className="text-sm text-red-600">{error}</p>}
        <p className="text-center text-xs text-win-muted">{t("or")}</p>
        <a href={getSocialLoginUrl("facebook")} className="block rounded-xl border px-4 py-3 text-center text-sm font-semibold">
          {t("facebook")}
        </a>
        <a href={getSocialLoginUrl("line")} className="block rounded-xl border px-4 py-3 text-center text-sm font-semibold">
          {t("line")}
        </a>
        <p className="text-center text-sm">
          <Link href="/register" className="font-semibold text-win-purple">
            {t("register")}
          </Link>
        </p>
        <p className="text-center text-xs text-win-muted">
          Demo: student@wineducation.local / password
        </p>
      </form>
    </section>
  );
}
