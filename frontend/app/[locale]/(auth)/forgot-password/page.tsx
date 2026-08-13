"use client";

import { useTranslations } from "next-intl";
import { FormEvent, useState } from "react";
import { Link } from "@/i18n/navigation";
import { forgotPassword } from "@/lib/api";

export default function ForgotPasswordPage() {
  const t = useTranslations("auth");
  const [status, setStatus] = useState<"idle" | "loading" | "sent" | "error">("idle");

  async function onSubmit(e: FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setStatus("loading");
    const form = new FormData(e.currentTarget);
    try {
      await forgotPassword(String(form.get("email")));
      setStatus("sent");
    } catch {
      setStatus("error");
    }
  }

  return (
    <section className="section">
      <form onSubmit={onSubmit} className="card-soft mx-auto max-w-md space-y-4">
        <h1 className="text-2xl font-bold">{t("forgotTitle")}</h1>
        <p className="text-sm text-win-muted">{t("forgotHint")}</p>
        <input name="email" type="email" required placeholder={t("email")} className="input" />
        <button className="btn-primary w-full" disabled={status === "loading" || status === "sent"}>
          {status === "loading" ? "..." : t("sendResetLink")}
        </button>
        {status === "sent" && <p className="text-sm text-win-blue">{t("forgotSent")}</p>}
        {status === "error" && <p className="text-sm text-red-600">{t("forgotError")}</p>}
        <p className="text-center text-sm">
          <Link href="/login" className="font-semibold text-win-purple">
            {t("backToLogin")}
          </Link>
        </p>
      </form>
    </section>
  );
}
