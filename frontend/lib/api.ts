import type {
  Country,
  Course,
  Paginated,
  Scholarship,
  University,
} from "@/types/catalog";
import { authHeaders, getToken } from "@/lib/auth";
import type { ProgramItem } from "@/lib/programs";

const API_URL = process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000/api";

type FetchOptions = RequestInit & {
  locale?: string;
  query?: Record<string, string | number | undefined | null>;
  next?: NextFetchRequestConfig;
  auth?: boolean;
  formData?: boolean;
};

async function apiFetch<T>(path: string, options: FetchOptions = {}): Promise<T> {
  const url = new URL(`${API_URL}${path}`);

  if (options.locale) {
    url.searchParams.set("locale", options.locale);
  }

  if (options.query) {
    Object.entries(options.query).forEach(([key, value]) => {
      if (value !== undefined && value !== null && value !== "") {
        url.searchParams.set(key, String(value));
      }
    });
  }

  const headers: Record<string, string> = {
    Accept: "application/json",
    ...(options.auth ? (authHeaders() as Record<string, string>) : {}),
    ...(options.headers as Record<string, string> | undefined),
  };

  if (!options.formData) {
    headers["Content-Type"] = "application/json";
  }

  const timeoutMs = Number(process.env.API_FETCH_TIMEOUT_MS ?? 12_000);
  const res = await fetch(url.toString(), {
    ...options,
    headers,
    next: options.next ?? { revalidate: 60 },
    // Cold Render free-tier wakeups can exceed Vercel's static generation budget.
    signal: options.signal ?? AbortSignal.timeout(timeoutMs),
  });

  if (!res.ok) {
    throw new Error(`API ${res.status}: ${path}`);
  }

  return res.json() as Promise<T>;
}

export type HomeSection = {
  id: number;
  key: string;
  layout?: string;
  title_th?: string | null;
  title_en?: string | null;
  subtitle_th?: string | null;
  subtitle_en?: string | null;
  cover_path?: string | null;
  cover_url?: string | null;
  items?: Array<Record<string, unknown>> | null;
  cta_label_th?: string | null;
  cta_label_en?: string | null;
  cta_url?: string | null;
  sort_order?: number;
};

export type HomePayload = {
  locale: string;
  hero: Record<string, unknown> | null;
  sections?: HomeSection[];
  countries: Country[];
  universities: University[];
  courses: Course[];
  programs?: ProgramItem[];
  scholarships: Scholarship[];
  services: Array<Record<string, unknown>>;
  reviews: Array<Record<string, unknown>>;
  partners: Array<Record<string, unknown>>;
  posts: Array<Record<string, unknown>>;
  events: Array<Record<string, unknown>>;
};

export async function getHome(locale: string): Promise<HomePayload | null> {
  try {
    return await apiFetch<HomePayload>("/home", { locale });
  } catch {
    return null;
  }
}

export async function getCountries() {
  try {
    const res = await apiFetch<{ data: Country[] }>("/countries");
    return res.data;
  } catch {
    return [];
  }
}

export async function getCountry(slug: string) {
  try {
    const res = await apiFetch<{ data: Country }>(`/countries/${slug}`);
    return res.data;
  } catch {
    return null;
  }
}

export async function getUniversities(query: Record<string, string | undefined> = {}) {
  try {
    return await apiFetch<Paginated<University>>("/universities", { query });
  } catch {
    return { data: [], current_page: 1, last_page: 1, per_page: 12, total: 0 };
  }
}

export async function getUniversity(slug: string) {
  try {
    const res = await apiFetch<{ data: University }>(`/universities/${slug}`);
    return res.data;
  } catch {
    return null;
  }
}

export async function getCourses(query: Record<string, string | undefined> = {}) {
  try {
    return await apiFetch<Paginated<Course>>("/courses", { query });
  } catch {
    return { data: [], current_page: 1, last_page: 1, per_page: 12, total: 0 };
  }
}

export async function getCourse(slug: string) {
  try {
    const res = await apiFetch<{ data: Course }>(`/courses/${slug}`);
    return res.data;
  } catch {
    return null;
  }
}

