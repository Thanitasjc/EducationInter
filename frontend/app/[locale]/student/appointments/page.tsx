import { setRequestLocale } from "next-intl/server";
import { StudentAuthGate } from "@/components/student/StudentAuthGate";
import { StudentAppointmentsClient } from "@/components/student/StudentAppointmentsClient";

type Props = { params: Promise<{ locale: string }> };

export default async function StudentAppointmentsPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);

  return (
    <StudentAuthGate>
      <StudentAppointmentsClient />
    </StudentAuthGate>
  );
}
