import { getTranslations, setRequestLocale } from "next-intl/server";
import { notFound } from "next/navigation";
import { CatalogCta } from "@/components/catalog/CatalogCta";
import { JsonLd } from "@/components/seo/JsonLd";
import { Link } from "@/i18n/navigation";
import { getPost } from "@/lib/api";
import { coverFor } from "@/lib/media";
import { buildMetadata, type SeoPayload } from "@/lib/seo";
import { localized } from "@/lib/utils";

type Props = {
  params: Promise<{ locale: string; slug: string }>;
};

export async function generateMetadata({ params }: Props) {
  const { locale, slug } = await params;
  const post = await getPost(slug);
  if (!post) return {};
  const seo = (post as { seo?: SeoPayload | null }).seo;
  return buildMetadata({
    locale,
    path: `/blog/${slug}`,
    title: `${localized(post, locale, "title")} | Education Interntions`,
    description: localized(post, locale, "excerpt"),
    seo,
  });
}

export default async function BlogDetailPage({ params }: Props) {
  const { locale, slug } = await params;
  setRequestLocale(locale);

  const post = await getPost(slug);
  if (!post) notFound();

  const t = await getTranslations("blog");
  const title = localized(post, locale, "title");
  const seo = (post as { seo?: SeoPayload | null }).seo;
  const cover = coverFor(post.slug, post.cover_path);

  return (
    <section className="section">
      <JsonLd
        data={
          seo?.schema_json || {
            "@context": "https://schema.org",
            "@type": "Article",
            headline: title,
            description: localized(post, locale, "excerpt"),
            image: cover,
            datePublished: post.published_at || undefined,
          }
        }
      />
      <article className="mx-auto max-w-3xl space-y-6">
        <div className="overflow-hidden rounded-3xl">
          {/* eslint-disable-next-line @next/next/no-img-element */}
          <img src={cover} alt={title} className="aspect-[21/9] w-full object-cover md:aspect-[2/1]" />
        </div>
        <div className="card-soft">
          {post.category ? (
            <p className="text-xs font-semibold uppercase text-win-purple">
              {localized(post.category, locale, "name")}
            </p>
          ) : null}
          <h1 className="mt-2 text-3xl font-bold md:text-4xl">{title}</h1>
          {post.published_at ? (
            <p className="mt-2 text-sm text-win-muted">
              {new Date(post.published_at).toLocaleDateString()}
            </p>
          ) : null}
          <div className="mt-6 whitespace-pre-line text-win-ink leading-relaxed">
            {localized(post, locale, "content") || localized(post, locale, "excerpt")}
          </div>
          <div className="mt-8 flex gap-3">
            <Link href="/contact" className="btn-primary">
              {t("consult")}
            </Link>
            <Link href="/blog" className="rounded-xl border border-black/10 px-5 py-3 text-sm font-semibold">
              {t("back")}
            </Link>
          </div>
        </div>
        <CatalogCta />
      </article>
    </section>
  );
}
