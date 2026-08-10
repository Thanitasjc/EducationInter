"use client";

import { useLocale, useTranslations } from "next-intl";
import { FormEvent, useEffect, useState } from "react";
import { getStudentDocuments, uploadStudentDocument } from "@/lib/api";
import { localized } from "@/lib/utils";

export function StudentDocumentsClient() {
  const t = useTranslations("student");
  const locale = useLocale();
  const [docs, setDocs] = useState<Array<Record<string, unknown>>>([]);
  const [types, setTypes] = useState<Array<Record<string, unknown>>>([]);
  const [loading, setLoading] = useState(true);
  const [uploading, setUploading] = useState(false);
  const [message, setMessage] = useState<string | null>(null);

  async function load() {
    setLoading(true);
    try {
      const res = await getStudentDocuments();
      setDocs(res.data);
      setTypes(res.types);
    } catch {
      setDocs([]);
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    load();
  }, []);

  async function onUpload(e: FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setUploading(true);
    setMessage(null);
    const form = new FormData(e.currentTarget);
    try {
      await uploadStudentDocument(form);
      setMessage(t("uploadSuccess"));
      e.currentTarget.reset();
      await load();
    } catch {
      setMessage(t("uploadError"));
    } finally {
      setUploading(false);
    }
  }

  if (loading) {
    return <div className="card-soft text-win-muted">{t("loading")}</div>;
  }

  return (
    <div className="space-y-6">
      <h1 className="text-3xl font-bold">{t("documents")}</h1>

      <form onSubmit={onUpload} className="card-soft space-y-3">
        <h2 className="font-semibold">{t("uploadTitle")}</h2>
        <select name="document_type_id" className="input">
          <option value="">{t("selectType")}</option>
          {types.map((type) => (
            <option key={String(type.id)} value={String(type.id)}>
              {localized(type, locale, "name")}
            </option>
          ))}
        </select>
        <input name="name" className="input" placeholder={t("docName")} />
        <input name="file" type="file" required accept=".pdf,.jpg,.jpeg,.png,.webp" className="input" />
        <button type="submit" className="btn-primary" disabled={uploading}>
          {uploading ? t("uploading") : t("upload")}
        </button>
        {message && <p className="text-sm text-win-blue">{message}</p>}
      </form>

      <div className="space-y-3">
        {docs.length === 0 && <div className="card-soft text-win-muted">{t("noDocuments")}</div>}
        {docs.map((doc) => (
          <div key={String(doc.id)} className="card-soft flex items-center justify-between gap-3">
            <div>
              <p className="font-semibold">{String(doc.name)}</p>
              <p className="text-sm text-win-muted">{String(doc.path)}</p>
            </div>
            <span className="rounded-full bg-black/5 px-3 py-1 text-xs font-semibold uppercase">
              {String(doc.status)}
            </span>
          </div>
        ))}
      </div>
    </div>
  );
}
