"use client";

import { useTranslations } from "next-intl";
import { FormEvent, useState } from "react";
import { createLead } from "@/lib/api";

export function LeadForm() {
  const t = useTranslations("contact");
  const [status, setStatus] = useState<"idle" | "loading" | "success" | "error">(
    "idle",
  );

  async function onSubmit(e: FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setStatus("loading");

    const form = new FormData(e.currentTarget);
    try {
      await createLead({
        name: form.get("name"),
        email: form.get("email"),
        phone: form.get("phone"),
        message: form.get("message"),
        source: "website",
      });
      setStatus("success");
      e.currentTarget.reset();
    } catch {
      setStatus("error");
    }
  }

  return (
    <form onSubmit={onSubmit} className="card-soft mx-auto max-w-xl space-y-4">
      <h1 className="text-2xl font-bold text-win-ink">{t("title")}</h1>
      <input
        name="name"
        required
        placeholder={t("name")}
        className="w-full rounded-xl border border-black/10 px-4 py-3"
      />
      <input
        name="email"
        type="email"
        placeholder={t("email")}
        className="w-full rounded-xl border border-black/10 px-4 py-3"
      />
      <input
        name="phone"
        placeholder={t("phone")}
        className="w-full rounded-xl border border-black/10 px-4 py-3"
      />
      <textarea
        name="message"
        rows={4}
        placeholder={t("message")}
        className="w-full rounded-xl border border-black/10 px-4 py-3"
      />
      <button type="submit" className="btn-primary w-full" disabled={status === "loading"}>
        {t("submit")}
      </button>
      {status === "success" && (
        <p className="text-sm text-green-700">{t("success")}</p>
      )}
      {status === "error" && (
        <p className="text-sm text-red-600">Unable to submit. Please try again.</p>
      )}
    </form>
  );
}
