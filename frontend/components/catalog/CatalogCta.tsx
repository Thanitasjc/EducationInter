import { getTranslations } from "next-intl/server";
import { Link } from "@/i18n/navigation";

type Props = {
  title?: string;
  body?: string;
};

export async function CatalogCta({ title, body }: Props) {
  const t = await getTranslations("home");

  return (
    <div className="mt-10 overflow-hidden rounded-3xl bg-gradient-to-r from-win-purple to-win-blue px-6 py-8 text-white md:px-10">
      <h2 className="text-2xl font-bold">{title ?? t("ctaTitle")}</h2>
      <p className="mt-2 max-w-2xl text-white/85">{body ?? t("ctaBody")}</p>
      <div className="mt-5 flex flex-wrap gap-3">
        <Link href="/contact" className="btn-primary bg-white text-win-purple hover:bg-win-sky">
          {t("consult")}
        </Link>
        <Link href="/apply" className="btn-secondary">
          {t("applyNow")}
        </Link>
      </div>
    </div>
  );
}
