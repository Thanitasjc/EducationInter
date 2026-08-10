"use client";

import { useTranslations } from "next-intl";
import { useRouter } from "@/i18n/navigation";
import { clearToken } from "@/lib/auth";

export function StudentLogoutButton() {
  const t = useTranslations("student");
  const router = useRouter();

  return (
    <button
      type="button"
      onClick={() => {
        clearToken();
        router.push("/login");
      }}
      className="mt-4 w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-red-600 hover:bg-red-50"
    >
      {t("logout")}
    </button>
  );
}
