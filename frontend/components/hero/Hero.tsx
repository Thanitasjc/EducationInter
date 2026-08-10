import { getTranslations } from "next-intl/server";
import { mediaUrl } from "@/lib/media";
import { HeroSlider, type HeroSlideView } from "./HeroSlider";

type Props = {
  hero?: Record<string, unknown> | null;
  locale: string;
};

const FALLBACK_SLIDES: Array<{ image: string }> = [
  {
    image:
      "https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=1920&q=80",
  },
  {
    image:
      "https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1920&q=80",
  },
  {
    image:
      "https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=1920&q=80",
  },
];

function asRecordArray(value: unknown): Array<Record<string, unknown>> {
  if (!Array.isArray(value)) return [];
  return value.filter((item): item is Record<string, unknown> => !!item && typeof item === "object");
}

function pickString(
  source: Record<string, unknown> | null | undefined,
  key: string,
): string | undefined {
  const value = source?.[key];
  return typeof value === "string" && value.trim() ? value : undefined;
}

export async function Hero({ hero, locale }: Props) {
  const t = await getTranslations("hero");
  const tBrand = await getTranslations();

  const headline =
    pickString(hero, `headline_${locale}`) ??
    pickString(hero, "headline_th") ??
    t("headline");
  const subheadline =
    pickString(hero, `subheadline_${locale}`) ??
    pickString(hero, "subheadline_th") ??
    t("subheadline");
  const ctaPrimary =
    pickString(hero, `cta_primary_${locale}`) ??
    pickString(hero, "cta_primary_th") ??
    t("ctaPrimary");
  const ctaSecondary =
    pickString(hero, `cta_secondary_${locale}`) ??
    pickString(hero, "cta_secondary_th") ??
    t("ctaSecondary");
  const ctaPrimaryHref = pickString(hero, "cta_primary_url") ?? "/contact";
  const ctaSecondaryHref = pickString(hero, "cta_secondary_url") ?? "/universities";

  const intervalRaw = hero?.slide_interval_ms;
  const intervalMs =
    typeof intervalRaw === "number"
      ? intervalRaw
      : typeof intervalRaw === "string" && intervalRaw.trim()
        ? Number(intervalRaw)
        : 5500;

  const cmsSlides: HeroSlideView[] = asRecordArray(hero?.slides).flatMap(
    (slide) => {
      const imageRaw = pickString(slide, "image");
      const image = mediaUrl(imageRaw) || imageRaw;
      if (!image) return [];

      return [
        {
          image,
          headline:
            pickString(slide, `headline_${locale}`) ??
            pickString(slide, "headline_th") ??
            headline,
          subheadline:
            pickString(slide, `subheadline_${locale}`) ??
            pickString(slide, "subheadline_th") ??
            subheadline,
          link: pickString(slide, "link"),
        },
      ];
    },
  );

  const slides: HeroSlideView[] =
    cmsSlides.length > 0
      ? cmsSlides
      : FALLBACK_SLIDES.map((slide) => ({
          image: slide.image,
          headline,
          subheadline,
        }));

  return (
    <HeroSlider
      brand={tBrand("brand")}
      slides={slides}
      ctaPrimary={ctaPrimary}
      ctaSecondary={ctaSecondary}
      ctaPrimaryHref={ctaPrimaryHref}
      ctaSecondaryHref={ctaSecondaryHref}
      intervalMs={Number.isFinite(intervalMs) ? intervalMs : 5500}
    />
  );
}
