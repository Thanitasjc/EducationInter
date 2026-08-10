import type { Metadata } from "next";

const SITE_URL = process.env.NEXT_PUBLIC_SITE_URL ?? "http://localhost:3000";
const SITE_NAME = "Education Interntions";

export type SeoPayload = {
  meta_title?: string | null;
  meta_description?: string | null;
  og_image?: string | null;
  canonical_url?: string | null;
  robots?: string | null;
  schema_json?: Record<string, unknown> | null;
  keywords?: string | null;
};

export function absoluteUrl(path = ""): string {
  return `${SITE_URL.replace(/\/$/, "")}${path.startsWith("/") ? path : `/${path}`}`;
}

export function buildMetadata({
  locale,
  path,
  title,
  description,
  seo,
  image,
}: {
  locale: string;
  path: string;
  title: string;
  description?: string;
  seo?: SeoPayload | null;
  image?: string | null;
}): Metadata {
  const finalTitle = seo?.meta_title || title;
  const finalDescription =
    seo?.meta_description || description || "Study abroad guidance with Education Interntions";
  const ogImage = seo?.og_image || image || undefined;
  const canonical = seo?.canonical_url || absoluteUrl(`/${locale}${path}`);

  return {
    title: finalTitle,
    description: finalDescription,
    robots: seo?.robots || "index,follow",
    alternates: {
      canonical,
      languages: {
        th: absoluteUrl(`/th${path}`),
        en: absoluteUrl(`/en${path}`),
      },
    },
    openGraph: {
      title: finalTitle,
      description: finalDescription,
      url: canonical,
      siteName: SITE_NAME,
      locale,
      type: "website",
      images: ogImage ? [{ url: ogImage }] : undefined,
    },
    twitter: {
      card: "summary_large_image",
      title: finalTitle,
      description: finalDescription,
      images: ogImage ? [ogImage] : undefined,
    },
  };
}
