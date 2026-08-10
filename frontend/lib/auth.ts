const TOKEN_KEY = "win_token";

export function getToken(): string | null {
  if (typeof window === "undefined") return null;
  return localStorage.getItem(TOKEN_KEY);
}

export function setToken(token: string) {
  localStorage.setItem(TOKEN_KEY, token);
}

export function clearToken() {
  localStorage.removeItem(TOKEN_KEY);
}

export function authHeaders(token?: string | null): HeadersInit {
  const value = token ?? getToken();
  if (!value) return {};
  return { Authorization: `Bearer ${value}` };
}
