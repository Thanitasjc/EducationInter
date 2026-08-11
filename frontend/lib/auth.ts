const TOKEN_KEY = "win_token";
export const AUTH_CHANGE_EVENT = "win-auth-change";

function notifyAuthChange() {
  if (typeof window === "undefined") return;
  window.dispatchEvent(new Event(AUTH_CHANGE_EVENT));
}

export function getToken(): string | null {
  if (typeof window === "undefined") return null;
  return localStorage.getItem(TOKEN_KEY);
}

export function setToken(token: string) {
  localStorage.setItem(TOKEN_KEY, token);
  notifyAuthChange();
}

export function clearToken() {
  localStorage.removeItem(TOKEN_KEY);
  notifyAuthChange();
}

export function authHeaders(token?: string | null): HeadersInit {
  const value = token ?? getToken();
  if (!value) return {};
  return { Authorization: `Bearer ${value}` };
}
