import { setRequestLocale } from "next-intl/server";
import { StudentNotificationsClient } from "@/components/student/StudentNotificationsClient";

type Props = { params: Promise<{ locale: string }> };

export default async function NotificationsPage({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);
  return <StudentNotificationsClient />;
}
