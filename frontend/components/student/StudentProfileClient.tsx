"use client";

import { useTranslations } from "next-intl";
import { FormEvent, useEffect, useState } from "react";
import { getStudentDashboard, updateStudentProfile } from "@/lib/api";

export function StudentProfileClient() {
  const t = useTranslations("student");
  const [name, setName] = useState("");
  const [phone, setPhone] = useState("");
  const [locale, setLocale] = useState("th");
  const [nationality, setNationality] = useState("");
  const [educationLevel, setEducationLevel] = useState("");
  const [message, setMessage] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getStudentDashboard()
      .then((data) => {
        const user = data.user as { name?: string; phone?: string; locale?: string };
        const student = data.student as {
          nationality?: string;
          education_level?: string;
          preferred_locale?: string;
        };
        setName(user.name ?? "");
        setPhone(user.phone ?? "");
        setLocale(user.locale ?? student.preferred_locale ?? "th");
        setNationality(student.nationality ?? "");
        setEducationLevel(student.education_level ?? "");
      })
      .finally(() => setLoading(false));
  }, []);

  async function onSubmit(e: FormEvent) {
    e.preventDefault();
    setMessage(null);
    try {
      await updateStudentProfile({
        name,
        phone,
        locale,
        nationality,
        education_level: educationLevel,
      });
      setMessage(t("profileSaved"));
    } catch {
      setMessage(t("profileError"));
    }
  }

  if (loading) {
    return <div className="card-soft text-win-muted">{t("loading")}</div>;
  }

  return (
    <form onSubmit={onSubmit} className="card-soft mx-auto max-w-xl space-y-4">
      <h1 className="text-3xl font-bold">{t("profile")}</h1>
      <input className="input" value={name} onChange={(e) => setName(e.target.value)} placeholder={t("name")} />
      <input className="input" value={phone} onChange={(e) => setPhone(e.target.value)} placeholder={t("phone")} />
      <select className="input" value={locale} onChange={(e) => setLocale(e.target.value)}>
        <option value="th">ไทย</option>
        <option value="en">English</option>
      </select>
      <input className="input" value={nationality} onChange={(e) => setNationality(e.target.value)} placeholder={t("nationality")} />
      <input className="input" value={educationLevel} onChange={(e) => setEducationLevel(e.target.value)} placeholder={t("educationLevel")} />
      <button type="submit" className="btn-primary">{t("saveProfile")}</button>
      {message && <p className="text-sm text-win-blue">{message}</p>}
    </form>
  );
}
