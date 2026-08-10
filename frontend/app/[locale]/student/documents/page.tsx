import { setRequestLocale } from "next-intl/server";
import { StudentAuthGate } from "@/components/student/StudentAuthGate";
import { StudentDocumentsClient } from "@/components/student/StudentDocumentsClient";

type Props = { params: Promise<{ locale: string }> };

export default async function StudentDocumentsPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  return (
    <StudentAuthGate>
      <StudentDocumentsClient />
    </StudentAuthGate>
  );
}
