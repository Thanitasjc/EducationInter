import { Link } from "@/i18n/navigation";

type Props = {
  title: string;
  description?: string;
};

export function PagePlaceholder({ title, description }: Props) {
  return (
    <section className="section">
      <div className="mx-auto max-w-3xl card-soft">
        <h1 className="text-3xl font-bold text-win-ink">{title}</h1>
        {description ? (
          <p className="mt-3 text-win-muted">{description}</p>
        ) : null}
        <div className="mt-6 flex gap-3">
          <Link href="/contact" className="btn-primary">
            Consult
          </Link>
          <Link href="/universities" className="rounded-xl border border-black/10 px-5 py-3 text-sm font-semibold">
            Universities
          </Link>
        </div>
      </div>
    </section>
  );
}
