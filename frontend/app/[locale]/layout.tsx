import { Prompt } from "next/font/google";
import type { Metadata } from "next";
import { NextIntlClientProvider } from "next-intl";
import { getMessages, setRequestLocale } from "next-intl/server";
import { notFound } from "next/navigation";
import type { ReactNode } from "react";
import { routing } from "@/i18n/routing";
import { buildMetadata } from "@/lib/seo";

const prompt = Prompt({
  subsets: ["thai", "latin"],
  weight: ["400", "500", "600", "700"],
  variable: "--font-prompt",
});

type Props = {
  children: ReactNode;
  params: Promise<{ locale: string }>;
};

export function generateStaticParams() {
  return routing.locales.map((locale) => ({ locale }));
}

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  const { locale } = await params;
  const isTh = locale === "th";
  return buildMetadata({
    locale,
    path: "",
    title: isTh
      ? "Education Interntions | ที่ปรึกษาเรียนต่อต่างประเทศ"
      : "Education Interntions | Study Abroad Consultants",
    description: isTh
      ? "ค้นหามหาวิทยาลัย หลักสูตร ทุนการศึกษา และบริการเรียนต่อครบวงจร"
      : "Find universities, courses, scholarships, and end-to-end study abroad support",
  });
}

export default async function LocaleLayout({ children, params }: Props) {
  const { locale } = await params;

  if (!routing.locales.includes(locale as "th" | "en")) {
    notFound();
  }

  setRequestLocale(locale);
  const messages = await getMessages();

  return (
    <html lang={locale} className={`${prompt.variable} h-full`}>
      <body className="min-h-full bg-background text-foreground antialiased">
        <NextIntlClientProvider messages={messages}>
          {children}
        </NextIntlClientProvider>
      </body>
    </html>
  );
}
