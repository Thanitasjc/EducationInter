import { setRequestLocale } from "next-intl/server";
import { PagePlaceholder } from "@/components/ui/PagePlaceholder";

type Props = { params: Promise<{ locale: string }> };

export default async function Page({ params }: Props) {
  const { locale } = await params;
  setRequestLocale(locale);
  return <PagePlaceholder title="Forgot Password" description="Phase 1 route scaffold — connect to Laravel API next." />;
}