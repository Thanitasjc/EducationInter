"use client";

import { useLocale, useTranslations } from "next-intl";
import { FormEvent, useEffect, useState } from "react";
import { getStudentDocuments, uploadStudentDocument } from "@/lib/api";
import { localized } from "@/lib/utils";

type ChecklistItem = {
  type: Record<string, unknown>;
  document: Record<string, unknown> | null;
  status: string;
};

export function StudentDocumentsClient() {
  const t = useTranslations("student");
  const locale = useLocale();
  const [docs, setDocs] = useState<Array<Record<string, unknown>>>([]);
  const [types, setTypes] = useState<Array<Record<string, unknown>>>([]);
  const [checklist, setChecklist] = useState<ChecklistItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [uploading, setUploading] = useState(false);
  const [message, setMessage] = useState<string | null>(null);

  async function load() {
    setLoading(true);
    try {
      const res = await getStudentDocuments();
      setDocs(res.data);
      setTypes(res.types);
      setChecklist(res.checklist ?? []);
    } catch {
      setDocs([]);
      setChecklist([]);
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    const loadInitialData = async () => {
      setLoading(true);
      try {
        const res = await getStudentDocuments();
        setDocs(res.data);
        setTypes(res.types);
        setChecklist(res.checklist ?? []);
      } catch {
        setDocs([]);
        setChecklist([]);
      } finally {
        setLoading(false);
      }
    };

    void loadInitialData();
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

      {checklist.length > 0 && (
        <div className="card-soft space-y-3">
          <h2 className="font-semibold">{t("checklistTitle")}</h2>
          <ul className="space-y-2">
            {checklist.map((item) => {
              const type = item.type;
              const required = Boolean(type.is_required);
              const statusLabel =
                item.status === "approved"
                  ? t("statusApproved")
                  : item.status === "rejected"
                    ? t("statusRejected")
                    : item.status === "pending"
                      ? t("statusPending")
                      : t("statusMissing");
              const tone =
                item.status === "approved"
                  ? "bg-emerald-50 text-emerald-700"
                  : item.status === "rejected"
                    ? "bg-red-50 text-red-700"
                    : item.status === "pending"
                      ? "bg-amber-50 text-amber-800"
                      : "bg-black/5 text-win-muted";
              return (
                <li
                  key={String(type.id)}
                  className="flex flex-wrap items-center justify-between gap-2 rounded-xl border border-black/5 px-3 py-2"
                >
                  <div>
                    <p className="text-sm font-semibold">
                      {localized(type, locale, "name")}
                      {required ? (
                        <span className="ml-2 text-xs font-medium text-win-purple">
                          ({t("required")})
                        </span>
                      ) : null}
                    </p>
                    {item.document?.name ? (
                      <p className="text-xs text-win-muted">{String(item.document.name)}</p>
                    ) : null}
                  </div>
                  <span className={`rounded-full px-3 py-1 text-xs font-semibold ${tone}`}>
                    {statusLabel}
                  </span>
                </li>
              );
            })}
          </ul>
        </div>
      )}

      <form onSubmit={onUpload} className="card-soft space-y-3">
        <h2 className="font-semibold">{t("uploadTitle")}</h2>
        <select name="document_type_id" className="input">
          <option value="">{t("selectType")}</option>
          {types.map((type) => (
            <option key={String(type.id)} value={String(type.id)}>
              {localized(type, locale, "name")}
              {type.is_required ? ` (${t("required")})` : ""}
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
        <h2 className="font-semibold">{t("uploadedList")}</h2>
        {docs.length === 0 && <div className="card-soft text-win-muted">{t("noDocuments")}</div>}
        {docs.map((doc) => (
          <div key={String(doc.id)} className="card-soft flex items-center justify-between gap-3">
            <div>
              <p className="font-semibold">{String(doc.name)}</p>
              <p className="text-sm text-win-muted capitalize">{String(doc.status)}</p>
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