export async function getScholarships(query: Record<string, string | undefined> = {}) {
  try {
    return await apiFetch<Paginated<Scholarship>>("/scholarships", { query });
  } catch {
    return { data: [], current_page: 1, last_page: 1, per_page: 12, total: 0 };
  }
}

export async function getScholarship(slug: string) {
  try {
    const res = await apiFetch<{ data: Scholarship }>(`/scholarships/${slug}`);
    return res.data;
  } catch {
    return null;
  }
}

export async function getPrograms(query: Record<string, string | undefined> = {}) {
  try {
    return await apiFetch<Paginated<ProgramItem>>("/programs", { query });
  } catch {
    return { data: [], current_page: 1, last_page: 1, per_page: 12, total: 0 };
  }
}

export async function getProgram(slug: string) {
  try {
    const res = await apiFetch<{ data: ProgramItem }>(`/programs/${slug}`);
    return res.data;
  } catch {
    return null;
  }
}

export type ServiceItem = {
  id: number;
  slug: string;
  title_th: string;
  title_en: string;
  summary_th?: string | null;
  summary_en?: string | null;
  content_th?: string | null;
  content_en?: string | null;
  cta_label_th?: string | null;
  cta_label_en?: string | null;
  cta_url?: string | null;
};

export async function getServices() {
  try {
    const res = await apiFetch<{ data: ServiceItem[] }>("/services");
    return res.data;
  } catch {
    return [];
  }
}

export async function getService(slug: string) {
  try {
    const res = await apiFetch<{ data: ServiceItem }>(`/services/${slug}`);
    return res.data;
  } catch {
    return null;
  }
}

export async function createLead(payload: Record<string, unknown>) {
  return apiFetch("/leads", {
    method: "POST",
    body: JSON.stringify(payload),
    next: { revalidate: 0 },
  });
}

export type DocumentTypeOption = {
  id: number;
  slug: string;
  name_th: string;
  name_en: string;
  is_required: boolean;
};

export async function getDocumentTypes() {
  return apiFetch<{ data: DocumentTypeOption[] }>("/document-types", {
    next: { revalidate: 300 },
  });
}

export async function submitApplication(payload: Record<string, unknown> | FormData) {
  const isFormData = typeof FormData !== "undefined" && payload instanceof FormData;

  return apiFetch<{
    message: string;
    data: { application_no: string; status: string; next_action?: string };
    lead_id: number;
    claim?: { email: string; token: string | null };
  }>("/applications", {
    method: "POST",
    body: isFormData ? payload : JSON.stringify(payload),
    formData: isFormData,
    next: { revalidate: 0 },
    cache: "no-store",
    signal: AbortSignal.timeout(Number(process.env.API_UPLOAD_TIMEOUT_MS ?? 60_000)),
  });
}

export function getSocialLoginUrl(provider: "facebook" | "line") {
  return `${API_URL}/auth/${provider}/redirect`;
}

export async function login(email: string, password: string) {
  return apiFetch<{ token: string; user: Record<string, unknown> }>("/auth/login", {
    method: "POST",
    body: JSON.stringify({ email, password }),
    next: { revalidate: 0 },
    cache: "no-store",
  });
}

export async function register(payload: Record<string, unknown>) {
  return apiFetch<{ token: string; user: Record<string, unknown> }>("/auth/register", {
    method: "POST",
    body: JSON.stringify(payload),
    next: { revalidate: 0 },
  });
}

export async function forgotPassword(email: string) {
  return apiFetch<{ message: string }>("/auth/forgot-password", {
    method: "POST",
    body: JSON.stringify({ email }),
    next: { revalidate: 0 },
    cache: "no-store",
  });
}

export async function resetPassword(payload: {
  email: string;
  token: string;
  password: string;
  password_confirmation: string;
}) {
  return apiFetch<{ message: string; token: string; user: Record<string, unknown> }>(
    "/auth/reset-password",
    {
      method: "POST",
      body: JSON.stringify(payload),
      next: { revalidate: 0 },
      cache: "no-store",
    },
  );
}

export async function getStudentDashboard() {
  if (!getToken()) throw new Error("Unauthorized");
  return apiFetch<Record<string, unknown>>("/student/dashboard", {
    auth: true,
    next: { revalidate: 0 },
  });
}

