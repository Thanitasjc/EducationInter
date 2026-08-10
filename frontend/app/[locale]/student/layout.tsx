import type { ReactNode } from "react";
import { getTranslations } from "next-intl/server";
import { Link } from "@/i18n/navigation";
import { WebsiteShell } from "@/components/layout/WebsiteShell";
import { StudentLogoutButton } from "@/components/student/StudentLogoutButton";

const menus = [
  ["dashboard", "/student/dashboard"],
  ["profile", "/student/profile"],
  ["applications", "/student/applications"],
  ["documents", "/student/documents"],
  ["appointments", "/student/appointments"],
  ["notifications", "/student/notifications"],
] as const;

export default async function StudentLayout({ children }: { children: ReactNode }) {
  const t = await getTranslations("student");

  return (
    <WebsiteShell>
      <div className="mx-auto grid max-w-7xl gap-6 px-4 py-8 md:grid-cols-[220px_1fr] md:px-8">
        <aside className="card-soft h-fit space-y-2">
          {menus.map(([key, href]) => (
            <Link
              key={href}
              href={href}
              className="block rounded-lg px-3 py-2 text-sm font-medium text-win-ink hover:bg-win-sky"
            >
              {t(key)}
            </Link>
          ))}
          <StudentLogoutButton />
        </aside>
        <div>{children}</div>
      </div>
    </WebsiteShell>
  );
}
