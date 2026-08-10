import { setRequestLocale } from "next-intl/server";
import { StudentAuthGate } from "@/components/student/StudentAuthGate";
import { StudentApplicationsClient } from "@/components/student/StudentApplicationsClient";

type Props = { params: Promise<{ locale: string }> };

export default async function StudentApplicationsPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  return (
    <StudentAuthGate>
      <StudentApplicationsClient />
    </StudentAuthGate>
  );
}
