import { setRequestLocale } from "next-intl/server";
import { StudentAuthGate } from "@/components/student/StudentAuthGate";
import { StudentDashboardClient } from "@/components/student/StudentDashboardClient";

type Props = { params: Promise<{ locale: string }> };

export default async function StudentDashboardPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  return (
    <StudentAuthGate>
      <StudentDashboardClient />
    </StudentAuthGate>
  );
}
