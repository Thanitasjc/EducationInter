export type Country = {
  id: number;
  slug: string;
  name_th: string;
  name_en: string;
  code?: string | null;
  cover_path?: string | null;
  summary_th?: string | null;
  summary_en?: string | null;
  content_th?: string | null;
  content_en?: string | null;
  tuition_info?: Record<string, unknown> | null;
  living_cost_info?: Record<string, unknown> | null;
  visa_info?: Record<string, unknown> | null;
  intakes?: string[] | null;
  universities?: University[];
  scholarships?: Scholarship[];
};

export type University = {
  id: number;
  country_id?: number;
  slug: string;
  name_th: string;
  name_en: string;
  type?: string | null;
  logo_path?: string | null;
  cover_path?: string | null;
  ranking_qs?: number | null;
  ranking_the?: number | null;
  tuition_min?: string | number | null;
  tuition_max?: string | number | null;
  currency?: string | null;
  about_th?: string | null;
  about_en?: string | null;
  entry_requirements?: Record<string, unknown> | string[] | null;
  accommodation_info?: Record<string, unknown> | null;
  country?: Pick<Country, "id" | "slug" | "name_th" | "name_en"> | null;
  city?: { id: number; slug: string; name_th: string; name_en: string } | null;
  courses?: Course[];
  scholarships?: Scholarship[];
};

export type Course = {
  id: number;
  university_id?: number;
  slug: string;
  name_th: string;
  name_en: string;
  degree_level?: string | null;
  duration_months?: number | null;
  tuition?: string | number | null;
  currency?: string | null;
  intakes?: string[] | null;
  entry_requirements?: Record<string, unknown> | string[] | null;
  english_requirements?: Record<string, unknown> | null;
  summary_th?: string | null;
  summary_en?: string | null;
  cover_path?: string | null;
  university?: Pick<
    University,
    "id" | "slug" | "name_th" | "name_en" | "country" | "cover_path" | "logo_path"
  > | null;
  category?: { id: number; slug: string; name_th: string; name_en: string } | null;
};

export type Scholarship = {
  id: number;
  slug: string;
  title_th: string;
  title_en: string;
  amount_label_th?: string | null;
  amount_label_en?: string | null;
  cover_path?: string | null;
  logo_path?: string | null;
  deadline?: string | null;
  eligibility?: string[] | null;
  requirements?: string[] | null;
  how_to_apply_th?: string | null;
  how_to_apply_en?: string | null;
  country?: Pick<Country, "id" | "slug" | "name_th" | "name_en"> | null;
  university?: Pick<
    University,
    "id" | "slug" | "name_th" | "name_en" | "logo_path" | "cover_path" | "about_th" | "about_en"
  > | null;
};

export type Paginated<T> = {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
};
