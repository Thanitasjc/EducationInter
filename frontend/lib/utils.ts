import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

export function localized<T extends Record<string, unknown>>(
  item: T,
  locale: string,
  field: string,
): string {
  const key = `${field}_${locale}` as keyof T;
  const fallback = `${field}_en` as keyof T;
  const value = item[key] ?? item[fallback] ?? "";
  return String(value);
}
