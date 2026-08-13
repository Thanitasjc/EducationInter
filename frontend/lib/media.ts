const FALLBACK_COVERS: Record<string, string> = {
  uk: "https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?auto=format&fit=crop&w=1200&q=80",
  australia:
    "https://images.unsplash.com/photo-1506973035872-a4ec16b8e8d9?auto=format&fit=crop&w=1200&q=80",
  usa: "https://images.unsplash.com/photo-1485738422979-f5c462d49f74?auto=format&fit=crop&w=1200&q=80",
  canada:
    "https://images.unsplash.com/photo-1517935706615-2717063c0395?auto=format&fit=crop&w=1200&q=80",
  "new-zealand":
    "https://images.unsplash.com/photo-1469521669194-babb45599dbd?auto=format&fit=crop&w=1200&q=80",
  ireland:
    "https://images.unsplash.com/photo-1590080875515-8a3a8dc5735e?auto=format&fit=crop&w=1200&q=80",
  singapore:
    "https://images.unsplash.com/photo-1525625293386-3f8f99389edd?auto=format&fit=crop&w=1200&q=80",
  "university-of-manchester":
    "https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1200&q=80",
  "university-college-london":
    "https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1200&q=80",
  "university-of-melbourne":
    "https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1200&q=80",
  "university-of-toronto":
    "https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1200&q=80",
  "msc-management":
    "https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?auto=format&fit=crop&w=1200&q=80",
  "msc-computer-science":
    "https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1200&q=80",
  "master-of-engineering":
    "https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&w=1200&q=80",
  "bachelor-of-commerce":
    "https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&w=1200&q=80",
  "university-application":
    "https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=1200&q=80",
  visa: "https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=1200&q=80",
  accommodation:
    "https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1200&q=80",
  ielts:
    "https://images.unsplash.com/photo-1456513080080-7e9b1b0c5f2f?auto=format&fit=crop&w=1200&q=80",
  "pre-departure":
    "https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=1200&q=80",
  consultation:
    "https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=1200&q=80",
  "why-advisors":
    "https://images.unsplash.com/photo-1521737711867-e3b97375f902?auto=format&fit=crop&w=1200&q=80",
  "why-network":
    "https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1200&q=80",
  "why-funding":
    "https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&w=1200&q=80",
  "why-support":
    "https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=1200&q=80",
  "review-manchester":
    "https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1200&q=80",
  "review-melbourne":
    "https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1200&q=80",
  "review-toronto":
    "https://images.unsplash.com/photo-1517935706615-2717063c0395?auto=format&fit=crop&w=1200&q=80",
  "why-study-uk-2026":
    "https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?auto=format&fit=crop&w=1200&q=80",
  "how-to-prepare-scholarship":
    "https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=1200&q=80",
  "student-visa-checklist":
    "https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&w=1200&q=80",
  "manchester-undergraduate-scholarship":
    "https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1200&q=80",
  "manchester-masters-scholarship":
    "https://images.unsplash.com/photo-1541339907198-e08756dedf3f?auto=format&fit=crop&w=1200&q=80",
  "ucl-global-undergraduate":
    "https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1200&q=80",
  "ucl-global-masters":
    "https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1200&q=80",
  "melbourne-international-undergraduate":
    "https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1200&q=80",
  "melbourne-international-masters":
    "https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?auto=format&fit=crop&w=1200&q=80",
  "toronto-undergraduate-scholarship":
    "https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1200&q=80",
  "toronto-masters-scholarship":
    "https://images.unsplash.com/photo-1517935706615-2717063c0395?auto=format&fit=crop&w=1200&q=80",
  "uk-education-fair-bkk":
    "https://images.unsplash.com/photo-1540575467063-178a50c2df87?auto=format&fit=crop&w=1200&q=80",
  "scholarship-workshop":
    "https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1200&q=80",
  "ielts-strategy-session":
    "https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80",
  cta: "https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1600&q=80",
  "bachelor-pathways":
    "https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1600&q=80",
};

export const DEFAULT_COVER =
  "https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1200&q=80";

function isAbsoluteUrl(path: string): boolean {
  return path.startsWith("http://") || path.startsWith("https://");
}

/**
 * Resolve a media path to a URL.
 * Absolute URLs are returned as-is.
 * Relative paths need either API /storage (ephemeral on Render free)
 * or NEXT_PUBLIC_MEDIA_BASE_URL (e.g. Supabase Storage public base).
 */
export function mediaUrl(path?: string | null): string | null {
  if (!path) return null;
  if (isAbsoluteUrl(path)) return path;

  const durableBase = process.env.NEXT_PUBLIC_MEDIA_BASE_URL?.replace(/\/$/, "");
  if (durableBase) {
    return `${durableBase}/${path.replace(/^\/+/, "")}`;
  }

  const apiBase = process.env.NEXT_PUBLIC_API_URL?.replace(/\/api\/?$/, "") ?? "";
  if (!apiBase) return null;
  return `${apiBase}/storage/${path.replace(/^\/+/, "")}`;
}

export function coverFor(
  slug: string,
  ...paths: Array<string | null | undefined>
): string {
  for (const path of paths) {
    if (!path) continue;

    // Prefer durable absolute URLs. Relative Render uploads often 404 after redeploy.
    if (isAbsoluteUrl(path)) return path;

    if (process.env.NEXT_PUBLIC_MEDIA_BASE_URL) {
      const url = mediaUrl(path);
      if (url) return url;
    }
  }

  return FALLBACK_COVERS[slug] || DEFAULT_COVER;
}
