import { getTranslations, setRequestLocale } from "next-intl/server";
import { CatalogCta } from "@/components/catalog/CatalogCta";
import { Link } from "@/i18n/navigation";
import { getPostCategories, getPosts } from "@/lib/api";
import { coverFor } from "@/lib/media";
import { buildMetadata } from "@/lib/seo";
import { localized } from "@/lib/utils";

type Props = {
  params: Promise<{ locale: string }>;
  searchParams: Promise<{ category?: string }>;
};

export async function generateMetadata({ params }: Props) {
  const { locale } = await params;
  const t = await getTranslations({ locale, namespace: "blog" });
  return buildMetadata({
    locale,
    path: "/blog",
    title: `${t("title")} | Education Interntions`,
    description: t("subtitle"),
  });
}

export default async function BlogPage({ params, searchParams }: Props) {
  const { locale } = await params;
  const { category } = await searchParams;
  setRequestLocale(locale);

  const t = await getTranslations("blog");
  const [posts, categories] = await Promise.all([
    getPosts(category ? { category } : {}),
    getPostCategories(),
  ]);

  return (
    <section className="section">
      <div className="mx-auto max-w-7xl">
        <h1 className="section-title">{t("title")}</h1>
        <p className="section-subtitle">{t("subtitle")}</p>

        <div className="mt-6 flex flex-wrap gap-2">
          <Link
            href="/blog"
            className={`rounded-lg px-3 py-1.5 text-sm font-semibold ${
              !category ? "bg-win-purple text-white" : "bg-white text-win-muted border border-black/10"
            }`}
          >
            {t("allCategories")}
          </Link>
          {categories.map((item) => (
            <Link
              key={item.id}
              href={`/blog?category=${item.slug}`}
              className={`rounded-lg px-3 py-1.5 text-sm font-semibold ${
                category === item.slug
                  ? "bg-win-purple text-white"
                  : "bg-white text-win-muted border border-black/10"
              }`}
            >
              {localized(item, locale, "name")}
            </Link>
          ))}
        </div>

        <div className="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
          {posts.data.map((post) => {
            const title = localized(post, locale, "title");
            const cover = coverFor(post.slug, post.cover_path);
            return (
              <Link
                key={post.id}
                href={`/blog/${post.slug}`}
                className="card-soft block overflow-hidden p-0 transition hover:-translate-y-1 hover:border-win-purple/30"
              >
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img src={cover} alt={title} className="aspect-[16/10] w-full object-cover" />
                <div className="p-5">
                  {post.category ? (
                    <p className="text-xs font-semibold uppercase text-win-purple">
                      {localized(post.category, locale, "name")}
                    </p>
                  ) : null}
                  <h2 className="mt-2 text-lg font-bold">{title}</h2>
                  <p className="mt-2 line-clamp-3 text-sm text-win-muted">
                    {localized(post, locale, "excerpt")}
                  </p>
                </div>
              </Link>
            );
          })}
        </div>
        {posts.data.length === 0 && (
          <div className="card-soft mt-6 text-win-muted">{t("empty")}</div>
        )}
        <CatalogCta />
      </div>
    </section>
  );
}
