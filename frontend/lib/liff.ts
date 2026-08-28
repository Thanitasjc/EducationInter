"use client";

import liff from "@line/liff";

export const LIFF_ID = process.env.NEXT_PUBLIC_LIFF_ID ?? "2011268960-1YH2wdBp";

export const LIFF_URL = process.env.NEXT_PUBLIC_LIFF_URL ?? `https://liff.line.me/${LIFF_ID}`;

const LINE_LIFF_LOGIN_KEY = "line_liff_login";

declare global {
  interface Window {
    liff?: typeof liff;
  }
}

type LiffInitState = {
  initialized: boolean;
  isInClient: boolean;
};

type LiffInitOptions = {
  withLoginOnExternalBrowser?: boolean;
};

let initialization: Promise<LiffInitState> | null = null;
let currentInitOptions: LiffInitOptions = {};

export function isLineInAppBrowser(): boolean {
  if (typeof navigator === "undefined") {
    return false;
  }

  return /Line\//i.test(navigator.userAgent);
}

export function isLiffLoginIntent(): boolean {
  if (typeof window === "undefined") {
    return false;
  }

  return sessionStorage.getItem(LINE_LIFF_LOGIN_KEY) === "1";
}

export function cleanupStaleLiffUrl(): void {
  if (typeof window === "undefined" || isLiffLoginIntent()) {
    return;
  }

  const url = new URL(window.location.href);
  if (!url.searchParams.has("liff.referrer")) {
    return;
  }

  url.searchParams.delete("liff.referrer");
  const query = url.searchParams.toString();
  const next = `${url.pathname}${query ? `?${query}` : ""}${url.hash}`;
  window.history.replaceState({}, "", next);
}

export function startLineLiffLogin(): void {
  sessionStorage.setItem(LINE_LIFF_LOGIN_KEY, "1");
}

export function clearLineLiffLoginIntent(): void {
  sessionStorage.removeItem(LINE_LIFF_LOGIN_KEY);
}

function createInitialization(options: LiffInitOptions): Promise<LiffInitState> {
  return liff
    .init({
      liffId: LIFF_ID,
      ...(options.withLoginOnExternalBrowser ? { withLoginOnExternalBrowser: true } : {}),
    })
    .then(() => ({
      initialized: true,
      isInClient: liff.isInClient(),
    }))
    .catch((error) => {
      initialization = null;
      throw error;
    });
}

export function initializeLiff(options: LiffInitOptions = {}): Promise<LiffInitState> {
  if (typeof window === "undefined") {
    return Promise.reject(new Error("LIFF can only be initialized in a browser."));
  }

  window.liff = liff;

  const optionsChanged =
    currentInitOptions.withLoginOnExternalBrowser !== options.withLoginOnExternalBrowser;

  if (optionsChanged) {
    initialization = null;
    currentInitOptions = options;
  }

  if (!initialization) {
    initialization = createInitialization(options);
  }

  return initialization;
}

export function isLiffClient(): boolean {
  return typeof window !== "undefined" && liff.isInClient();
}

function getLiffRedirectUri(): string {
  return `${window.location.origin}${window.location.pathname}`;
}

export async function getLineIdToken(
  options: { allowExternalBrowser?: boolean } = {},
): Promise<string | null> {
  const state = await initializeLiff({
    withLoginOnExternalBrowser: options.allowExternalBrowser,
  });

  if (!state.isInClient && !options.allowExternalBrowser) {
    return null;
  }

  if (!liff.isLoggedIn()) {
    liff.login({ redirectUri: getLiffRedirectUri() });
    return null;
  }

  return liff.getIDToken();
}
