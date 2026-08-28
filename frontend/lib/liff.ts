"use client";

import liff from "@line/liff";

export const LIFF_ID = "2011268960-1YH2wdBp";

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
