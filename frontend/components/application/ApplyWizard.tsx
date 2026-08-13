"use client";

import { useLocale, useTranslations } from "next-intl";
import { FormEvent, useMemo, useState, type ReactNode } from "react";
import { Link } from "@/i18n/navigation";
import { submitApplication } from "@/lib/api";
import type { Country, Course, University } from "@/types/catalog";
import { localized } from "@/lib/utils";

type Props = {
  countries: Country[];
  universities: University[];
  courses: Course[];
};

type FormState = {
  name: string;
  email: string;
  phone: string;
  country_id: string;
  university_id: string;
  course_id: string;
  intake: string;
  education_level: string;
  school_name: string;
  gpa: string;
  documents_note: string;
  message: string;
};

const initial: FormState = {
  name: "",
  email: "",
  phone: "",
  country_id: "",
  university_id: "",
  course_id: "",
  intake: "",
  education_level: "",
  school_name: "",
  gpa: "",
  documents_note: "",
  message: "",
};

export function ApplyWizard({ countries, universities, courses }: Props) {
  const t = useTranslations("apply");
  const locale = useLocale();
  const [step, setStep] = useState(1);
  const [form, setForm] = useState<FormState>(initial);
  const [status, setStatus] = useState<"idle" | "loading" | "success" | "error">("idle");
  const [resultNo, setResultNo] = useState<string | null>(null);

  const universitiesForCountry = useMemo(() => {
    if (!form.country_id) return universities;
    return universities.filter(
      (u) =>
        String(u.country_id ?? "") === form.country_id ||
        String(u.country?.id ?? "") === form.country_id,
    );
  }, [universities, form.country_id]);

  const coursesForUniversity = useMemo(() => {
    if (!form.university_id) return courses;
    return courses.filter(
      (c) =>
        String(c.university_id ?? "") === form.university_id ||
        String(c.university?.id ?? "") === form.university_id,
    );
  }, [courses, form.university_id]);

  function update<K extends keyof FormState>(key: K, value: FormState[K]) {
    setForm((prev) => ({ ...prev, [key]: value }));
  }

  async function submit() {
    if (step < 6 || status === "loading") return;

    setStatus("loading");
    try {
      const res = await submitApplication({
        name: form.name,
        email: form.email,
        phone: form.phone || null,
        locale,
        country_id: form.country_id ? Number(form.country_id) : null,
        university_id: form.university_id ? Number(form.university_id) : null,
        course_id: form.course_id ? Number(form.course_id) : null,
        intake: form.intake || null,
        education_level: form.education_level || null,
        education_history: [
          {
            school: form.school_name || null,
            gpa: form.gpa || null,
          },
        ],
        documents_note: form.documents_note || null,
        message: form.message || null,
      });
      setResultNo(res.data.application_no);
      setStatus("success");
    } catch {
      setStatus("error");
    }
  }

  function onSubmit(e: FormEvent) {
    // Prevent implicit form submit (e.g. Enter). Only the Submit button may send.
    e.preventDefault();
  }

  if (status === "success" && resultNo) {
    return (
      <div className="card-soft mx-auto max-w-2xl space-y-4 text-center">
        <h1 className="text-2xl font-bold text-win-ink">{t("successTitle")}</h1>
        <p className="text-win-muted">{t("successBody")}</p>
        <p className="text-lg font-semibold text-win-purple">
          {t("applicationNo")}: {resultNo}
        </p>
        <div className="flex justify-center gap-3">
          <Link href="/student/dashboard" className="btn-primary">
            {t("goDashboard")}
          </Link>
          <Link href="/" className="rounded-xl border border-black/10 px-5 py-3 text-sm font-semibold">
            {t("backHome")}
          </Link>
        </div>
      </div>
    );
  }

  return (
    <form onSubmit={onSubmit} className="mx-auto max-w-3xl space-y-6">
      <div>
        <h1 className="section-title">{t("title")}</h1>
        <p className="section-subtitle">{t("subtitle")}</p>
      </div>

      <div className="flex flex-wrap gap-2">
        {[1, 2, 3, 4, 5, 6].map((n) => (
          <div
            key={n}
            className={`rounded-full px-3 py-1 text-xs font-semibold ${
              n === step
                ? "bg-win-purple text-white"
                : n < step
                  ? "bg-win-sky text-win-blue"
                  : "bg-black/5 text-win-muted"
            }`}
          >
            {t(`step${n}` as "step1")}
          </div>
        ))}
      </div>

      <div className="card-soft space-y-4">
        {step === 1 && (
          <>
            <Field label={t("name")}>
              <input required className="input" value={form.name} onChange={(e) => update("name", e.target.value)} />
            </Field>
            <Field label={t("email")}>
              <input required type="email" className="input" value={form.email} onChange={(e) => update("email", e.target.value)} />
            </Field>
            <Field label={t("phone")}>
              <input className="input" value={form.phone} onChange={(e) => update("phone", e.target.value)} />
            </Field>
          </>
        )}

        {step === 2 && (
          <Field label={t("country")}>
            <select
              required
              className="input"
              value={form.country_id}
              onChange={(e) => {
                update("country_id", e.target.value);
                update("university_id", "");
                update("course_id", "");
              }}
            >
              <option value="">{t("select")}</option>
              {countries.map((c) => (
                <option key={c.id} value={c.id}>
                  {localized(c, locale, "name")}
                </option>
              ))}
            </select>
          </Field>
        )}

        {step === 3 && (
          <Field label={t("university")}>
            <select
              className="input"
              value={form.university_id}
              onChange={(e) => {
                update("university_id", e.target.value);
                update("course_id", "");
              }}
            >
              <option value="">{t("select")}</option>
              {universitiesForCountry.map((u) => (
                <option key={u.id} value={u.id}>
                  {localized(u, locale, "name")}
                </option>
              ))}
            </select>
          </Field>
        )}

        {step === 4 && (
          <>
            <Field label={t("course")}>
              <select
                className="input"
                value={form.course_id}
                onChange={(e) => update("course_id", e.target.value)}
              >
                <option value="">{t("select")}</option>
                {coursesForUniversity.map((c) => (
                  <option key={c.id} value={c.id}>
                    {localized(c, locale, "name")}
                  </option>
                ))}
              </select>
            </Field>
            <Field label={t("intake")}>
              <input
                className="input"
                value={form.intake}
                onChange={(e) => update("intake", e.target.value)}
                placeholder="September 2026"
              />
            </Field>
          </>
        )}

        {step === 5 && (
          <>
            <Field label={t("educationLevel")}>
              <input className="input" value={form.education_level} onChange={(e) => update("education_level", e.target.value)} />
            </Field>
            <Field label={t("school")}>
              <input className="input" value={form.school_name} onChange={(e) => update("school_name", e.target.value)} />
            </Field>
            <Field label={t("gpa")}>
              <input className="input" value={form.gpa} onChange={(e) => update("gpa", e.target.value)} />
            </Field>
          </>
        )}

        {step === 6 && (
          <>
            <Field label={t("documentsNote")}>
              <textarea
                className="input"
                rows={3}
                value={form.documents_note}
                onChange={(e) => update("documents_note", e.target.value)}
              />
            </Field>
            <Field label={t("message")}>
              <textarea
                className="input"
                rows={3}
                value={form.message}
                onChange={(e) => update("message", e.target.value)}
              />
            </Field>
            <div className="rounded-xl bg-win-sky/60 p-4 text-sm text-win-ink">
              <p className="font-semibold">{t("review")}</p>
              <p className="mt-2">{form.name} · {form.email}</p>
              <p>{form.phone}</p>
            </div>
          </>
        )}

        <div className="flex justify-between gap-3 pt-2">
          <button
            type="button"
            onClick={() => setStep((s) => Math.max(1, s - 1))}
            disabled={step === 1}
            className="rounded-xl border border-black/10 px-5 py-3 text-sm font-semibold disabled:opacity-40"
          >
            {t("back")}
          </button>
          {step < 6 ? (
            <button
              key="next"
              type="button"
              onClick={() => setStep((s) => Math.min(6, s + 1))}
              className="btn-primary"
            >
              {t("next")}
            </button>
          ) : (
            <button
              key="submit"
              type="button"
              className="btn-primary"
              disabled={status === "loading"}
              onClick={() => void submit()}
            >
              {status === "loading" ? t("submitting") : t("submit")}
            </button>
          )}
        </div>
        {status === "error" && <p className="text-sm text-red-600">{t("error")}</p>}
      </div>
    </form>
  );
}

function Field({ label, children }: { label: string; children: ReactNode }) {
  return (
    <label className="block space-y-1.5">
      <span className="text-sm font-semibold text-win-ink">{label}</span>
      {children}
    </label>
  );
}
