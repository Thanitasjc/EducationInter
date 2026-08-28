"use client";

import { useLocale, useTranslations } from "next-intl";
import { FormEvent, useState } from "react";
import { Link, useRouter } from "@/i18n/navigation";
import { getSocialLoginUrl, register } from "@/lib/api";
import { setToken } from "@/lib/auth";

export default function RegisterPage() {
  const t = useTranslations("auth");
  const locale = useLocale();
  const router = useRouter();
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  async function onSubmit(e: FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setLoading(true);
    setError(null);
    const form = new FormData(e.currentTarget);
    try {
      const res = await register({
        name: form.get("name"),
        email: form.get("email"),
        password: form.get("password"),
        password_confirmation: form.get("password_confirmation"),
        phone: form.get("phone"),
        locale,
      });
      setToken(res.token);
      router.push("/student/dashboard");
    } catch {
      setError(t("registerError"));
    } finally {
      setLoading(false);
    }
  }

  return (
    <section className="section">
      <form onSubmit={onSubmit} className="card-soft mx-auto max-w-md space-y-4">
        <h1 className="text-2xl font-bold">{t("registerTitle")}</h1>
        <input name="name" required placeholder={t("name")} className="input" />
        <input name="email" type="email" required placeholder={t("email")} className="input" />
        <input name="phone" placeholder={t("phone") ?? "Phone"} className="input" />
        <input name="password" type="password" required placeholder={t("password")} className="input" />
        <input name="password_confirmation" type="password" required placeholder={t("confirmPassword")} className="input" />
        <button className="btn-primary w-full" disabled={loading}>
          {loading ? "..." : t("register")}
        </button>
        {error && <p className="text-sm text-red-600">{error}</p>}
        <p className="text-center text-xs text-win-muted">{t("or")}</p>
        <a href={getSocialLoginUrl("facebook")} className="block rounded-xl border px-4 py-3 text-center text-sm font-semibold">
          {t("facebook")}
        </a>
        <p className="text-center text-sm">
          <Link href="/login" className="font-semibold text-win-purple">
            {t("login")}
          </Link>
        </p>
      </form>
    </section>
  );
}
