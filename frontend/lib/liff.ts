"use client";

import liff from "@line/liff";

export const LIFF_ID = process.env.NEXT_PUBLIC_LIFF_ID ?? "2011268960-1YH2wdBp";

export const LIFF_URL = process.env.NEXT_PUBLIC_LIFF_URL ?? `https://liff.line.me/${LIFF_ID}`;

declare global {
  interface Window {
    liff?: typeof liff;
  }
}

type LiffInitState = {
  initialized: boolean;
  isInClient: boolean;
};

let initialization: Promise<LiffInitState> | null = null;

export function initializeLiff(): Promise<LiffInitState> {
  if (typeof window === "undefined") {
    return Promise.reject(new Error("LIFF can only be initialized in a browser."));
  }

  window.liff = liff;

  if (!initialization) {
    initialization = liff.init({ liffId: LIFF_ID }).then(() => ({
      initialized: true,
      isInClient: liff.isInClient(),
    }));
  }

  return initialization;
}

export function isLiffClient(): boolean {
  return typeof window !== "undefined" && liff.isInClient();
}

export async function getLineIdToken(): Promise<string | null> {
  const state = await initializeLiff();

  if (!state.isInClient) {
    return null;
  }

  if (!liff.isLoggedIn()) {
    liff.login({ redirectUri: window.location.href });
    return null;
  }

  return liff.getIDToken();
}
