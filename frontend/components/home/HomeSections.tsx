import { getTranslations } from "next-intl/server";
import { Link } from "@/i18n/navigation";
import { HomeCardSlider } from "@/components/home/HomeCardSlider";
import { LearnLanguageSection } from "@/components/home/LearnLanguageSection";
import { PartnersSection } from "@/components/home/PartnersSection";
import { PathwaysSection } from "@/components/home/PathwaysSection";
import { ScholarshipsSection } from "@/components/home/ScholarshipsSection";
import type { HomePayload } from "@/lib/api";
import { coverFor } from "@/lib/media";
import { localized } from "@/lib/utils";

type Props = {
  data: HomePayload | null;
  locale: string;
};

export async function HomeSections({ data, locale }: Props) {
  const t = await getTranslations("home");

  const countries = data?.countries ?? [];
  const universities = data?.universities ?? [];
  const courses = data?.courses ?? [];
  const scholarships = data?.scholarships ?? [];
  const services = data?.services ?? [];
  const reviews = data?.reviews ?? [];
  const posts = data?.posts ?? [];
  const events = data?.events ?? [];
  const partners = data?.partners ?? [];
  const sections = data?.sections ?? [];
  const bachelorPathways = sections.find((s) => s.key === "bachelor-pathways");
  const learnLanguageSection = sections.find((s) => s.key === "learn-language");
  const tBrand = await getTranslations();

  return (
    <>
      <section className="section">
        <div className="mx-auto max-w-7xl">
          <div className="flex items-end justify-between gap-4">
            <div>
              <h2 className="section-title">{t("countriesTitle")}</h2>
              <p className="section-subtitle">{t("countriesSubtitle")}</p>
            </div>
            <Link href="/countries" className="text-sm font-semibold text-win-purple">
              {t("viewAll")}
            </Link>
          </div>
          <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {countries.length > 0 ? (
              countries.map((country) => {
                const cover = coverFor(String(country.slug), country.cover_path);
                const name = localized(country, locale, "name");
                return (
                  <Link
                    key={String(country.id)}
                    href={`/countries/${country.slug}`}
                    className="card-soft overflow-hidden p-0 transition hover:-translate-y-1 hover:border-win-purple/30"
                  >
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img
                      src={cover}
                      alt={name}
                      className="aspect-[16/10] w-full object-cover"
                    />
                    <div className="p-5">
                      <p className="text-lg font-bold text-win-blue">{name}</p>
                      <p className="mt-2 line-clamp-2 text-sm text-win-muted">
                        {localized(country, locale, "summary")}
                      </p>
                    </div>
                  </Link>
                );
              })
            ) : (
              <EmptyCard label="UK / Australia / USA / Canada" />
            )}
          </div>
        </div>
      </section>

      <section className="section bg-win-sky/60">
        <div className="mx-auto max-w-7xl">
          <div className="flex items-end justify-between gap-4">
            <div>
              <h2 className="section-title">{t("universitiesTitle")}</h2>
              <p className="section-subtitle">{t("universitiesSubtitle")}</p>
            </div>
            <Link href="/universities" className="text-sm font-semibold text-win-purple">
              {t("viewAll")}
            </Link>
          </div>
          <div className="mt-8">
            {universities.length > 0 ? (
              <HomeCardSlider>
                {universities.map((uni) => {
                  const cover = coverFor(String(uni.slug), uni.cover_path);
                  return (
                    <Link
                      key={String(uni.id)}
                      href={`/universities/${uni.slug}`}
                      className="card-soft block h-full overflow-hidden p-0 transition hover:-translate-y-1 hover:border-win-purple/30"
                    >
                      {/* eslint-disable-next-line @next/next/no-img-element */}
                      <img
                        src={cover}
                        alt={localized(uni, locale, "name")}
                        className="aspect-[16/10] w-full object-cover"
                      />
                      <div className="p-5">
                        <p className="text-lg font-bold">{localized(uni, locale, "name")}</p>
                        <p className="mt-2 text-sm text-win-muted">
                          QS {String(uni.ranking_qs ?? "-")} · {t("viewDetails")}
                        </p>
                      </div>
                    </Link>
                  );
                })}
              </HomeCardSlider>
            ) : (
              <EmptyCard label="Featured universities from API" />
            )}
          </div>
        </div>
      </section>

      <section className="section">
        <div className="mx-auto max-w-7xl">
          <div className="flex items-end justify-between gap-4">
            <h2 className="section-title">{t("coursesTitle")}</h2>
            <Link href="/courses" className="text-sm font-semibold text-win-purple">
              {t("viewAll")}
            </Link>
          </div>
          <div className="mt-8">
            {courses.length > 0 ? (
              <HomeCardSlider>
                {courses.map((course) => {
                  const cover = coverFor(
                    String(course.slug),
                    course.cover_path,
                    course.university?.cover_path,
                  );
                  return (
                    <Link
                      key={String(course.id)}
                      href={`/courses/${course.slug}`}
                      className="card-soft block h-full overflow-hidden p-0 transition hover:-translate-y-1 hover:border-win-purple/30"
                    >
                      {/* eslint-disable-next-line @next/next/no-img-element */}
                      <img
                        src={cover}
                        alt={localized(course, locale, "name")}
                        className="aspect-[16/10] w-full object-cover"
                      />
                      <div className="p-5">
                        <p className="font-bold text-win-purple">
                          {localized(course, locale, "name")}
                        </p>
                        <p className="mt-2 text-sm text-win-muted">
                          {String(course.degree_level ?? "")} · {String(course.tuition ?? "-")}{" "}
                          {String(course.currency ?? "")}
                        </p>
                      </div>
                    </Link>
                  );
                })}
              </HomeCardSlider>
            ) : (
              <EmptyCard label="Popular courses" />
            )}
          </div>
        </div>
      </section>

      <PartnersSection
        partners={partners}
        brand={tBrand("brand")}
        title={t("partnersTitle")}
        seeMore={t("seeMore")}
        seeMoreHref="/universities"
      />

      <LearnLanguageSection locale={locale} section={learnLanguageSection} />

      {bachelorPathways ? (
        <PathwaysSection section={bachelorPathways} locale={locale} />
      ) : null}

      <ScholarshipsSection
        scholarships={scholarships}
        locale={locale}
        title={t("scholarshipsTitle")}
        subtitle={t.rich("scholarshipsSubtitle", {
          highlight: (chunks) => (
            <span className="font-semibold text-win-ink">{chunks}</span>
          ),
        })}
        seeMore={t("seeMore")}
        applyScholarship={t("applyScholarship")}
        amountTbd={t("amountTbd")}
      />

      <section className="section">
        <div className="mx-auto max-w-7xl">
          <h2 className="section-title">{t("servicesTitle")}</h2>
          <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {services.length > 0 ? (
              services.map((service) => {
                const slug = String(service.slug ?? "");
                const cover = coverFor(
                  slug,
                  service.image_path as string | undefined,
                );
                const title = localized(service, locale, "title");
                return (
                  <Link
                    key={String(service.id)}
                    href={`/services/${slug}`}
                    className="card-soft overflow-hidden p-0 transition hover:-translate-y-1 hover:border-win-purple/30"
                  >
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img src={cover} alt={title} className="aspect-[16/10] w-full object-cover" />
                    <div className="p-5">
                      <p className="font-bold text-win-blue">{title}</p>
                      <p className="mt-2 line-clamp-3 text-sm text-win-muted">
                        {localized(service, locale, "summary")}
                      </p>
                    </div>
                  </Link>
                );
              })
            ) : (
              <EmptyCard label="University Application / Visa / IELTS" />
            )}
          </div>
        </div>
      </section>

      <section className="section bg-win-sky/50">
        <div className="mx-auto max-w-7xl">
          <h2 className="section-title">{t("whyTitle")}</h2>
          <div className="mt-8 grid gap-4 md:grid-cols-4">
            {(
              [
                {
                  key: "why-advisors",
                  label: locale === "th" ? "ที่ปรึกษาครบวงจร" : "End-to-end advisors",
                },
                {
                  key: "why-network",
                  label: locale === "th" ? "เครือข่ายมหาวิทยาลัย" : "University network",
                },
                {
                  key: "why-funding",
                  label: locale === "th" ? "ช่วยเรื่องทุนและวีซ่า" : "Scholarship & visa help",
                },
                {
                  key: "why-support",
                  label: locale === "th" ? "ติดตามจนถึงวันเดินทาง" : "Support until departure",
                },
              ] as const
            ).map((item) => (
              <div key={item.key} className="card-soft overflow-hidden p-0">
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img
                  src={coverFor(item.key)}
                  alt={item.label}
                  className="aspect-[16/10] w-full object-cover"
                />
                <div className="p-5">
                  <p className="font-semibold text-win-ink">{item.label}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      <section className="section">
        <div className="mx-auto max-w-7xl">
          <h2 className="section-title">{t("reviewsTitle")}</h2>
          <div className="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            {reviews.length > 0 ? (
              reviews.map((review) => {
                const uniLabel = String(review.university_label ?? "").toLowerCase();
                const reviewKey = uniLabel.includes("melbourne")
                  ? "review-melbourne"
                  : uniLabel.includes("toronto")
                    ? "review-toronto"
                    : "review-manchester";
                const cover = coverFor(
                  reviewKey,
                  review.image_path as string | undefined,
                );
                return (
                  <article
                    key={String(review.id)}
                    className="card-soft overflow-hidden p-0"
                  >
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img
                      src={cover}
                      alt={String(review.student_name)}
                      className="aspect-[16/10] w-full object-cover"
                    />
                    <div className="p-5">
                      <p className="text-sm text-win-muted">
                        {localized(review, locale, "quote")}
                      </p>
                      <p className="mt-4 font-semibold">{String(review.student_name)}</p>
                      {review.university_label ? (
                        <p className="mt-1 text-xs text-win-muted">
                          {String(review.university_label)}
                          {review.country_label ? ` · ${String(review.country_label)}` : ""}
                        </p>
                      ) : null}
                    </div>
                  </article>
                );
              })
            ) : (
              <EmptyCard label="Success stories" />
            )}
          </div>
        </div>
      </section>

      <section className="section bg-win-sky/40">
        <div className="mx-auto max-w-7xl">
          <div className="flex items-end justify-between gap-4">
            <div>
              <h2 className="section-title">{t("eventsTitle")}</h2>
              <p className="section-subtitle">{t("eventsSubtitle")}</p>
            </div>
            <Link href="/events" className="text-sm font-semibold text-win-purple">
              {t("viewAll")}
            </Link>
          </div>
          <div className="mt-8 grid gap-4 md:grid-cols-3">
            {events.length > 0 ? (
              events.map((event) => {
                const slug = String(event.slug ?? "");
                const cover = coverFor(slug, event.cover_path as string | undefined);
                const title = localized(event, locale, "title");
                return (
                  <Link
                    key={String(event.id)}
                    href={`/events/${slug}`}
                    className="card-soft overflow-hidden p-0 transition hover:-translate-y-1 hover:border-win-purple/30"
                  >
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img src={cover} alt={title} className="aspect-[16/10] w-full object-cover" />
                    <div className="p-5">
                      <p className="text-xs font-semibold uppercase text-win-purple">
                        {event.starts_at
                          ? new Date(String(event.starts_at)).toLocaleDateString(
                              locale === "th" ? "th-TH" : "en-GB",
                              { dateStyle: "medium" },
                            )
                          : "—"}
                      </p>
                      <p className="mt-2 font-bold">{title}</p>
                      <p className="mt-2 line-clamp-2 text-sm text-win-muted">
                        {localized(event, locale, "summary")}
                      </p>
                    </div>
                  </Link>
                );
              })
            ) : (
              <EmptyCard label="Upcoming events" />
            )}
          </div>
        </div>
      </section>

      <section className="section bg-white">
        <div className="mx-auto max-w-7xl">
          <h2 className="section-title">{t("blogTitle")}</h2>
          <div className="mt-8 grid gap-4 md:grid-cols-3">
            {posts.length > 0 ? (
              posts.map((post) => {
                const slug = String(post.slug ?? "");
                const cover = coverFor(slug, post.cover_path as string | undefined);
                const title = localized(post, locale, "title");
                return (
                  <Link
                    key={String(post.id)}
                    href={`/blog/${slug}`}
                    className="card-soft overflow-hidden p-0 transition hover:-translate-y-1 hover:border-win-purple/30"
                  >
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img src={cover} alt={title} className="aspect-[16/10] w-full object-cover" />
                    <div className="p-5">
                      <p className="font-bold">{title}</p>
                    </div>
                  </Link>
                );
              })
            ) : (
              <EmptyCard label="SEO blog posts" />
            )}
          </div>
        </div>
      </section>

      <section className="section">
        <div className="relative mx-auto max-w-5xl overflow-hidden rounded-3xl">
          {/* eslint-disable-next-line @next/next/no-img-element */}
          <img
            src={coverFor("cta")}
            alt=""
            className="absolute inset-0 h-full w-full object-cover"
          />
          <div className="absolute inset-0 bg-gradient-to-r from-win-purple/95 via-win-blue/90 to-win-purple-deep/90" />
          <div className="relative px-6 py-12 text-white md:px-12">
            <h2 className="text-3xl font-bold">{t("ctaTitle")}</h2>
            <p className="mt-3 max-w-2xl text-white/85">{t("ctaBody")}</p>
            <div className="mt-6 flex flex-wrap gap-3">
              <Link href="/contact" className="btn-primary bg-white text-win-purple hover:bg-win-sky">
                {t("consult")}
              </Link>
              <Link href="/apply" className="btn-secondary">
                {t("applyNow")}
              </Link>
            </div>
          </div>
        </div>
      </section>
    </>
  );
}

function EmptyCard({ label }: { label: string }) {
  return (
    <div className="card-soft text-sm text-win-muted">
      {label}
    </div>
  );
}
