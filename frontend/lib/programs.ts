export type AgeGroup =
  | "all"
  | "12-16"
  | "16-18"
  | "18-25"
  | "25-plus"
  | "50-plus";

export const AGE_GROUPS: AgeGroup[] = [
  "all",
  "12-16",
  "16-18",
  "18-25",
  "25-plus",
  "50-plus",
];

export type ProgramItem = {
  id: number;
  slug: string;
  title_th: string;
  title_en: string;
  summary_th?: string | null;
  summary_en?: string | null;
  content_th?: string | null;
  content_en?: string | null;
  age_min?: number | null;
  age_max?: number | null;
  age_label?: string | null;
  duration_label_th?: string | null;
  duration_label_en?: string | null;
  language?: string | null;
  destinations?: string[] | null;
  cover_path?: string | null;
  cover_url?: string | null;
  cta_label_th?: string | null;
  cta_label_en?: string | null;
  cta_url?: string | null;
  is_featured?: boolean;
};

function groupRange(group: AgeGroup): [number, number] | null {
  switch (group) {
    case "12-16":
      return [12, 16];
    case "16-18":
      return [16, 18];
    case "18-25":
      return [18, 25];
    case "25-plus":
      return [25, 99];
    case "50-plus":
      return [50, 99];
    default:
      return null;
  }
}

/** Client-side filter matching backend Program::scopeForAgeGroup */
export function filterProgramsByAge(
  programs: ProgramItem[],
  group: AgeGroup,
): ProgramItem[] {
  const range = groupRange(group);
  if (!range) return programs;

  const [min, max] = range;
  return programs.filter((program) => {
    if (program.age_min == null && program.age_max == null) return true;
    const pMin = program.age_min ?? 0;
    const pMax = program.age_max ?? 99;
    return pMin <= max && pMax >= min;
  });
}

export function parseAgeGroup(value?: string | null): AgeGroup {
  if (value && AGE_GROUPS.includes(value as AgeGroup)) {
    return value as AgeGroup;
  }
  return "all";
}
