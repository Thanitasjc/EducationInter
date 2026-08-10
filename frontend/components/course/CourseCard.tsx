import { getTranslations } from "next-intl/server";
import { Link } from "@/i18n/navigation";
import type { Course } from "@/types/catalog";
import { coverFor } from "@/lib/media";
import { localized } from "@/lib/utils";

type Props = {
  course: Course;
  locale: string;
};

export async function CourseCard({ course, locale }: Props) {
  const t = await getTranslations("catalog");
  const uni = course.university
    ? localized(course.university, locale, "name")
    : "";
  const name = localized(course, locale, "name");
  const cover = coverFor(
    course.slug,
    course.cover_path,
    course.university?.cover_path,
  );

  return (
    <Link
      href={`/courses/${course.slug}`}
      className="card-soft block overflow-hidden p-0 transition hover:-translate-y-1 hover:border-win-purple/30"
    >
      {/* eslint-disable-next-line @next/next/no-img-element */}
      <img src={cover} alt={name} className="aspect-[16/10] w-full object-cover" />
      <div className="p-5">
        <p className="font-bold text-win-purple">{name}</p>
        <p className="mt-2 text-sm text-win-muted">{uni}</p>
        <p className="mt-3 text-sm font-medium text-win-ink">
          {[
            course.degree_level,
            course.duration_months ? `${course.duration_months} ${t("months")}` : null,
          ]
            .filter(Boolean)
            .join(" · ")}
        </p>
        {course.tuition ? (
          <p className="mt-2 text-sm font-semibold text-win-blue">
            {course.tuition} {course.currency}
          </p>
        ) : null}
      </div>
    </Link>
  );
}
