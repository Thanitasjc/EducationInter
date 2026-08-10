import type { MetadataRoute } from "next";
import { API_URL } from "@/lib/api";
import { absoluteUrl } from "@/lib/seo";

type SitemapPayload = {
  countries: string[];
  universities: string[];
  courses: string[];
  scholarships: string[];
  programs?: string[];
  posts: string[];
  events: string[];
  static: string[];
};

async function fetchSitemap(): Promise<SitemapPayload | null> {
  try {
    // Don't hang Vercel builds when Render is cold / down.
    const res = await fetch(`${API_URL}/sitemap`, {
      next: { revalidate: 3600 },
      signal: AbortSignal.timeout(8_000),
    });
    if (!res.ok) return null;
    return (await res.json()) as SitemapPayload;
  } catch {
    return null;
  }
}

export default async function sitemap(): Promise<MetadataRoute.Sitemap> {
  const data = await fetchSitemap();
  const locales = ["th", "en"] as const;
  const entries: MetadataRoute.Sitemap = [];

  const pushPath = (path: string, priority = 0.7) => {
    for (const locale of locales) {
      const normalized = path ? `/${path.replace(/^\//, "")}` : "";
      entries.push({
        url: absoluteUrl(`/${locale}${normalized}`),
        lastModified: new Date(),
        changeFrequency: "weekly",
        priority,
      });
    }
  };

  if (!data) {
    pushPath("", 1);
    pushPath("universities");
    pushPath("scholarships");
    pushPath("blog");
    return entries;
  }

  for (const path of data.static) {
    pushPath(path, path === "" ? 1 : 0.8);
  }

  for (const slug of data.countries) pushPath(`countries/${slug}`);
  for (const slug of data.universities) pushPath(`universities/${slug}`);
  for (const slug of data.courses) pushPath(`courses/${slug}`);
  for (const slug of data.scholarships) pushPath(`scholarships/${slug}`);
  for (const slug of data.programs ?? []) pushPath(`learn-language/${slug}`);
  for (const slug of data.posts) pushPath(`blog/${slug}`, 0.6);
  for (const slug of data.events) pushPath(`events/${slug}`, 0.6);

  return entries;
}
