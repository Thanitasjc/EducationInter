import { getTranslations } from "next-intl/server";
import { Link } from "@/i18n/navigation";

export async function SiteFooter() {
  const t = await getTranslations("footer");
  const brand = await getTranslations();

  return (
    <footer className="bg-win-purple-deep text-white">
      <div className="mx-auto flex max-w-7xl flex-col gap-6 px-4 py-12 md:flex-row md:items-center md:justify-between md:px-8">
        <div>
          <p className="text-xl font-bold">{brand("brand")}</p>
          <p className="mt-2 text-sm text-white/70">{t("tagline")}</p>
        </div>
        <div className="flex gap-4 text-sm text-white/80">
          <Link href="/contact">Contact</Link>
          <Link href="/apply">Apply</Link>
          <Link href="/blog">Blog</Link>
        </div>
      </div>
      <div className="border-t border-white/10 px-4 py-4 text-center text-xs text-white/60">
        © {new Date().getFullYear()} {brand("brand")}. {t("rights")}
      </div>
    </footer>
  );
}
