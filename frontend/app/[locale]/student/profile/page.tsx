import { setRequestLocale } from "next-intl/server";
import { StudentAuthGate } from "@/components/student/StudentAuthGate";
import { StudentProfileClient } from "@/components/student/StudentProfileClient";

type Props = { params: Promise<{ locale: string }> };

export default async function StudentProfilePage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  return (
    <StudentAuthGate>
      <StudentProfileClient />
    </StudentAuthGate>
  );
}