export async function getStudentApplications() {
  return apiFetch<{ data: Array<Record<string, unknown>> }>("/student/applications", {
    auth: true,
    next: { revalidate: 0 },
  });
}

export async function getStudentDocuments() {
  return apiFetch<{
    data: Array<Record<string, unknown>>;
    types: Array<Record<string, unknown>>;
    checklist?: Array<{
      type: Record<string, unknown>;
      document: Record<string, unknown> | null;
      status: string;
    }>;
  }>("/student/documents", {
    auth: true,
    next: { revalidate: 0 },
  });
}

export async function uploadStudentDocument(formData: FormData) {
  return apiFetch<{ data: Record<string, unknown> }>("/student/documents", {
    method: "POST",
    body: formData,
    auth: true,
    formData: true,
    next: { revalidate: 0 },
  });
}

export async function getStudentAppointments() {
  return apiFetch<{ data: Array<Record<string, unknown>> }>("/student/appointments", {
    auth: true,
    next: { revalidate: 0 },
  });
}

export async function updateStudentProfile(payload: Record<string, unknown>) {
  return apiFetch<{ user: Record<string, unknown> }>("/student/profile", {
    method: "PATCH",
    body: JSON.stringify(payload),
    auth: true,
    next: { revalidate: 0 },
  });
}

export type BlogPost = {
  id: number;
  slug: string;
  title_th: string;
  title_en: string;
  excerpt_th?: string | null;
  excerpt_en?: string | null;
  content_th?: string | null;
  content_en?: string | null;
  cover_path?: string | null;
  published_at?: string | null;
  category?: { id: number; slug: string; name_th: string; name_en: string } | null;
};

export async function getPosts(query: Record<string, string | undefined> = {}) {
  try {
    return await apiFetch<Paginated<BlogPost>>("/posts", { query });
  } catch {
    return { data: [], current_page: 1, last_page: 1, per_page: 12, total: 0 };
  }
}

export async function getPost(slug: string) {
  try {
    const res = await apiFetch<{ data: BlogPost & { seo?: Record<string, unknown> | null } }>(
      `/posts/${slug}`,
    );
    return res.data;
  } catch {
    return null;
  }
}

export async function getPostCategories() {
  try {
    const res = await apiFetch<{
      data: Array<{ id: number; slug: string; name_th: string; name_en: string }>;
    }>("/post-categories");
    return res.data;
  } catch {
    return [];
  }
}

export type EventItem = {
  id: number;
  slug: string;
  title_th: string;
  title_en: string;
  summary_th?: string | null;
  summary_en?: string | null;
  content_th?: string | null;
  content_en?: string | null;
  cover_path?: string | null;
  starts_at?: string | null;
  ends_at?: string | null;
  location?: string | null;
};

export async function getEvents(query: Record<string, string | undefined> = {}) {
  try {
    return await apiFetch<Paginated<EventItem>>("/events", { query });
  } catch {
    return { data: [], current_page: 1, last_page: 1, per_page: 12, total: 0 };
  }
}

export async function getEvent(slug: string) {
  try {
    const res = await apiFetch<{ data: EventItem }>(`/events/${slug}`);
    return res.data;
  } catch {
    return null;
  }
}

export type PageContentValue = Record<string, unknown>;

export async function getPageContent(key: string) {
  try {
    const res = await apiFetch<{ data: { key: string; value: PageContentValue } }>(
      `/pages/${key}`,
    );
    return res.data;
  } catch {
    return null;
  }
}

export async function getStudentNotifications() {
  return apiFetch<{
    data: Array<Record<string, unknown>>;
    unread_count: number;
  }>("/student/notifications", {
    auth: true,
    next: { revalidate: 0 },
  });
}

export async function markNotificationRead(id: number) {
  return apiFetch(`/student/notifications/${id}/read`, {
    method: "PATCH",
    auth: true,
    next: { revalidate: 0 },
  });
}

export async function markAllNotificationsRead() {
  return apiFetch("/student/notifications/read-all", {
    method: "PATCH",
    auth: true,
    next: { revalidate: 0 },
  });
}

export { API_URL };
